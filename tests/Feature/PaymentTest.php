<?php

namespace Tests\Feature;

use App\Models\Attempt;
use App\Models\Order;
use App\Models\Tryout;
use App\Models\User;
use App\Models\Voucher;
use App\Services\PaymentService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Inertia\Testing\AssertableInertia as Assert;
use Tests\TestCase;

class PaymentTest extends TestCase
{
    use RefreshDatabase;

    public function test_paid_tryout_is_blocked_while_payment_disabled(): void
    {
        config(['payments.enabled' => false, 'payments.gateway' => 'none']);

        $user = User::factory()->create();
        $tryout = Tryout::factory()->create(['price' => 50000]);

        $this->actingAs($user)
            ->from(route('tryouts.show', $tryout))
            ->post(route('exam.start', $tryout))
            ->assertRedirect(route('tryouts.show', $tryout))
            ->assertSessionHasErrors('attempt');

        $this->assertDatabaseCount('attempts', 0);

        $this->actingAs($user)
            ->get(route('tryouts.show', $tryout))
            ->assertOk()
            ->assertInertia(fn (Assert $page) => $page
                ->component('Tryouts/Show')
                ->where('tryout.has_access', false)
                ->where('tryout.payments_available', false)
            );

        $this->actingAs($user)
            ->get(route('checkout.show', $tryout))
            ->assertOk()
            ->assertInertia(fn (Assert $page) => $page
                ->component('Checkout')
                ->where('available', false)
                ->where('tryout.price', 50000)
            );
    }

    public function test_free_tryout_starts_without_payment(): void
    {
        config(['payments.enabled' => false, 'payments.gateway' => 'none']);

        $user = User::factory()->create();
        $tryout = Tryout::factory()->create(['price' => 0]);

        $this->actingAs($user)
            ->post(route('exam.start', $tryout))
            ->assertRedirect();

        $this->assertSame(1, Attempt::query()->where('user_id', $user->id)->count());
    }

    public function test_paid_order_grants_access_even_when_payment_disabled(): void
    {
        config(['payments.enabled' => false, 'payments.gateway' => 'none']);

        $user = User::factory()->create();
        $tryout = Tryout::factory()->create(['price' => 50000]);
        Order::factory()->paid()->create(['user_id' => $user->id, 'tryout_id' => $tryout->id]);

        $this->actingAs($user)
            ->post(route('exam.start', $tryout))
            ->assertRedirect();

        $this->assertSame(1, Attempt::query()->where('user_id', $user->id)->count());
    }

    public function test_checkout_creates_pending_order_with_percent_voucher(): void
    {
        config(['payments.enabled' => true, 'payments.gateway' => 'midtrans']);

        $user = User::factory()->create();
        $tryout = Tryout::factory()->create(['price' => 50000]);
        $voucher = Voucher::factory()->create(['code' => 'HEMAT20', 'value' => 20]);

        $this->actingAs($user)
            ->post(route('checkout.store', $tryout), ['voucher' => 'hemat20'])
            ->assertRedirect(route('checkout.show', $tryout));

        $order = Order::query()->sole();

        $this->assertSame(50000, $order->amount);
        $this->assertSame(10000, $order->discount);
        $this->assertSame(40000, $order->total);
        $this->assertSame(Order::STATUS_PENDING, $order->status);
        $this->assertSame($voucher->id, $order->voucher_id);
        $this->assertSame(0, $voucher->fresh()->used_count);
    }

    public function test_checkout_is_rejected_when_gateway_not_configured(): void
    {
        config(['payments.enabled' => true, 'payments.gateway' => 'none']);

        $user = User::factory()->create();
        $tryout = Tryout::factory()->create(['price' => 50000]);

        $this->actingAs($user)
            ->post(route('checkout.store', $tryout))
            ->assertSessionHasErrors('payment');

        $this->assertDatabaseCount('orders', 0);
    }

    public function test_expired_voucher_is_rejected(): void
    {
        config(['payments.enabled' => true, 'payments.gateway' => 'midtrans']);

        $user = User::factory()->create();
        $tryout = Tryout::factory()->create(['price' => 50000]);
        Voucher::factory()->create(['code' => 'OLD', 'expires_at' => now()->subDay()]);

        $this->actingAs($user)
            ->post(route('checkout.store', $tryout), ['voucher' => 'OLD'])
            ->assertSessionHasErrors('voucher');

        $this->assertDatabaseCount('orders', 0);
    }

    public function test_marking_order_paid_uses_voucher_once_and_grants_access(): void
    {
        config(['payments.enabled' => false, 'payments.gateway' => 'none']);

        $user = User::factory()->create();
        $tryout = Tryout::factory()->create(['price' => 50000]);
        $voucher = Voucher::factory()->create(['max_uses' => 2]);
        $order = Order::factory()->create([
            'user_id' => $user->id,
            'tryout_id' => $tryout->id,
            'voucher_id' => $voucher->id,
        ]);

        $service = app(PaymentService::class);
        $service->markPaid($order, 'TRX-1');
        $service->markPaid($order->fresh());

        $order->refresh();

        $this->assertTrue($order->isPaid());
        $this->assertSame('TRX-1', $order->gateway_reference);
        $this->assertSame(1, $voucher->fresh()->used_count);

        $this->actingAs($user)
            ->post(route('exam.start', $tryout))
            ->assertRedirect();
    }

    public function test_admin_can_create_voucher(): void
    {
        $admin = User::factory()->create(['role' => 'admin']);

        $this->actingAs($admin)
            ->post(route('admin.vouchers.store'), [
                'code' => 'hemat10',
                'type' => 'percent',
                'value' => 10,
                'is_active' => true,
            ])
            ->assertRedirect();

        $this->assertDatabaseHas('vouchers', [
            'code' => 'HEMAT10',
            'type' => 'percent',
            'value' => 10,
            'is_active' => true,
        ]);
    }
}
