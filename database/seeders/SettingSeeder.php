<?php

namespace Database\Seeders;

use App\Models\Setting;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class SettingSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $this->command->info('  ⚙️ Starting Settings seeding...');

        $settings = [
            // General Setting
            [
                'key' => 'site_name',
                'value' => 'SIPA - Sistem Informasi Persuratan Akademik',
                'type' => 'string',
                'group' => 'general',
                'label' => 'Nama Situs',
                'description' => 'Nama aplikasi yang ditampilkan di browser',
                'order' => 1,
            ],
            [
                'key' => 'site_logo',
                'value' => null,
                'type' => 'image',
                'group' => 'general',
                'label' => 'Logo Situs',
                'description' => 'Logo utama aplikasi (untuk login page, register page, sidebar)',
                'order' => 2,
            ],
            [
                'key' => 'favicon',
                'value' => null,
                'type' => 'image',
                'group' => 'general',
                'label' => 'Favicon',
                'description' => 'Icon yang muncul di tab browser (recommended: 32x32px)',
                'order' => 3,
            ],

            // Header Setting (untuk PDF/DOCX)
            [
                'key' => 'header_logo',
                'value' => null,
                'type' => 'image',
                'group' => 'header',
                'label' => 'Logo Header Surat',
                'description' => 'Logo Universitas untuk header surat',
                'order' => 10,
            ],
            [
                'key' => 'header_ministry',
                'value' => 'KEMENTERIAN PENDIDIKAN TINGGI, SAINS, DAN TEKNOLOGI',
                'type' => 'text',
                'group' => 'header',
                'label' => 'Nama Kementerian',
                'description' => 'Nama kementerian untuk header surat',
                'order' => 11,
            ],
            [
                'key' => 'header_university',
                'value' => 'UNIVERSITAS BRAWIJAYA',
                'type' => 'string',
                'group' => 'header',
                'label' => 'Nama Universitas',
                'description' => 'Nama universitas untuk header surat',
                'order' => 12,
            ],
            [
                'key' => 'header_faculty',
                'value' => 'Fakultas Vokasi',
                'type' => 'string',
                'group' => 'header',
                'label' => 'Nama Fakultas',
                'description' => 'Nama fakultas untuk header surat',
                'order' => 13,
            ],
            [
                'key' => 'header_address',
                'value' => 'Jalan. Veteran No 12-16, Malang 65145, Indonesia',
                'type' => 'text',
                'group' => 'header',
                'label' => 'Alamat',
                'description' => 'Alamat lengkap fakultas',
                'order' => 14,
            ],
            [
                'key' => 'header_phone',
                'value' => '+62341 553240',
                'type' => 'string',
                'group' => 'header',
                'label' => 'Nomor Telepon',
                'description' => 'Nomor telepon fakultas',
                'order' => 15,
            ],
            [
                'key' => 'header_fax',
                'value' => '+62341 553448',
                'type' => 'string',
                'group' => 'header',
                'label' => 'Nomor Fax',
                'description' => 'Nomor fax fakultas',
                'order' => 16,
            ],
            [
                'key' => 'header_email',
                'value' => 'vokasi@ub.ac.id',
                'type' => 'string',
                'group' => 'header',
                'label' => 'Email',
                'description' => 'Email resmi fakultas',
                'order' => 17,
            ],
            [
                'key' => 'header_website',
                'value' => 'http://vokasi.ub.ac.id',
                'type' => 'string',
                'group' => 'header',
                'label' => 'Website',
                'description' => 'URL website fakultas',
                'order' => 18,
            ],

            // Footer Setting (untuk PDF/DOCX)
            [
                'key' => 'footer_logo',
                'value' => null,
                'type' => 'image',
                'group' => 'footer',
                'label' => 'Logo Footer',
                'description' => 'Logo untuk footer surat (contoh: logo BSrE)',
                'order' => 30,
            ],
            [
                'key' => 'footer_text',
                'value' => 'UU ITE No. 11 Tahun 2008 Pasal 5 Ayat 1
"Informasi Elektronik dan/atau Dokumen Elektronik dan/atau hasil cetaknya merupakan alat bukti hukum yang sah."
Dokumen ini telah ditandatangani secara elektronik menggunakan sertifikat elektronik yang diterbitkan BSrE',
                'type' => 'text',
                'group' => 'footer',
                'label' => 'Teks Footer',
                'description' => 'Teks watermark/informasi di footer surat',
                'order' => 31,
            ],
        ];

        foreach ($settings as $setting) {
            Setting::updateOrCreate(
                ['key' => $setting['key']],
                $setting
            );

            $this->command->info("  ✅  {$setting['label']} configured");
        }

        $this->command->info('  🎉 Settings seeding completed!');
    }
}
