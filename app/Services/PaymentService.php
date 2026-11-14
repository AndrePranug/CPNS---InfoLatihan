<?php

namespace App\Services;

use App\Models\Order;
use App\Models\Tryout;
use App\Models\User;
use App\Models\Voucher;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class PaymentService
{
    public function enabled(): bool
    {
        return (bool) config('payments.enabled', false);
    }

    public function gateway(): string
    {
        return (string) config('payments.gateway', 'none');
    }

    public function available(): bool
    {
        return $this->enabled() && $this->gateway() !== 'none';
    }

    public function hasAccess(User $user, Tryout $tryout): bool
    {
        return $tryout->price === 0 || Order::query()
            ->where('user_id', $user->id)
            ->where('tryout_id', $tryout->id)
            ->where('status', Order::STATUS_PAID)
            ->exists();
    }

    /**
     * @return array{voucher: Voucher|null, amount: int, discount: int, total: int}
     */
    public function quote(Tryout $tryout, ?string $code = null): array
    {
        $amount = max(0, (int) $tryout->price);
        $voucher = $this->voucher($code);
        $discount = $voucher?->discountFor($amount) ?? 0;

        return [
            'voucher' => $voucher,
            'amount' => $amount,
            'discount' => $discount,
            'total' => $amount - $discount,
        ];
    }

    public function voucher(?string $code): ?Voucher
    {
        $code = strtoupper(trim((string) $code));

        if ($code === '') {
            return null;
        }

        $voucher = Voucher::query()->where('code', $code)->first();

        if ($voucher === null || ! $voucher->isUsable()) {
            throw ValidationException::withMessages([
                'voucher' => 'Kode voucher tidak valid atau sudah kedaluwarsa.',
            ]);
        }

        return $voucher;
    }

    public function createOrder(User $user, Tryout $tryout, ?string $code = null): Order
    {
        if (! $this->available()) {
            throw ValidationException::withMessages([
                'payment' => 'Pembayaran belum tersedia. Coba lagi nanti.',
            ]);
        }

        // ponytail: order berhenti di status pending. Tambahkan adapter gateway
        // (redirect pembayaran + webhook) setelah akun Midtrans/Xendit tersedia.
        return DB::transaction(function () use ($user, $tryout, $code): Order {
            $quote = $this->quote($tryout, $code);

            return Order::create([
                'user_id' => $user->id,
                'tryout_id' => $tryout->id,
                'voucher_id' => $quote['voucher']?->id,
                'amount' => $quote['amount'],
                'discount' => $quote['discount'],
                'total' => $quote['total'],
                'status' => Order::STATUS_PENDING,
                'gateway' => $this->gateway(),
            ]);
        });
    }

    public function markPaid(Order $order, ?string $reference = null): Order
    {
        return DB::transaction(function () use ($order, $reference): Order {
            $order = Order::query()->whereKey($order->id)->lockForUpdate()->firstOrFail();

            if ($order->isPaid()) {
                return $order;
            }

            $order->update([
                'status' => Order::STATUS_PAID,
                'gateway_reference' => $reference,
                'paid_at' => now(),
            ]);

            $order->voucher?->increment('used_count');

            return $order;
        });
    }
}
