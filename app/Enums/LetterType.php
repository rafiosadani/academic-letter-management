<?php

namespace App\Enums;

enum LetterType: string
{
    case SKAK = 'skak';
    case SKAK_TUNJANGAN = 'skak_tunjangan';
    case PENELITIAN = 'penelitian';
    case DISPENSASI_KULIAH = 'dispensasi_kuliah';
    case DISPENSASI_MAHASISWA = 'dispensasi_mahasiswa';

    /**
     * Get label untuk UI
     */
    public function label(): string
    {
        return match($this) {
            self::SKAK => 'Surat Keterangan Aktif Kuliah',
            self::SKAK_TUNJANGAN => 'Surat Keterangan Aktif Kuliah untuk Tunjangan Orang Tua',
            self::PENELITIAN => 'Surat Permohonan Penelitian',
            self::DISPENSASI_KULIAH => 'Surat Dispensasi Perkuliahan',
            self::DISPENSASI_MAHASISWA => 'Surat Dispensasi Mahasiswa',
        };
    }

    public function labelFileName(): string
    {
        return match($this) {
            self::SKAK, self::SKAK_TUNJANGAN => 'Surat Keterangan Aktif Kuliah',
            self::PENELITIAN => 'Surat Permohonan Penelitian',
            self::DISPENSASI_KULIAH => 'Surat Dispensasi Perkuliahan',
            self::DISPENSASI_MAHASISWA => 'Surat Dispensasi Mahasiswa',
        };
    }

    /**
     * Get short label
     */
    public function shortLabel(): string
    {
        return match($this) {
            self::SKAK => 'SKAK',
            self::SKAK_TUNJANGAN => 'SKAK Tunjangan',
            self::PENELITIAN => 'Penelitian',
            self::DISPENSASI_KULIAH => 'Dispensasi Kuliah',
            self::DISPENSASI_MAHASISWA => 'Dispensasi Mahasiswa',
        };
    }

    /**
     * Check if letter type uses external system (Word → UB System → PDF)
     */
    public function isExternal(): bool
    {
        return match($this) {
            self::SKAK, self::SKAK_TUNJANGAN => true,
            default => false,
        };
    }

    /**
     * Get output format
     */
    public function outputFormat(): string
    {
        return $this->isExternal() ? 'word' : 'pdf';
    }

    /**
     * Get color output format
     */
    public function colorOutputFormat(): string
    {
        return $this->isExternal() ? 'info' : 'error';
    }


    /**
     * Get icon for UI
     */
    public function icon(): string
    {
        return match($this) {
            self::SKAK, self::SKAK_TUNJANGAN => 'fa-file-word',
            default => 'fa-file-pdf',
        };
    }

    /**
     * Get color for badge
     */
    public function color(): string
    {
        return match($this) {
            self::SKAK => 'primary',
            self::SKAK_TUNJANGAN => 'info',
            self::PENELITIAN => 'success',
            self::DISPENSASI_KULIAH => 'warning',
            self::DISPENSASI_MAHASISWA => 'secondary',
        };
    }

    /**
     * Get description
     */
    public function description(): string
    {
        return match($this) {
            self::SKAK => 'Surat keterangan bahwa mahasiswa masih aktif kuliah di semester berjalan',
            self::SKAK_TUNJANGAN => 'Surat keterangan aktif kuliah untuk persyaratan tunjangan orang tua/wali',
            self::PENELITIAN => 'Surat permohonan izin penelitian di instansi/perusahaan',
            self::DISPENSASI_KULIAH => 'Surat dispensasi tidak mengikuti perkuliahan karena alasan tertentu',
            self::DISPENSASI_MAHASISWA => 'Surat dispensasi untuk keperluan mahasiswa lainnya',
        };
    }

    /**
     * Check if letter type needs auto numbering
     * Only PDF types (internal system) need auto numbering
     * Word types (external system) get number from external
     */
    public function needsAutoNumbering(): bool
    {
        return !$this->isExternal();
    }

