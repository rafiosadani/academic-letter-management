<?php

namespace App\Notifications;

use App\Enums\NotificationCategory;
use App\Enums\NotificationPriority;
use App\Models\NotificationSetting;
use App\Models\Semester;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class SemesterToggleSuggestion extends Notification
{
    use Queueable;

    /**
     * Create a new notification instance.
     */
    public function __construct(
        public Semester $semester,
    ) {
        //
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
            ->subject('Pengingat: Aktivasi Semester - ' . $this->semester->full_label)
            ->greeting('Halo ' . $notifiable->profile->full_name . ',')
            ->line('Berdasarkan tanggal saat ini, Semester berikut seharusnya sudah aktif:')
            ->line('Semester: ' . $this->semester->full_label)
            ->line('Periode: ' . $this->semester->period_text)
            ->action('Aktifkan Sekarang', route('master.semesters.index'))
            ->line('Jika Anda tidak ingin mengaktifkan semester ini, abaikan pesan ini.')
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
            'title' => 'Saatnya Mengaktifkan Semester ' . $this->semester->semester_type->label(),
            'message' => "Semester {$this->semester->full_label} seharusnya sudah aktif berdasarkan tanggal saat ini ({$this->semester->start_date?->translatedFormat('d F Y')}).",
            'action' => [
                'text' => 'Lihat & Aktifkan',
                'url' => route('master.semesters.index'),
                'method' => 'GET',
            ],
            'icon' => 'fa-triangle-exclamation',
            'color' => 'warning',
            'category' => NotificationCategory::SEMESTER->value,
            'priority' => NotificationPriority::HIGH->value,
            'related_type' => 'Semester',
            'related_id' => $this->semester->id,
            'metadata' => [
                'semester_type' => $this->semester->semester_type->value,
                'academic_year' => $this->semester->academicYear->year_label,
                'start_date' => $this->semester->start_date?->format('Y-m-d'),
                'end_date' => $this->semester->end_date?->format('Y-m-d'),
                'suggested_at' => now()->format('Y-m-d H:i:s'),
            ]
        ];
    }
}
