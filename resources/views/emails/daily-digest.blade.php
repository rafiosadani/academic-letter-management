<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Ringkasan Notifikasi Harian</title>
    <style>
        body {
            font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, 'Helvetica Neue', Arial, sans-serif;
            line-height: 1.6;
            color: #334155;
            background-color: #f1f5f9;
            margin: 0;
            padding: 0;
        }
        .container {
            max-width: 600px;
            margin: 0 auto;
            background-color: #ffffff;
        }
        .header {
            background: linear-gradient(135deg, #0ea5e9 0%, #3b82f6 100%);
            color: #ffffff;
            padding: 30px 20px;
            text-align: center;
        }
        .header h1 {
            margin: 0;
            font-size: 24px;
            font-weight: 600;
        }
        .content {
            padding: 30px 20px;
        }
        .greeting {
            font-size: 16px;
            color: #1e293b;
            margin-bottom: 20px;
        }
        .stats {
            background-color: #eff6ff;
            border-left: 4px solid #3b82f6;
            padding: 15px;
            margin: 20px 0;
            border-radius: 4px;
        }
        .notification-item {
            background-color: #f8fafc;
            border: 1px solid #e2e8f0;
            border-radius: 8px;
            padding: 15px;
            margin-bottom: 15px;
        }
        .notification-title {
            font-size: 16px;
            font-weight: 600;
            color: #1e293b;
            margin: 0 0 8px 0;
        }
        .notification-message {
            font-size: 14px;
            color: #64748b;
            margin: 0 0 8px 0;
        }
        .notification-time {
            font-size: 12px;
            color: #94a3b8;
        }
        .notification-badge {
            display: inline-block;
            padding: 4px 8px;
            border-radius: 4px;
            font-size: 11px;
            font-weight: 600;
            text-transform: uppercase;
            margin-right: 8px;
        }
        .badge-primary { background-color: #dbeafe; color: #1e40af; }
        .badge-success { background-color: #d1fae5; color: #065f46; }
        .badge-warning { background-color: #fef3c7; color: #92400e; }
        .badge-info { background-color: #dbeafe; color: #1e40af; }
        .button {
            display: inline-block;
            padding: 12px 24px;
            background: linear-gradient(135deg, #0ea5e9 0%, #3b82f6 100%);
            color: #ffffff;
            text-decoration: none;
            border-radius: 6px;
            font-weight: 600;
            margin: 20px 0;
        }
        .footer {
            background-color: #f8fafc;
            padding: 20px;
            text-align: center;
            font-size: 12px;
            color: #64748b;
            border-top: 1px solid #e2e8f0;
        }
        .divider {
            height: 1px;
            background-color: #e2e8f0;
            margin: 20px 0;
        }
    </style>
</head>
<body>
<div class="container">
    <!-- Header -->
    <div class="header">
        <h1>📬 Ringkasan Notifikasi Harian</h1>
    </div>

    <!-- Content -->
    <div class="content">
        <!-- Greeting -->
        <div class="greeting">
            <strong>Halo {{ $user->profile->full_name ?? $user->name }},</strong>
            <p>Berikut adalah ringkasan notifikasi yang belum Anda baca dalam 24 jam terakhir.</p>
        </div>

        <!-- Stats -->
        <div class="stats">
            <strong>📊 Total Notifikasi:</strong> {{ $unreadCount }} notifikasi belum dibaca
        </div>

        <div class="divider"></div>

        <!-- Notifications -->
        @foreach($notifications as $notification)
            @php
                $data = $notification->data;
                $color = $data['color'] ?? 'primary';
                $badgeClass = "badge-{$color}";
            @endphp

            <div class="notification-item">
                    <span class="notification-badge {{ $badgeClass }}">
                        {{ $data['category'] ?? 'Notifikasi' }}
                    </span>

                <h3 class="notification-title">
                    {{ $data['title'] ?? 'Notifikasi' }}
                </h3>

                <p class="notification-message">
                    {{ $data['message'] ?? '' }}
                </p>

                <div class="notification-time">
                    🕒 {{ $notification->created_at->diffForHumans() }}
                </div>
            </div>
        @endforeach

        <!-- Action Button -->
        <center>
            <a href="{{ config('app.url') }}/notifications" class="button">
                Lihat Semua Notifikasi
            </a>
        </center>

        <div class="divider"></div>

        <!-- Settings Info -->
        <p style="font-size: 13px; color: #64748b;">
            <strong>💡 Tips:</strong> Anda menerima email ini karena mengaktifkan "Ringkasan Harian" di pengaturan notifikasi.
            <a href="{{ config('app.url') }}/notifications/settings" style="color: #3b82f6;">Ubah pengaturan</a>
        </p>
    </div>

    <!-- Footer -->
    <div class="footer">
        <p>
            Sistem Informasi Persuratan Akademik<br>
            Fakultas Vokasi Universitas Brawijaya
        </p>
        <p style="margin-top: 10px;">
            Email ini dikirim otomatis. Jangan membalas email ini.
        </p>
    </div>
</div>
</body>
</html>