    /**
     * Get default letter code for numbering
     */
    public function defaultCode(): string
    {
        return match($this) {
            self::PENELITIAN => 'LL',
            self::DISPENSASI_KULIAH => 'DK',
            self::DISPENSASI_MAHASISWA => 'DM',
            default => '',
        };
    }

    /**
     * Get required documents for this letter type
     */
    public function requiredDocuments(): array
    {
        return match($this) {
            self::PENELITIAN => [
                'proposal' => [
                    'label' => 'Proposal Penelitian',
                    'required' => true,
                    'max_size' => 5120, // 5MB
                    'types' => ['pdf'],
                    'helper' => 'Upload proposal penelitian dalam format PDF (Max 5MB)'
                ],
            ],
            self::DISPENSASI_KULIAH => [
                'lampiran' => [
                    'label' => 'Lampiran Pendukung',
                    'required' => false,
                    'max_size' => 2048, // 2MB
                    'types' => ['pdf', 'jpg', 'jpeg', 'png'],
                    'helper' => 'Upload dokumen pendukung jika ada (Max 2MB)',
                    'multiple' => true,
                ],
            ],
            default => [], // No documents required
        };
    }

    /**
     * Get form fields for this letter type
     */
    public function formFields(): array
    {
        return match($this) {
            self::SKAK => $this->skakFields(),
            self::SKAK_TUNJANGAN => $this->skakTunjanganFields(),
            self::PENELITIAN => $this->penelitianFields(),
            self::DISPENSASI_MAHASISWA => $this->dispensasiMahasiswaFields(),
            self::DISPENSASI_KULIAH => $this->dispensasiKuliahFields(),
        };
    }

    private function skakFields(): array
    {
        return [
            'keperluan' => [
                'type' => 'select',
                'label' => 'Keperluan Surat Aktif Kuliah',
                'required' => true,
                'options' => [
                    'Pengurusan BPJS Kesehatan' => 'Pengurusan BPJS Kesehatan',
                    'Pelaporan ke Kepolisian (Kehilangan KTM)' => 'Pelaporan ke Kepolisian (Kehilangan KTM)',
                    'Pengurusan Beasiswa' => 'Pengurusan Beasiswa',
                    'Pengurusan VISA' => 'Pengurusan VISA',
                    'Pengurusan Paspor' => 'Pengurusan Paspor',
                    'Lainnya' => 'Lainnya',
                ],
                'placeholder' => '-- Pilih Keperluan --',
                'helper' => 'Pilih alasan utama Anda mengajukan surat keterangan aktif kuliah ini.',
            ],
            'keterangan' => [
                'type' => 'textarea',
                'label' => 'Keterangan Tambahan',
                'required' => false,
                'rows' => 3,
                'placeholder' => 'Contoh: Untuk keperluan pendaftaran beasiswa Bank Indonesia...',
                'helper' => 'Opsional: Berikan informasi tambahan jika diperlukan dicantumkan',
            ],
        ];
    }

