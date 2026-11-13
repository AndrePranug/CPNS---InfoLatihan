<?php

namespace App\Services;

use App\Models\Attempt;

class ExamOrderService
{
    /**
     * Deterministic-but-per-attempt shuffle order. Same attempt always gets the same order,
     * different attempts get different orders.
     *
     * @param  array<int, int>  $ids
     * @return array<int, int>
     */
    public function shuffle(array $ids, Attempt $attempt, string $salt): array
    {
        usort($ids, function (int $a, int $b) use ($attempt, $salt): int {
            return strcmp(
                hash('sha256', $salt.$attempt->id.'-'.$a),
                hash('sha256', $salt.$attempt->id.'-'.$b),
            );
        });

        return $ids;
    }
}
