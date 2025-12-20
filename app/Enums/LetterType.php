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
            ],
            'keterangan' => [
                'type' => 'textarea',
                'label' => 'Keterangan Tambahan',
                'required' => false,
                'rows' => 3,
                'helper' => 'Isi jika ada informasi tambahan yang perlu dicantumkan',
            ],
        ];
    }

    private function skakTunjanganFields(): array
    {
        return [
            'keperluan' => [
                'type' => 'text',
                'label' => 'Keperluan',
                'required' => true,
                'readonly' => true,
                'value' => 'Persyaratan Tunjangan Orang Tua',
            ],
            'nama_orangtua' => [
                'type' => 'text',
                'label' => 'Nama Orang Tua',
                'required' => true,
                'placeholder' => 'Nama lengkap orang tua',
            ],
            'nip_orangtua' => [
                'type' => 'text',
                'label' => 'NIP Orang Tua',
                'required' => true,
                'placeholder' => 'Nomor Induk Pegawai',
            ],
            'pangkat_golongan_orangtua' => [
                'type' => 'select_or_other',
                'label' => 'Pangkat/Golongan Orang Tua',
                'required' => true,
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
            ],
            'nama_instansi_orangtua' => [
                'type' => 'text',
                'label' => 'Nama Instansi Orang Tua',
                'required' => true,
                'placeholder' => 'Nama instansi tempat bekerja',
            ],
            'alamat_instansi_orangtua' => [
                'type' => 'textarea',
                'label' => 'Alamat Instansi Orang Tua',
                'required' => true,
                'rows' => 2,
                'placeholder' => 'Alamat lengkap instansi',
            ],
            'keterangan' => [
                'type' => 'textarea',
                'label' => 'Keterangan Tambahan',
                'required' => false,
                'rows' => 3,
                'helper' => 'Isi jika ada informasi tambahan',
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
                'placeholder' => 'Judul lengkap penelitian',
            ],
            'nama_tempat_penelitian' => [
                'type' => 'text',
                'label' => 'Nama Tempat Penelitian',
                'required' => true,
                'placeholder' => 'Nama instansi/perusahaan',
            ],
            'alamat_tempat_penelitian' => [
                'type' => 'textarea',
                'label' => 'Alamat Tempat Penelitian',
                'required' => true,
                'rows' => 2,
                'placeholder' => 'Alamat lengkap lokasi penelitian',
            ],
            'no_hp' => [
                'type' => 'text',
                'label' => 'Nomor HP/WhatsApp',
                'required' => true,
                'placeholder' => '08xxxxxxxxxx',
            ],
            'dosen_pembimbing' => [
                'type' => 'text',
                'label' => 'Dosen Pembimbing',
                'required' => true,
                'placeholder' => 'Nama dosen pembimbing',
            ],
            'bulan_pelaksanaan' => [
                'type' => 'text',
                'label' => 'Bulan Pelaksanaan',
                'required' => true,
                'placeholder' => 'Contoh: Juli 2025',
            ],
            'keterangan' => [
                'type' => 'textarea',
                'label' => 'Keterangan Tambahan',
                'required' => false,
                'rows' => 3,
                'helper' => 'Isi jika ada informasi tambahan',
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
                'placeholder' => 'Contoh: KPPN Kediri',
            ],
            'jabatan_penerima' => [
                'type' => 'text',
                'label' => 'Jabatan Penerima Surat',
                'required' => true,
                'placeholder' => 'Contoh: Kepala Sub Bagian Umum',
            ],
            'alamat_instansi' => [
                'type' => 'textarea',
                'label' => 'Alamat Instansi',
                'required' => true,
                'rows' => 2,
                'placeholder' => 'Alamat lengkap instansi',
            ],
            'alasan_dispensasi' => [
                'type' => 'textarea',
                'label' => 'Alasan Dispensasi',
                'required' => true,
                'rows' => 2,
                'placeholder' => 'Contoh: tidak mengikuti kegiatan magang',
            ],
            'posisi_magang' => [
                'type' => 'text',
                'label' => 'Posisi Magang/Kegiatan',
                'required' => false,
                'placeholder' => 'Posisi atau kegiatan yang akan ditinggalkan',
            ],
            'keperluan_detail' => [
                'type' => 'textarea',
                'label' => 'Keperluan Detail',
                'required' => true,
                'rows' => 2,
                'placeholder' => 'Contoh: menghadiri acara pernikahan saudara',
            ],
            'tanggal_mulai' => [
                'type' => 'date',
                'label' => 'Tanggal Mulai',
                'required' => true,
            ],
            'tanggal_selesai' => [
                'type' => 'date',
                'label' => 'Tanggal Selesai',
                'required' => true,
            ],
            'keterangan' => [
                'type' => 'textarea',
                'label' => 'Keterangan Tambahan',
                'required' => false,
                'rows' => 3,
                'helper' => 'Isi jika ada informasi tambahan',
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
                'placeholder' => 'Nama kegiatan fakultas/universitas',
            ],
            'tanggal_kegiatan' => [
                'type' => 'date',
                'label' => 'Tanggal Kegiatan',
                'required' => true,
            ],
            'waktu_mulai' => [
                'type' => 'time',
                'label' => 'Waktu Mulai',
                'required' => true,
            ],
            'waktu_selesai' => [
                'type' => 'time',
                'label' => 'Waktu Selesai',
                'required' => true,
            ],
            'tempat_kegiatan' => [
                'type' => 'text',
                'label' => 'Tempat Kegiatan',
                'required' => true,
                'placeholder' => 'Lokasi kegiatan',
            ],
            'keterangan' => [
                'type' => 'textarea',
                'label' => 'Keterangan Tambahan',
                'required' => false,
                'rows' => 3,
                'helper' => 'Isi jika ada informasi tambahan',
            ],
        ];
    }
}
