<?php

namespace Tests\Feature;

use App\Models\Attempt;
use App\Models\Tryout;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Inertia\Testing\AssertableInertia as Assert;
use Tests\TestCase;

class ReportTest extends TestCase
{
    use RefreshDatabase;

    public function test_admin_can_open_report_with_period_summary(): void
    {
        $admin = User::factory()->create(['role' => 'admin']);
        $participant = User::factory()->create(['name' => 'Budi Peserta']);
        $tryout = Tryout::factory()->create(['title' => 'Tryout Nasional']);

        Attempt::factory()->create([
            'user_id' => $participant->id,
            'tryout_id' => $tryout->id,
            'submitted_at' => now()->subDay(),
            'total_score' => 420,
            'is_passed' => true,
        ]);

        $this->actingAs($admin)
            ->get('/admin/reports')
            ->assertOk()
            ->assertInertia(fn (Assert $page) => $page
                ->component('Admin/Reports/Index')
                ->where('summary.attempts', 1)
                ->where('summary.passed', 1)
                ->where('summary.average', 420)
                ->has('users', 1)
                ->where('users.0.name', 'Budi Peserta')
                ->where('users.0.best_score', 420)
                ->has('tryouts', 1)
                ->where('tryouts.0.title', 'Tryout Nasional')
            );
    }

    public function test_non_admin_cannot_open_report(): void
    {
        $this->actingAs(User::factory()->create())
            ->get('/admin/reports')
            ->assertForbidden();

        $this->actingAs(User::factory()->create())
            ->get('/admin/reports/export')
            ->assertForbidden();
    }

    public function test_export_streams_csv_with_header_and_rows(): void
    {
        $admin = User::factory()->create(['role' => 'admin']);
        $participant = User::factory()->create(['name' => 'Sari Peserta', 'email' => 'sari@example.test']);

        Attempt::factory()->create([
            'user_id' => $participant->id,
            'submitted_at' => now()->subDay(),
            'total_score' => 380,
            'is_passed' => false,
        ]);

        $response = $this->actingAs($admin)->get('/admin/reports/export');

        $response->assertOk();
        $response->assertHeader('content-type', 'text/csv; charset=UTF-8');
        $this->assertStringContainsString('laporan-mitra-', $response->headers->get('content-disposition'));

        $csv = $response->streamedContent();

        $this->assertStringContainsString('Nama,Email,"Tryout selesai","Skor terbaik",Rata-rata,Terakhir', $csv);
        $this->assertStringContainsString('"Sari Peserta",sari@example.test,1,380,380', $csv);
    }
}
