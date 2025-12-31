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

class ContentEditedNotification extends Notification implements ShouldQueue
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
        $letterType = $this->approval->letterRequest->letter_type->label();
        $approverName = $this->approval->assigned_authority ?? 'Approver';
        $stepLabel = $this->approval->step_label;

        return (new MailMessage)
            ->subject('Konten Surat Telah Diedit')
            ->greeting('Halo ' . $notifiable->profile->full_name . ',')
            ->line($this->getMessage())
            ->line('**Detail Perubahan:**')
            ->line('- Jenis Surat: ' . $letterType)
            ->line('- Diedit oleh: ' . $approverName)
            ->line('- Pada Tahap: ' .  $stepLabel)
            ->action('Lihat Perubahan', route('letters.show', $this->approval->letterRequest))
            ->line('Mohon periksa kembali detail surat Anda.')
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
            'title' => 'Konten Surat Telah Diedit',
            'message' => $this->getMessage(),
            'action' => [
                'text' => 'Lihat Perubahan',
                'url' => route('letters.show', $this->approval->letterRequest),
                'method' => 'GET',
            ],
            'icon' => 'fa-edit',
            'color' => 'info',
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
        $approverName = $this->approval->assigned_authority ?? 'Approver';
        $stepLabel = $this->approval->step_label;

        return "Konten {$letterType} Anda telah diedit oleh {$approverName} pada tahap {$stepLabel}. Mohon periksa kembali detail surat Anda.";
    }
}
