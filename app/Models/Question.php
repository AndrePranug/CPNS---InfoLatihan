<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

#[Fillable(['exam_type', 'subtest_id', 'formation_id', 'topic_id', 'body', 'image_path', 'explanation', 'difficulty', 'version'])]
class Question extends Model
{
    use HasFactory, SoftDeletes;

    public const EXAM_SKD = 'skd';

    public const EXAM_SKB = 'skb';

    /**
     * @var array<string, mixed>
     */
    protected $attributes = [
        'exam_type' => self::EXAM_SKD,
        'difficulty' => 2,
        'version' => 1,
    ];

    /** @return BelongsTo<Formation, $this> */
    public function formation(): BelongsTo
    {
        return $this->belongsTo(Formation::class);
    }

    /** @return BelongsTo<Subtest, $this> */
    public function subtest(): BelongsTo
    {
        return $this->belongsTo(Subtest::class);
    }

    /** @return BelongsTo<Topic, $this> */
    public function topic(): BelongsTo
    {
        return $this->belongsTo(Topic::class);
    }

    /** @return HasMany<Option, $this> */
    public function options(): HasMany
    {
        return $this->hasMany(Option::class);
    }

    /** @return BelongsToMany<Tryout, $this> */
    public function tryouts(): BelongsToMany
    {
        return $this->belongsToMany(Tryout::class, 'tryout_question')->withPivot('position');
    }
}
