<?php

namespace Tests\Feature;

use App\Enums\Role;
use App\Models\Question;
use App\Models\Subtest;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class QuestionImportTest extends TestCase
{
    use RefreshDatabase;

    private const HEADER = 'subtest,topik,soal,pembahasan,kesulitan,a,b,c,d,e,kunci,bobot';

    public function test_valid_csv_rows_are_imported_and_bad_rows_reported(): void
    {
        Storage::fake('local');
        Subtest::create(['code' => 'TWK', 'name' => 'TWK', 'question_count' => 30, 'passing_grade' => 65]);
        Subtest::create(['code' => 'TKP', 'name' => 'TKP', 'question_count' => 45, 'passing_grade' => 166]);

        $csv = implode("\n", [
            self::HEADER,
            'TWK,Pancasila,Soal TWK?,Karena itu.,2,a,b,c,d,e,B,',
            'TKP,Integritas,Soal TKP?,Penjelasan.,3,a,b,c,d,e,,5|4|3|2|1',
            'TWK,Pancasila,Soal rusak?,x,2,a,b,c,d,e,Z,',
            'TIU,Silogisme,Subtes kosong?,x,2,a,b,c,d,e,A,',
        ]);

        $admin = User::factory()->create(['role' => Role::Admin]);

        $this->actingAs($admin)->post('/admin/questions/import', [
            'file' => UploadedFile::fake()->createWithContent('soal.csv', $csv),
        ])->assertRedirect()->assertSessionHas('import_errors');

        $this->assertCount(2, session('import_errors'));
        $this->assertSame(2, Question::count());

        $tkp = Question::where('body', 'Soal TKP?')->with('options')->sole();
        $this->assertSame(5, $tkp->options()->where('weight', 5)->count() + $tkp->options()->where('weight', 4)->count() + $tkp->options()->where('weight', 3)->count() + $tkp->options()->where('weight', 2)->count() + $tkp->options()->where('weight', 1)->count());
        $this->assertSame('Integritas', $tkp->topic->name);

        $twk = Question::where('body', 'Soal TWK?')->with('options')->sole();
        $this->assertSame('B', $twk->options->firstWhere('is_correct', true)->label);
    }

    public function test_wrong_header_is_rejected(): void
    {
        Subtest::create(['code' => 'TWK', 'name' => 'TWK', 'question_count' => 30, 'passing_grade' => 65]);
        $admin = User::factory()->create(['role' => Role::Admin]);

        $this->actingAs($admin)->post('/admin/questions/import', [
            'file' => UploadedFile::fake()->createWithContent('soal.csv', "kolom,salah\n1,2"),
        ])->assertRedirect();

        $this->assertSame(0, Question::count());
    }
}