    private function skakTunjanganFields(): array
    {
        $profile = auth()->user()->profile;

        return [
            'keperluan' => [
                'type' => 'text',
                'label' => 'Keperluan',
                'required' => true,
                'readonly' => true,
                'value' => 'Persyaratan Tunjangan Orang Tua',
                'helper' => 'Kolom ini otomatis terisi untuk pengajuan SKAK Tunjangan Orang Tua.',
            ],
            'parent_name' => [
                'type' => 'text',
                'label' => 'Nama Orang Tua',
                'required' => true,
                'value' => $profile?->parent_name ?? '',
                'placeholder' => 'Contoh: Agus Subiyantoro',
                'helper' => 'Gunakan nama lengkap sesuai dengan yang tertera di SK Jabatan/Daftar Gaji.',
            ],
            'parent_nip' => [
                'type' => 'text',
                'label' => 'NIP Orang Tua',
                'required' => true,
                'value' => $profile?->parent_nip ?? '',
                'placeholder' => 'Contoh: 198510122010121003',
                'helper' => 'Masukkan 18 digit NIP tanpa spasi. Gunakan tanda (-) jika orang tua tidak memiliki NIP/Pensiunan.',
            ],
            'parent_rank' => [
                'type' => 'select_or_other',
                'label' => 'Pangkat/Golongan Orang Tua',
                'required' => true,
                'value' => $profile?->parent_rank ?? '',
                'options' => [
                    'Juru Muda / I a' => 'Juru Muda / I a',
                    'Juru Muda Tingkat I / I b' => 'Juru Muda Tingkat I / I b',
                    'Juru / I c' => 'Juru / I c',
                    'Juru Tingkat I / I d' => 'Juru Tingkat I / I d',
                    'Pengatur Muda / II a' => 'Pengatur Muda / II a',
                    'Pengatur Muda Tingkat I / II b' => 'Pengatur Muda Tingkat I / II b',
                    'Pengatur / II c' => 'Pengatur / II c',
                    'Pengatur Tingkat I / II d' => 'Pengatur Tingkat I / II d',
                    'Penata Muda / III a' => 'Penata Muda / III a',
                    'Penata Muda Tingkat I / III b' => 'Penata Muda Tingkat I / III b',
                    'Penata / III c' => 'Penata / III c',
                    'Penata Tingkat I / III d' => 'Penata Tingkat I / III d',
                    'Pembina / IV a' => 'Pembina / IV a',
                    'Pembina Tingkat I / IV b' => 'Pembina Tingkat I / IV b',
                    'Pembina Utama Muda / IV c' => 'Pembina Utama Muda / IV c',
                    'Pembina Utama Madya / IV d' => 'Pembina Utama Madya / IV d',
                    'Pembina Utama / IV e' => 'Pembina Utama / IV e',
                    'Lainnya' => 'Lainnya',
                ],
                'placeholder' => '-- Pilih Pangkat/Golongan --',
                'other_placeholder' => 'Masukkan pangkat/golongan lainnya',
                'helper' => 'Pilih golongan terakhir. Jika memilih "Lainnya", sebutkan secara detail (Contoh: Pembina Tingkat I / IV b).',
            ],
            'parent_institution' => [
                'type' => 'text',
                'label' => 'Nama Instansi Orang Tua',
                'required' => true,
                'value' => $profile?->parent_institution ?? '',
                'placeholder' => 'Contoh: Dinas Pendidikan Provinsi Jawa Timur',
                'helper' => 'Nama kantor atau instansi tempat orang tua bekerja.',
            ],
            'parent_institution_address' => [
                'type' => 'textarea',
                'label' => 'Alamat Instansi Orang Tua',
                'required' => true,
                'value' => $profile?->parent_institution_address ?? '',
                'rows' => 2,
                'placeholder' => 'Contoh: Jl. Ahmad Yani No. 1, Surabaya',
                'helper' => 'Alamat lengkap instansi (Jalan, Kota/Kabupaten).',
            ],
            'keterangan' => [
                'type' => 'textarea',
                'label' => 'Keterangan Tambahan',
                'required' => false,
                'rows' => 2,
                'placeholder' => 'Isi jika ada informasi tambahan lainnya...',
                'helper' => 'Opsional: Berikan informasi tambahan jika diperlukan dicantumkan',
            ],
        ];
    }

