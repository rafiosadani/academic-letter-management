<?php

namespace App\Services;

use App\Enums\LetterType;
use App\Enums\OfficialPosition;
use App\Models\FacultyOfficial;
use App\Models\LetterNumberConfig;
use App\Models\LetterRequest;
use Barryvdh\DomPDF\Facade\Pdf;
use Carbon\Carbon;

class LetterPdfService
{
    public function __construct(
        protected QRCodeService $qrCodeService,
    ) {}

    public function generate(LetterRequest $letter, string $letterNumber): array
    {
        self::validateRequiredData();
        $view = $letter->letter_type->templateView();

        // Generate filename with letter number prefix
        $filename = $this->generateFilename($letter, $letterNumber);
        $date = $letter->created_at ?? now();

        $directory = storage_path(sprintf(
            'app/private/documents/letters/%s/%s/generated',
            $date->format('Y'),
            $date->format('m')
        ));

        // Create directory if not exists
        if (!file_exists($directory)) {
            mkdir($directory, 0755, true);
        }

        $outputPath = "{$directory}/{$filename}";

        $documentSignature = $this->generateDocumentSignature($letter, $letterNumber);
        $data = $this->getPdfData($letter, $letterNumber, $documentSignature);

        $pdf = Pdf::loadView($view, $data);
        $pdf->setPaper('A4', 'portrait');
        $pdf->save($outputPath);

        if (!file_exists($outputPath)) {
            throw new \Exception("Gagal menyimpan file PDF pada lokasi: {$outputPath}");
        }

        $fileHash = $this->calculateHash($outputPath);

        $relativePath = sprintf(
            'documents/letters/%s/%s/generated/%s',
            $date->format('Y'),
            $date->format('m'),
            $filename
        );

        return [
            'path' => $outputPath,
            'hash' => $documentSignature,
            'file_hash' => $fileHash,
        ];
    }

    public static function validateWDData(): void
    {
        $wd = FacultyOfficial::where('position', OfficialPosition::WAKIL_DEKAN_AKADEMIK)
            ->where('start_date', '<=', now())
            ->where(function ($query) {
                $query->whereNull('end_date')
                    ->orWhere('end_date', '>=', now());
            })
            ->with('user.profile')
            ->first();

        if (!$wd || !$wd->user?->profile) {
            throw new \Exception('Data Wakil Dekan tidak ditemukan. Silakan hubungi administrator untuk melengkapi data pejabat.');
        }

        $nip = $wd->user->profile->student_or_employee_id;
        if (empty($nip)) {
            throw new \Exception('NIP Wakil Dekan Bidang Akademik belum diisi. Silakan hubungi administrator untuk melengkapi data di menu Pejabat Fakultas.');
        }

        if (empty($wd->rank)) {
            throw new \Exception('Pangkat/Golongan Wakil Dekan Bidang Akademik belum diisi. Silakan hubungi administrator untuk melengkapi data di menu Pejabat Fakultas.');
        }
    }

    public static function validateRequiredData(): void
    {
        self::validateWDData();

        $headerLogo = setting('header_logo');
        $storagePath = public_path('storage/' . $headerLogo);
        $fallbackPath = public_path('assets/images/logo-ub.png');

        if ((empty($headerLogo) || !file_exists($storagePath)) && !file_exists($fallbackPath)) {
            throw new \Exception('Logo kop surat tidak ditemukan. Mohon unggah logo kop surat di Menu Pengaturan atau hubungi Administrator.');
        }

        $requiredSettings = [
            'header_ministry' => 'Nama Kementerian',
            'header_university' => 'Nama Universitas',
            'header_faculty' => 'Nama Fakultas',
            'header_address' => 'Alamat Fakultas',
            'header_phone' => 'Nomor Telepon',
            'header_fax' => 'Nomor Fax',
            'header_email' => 'Email Fakultas',
            'header_website' => 'Website Fakultas',
        ];

        foreach ($requiredSettings as $key => $label) {
            if (empty(setting($key))) {
                throw new \Exception("Data {$label} belum diatur. Mohon untuk melengkapi data di Menu Pengaturan atau hubungi Administrator.");
            }
        }
    }

    /**
     * Prepare all data for PDF template.
     */
    private function getPdfData(LetterRequest $letter, string $letterNumber, string $documentHash): array
    {
        $student = $letter->student;
        $profile = $student->profile;
        $wdData = $this->getWakilDekanData();

        $verificationUrl = route('documents.verify', ['hash' => $documentHash]);
        $qrCodeDataUri = $this->qrCodeService->generateForLetter($verificationUrl, $letter->id);

        // Base data
        $data = [
            'student_name' => $this->formatForDocument($profile->full_name),
            'student_nim' => $profile->student_or_employee_id,
            'study_program' => $this->formatForDocument($profile->studyProgram->degree_name ?? '-'),
            'semester' => $this->formatForDocument($letter->semester->semester_type->value ?? '-'),
            'academic_year' => $this->formatForDocument($letter->academicYear->year_label),
            'letter_number' => $letterNumber,
            'letter_date' => now()->translatedFormat('d F Y'),
            'wd_name' => $wdData['name'],
            'wd_nip' => $wdData['nip'],
            'qr_code_data_uri' => $qrCodeDataUri,
            'verification_url' => $verificationUrl,
            'document_hash' => $documentHash,
        ];

        // Merge with letter-specific data
        return array_merge($data, $this->getLetterSpecificData($letter));
    }

