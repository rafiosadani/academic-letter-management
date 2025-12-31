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

class LetterSubmittedNotification extends Notification implements ShouldQueue
{
    use Queueable;

    protected LetterRequest $letterRequest;
    protected string $recipientType;


    /**
     * Create a new notification instance.
     */
    public function __construct(LetterRequest $letterRequest, string $recipientType = 'student')
    {
        $this->letterRequest = $letterRequest;
        $this->recipientType = $recipientType;
    }

    /**
     * Get the notification's delivery channels.
     *
     * @return array<int, string>
     */
    public function via(object $notifiable): array
    {
        $category = NotificationCategory::LETTER_STATUS->value;
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

        if ($this->recipientType === 'student') {
            return (new MailMessage)
                ->subject('Pengajuan Surat Berhasil Dikirim')
                ->greeting('Halo ' . $notifiable->profile->full_name . ',')
                ->line("Pengajuan {$letterType} Anda telah berhasil dikirim dan akan diproses oleh pihak terkait.")
                ->action('Lihat Detail Surat', $this->getActionUrl($notifiable))
                ->line('Terima kasih telah menggunakan sistem kami!');
        }

        return (new MailMessage)
            ->subject('Surat Baru Menunggu Persetujuan')
            ->greeting('Halo ' . $notifiable->profile->full_name . ',')
            ->line("Terdapat pengajuan {$letterType} baru dari {$studentName} yang memerlukan persetujuan Anda.")
            ->action('Proses Sekarang', $this->getActionUrl($notifiable))
            ->line('Mohon segera diproses. Terima kasih!');
    }

    /**
     * Get the array representation of the notification.
     */
    public function toArray(object $notifiable): array
    {
        return [
            'title' => $this->getTitle(),
            'message' => $this->getMessage(),
            'action' => [
                'text' => $this->recipientType === 'student' ? 'Lihat Detail' : 'Proses Sekarang',
                'url' => $this->getActionUrl($notifiable),
                'method' => 'GET',
            ],
            'icon' => $this->recipientType === 'student' ? 'fa-paper-plane' : 'fa-inbox',
            'color' => 'primary',
            'category' => NotificationCategory::LETTER_STATUS->value,
            'priority' => NotificationPriority::NORMAL->value,
            'related_type' => 'LetterRequest',
            'related_id' => $this->letterRequest->id,
            'metadata' => [
                'letter_type' => $this->letterRequest->letter_type->value,
                'recipient_type' => $this->recipientType,
            ]
        ];
    }

    private function getTitle(): string
    {
        if ($this->recipientType === 'student') {
            return 'Pengajuan Surat Berhasil Dikirim';
        }

        return 'Surat Baru Menunggu Persetujuan';
    }

    private function getMessage(): string
    {
        $letterType = $this->letterRequest->letter_type->label();
        $studentName = $this->letterRequest->student->profile->full_name;

        if ($this->recipientType === 'student') {
            return "Pengajuan {$letterType} Anda telah berhasil dikirim dan akan diproses oleh pihak terkait.";
        }

        return "Terdapat pengajuan {$letterType} baru dari {$studentName} yang memerlukan persetujuan Anda.";
    }

    private function getActionUrl(object $notifiable): string
    {
        if ($this->recipientType === 'student') {
            return route('letters.show', $this->letterRequest);
        }

        // Get first active pending approval for this user
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
