@extends('templates.letters.pdf.layouts.official-letter', ['title' => 'Surat Dispensasi Mahasiswa'])

@section('extra-style')
    <style>
        /* Recipient section */
        .recipient-section {
            margin-top: 20px;
            margin-bottom: 15px;
            line-height: 1.15;
        }

        .recipient-line {
            text-align: justify;
        }

        /* Letter body */
        .letter-body {
            text-align: justify;
            line-height: 1.15;
            margin: 15px 0;
        }

        .no-margin-top {
            margin-top: 0 !important;
        }

        .no-margin-bottom {
            margin-bottom: 0 !important;
        }

        /* Student detail table - Mengikuti style penelitian agar konsisten */
        .student-detail-table {
            width: 100%;
            margin: 15px 0;
            border-collapse: collapse;
        }

        .student-detail-table td {
            vertical-align: top;
            line-height: 1.15;
        }

        .student-label {
            width: 140px;
        }

        .student-separator {
            width: 10px;
            text-align: center;
        }

        .student-value {
            text-align: justify;
        }

        /* Signature section */
        .signature-section {
            margin-top: 30px;
            width: 100%;
        }

        .signature-table {
            width: 100%;
            border-collapse: collapse;
        }

        .signature-left {
            width: 50%;
        }

        .signature-right {
            width: 50%;
            text-align: left;
            vertical-align: top;
        }

        .signature-line {
            line-height: 1.15;
        }

        .tte-box {
            width: 110px;
            height: 110px;
            border: 1px dashed #ccc;
            margin: 10px 0;
            text-align: center;
            line-height: 70px;
            font-size: 8pt;
            color: #999;
        }

        .qr-box {
            width: 110px;
            height: 110px;
            margin: 10px 0;
        }

        .qr-box img {
            width: 110px;
            height: 110px;
        }

        .officer-name {
            margin-top: 5px;
            font-weight: normal;
        }

        /* Distribution - Terkunci di bawah main-content */
        .letter-distribution {
            position: absolute;
            bottom: 20px;
            left: 0;
            width: 100%;
            padding-bottom: 10px;
        }

        .distribution-item {
            margin-left: 20px;
            line-height: 1.15;
        }
    </style>
@endsection

@section('content')
    <table width="100%" cellpadding="0" cellspacing="0" style="margin-bottom: 15px;">
        <tr>
            <td width="70" style="line-height: 1.15; vertical-align: top;">Nomor</td>
            <td width="10" style="line-height: 1.15; vertical-align: top; text-align: center;">:</td>
            <td style="line-height: 1.15; vertical-align: top;">{{ $letter_number }}</td>
        </tr>
        <tr>
            <td width="70" style="line-height: 1.15; vertical-align: top;">Lampiran</td>
            <td width="10" style="line-height: 1.15; vertical-align: top; text-align: center;">:</td>
            <td style="line-height: 1.15; vertical-align: top;">-</td>
        </tr>
        <tr>
            <td width="70" style="line-height: 1.15; vertical-align: top;">Perihal</td>
            <td width="10" style="line-height: 1.15; vertical-align: top; text-align: center;">:</td>
            <td style="line-height: 1.15; vertical-align: top;">Surat Dispensasi Mahasiswa</td>
        </tr>
    </table>

    <div class="recipient-section">
        <div class="recipient-line">Yth. {{ $jabatan_penerima }} {{ $nama_instansi_tujuan }}</div>
        <div class="recipient-line">{{ $alamat_instansi_tujuan }}</div>
    </div>

    <div class="letter-body no-margin-bottom">
        Dengan hormat,
    </div>
    <div class="letter-body no-margin-top">
        Bersama ini kami mohon dapat diberikan dispensasi untuk {{ $alasan_dispensasi }} di instansi yang Bapak/Ibu pimpin kepada mahasiswa kami:
    </div>

    <table class="student-detail-table">
        <tr>
            <td class="student-label">Nama</td>
            <td class="student-separator">:</td>
            <td class="student-value">{{ $student_name }}</td>
        </tr>
        <tr>
            <td class="student-label">NIM</td>
            <td class="student-separator">:</td>
            <td class="student-value">{{ $student_nim }}</td>
        </tr>
        <tr>
            <td class="student-label">Program Studi</td>
            <td class="student-separator">:</td>
            <td class="student-value">{{ $study_program }}</td>
        </tr>
        <tr>
            <td class="student-label">Posisi</td>
            <td class="student-separator">:</td>
            <td class="student-value">{{ $posisi_magang }}</td>
        </tr>
    </table>

    <div class="letter-body">
        Untuk dapat menghadiri {{ $keperluan }} yang akan dilaksanakan pada:
    </div>
    <div class="letter-body">
        Hari dan tanggal: {{ $tanggal_mulai }} s.d. {{ $tanggal_selesai }}
    </div>

    <div class="letter-body">
        Demikian surat permohonan dispensasi ini kami sampaikan, atas perhatian dan kerjasamanya kami ucapkan terima kasih.
    </div>

    <div class="signature-section">
        <table class="signature-table">
            <tr>
                <td class="signature-left"></td>
                <td class="signature-right">
                    <div class="signature-line">Malang, {{ $letter_date }}</div>
                    <div class="signature-line">a.n. Dekan</div>
                    <div class="signature-line">Wakil Dekan Bidang Akademik,</div>

                    @if(isset($qr_code_data_uri))
                        <div class="qr-box">
                            <img src="{{ $qr_code_data_uri }}" alt="QR Code">
                        </div>
                    @else
                        <div class="tte-box">
                            ${tte}
                        </div>
                    @endif

                    <div class="signature-line officer-name">{{ $wd_name }}</div>
                    <div class="signature-line">NIK {{ $wd_nip }}</div>
                </td>
            </tr>
        </table>
    </div>

    @if(isset($tembusan) && is_array($tembusan) && count($tembusan) > 0)
        <div class="letter-distribution">
            <div class="distribution-title">Tembusan:</div>
            @foreach($tembusan as $index => $item)
                <div class="distribution-item">
                    {{ $index + 1 }}. {{ $item }}
                </div>
            @endforeach
        </div>
    @endif
@endsection