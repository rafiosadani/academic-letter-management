<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class NotificationSetting extends Model
{
    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'user_id',
        'category',
        'channel_database',
        'channel_email',
        'email_immediately',
        'email_daily_digest',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'channel_database' => 'boolean',
            'channel_email' => 'boolean',
            'email_immediately' => 'boolean',
            'email_daily_digest' => 'boolean',
        ];
    }

    // ==========================================================
    // RELATIONSHIPS
    // ==========================================================

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    // ==========================================================
    // HELPER METHODS
    // ==========================================================

    /**
     * Get or create notification setting for user and category
     */
    public static function getOrCreate(int $userId, string $category): self
    {
        return self::firstOrCreate(
            ['user_id' => $userId, 'category' => $category],
            [
                'channel_database' => true,
                'channel_email' => false,
                'email_immediately' => false,
                'email_daily_digest' => false,
            ]
        );
    }

    /**
     * Check if should send database notification
     */
    public function shouldSendDatabase(): bool
    {
        return $this->channel_database;
    }

    /**
     * Check if should send email notification
     */
    public function shouldSendEmail(): bool
    {
        return $this->channel_email && ($this->email_immediately || $this->email_daily_digest);
    }

    /**
     * Check if should send immediate email
     */
    public function shouldSendImmediateEmail(): bool
    {
        return $this->channel_email && $this->email_immediately;
    }

    /**
     * Check if should include in daily digest
     */
    public function shouldIncludeInDigest(): bool
    {
        return $this->channel_email && $this->email_daily_digest;
    }
}
