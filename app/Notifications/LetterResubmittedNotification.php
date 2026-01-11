<?php

namespace App\Notifications;

use App\Enums\NotificationCategory;
use App\Enums\NotificationPriority;
use App\Models\LetterRequest;
use App\Models\NotificationSetting;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class LetterResubmittedNotification extends Notification implements ShouldQueue
{
    use Queueable;

    protected LetterRequest $letterRequest;
    protected ?string $previousRejectionReason;

    /**
     * Create a new notification instance.
     */
    public function __construct(LetterRequest $letterRequest, ?string $previousRejectionReason = null)
    {
        $this->letterRequest = $letterRequest;
        $this->previousRejectionReason = $previousRejectionReason;
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
        $letterType = $this->letterRequest->letter_type->label();
        $studentName = $this->letterRequest->student->profile->full_name;

        $mail = (new MailMessage)
            ->subject('Surat Telah Direvisi dan Diajukan Kembali')
            ->greeting('Halo ' . $notifiable->profile->full_name . ',')
            ->line("Pengajuan {$letterType} dari {$studentName} telah direvisi dan diajukan kembali untuk persetujuan Anda.");

        if ($this->previousRejectionReason) {
            $mail->line('**Alasan Penolakan Sebelumnya:**')
                ->line($this->previousRejectionReason);
        }

        $mail->line('**Detail Surat:**')
            ->line('- Jenis: ' . $letterType)
            ->line('- Nama Mahasiswa: ' . $studentName)
            ->line('- Status: Direvisi dan Diajukan Kembali')
            ->action('Proses Persetujuan', $this->getActionUrl())
            ->line('Mohon segera ditinjau kembali. Terima kasih!');

        return $mail;
    }

    /**
     * Get the array representation of the notification.
     *
     * @return array<string, mixed>
     */
    public function toArray(object $notifiable): array
    {
        return [
            'title' => 'Surat Direvisi dan Diajukan Kembali',
            'message' => $this->getMessage(),
            'action' => [
                'text' => 'Proses Sekarang',
                'url' => $this->getActionUrl(),
                'method' => 'GET',
            ],
            'icon' => 'fa-rotate-right',
            'color' => 'warning',
            'category' => NotificationCategory::LETTER_APPROVAL->value,
            'priority' => NotificationPriority::NORMAL->value,
            'related_type' => 'LetterRequest',
            'related_id' => $this->letterRequest->id,
            'metadata' => [
                'letter_type' => $this->letterRequest->letter_type->value,
                'previous_rejection_reason' => $this->previousRejectionReason,
                'is_resubmission' => true,
            ]
        ];
    }

    private function getMessage(): string
    {
        $letterType = $this->letterRequest->letter_type->label();
        $studentName = $this->letterRequest->student->profile->full_name;

        return "Pengajuan {$letterType} dari {$studentName} telah direvisi dan diajukan kembali untuk persetujuan Anda.";
    }

    private function getActionUrl(): string
    {
        $approval = $this->letterRequest->approvals()
            ->where('status', 'pending')
            ->where('is_active', true)
            ->first();

        if ($approval) {
            return route('approvals.show', $approval);
        }

        return route('approvals.index');
    }
}
