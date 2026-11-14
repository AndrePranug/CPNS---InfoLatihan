<?php

namespace App\Http\Controllers;

use App\Models\Order;
use App\Models\Tryout;
use App\Services\PaymentService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class CheckoutController extends Controller
{
    public function __construct(private readonly PaymentService $payments) {}

    public function show(Request $request, Tryout $tryout): Response|RedirectResponse
    {
        abort_unless($tryout->isOpen(), 404);

        if ($this->payments->hasAccess($request->user(), $tryout)) {
            return redirect()->route('tryouts.show', $tryout);
        }

        $order = Order::query()
            ->where('user_id', $request->user()->id)
            ->where('tryout_id', $tryout->id)
            ->where('status', Order::STATUS_PENDING)
            ->latest('id')
            ->first();

        return Inertia::render('Checkout', [
            'tryout' => [
                'id' => $tryout->id,
                'title' => $tryout->title,
                'duration_minutes' => $tryout->duration_minutes,
                'question_count' => $tryout->questions()->count(),
                'price' => $tryout->price,
            ],
            'order' => $order === null ? null : [
                'id' => $order->id,
                'amount' => $order->amount,
                'discount' => $order->discount,
                'total' => $order->total,
                'voucher' => $order->voucher?->code,
            ],
            'available' => $this->payments->available(),
            'gateway' => $this->payments->gateway(),
        ]);
    }

    public function store(Request $request, Tryout $tryout): RedirectResponse
    {
        abort_unless($tryout->isOpen(), 404);

        $data = $request->validate([
            'voucher' => ['nullable', 'string', 'max:32'],
        ]);

        if ($this->payments->hasAccess($request->user(), $tryout)) {
            return redirect()->route('tryouts.show', $tryout);
        }

        $this->payments->createOrder($request->user(), $tryout, $data['voucher'] ?? null);

        return redirect()
            ->route('checkout.show', $tryout)
            ->with('success', 'Pesanan dibuat dan menunggu pembayaran.');
    }
}
