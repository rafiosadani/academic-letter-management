<?php

namespace App\Models;

use App\Enums\LetterType;
use Illuminate\Database\Eloquent\Model;

class LetterCounter extends Model
{
    /**
     * Indicates if the model should be timestamped.
     * Only updated_at is used.
     */
    public $timestamps = false;

    /**
     * The attributes that are mass assignable.
     */
    protected $fillable = [
        'letter_type',
        'year',
        'last_sequence'
    ];

    /**
     * The attributes that should be cast.
     */
    protected $casts = [
        'letter_type' => LetterType::class,
        'year' => 'integer',
        'last_sequence' => 'integer'
    ];

    /**
     * The attributes that should be mutated to dates.
     */
    protected $dates = [
        'updated_at'
    ];

    // ============================================
    // BUSINESS METHODS
    // ============================================

    /**
     * Get the next sequence number for this counter
     */
    public function nextSequence(): int
    {
        return $this->last_sequence + 1;
    }

    /**
     * Increment the counter and return new sequence
     */
    public function incrementAndGet(): int
    {
        $this->increment('last_sequence');
        $this->refresh();

        // Update timestamp manually
        $this->updated_at = now();
        $this->save();

        return $this->last_sequence;
    }
}
