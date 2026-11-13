<?php

namespace App\Services;

use App\Models\Question;
use App\Models\Subtest;
use App\Models\Topic;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;

class QuestionImportService
{
    private const HEADER = ['subtest', 'topik', 'soal', 'pembahasan', 'kesulitan', 'a', 'b', 'c', 'd', 'e', 'kunci', 'bobot'];

    /**
     * Import questions from a CSV file. Returns per-row errors; valid rows are stored.
     *
     * @return array{imported: int, errors: array<int, string>}
     */
    public function import(UploadedFile $file): array
    {
        $handle = fopen($file->getRealPath(), 'r');

        if ($handle === false) {
            return ['imported' => 0, 'errors' => ['File tidak bisa dibaca.']];
        }

        $header = array_map(fn ($value): string => strtolower(trim((string) $value)), fgetcsv($handle) ?: []);

        if ($header !== self::HEADER) {
            fclose($handle);

            return ['imported' => 0, 'errors' => ['Header CSV harus: '.implode(',', self::HEADER)]];
        }

        $imported = 0;
        $errors = [];
        $line = 1;

        while (($row = fgetcsv($handle)) !== false) {
            $line++;

            if (count(array_filter($row, fn ($value) => trim((string) $value) !== '')) === 0) {
                continue;
            }

            $data = array_combine(self::HEADER, array_pad(array_slice($row, 0, count(self::HEADER)), count(self::HEADER), null));

            $validator = Validator::make($data, [
                'subtest' => ['required', 'string', 'in:TWK,TIU,TKP'],
                'soal' => ['required', 'string'],
                'a' => ['required', 'string'],
                'b' => ['required', 'string'],
                'c' => ['required', 'string'],
                'd' => ['required', 'string'],
                'e' => ['required', 'string'],
                'kesulitan' => ['nullable', 'integer', 'between:1,3'],
            ]);

            if ($validator->fails()) {
                $errors[] = "Baris {$line}: ".$validator->errors()->first();

                continue;
            }

            $subtest = Subtest::where('code', $data['subtest'])->first();

            if ($subtest === null) {
                $errors[] = "Baris {$line}: subtes {$data['subtest']} belum ada di pengaturan.";

                continue;
            }

            $labels = ['A', 'B', 'C', 'D', 'E'];
            $bodies = [$data['a'], $data['b'], $data['c'], $data['d'], $data['e']];
            $kunci = strtoupper(trim((string) ($data['kunci'] ?? '')));
            $bobot = array_map('intval', explode('|', (string) ($data['bobot'] ?? '')));
            $isTkp = $subtest->code->value === 'TKP';

            if ($isTkp && count($bobot) !== 5) {
                $errors[] = "Baris {$line}: TKP butuh bobot 5 nilai dipisah |, contoh 5|4|3|2|1.";

                continue;
            }

            if (! $isTkp && ! in_array($kunci, $labels, true)) {
                $errors[] = "Baris {$line}: kunci harus A/B/C/D/E.";

                continue;
            }

            DB::transaction(function () use ($subtest, $data, $labels, $bodies, $kunci, $bobot, $isTkp): void {
                $question = Question::create([
                    'subtest_id' => $subtest->id,
                    'topic_id' => $this->topicId($subtest->id, $data['topik'] ?? null),
                    'body' => $data['soal'],
                    'explanation' => $data['pembahasan'] ?? null,
                    'difficulty' => (int) ($data['kesulitan'] ?? 2) ?: 2,
                ]);

                foreach ($labels as $index => $label) {
                    $question->options()->create([
                        'label' => $label,
                        'body' => $bodies[$index],
                        'is_correct' => ! $isTkp && $label === $kunci,
                        'weight' => $isTkp ? ($bobot[$index] ?? 0) : ($label === $kunci ? 5 : 0),
                    ]);
                }
            });

            $imported++;
        }

        fclose($handle);

        return ['imported' => $imported, 'errors' => $errors];
    }

    private function topicId(int $subtestId, ?string $name): ?int
    {
        $name = trim((string) $name);

        if ($name === '') {
            return null;
        }

        return Topic::firstOrCreate(['subtest_id' => $subtestId, 'name' => $name])->id;
    }
}
