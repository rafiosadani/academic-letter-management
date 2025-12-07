<?php

namespace App\Notifications;

use App\Enums\NotificationCategory;
use App\Enums\NotificationPriority;
use App\Models\AcademicYear;
use App\Models\NotificationSetting;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class AcademicYearCreated extends Notification
{
    use Queueable;

    /**
     * Create a new notification instance.
     */
    public function __construct(
        public AcademicYear $academicYear
    ) {

    }

    /**
     * Get the notification's delivery channels.
     *
     * @return array<int, string>
     */
    public function via(object $notifiable): array
    {
        $settings = NotificationSetting::getOrCreate($notifiable->id, NotificationCategory::ACADEMIC_YEAR->value);

        $channels = [];

        if ($settings->shouldSendDatabase()) {
            $channels[] = 'database';
        }

        if ($settings->shouldSendImmediateEmail()) {
            $channels[] = 'mail';
        }

        return $channels;
    }

    /**
     * Get the mail representation of the notification.
     */
    public function toMail(object $notifiable): MailMessage
    {
        return (new MailMessage)
            ->subject('Tahun Akademik Baru - ' . $this->academicYear->year_label)
            ->greeting('Halo ' . $notifiable->profile->full_name . ',')
            ->line('Tahun Akademik baru telah ditambahkan ke sistem.')
            ->line('Tahun Akademik: ' . $this->academicYear->year_label)
            ->line('Periode: ' . $this->academicYear->period_text)
            ->action('Lihat Detail', route('master.academic-years.show', $this->academicYear))
            ->line('Terima kasih!');
    }

    /**
     * Get the array representation of the notification.
     *
     * @return array<string, mixed>
     */
    public function toArray(object $notifiable): array
    {
        return [
            'title' => 'Tahun Akademik Baru Ditambahkan',
            'message' => "Tahun Akademik {$this->academicYear->year_label} telah ditambahkan ke sistem.",
            'action' => [
                'text' => 'Lihat Detail',
                'url' => route('master.academic-years.show', $this->academicYear),
                'method' => 'GET',
            ],
            'icon' => 'fa-calendar-plus',
            'color' => 'primary',
            'category' => NotificationCategory::ACADEMIC_YEAR->value,
            'priority' => NotificationPriority::NORMAL->value,
            'related_type' => 'AcademicYear',
            'related_id' => $this->academicYear->id,
            'metadata' => [
                'year_label' => $this->academicYear->year_label,
                'start_date' => $this->academicYear->start_date->format('Y-m-d'),
                'end_date' => $this->academicYear->end_date->format('Y-m-d'),
                'is_active' => $this->academicYear->is_active,
            ]
        ];
    }
}
