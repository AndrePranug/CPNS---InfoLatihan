<?php

namespace Database\Seeders;

use App\Enums\Role;
use App\Models\Option;
use App\Models\Question;
use App\Models\Subtest;
use App\Models\Topic;
use App\Models\Tryout;
use App\Models\User;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        User::factory()->create([
            'name' => 'Admin',
            'email' => 'admin@example.com',
            'role' => Role::Admin,
        ]);

        User::factory()->create([
            'name' => 'Test User',
            'email' => 'test@example.com',
        ]);

        /** @var array<string, array{name: string, count: int, grade: int, topics: list<string>}> $subtests */
        $subtests = [
            'TWK' => ['name' => 'Tes Wawasan Kebangsaan', 'count' => 30, 'grade' => 65, 'topics' => ['Pancasila', 'UUD 1945', 'Bhinneka Tunggal Ika', 'NKRI']],
            'TIU' => ['name' => 'Tes Intelegensia Umum', 'count' => 35, 'grade' => 80, 'topics' => ['Silogisme', 'Numerik', 'Analitis', 'Figural']],
            'TKP' => ['name' => 'Tes Karakteristik Pribadi', 'count' => 45, 'grade' => 166, 'topics' => ['Pelayanan Publik', 'Integritas', 'Profesionalisme']],
        ];

        $tryout = Tryout::create([
            'title' => 'Tryout SKD Perdana',
            'description' => 'Simulasi SKD lengkap 110 soal, 100 menit.',
            'duration_minutes' => 100,
            'is_published' => true,
        ]);

        $position = 1;

        foreach ($subtests as $code => $meta) {
            $subtest = Subtest::create([
                'code' => $code,
                'name' => $meta['name'],
                'question_count' => $meta['count'],
                'passing_grade' => $meta['grade'],
            ]);

            foreach ($meta['topics'] as $topicName) {
                $topic = Topic::create(['subtest_id' => $subtest->id, 'name' => $topicName]);

                $question = Question::create([
                    'subtest_id' => $subtest->id,
                    'topic_id' => $topic->id,
                    'body' => "Contoh soal {$code} topik {$topicName}?",
                    'explanation' => "Pembahasan contoh soal {$code} topik {$topicName}.",
                    'difficulty' => 2,
                ]);

                $weights = $code === 'TKP' ? [5, 4, 3, 2, 1] : [0, 0, 0, 0, 0];

                foreach (['A', 'B', 'C', 'D', 'E'] as $index => $label) {
                    Option::create([
                        'question_id' => $question->id,
                        'label' => $label,
                        'body' => "Pilihan {$label}",
                        'is_correct' => $code !== 'TKP' && $index === 0,
                        'weight' => $code === 'TKP' ? $weights[$index] : ($index === 0 ? 5 : 0),
                    ]);
                }

                $tryout->questions()->attach($question->id, ['position' => $position++]);
            }
        }
    }
}