    private function penelitianFields(): array
    {
        return [
            'judul_penelitian' => [
                'type' => 'textarea',
                'label' => 'Judul Penelitian',
                'required' => true,
                'rows' => 2,
                'placeholder' => 'Contoh: Analisis Kinerja Sistem Keuangan Daerah Berbasis Cloud Computing',
                'helper' => 'Tuliskan judul lengkap skripsi atau penelitian Anda.',
            ],
            'nama_tempat_penelitian' => [
                'type' => 'text',
                'label' => 'Nama Tempat Penelitian',
                'required' => true,
                'placeholder' => 'Contoh: PT. Bank Mandiri (Persero) Tbk. Cabang Malang',
                'helper' => 'Nama perusahaan, instansi pemerintah, atau laboratorium tujuan.',
            ],
            'alamat_tempat_penelitian' => [
                'type' => 'textarea',
                'label' => 'Alamat Tempat Penelitian',
                'required' => true,
                'rows' => 2,
                'placeholder' => 'Contoh: Jl. Pemuda No. 123, Kota Malang',
                'helper' => 'Pastikan alamat benar agar surat permohonan sampai ke tujuan yang tepat.',
            ],
            'no_hp' => [
                'type' => 'text',
                'label' => 'Nomor HP/WhatsApp',
                'required' => true,
                'placeholder' => 'Contoh: 081234567890',
                'helper' => 'Nomor yang dapat dihubungi oleh pihak instansi jika diperlukan.',
            ],
            'dosen_pembimbing' => [
                'type' => 'text',
                'label' => 'Dosen Pembimbing',
                'required' => true,
                'placeholder' => 'Contoh: Dr. Budi Santoso, S.E., M.M.',
                'helper' => 'Tulis nama lengkap dosen pembimbing beserta gelar',
            ],
            'bulan_pelaksanaan' => [
                'type' => 'text',
                'label' => 'Bulan Pelaksanaan',
                'required' => true,
                'placeholder' => 'Contoh: Agustus - September 2025',
                'helper' => 'Perkiraan durasi waktu Anda melakukan pengambilan data/penelitian.',
            ],
            'keterangan' => [
                'type' => 'textarea',
                'label' => 'Keterangan Tambahan',
                'required' => false,
                'rows' => 2,
                'placeholder' => 'Isi jika ada informasi tambahan lainnya...',
                'helper' => 'Opsional: Berikan informasi tambahan jika diperlukan dicantumkan',
            ],
        ];
    }

    private function dispensasiMahasiswaFields(): array
    {
        return [
            'nama_instansi_tujuan' => [
                'type' => 'text',
                'label' => 'Nama Instansi Tujuan Surat',
                'required' => true,
                'placeholder' => 'Contoh: PT. Sumber Alfaria Trijaya',
                'helper' => 'Instansi tempat Anda memohon dispensasi.',
            ],
            'jabatan_penerima' => [
                'type' => 'text',
                'label' => 'Jabatan Penerima Surat',
                'required' => true,
                'placeholder' => 'Contoh: Manajer Human Resource Development',
                'helper' => 'Gunakan "Pimpinan" jika tidak tahu jabatan spesifiknya.',
            ],
            'alamat_instansi' => [
                'type' => 'textarea',
                'label' => 'Alamat Instansi',
                'required' => true,
                'rows' => 2,
                'placeholder' => 'Contoh: Jl. Pemuda No. 123, Kota Malang',
                'helper' => 'Pastikan alamat benar agar surat dispensasi sampai ke tujuan yang tepat.',
            ],
            'alasan_dispensasi' => [
                'type' => 'textarea',
                'label' => 'Alasan Dispensasi',
                'required' => true,
                'rows' => 2,
                'placeholder' => 'Contoh: tidak mengikuti kegiatan magang',
                'helper' => 'Sebutkan kegiatan utama yang menjadi alasan permohonan.',
            ],
            'posisi_magang' => [
                'type' => 'text',
                'label' => 'Posisi Magang / Kegiatan',
                'required' => true,
                'placeholder' => 'Contoh: Web Developer / Peserta Pelatihan',
                'helper' => 'Posisi Anda di instansi/kegiatan tersebut.',
            ],
            'keperluan_detail' => [
                'type' => 'textarea',
                'label' => 'Keperluan Detail',
                'required' => true,
                'rows' => 2,
                'placeholder' => 'Contoh: menghadiri acara pernikahan saudara',
                'helper' => 'Jelaskan alasan mendesak mengapa Anda memerlukan dispensasi ini.',
            ],
            'tanggal_mulai' => [
                'type' => 'date',
                'label' => 'Tanggal Mulai',
                'required' => true,
                'helper' => 'Tanggal awal dimulainya izin dispensasi.',
            ],
            'tanggal_selesai' => [
                'type' => 'date',
                'label' => 'Tanggal Selesai',
                'required' => true,
                'helper' => 'Tanggal akhir masa dispensasi.',
            ],
            'keterangan' => [
                'type' => 'textarea',
                'label' => 'Keterangan Tambahan',
                'required' => false,
                'rows' => 2,
                'placeholder' => 'Isi jika ada informasi tambahan lainnya...',
                'helper' => 'Opsional: Berikan informasi tambahan jika diperlukan dicantumkan',
            ],
        ];
    }

