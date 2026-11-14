<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Voucher;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Inertia\Inertia;
use Inertia\Response;

class VoucherController extends Controller
{
    public function index(): Response
    {
        return Inertia::render('Admin/Vouchers/Index', [
            'vouchers' => Voucher::query()
                ->latest('id')
                ->get()
                ->map(fn (Voucher $voucher): array => [
                    'id' => $voucher->id,
                    'code' => $voucher->code,
                    'type' => $voucher->type,
                    'value' => $voucher->value,
                    'used_count' => $voucher->used_count,
                    'max_uses' => $voucher->max_uses,
                    'expires_at' => $voucher->expires_at?->format('d M Y'),
                    'is_active' => $voucher->is_active,
                ]),
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        $request->merge(['code' => strtoupper(trim((string) $request->input('code')))]);

        $data = $request->validate([
            'code' => ['required', 'string', 'max:32', Rule::unique('vouchers', 'code')],
            'type' => ['required', Rule::in([Voucher::TYPE_PERCENT, Voucher::TYPE_FIXED])],
            'value' => ['required', 'integer', 'min:1', $request->input('type') === Voucher::TYPE_PERCENT ? 'max:100' : 'max:100000000'],
            'max_uses' => ['nullable', 'integer', 'min:1'],
            'expires_at' => ['nullable', 'date', 'after:now'],
        ]);

        $data['is_active'] = $request->boolean('is_active');

        Voucher::create($data);

        return back()->with('success', 'Voucher dibuat.');
    }

    public function update(Voucher $voucher): RedirectResponse
    {
        $voucher->update(['is_active' => ! $voucher->is_active]);

        return back()->with('success', $voucher->is_active ? 'Voucher diaktifkan.' : 'Voucher dinonaktifkan.');
    }
}
