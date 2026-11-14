<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Attempt;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Inertia\Inertia;
use Inertia\Response;
use Symfony\Component\HttpFoundation\StreamedResponse;

class ReportController extends Controller
{
    public function index(Request $request): Response
    {
        [$from, $to] = $this->range($request);

        $attempts = Attempt::query()
            ->whereNotNull('submitted_at')
            ->whereBetween('submitted_at', [$from, $to]);

        return Inertia::render('Admin/Reports/Index', [
            'filters' => [
                'from' => $from->toDateString(),
                'to' => $to->toDateString(),
            ],
            'summary' => [
                'participants' => User::query()->where('role', 'peserta')->count(),
                'attempts' => (clone $attempts)->count(),
                'average' => (int) round((clone $attempts)->avg('total_score') ?? 0),
                'passed' => (clone $attempts)->where('is_passed', true)->count(),
            ],
            'users' => $this->userRows($from, $to)->take(100)->values(),
            'tryouts' => $this->tryoutRows($from, $to),
        ]);
    }

    public function export(Request $request): StreamedResponse
    {
        [$from, $to] = $this->range($request);
        $rows = $this->userRows($from, $to);

        return response()->streamDownload(function () use ($rows): void {
            $output = fopen('php://output', 'w');

            fputcsv($output, ['Nama', 'Email', 'Tryout selesai', 'Skor terbaik', 'Rata-rata', 'Terakhir']);

            foreach ($rows as $row) {
                fputcsv($output, [
                    $row['name'],
                    $row['email'],
                    $row['attempts'],
                    $row['best_score'],
                    $row['average_score'],
                    $row['last_submitted_at'],
                ]);
            }

            fclose($output);
        }, "laporan-mitra-{$from->toDateString()}-{$to->toDateString()}.csv", [
            'Content-Type' => 'text/csv; charset=UTF-8',
        ]);
    }

    /**
     * @return array{0: Carbon, 1: Carbon}
     */
    private function range(Request $request): array
    {
        $data = $request->validate([
            'from' => ['nullable', 'date'],
            'to' => ['nullable', 'date', 'after_or_equal:from'],
        ]);

        $from = isset($data['from'])
            ? Carbon::parse($data['from'])->startOfDay()
            : now()->subDays(29)->startOfDay();

        $to = isset($data['to'])
            ? Carbon::parse($data['to'])->endOfDay()
            : now()->endOfDay();

        return [$from, $to];
    }

    /**
     * @return Collection<int, array{name: string, email: string, attempts: int, best_score: int, average_score: int, last_submitted_at: string}>
     */
    private function userRows(Carbon $from, Carbon $to): Collection
    {
        return DB::table('attempts')
            ->join('users', 'users.id', '=', 'attempts.user_id')
            ->whereNotNull('attempts.submitted_at')
            ->whereBetween('attempts.submitted_at', [$from, $to])
            ->groupBy('users.id', 'users.name', 'users.email')
            ->orderByDesc('best_score')
            ->orderBy('users.name')
            ->get([
                'users.name',
                'users.email',
                DB::raw('count(*) as attempts'),
                DB::raw('max(attempts.total_score) as best_score'),
                DB::raw('round(avg(attempts.total_score)) as average_score'),
                DB::raw('max(attempts.submitted_at) as last_submitted_at'),
            ])
            ->map(fn (object $row): array => [
                'name' => $row->name,
                'email' => $row->email,
                'attempts' => (int) $row->attempts,
                'best_score' => (int) $row->best_score,
                'average_score' => (int) $row->average_score,
                'last_submitted_at' => Carbon::parse($row->last_submitted_at)->format('d M Y'),
            ]);
    }

    /**
     * @return Collection<int, array{title: string, attempts: int, average_score: int, passed: int}>
     */
    private function tryoutRows(Carbon $from, Carbon $to): Collection
    {
        return DB::table('attempts')
            ->join('tryouts', 'tryouts.id', '=', 'attempts.tryout_id')
            ->whereNotNull('attempts.submitted_at')
            ->whereBetween('attempts.submitted_at', [$from, $to])
            ->groupBy('tryouts.id', 'tryouts.title')
            ->orderByDesc('attempts')
            ->get([
                'tryouts.title',
                DB::raw('count(*) as attempts'),
                DB::raw('round(avg(attempts.total_score)) as average_score'),
                DB::raw('sum(case when attempts.is_passed = 1 then 1 else 0 end) as passed'),
            ])
            ->map(fn (object $row): array => [
                'title' => $row->title,
                'attempts' => (int) $row->attempts,
                'average_score' => (int) $row->average_score,
                'passed' => (int) $row->passed,
            ]);
    }
}