    private function dispensasiKuliahFields(): array
    {
        return [
            'nama_kegiatan' => [
                'type' => 'text',
                'label' => 'Nama Kegiatan',
                'required' => true,
                'placeholder' => 'Contoh: Seminar Nasional Teknologi Informasi 2025',
                'helper' => 'Sebutkan nama kegiatan resmi yang diikuti.',
            ],
            'tanggal_kegiatan' => [
                'type' => 'date',
                'label' => 'Tanggal Kegiatan',
                'required' => true,
                'helper' => 'Tanggal pelaksanaan kegiatan yang menyebabkan dispensasi.',
            ],
            'waktu_mulai' => [
                'type' => 'time',
                'label' => 'Waktu Mulai',
                'required' => true,
                'helper' => 'Jam dimulainya dispensasi (Contoh: 08:00).',
            ],
            'waktu_selesai' => [
                'type' => 'time',
                'label' => 'Waktu Selesai',
                'required' => true,
                'helper' => 'Perkiraan jam berakhirnya kegiatan (Contoh: 16:00).',
            ],
            'tempat_kegiatan' => [
                'type' => 'text',
                'label' => 'Tempat Kegiatan',
                'required' => true,
                'placeholder' => 'Contoh: Hotel Lotus Kediri / Ruang Sidang Lt. 2 Gedung Dieng',
                'helper' => 'Lokasi spesifik tempat kegiatan berlangsung.',
            ],
            'student_list' => [
                'type' => 'student_list',
                'label' => 'Daftar Mahasiswa',
                'required' => true,
                'placeholder' => 'Belum ada data mahasiswa. Klik tombol "Tambah Data" untuk menambahkan.',
                'helper' => 'Tambahkan Nama mahasiswa, NIM, dan Program Studi yang terlibat dalam kegiatan ini.',
                'min_students' => 1,
                'max_students' => 50,
            ],
            'keterangan' => [
                'type' => 'textarea',
                'label' => 'Keterangan Tambahan',
                'required' => false,
                'rows' => 2,
                'placeholder' => 'Isi jika ada catatan tambahan untuk dosen pengampu mata kuliah...',
                'helper' => 'Opsional: Informasi lain yang relevan dengan permohonan dispensasi.',
            ],
        ];
    }

    public function templateFile(): string
    {
        return match($this) {
            self::SKAK => 'skak_general.docx',
            self::SKAK_TUNJANGAN => 'skak_tunjangan.docx',
            default => throw new \Exception("Template DOCX tidak didefinisikan untuk jenis surat ini"),
        };
    }

    public function templateView(): string
    {
        return match($this) {
            self::PENELITIAN => 'templates.letters.pdf.penelitian',
            self::DISPENSASI_KULIAH => 'templates.letters.pdf.dispensasi_kuliah',
            self::DISPENSASI_MAHASISWA => 'templates.letters.pdf.dispensasi_mahasiswa',
            default => throw new \Exception("Template PDF tidak didefinisikan untuk jenis surat ini"),
        };
    }
}