    /**
     * Get data specific to each letter type.
     */
    private function getLetterSpecificData(LetterRequest $letter): array
    {
        return match($letter->letter_type) {
            LetterType::PENELITIAN => [
                'judul_penelitian' => $this->formatForDocument($letter->data_input['judul_penelitian'] ?? '-'),
                'nama_instansi_tujuan' => $letter->data_input['nama_tempat_penelitian'] ?? '-',
                'alamat_instansi_tujuan' => $this->formatForDocument($letter->data_input['alamat_tempat_penelitian'] ?? '-'),
                'no_hp' => $letter->data_input['no_hp'] ?? '-',
                'dosen_pembimbing' => $letter->data_input['dosen_pembimbing'] ?? '-',
                'bulan_pelaksanaan' => $letter->data_input['bulan_pelaksanaan'] ?? '-',
                'tembusan' => [
                    'Mahasiswa yang bersangkutan'
                ],
            ],
            LetterType::DISPENSASI_KULIAH => [
                'nama_kegiatan' => $letter->data_input['nama_kegiatan'] ?? '-',
                'tanggal_kegiatan' => $letter->data_input['tanggal_kegiatan']
                    ? Carbon::parse($letter->data_input['tanggal_kegiatan'])->translatedFormat('l, d F Y')
                    : '-',
                'waktu_mulai' => $letter->data_input['waktu_mulai'] ?? '-',
                'waktu_selesai' => $letter->data_input['waktu_selesai'] ?? '-',
                'tempat_kegiatan' => $this->formatForDocument($letter->data_input['tempat_kegiatan'] ?? '-'),
                'student_list' => $this->parseStudentList($letter->data_input['student_list'] ?? ''),
            ],
            LetterType::DISPENSASI_MAHASISWA => [
                'nama_instansi_tujuan' => $this->formatForDocument($letter->data_input['nama_instansi_tujuan'] ?? '-'),
                'jabatan_penerima' => $this->formatForDocument($letter->data_input['jabatan_penerima'] ?? '-'),
                'alamat_instansi_tujuan' => $this->formatForDocument($letter->data_input['alamat_instansi'] ?? '-'),
                'alasan_dispensasi' => strtolower($letter->data_input['alasan_dispensasi'] ?? '-'),
                'posisi_magang' => $letter->data_input['posisi_magang'] ?? '-',
                'keperluan' => strtolower($letter->data_input['keperluan_detail'] ?? '-'),
                'tanggal_mulai' => $letter->data_input['tanggal_mulai']
                    ? Carbon::parse($letter->data_input['tanggal_mulai'])->translatedFormat('l, d F Y')
                    : '-',
                'tanggal_selesai' => $letter->data_input['tanggal_selesai']
                    ? Carbon::parse($letter->data_input['tanggal_selesai'])->translatedFormat('l, d F Y')
                    : '-',
            ],
            default => [],
        };
    }

    /**
     * Parse student list for Dispensasi Kuliah.
     * Format: Name | NIM | Program (each line)
     */
    private function parseStudentList(array $students): array
    {
        return collect($students)->map(function ($student) {
            return [
                'name' => $this->formatForDocument($student['name'] ?? '-'),
                'nim' => $student['nim'] ?? '-',
                'program' => $this->formatForDocument($student['program'] ?? '-'),
            ];
        })->toArray();
    }

    /**
     * Format text for official documents (proper title case with academic titles).
     */
    private function formatForDocument(string $text): string
    {
        if (empty($text) || $text === '-') return $text;

        $formatted = ucwords(strtolower($text));

        return $formatted;
    }

    /**
     * Get Wakil Dekan Akademik data.
     */
    private function getWakilDekanData(): array
    {
        $wd = FacultyOfficial::where('position', OfficialPosition::WAKIL_DEKAN_AKADEMIK)
            ->where('start_date', '<=', now())
            ->where(function ($query) {
                $query->whereNull('end_date')->orWhere('end_date', '>=', now());
            })
            ->with('user.profile')
            ->first();

        if (!$wd || !$wd->user?->profile) {
            return [
                'name' => 'Dr. A. Faidlal Rahman, SE.Par., M.Sc., CHE.',
                'nip' => '2012018202081001',
            ];
        }

        return [
            'name' => $wd->user->profile->full_name,
            'nip' => $wd->user->profile->student_or_employee_id,
        ];
    }

    /**
     * Generate PDF filename with letter number prefix.
     * Format: {counter} {jenis} {nama}.pdf
     * Example: 156 Surat Penelitian Ahmad Rizki.pdf
     */
    private function generateFilename(LetterRequest $letter, string $letterNumber): string
    {
        $letterNumberConfig = LetterNumberConfig::where('letter_type', $letter->letter_type->value)->first();
        $padding = $letterNumberConfig?->padding ?? 5;
        $counter = substr($letterNumber, 0, $padding);

        $typeLabel = $letter->letter_type->labelFileName();
        $studentName = $letter->student->profile->full_name;

        return "{$counter} {$typeLabel} {$studentName}.pdf";
    }

    /**
     * Calculate hash for generated PDF file.
     */
    public function calculateHash(string $filePath): string
    {
        if (!file_exists($filePath)) {
            throw new \Exception("File tidak ditemukan untuk kalkulasi hash: {$filePath}");
        }

        return hash_file('sha256', $filePath);
    }

    private function generateDocumentSignature(LetterRequest $letter, string $letterNumber): string
    {
        $components = [
            $letter->id,
            $letterNumber,
            $letter->student_id,
            $letter->letter_type->value,
            $letter->created_at->timestamp,
            config('app.key'),
        ];

        return hash('sha256', implode('|', $components));
    }
}