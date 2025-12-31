<?php

namespace App\Http\Controllers\PDF;

use App\Enums\LetterType;
use App\Enums\OfficialPosition;
use App\Helpers\LogHelper;
use App\Http\Controllers\Controller;
use App\Models\FacultyOfficial;
use App\Models\LetterRequest;
use App\Services\LetterPdfService;
use App\Services\QRCodeService;
use Barryvdh\DomPDF\Facade\Pdf;
use Carbon\Carbon;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Support\Facades\Log;

class PDFController extends Controller
{
    use AuthorizesRequests;

    public function __construct(
        protected LetterPdfService $pdfService,
        protected QrCodeService $qrCodeService,
    ) {}

    public function preview(LetterRequest $letter) {
        $this->authorize('previewPdf', $letter);

        try {
            if ($letter->letter_type->isExternal()) {
                return redirect()
                    ->back()
                    ->with('notification_data', [
                        'type' => 'error',
                        'text' => 'Preview PDF hanya tersedia untuk surat internal.',
                        'position' => 'center-top',
                        'duration' => 5000,
                    ]);
            }

            $finalApproval = $letter->approvals()
                ->final()
                ->where('is_active', true)
                ->where('status', 'pending')
                ->exists();

            $currentActiveApproval = $letter->approvals()
                ->where('is_active', true)
                ->where('status', 'pending')
                ->first();

            $canEdit = $currentActiveApproval && auth()->user()->can('editContent', $currentActiveApproval);

            if (!$finalApproval && !$canEdit) {
                return redirect()
                    ->back()
                    ->with('notification_data', [
                        'type' => 'error',
                        'text' => 'Preview File PDF hanya tersedia untuk editor dan surat yang menunggu persetujuan akhir.',
                        'position' => 'center-top',
                        'duration' => 5000,
                    ]);
            }

            LetterPdfService::validateRequiredData();

            $view = $letter->letter_type->templateView();

            $data = $this->preparePreviewData($letter);

            $pdf = Pdf::loadView($view, $data);
            $pdf->setPaper('A4', 'portrait');

            $filename = sprintf(
                "Preview - %s - %s.pdf",
                $letter->letter_type->label(),
                $letter->student->profile->full_name
            );

            return $pdf->stream($filename);
        } catch (\Exception $e) {
            LogHelper::logError('preview', 'preview_pdf', $e, [
                'letter_request_id' => $letter->id,
            ], request());
            Log::error("PDF Preview Failed: " . $e->getMessage());

            return redirect()
                ->back()
                ->with('notification_data', [
                    'type' => 'error',
                    'text' => 'Gagal generate preview PDF: ' . $e->getMessage(),
                    'position' => 'center-top',
                    'duration' => 5000,
                ]);
        }
    }

    private function preparePreviewData(LetterRequest $letter): array
    {
        $student = $letter->student;
        $profile = $student->profile;

        // Get WD data
        $wd = FacultyOfficial::where('position', OfficialPosition::WAKIL_DEKAN_AKADEMIK)
            ->where('start_date', '<=', now())
            ->where(function ($query) {
                $query->whereNull('end_date')->orWhere('end_date', '>=', now());
            })
            ->with('user.profile')
            ->first();

        $wdName = $wd?->user?->profile?->full_name ?? 'Dr. A. Faidlal Rahman, SE.Par., M.Sc., CHE.';
        $wdNip = $wd?->user?->profile?->student_or_employee_id ?? '2012018202081001';

        // Generate letter number for preview
        $letterNumber = $letter->letter_number ?? 'PREVIEW/' . now()->format('Y/m/d') . '/' . str_pad($letter->id, 3, '0', STR_PAD_LEFT);

        // Base data
        $data = [
            'student_name' => $this->formatForDocument($profile->full_name),
            'student_nim' => $profile->student_or_employee_id,
            'study_program' => $this->formatForDocument($profile->studyProgram->degree_name ?? '-'),
            'letter_number' => $letterNumber,
            'letter_date' => now()->translatedFormat('d F Y'),
            'wd_name' => $wdName,
            'wd_nip' => $wdNip
        ];

        // Merge with letter-specific data
        return array_merge($data, $this->getLetterSpecificData($letter));
    }

    /**
     * Get letter-specific data.
     */
    private function getLetterSpecificData(LetterRequest $letter): array
    {
        return match($letter->letter_type) {
            LetterType::PENELITIAN => [
                'judul_penelitian' => $this->formatForDocument($letter->data_input['judul_penelitian'] ?? '-'),
                'nama_instansi_tujuan' => $this->formatForDocument($letter->data_input['nama_tempat_penelitian'] ?? '-'),
                'alamat_instansi_tujuan' => $this->formatForDocument($letter->data_input['alamat_tempat_penelitian'] ?? '-'),
                'no_hp' => $letter->data_input['no_hp'] ?? '-',
                'dosen_pembimbing' => $this->formatForDocument($letter->data_input['dosen_pembimbing'] ?? '-'),
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
                'jabatan_penerima' => $this->formatForDocument($letter->data_input['jabatan_peneriama'] ?? '-'),
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
     * Parse student list from array.
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
     * Format text for documents.
     */
    private function formatForDocument(string $text): string
    {
        if (empty($text) || $text === '-') return $text;

        return ucwords(strtolower($text));
    }
}
