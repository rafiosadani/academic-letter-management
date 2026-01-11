<?php

namespace App\Notifications;

use App\Enums\NotificationCategory;
use App\Enums\NotificationPriority;
use App\Models\Approval;
use App\Models\NotificationSetting;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class NewApprovalTaskNotification extends Notification implements ShouldQueue
{
    use Queueable;

    protected Approval $approval;

    /**
     * Create a new notification instance.
     */
    public function __construct(Approval $approval)
    {
        $this->approval = $approval;
    }

    /**
     * Get the notification's delivery channels.
     *
     * @return array<int, string>
     */
    public function via(object $notifiable): array
    {
        $category = NotificationCategory::LETTER_APPROVAL->value;
        $settings = NotificationSetting::getOrCreate($notifiable->id, $category);

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
            ->subject('Surat Baru Menunggu Persetujuan Anda')
            ->greeting('Halo ' . $notifiable->profile->full_name . ',')
            ->line($this->getMessage())
            ->line('**Detail Surat:**')
            ->line('- Jenis Surat: ' . $this->approval->letterRequest->letter_type->label())
            ->line('- Nama Mahasiswa: ' . $this->approval->letterRequest->student->full_name)
            ->line('- Tahap: ' . $this->approval->step_label)
            ->action('Proses Persetujuan', route('approvals.show', $this->approval))
            ->line('Mohon segera diproses. Terima kasih!');
    }

    /**
     * Get the array representation of the notification.
     *
     * @return array<string, mixed>
     */
    public function toArray(object $notifiable): array
    {
        return [
            'title' => 'Surat Baru Menunggu Persetujuan',
            'message' => $this->getMessage(),
            'action' => [
                'text' => 'Proses Sekarang',
                'url' => route('approvals.show', $this->approval),
                'method' => 'GET',
            ],
            'icon' => 'fa-list-check',
            'color' => 'warning',
            'category' => NotificationCategory::LETTER_APPROVAL->value,
            'priority' => NotificationPriority::NORMAL->value,
            'related_type' => 'LetterRequest',
            'related_id' => $this->approval->letterRequest->id,
            'metadata' => [
                'step' => $this->approval->step,
                'step_label' => $this->approval->step_label,
            ]
        ];
    }

    private function getMessage(): string
    {
        $letterType = $this->approval->letterRequest->letter_type->label();
        $studentName = $this->approval->letterRequest->student->profile->full_name;
        $stepLabel = $this->approval->step_label;

        return "Terdapat pengajuan {$letterType} dari {$studentName} yang memerlukan persetujuan Anda pada tahap {$stepLabel}.";
    }
}
