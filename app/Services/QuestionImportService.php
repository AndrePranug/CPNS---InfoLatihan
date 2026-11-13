<?php

namespace App\Services;

use App\Models\Question;
use App\Models\Subtest;
use App\Models\Topic;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use PhpOffice\PhpSpreadsheet\IOFactory;
use Throwable;

class QuestionImportService
{
    private const HEADER = ['subtest', 'topik', 'soal', 'pembahasan', 'kesulitan', 'a', 'b', 'c', 'd', 'e', 'kunci', 'bobot'];

    public function __construct(private HtmlSanitizer $sanitizer) {}

    /**
     * Import questions from a CSV or Excel file. Returns per-row errors; valid rows are stored.
     *
     * @return array{imported: int, errors: array<int, string>}
     */
    public function import(UploadedFile $file): array
    {
        try {
            $rows = $this->rows($file);
        } catch (Throwable) {
            return ['imported' => 0, 'errors' => ['File tidak bisa dibaca. Gunakan CSV atau Excel (.xlsx).']];
        }

        if ($rows === []) {
            return ['imported' => 0, 'errors' => ['File kosong.']];
        }

        $header = array_map(fn ($value): string => strtolower(trim((string) $value)), array_shift($rows));

        if ($header !== self::HEADER) {
            return ['imported' => 0, 'errors' => ['Header harus: '.implode(',', self::HEADER)]];
        }

        $imported = 0;
        $errors = [];

        foreach ($rows as $index => $row) {
            $line = (int) $index + 2;

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

            $body = $this->sanitizer->clean($data['soal']);

            if ($body === '') {
                $errors[] = "Baris {$line}: isi soal kosong setelah dibersihkan.";

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

            DB::transaction(function () use ($subtest, $data, $labels, $bodies, $kunci, $bobot, $isTkp, $body): void {
                $question = Question::create([
                    'subtest_id' => $subtest->id,
                    'topic_id' => $this->topicId($subtest->id, $data['topik'] ?? null),
                    'body' => $body,
                    'explanation' => $this->sanitizer->clean($data['pembahasan'] ?? null) ?: null,
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

        return ['imported' => $imported, 'errors' => $errors];
    }

    /**
     * @return array<int, array<int, string|null>>
     */
    private function rows(UploadedFile $file): array
    {
        if (in_array(strtolower($file->getClientOriginalExtension()), ['xlsx', 'xls'], true)) {
            $sheet = IOFactory::load($file->getRealPath())->getActiveSheet();

            return array_map(
                fn (array $row): array => array_map(fn ($value): ?string => $value === null ? null : (string) $value, $row),
                $sheet->toArray(null, true, false),
            );
        }

        $handle = fopen($file->getRealPath(), 'r');

        if ($handle === false) {
            throw new \RuntimeException('File tidak bisa dibaca.');
        }

        $rows = [];

        while (($row = fgetcsv($handle)) !== false) {
            $rows[] = array_map(fn ($value): ?string => $value === null ? null : (string) $value, $row);
        }

        fclose($handle);

        return $rows;
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
