@extends('templates.letters.pdf.layouts.official-letter', ['title' => 'Surat Dispensasi Perkuliahan'])

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

        /* Event Details Table - Mengikuti style detail agar rapi */
        .event-detail-table {
            width: 100%;
            margin: 15px 0;
            border-collapse: collapse;
        }

        .event-detail-table td {
            vertical-align: top;
            line-height: 1.15;
        }

        .label-col {
            width: 120px;
        }

        .sep-col {
            width: 10px;
            text-align: center;
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

        /* Page Break */
        .page-break {
            page-break-before: always;
        }

        .attachment-header {
            margin-top: 20px;
            margin-bottom: 15px;
            line-height: 1.15;
        }

        .attachment-header-row {
            text-align: justify;
        }

        /* Table Mahasiswa (Lampiran) */
        .attachment-table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 15px;
        }

        .attachment-table th, .attachment-table td {
            border: 1px solid black;
            padding: 6px;
            text-align: left;
        }

        .attachment-table th {
            background-color: #f2f2f2;
            text-align: center;
        }
    </style>
@endsection

@section('content')
    <!-- Metadata -->
    <table width="100%" cellpadding="0" cellspacing="0" style="margin-bottom: 15px;">
        <tr>
            <td width="70" style="line-height: 1.15; vertical-align: top;">Nomor</td>
            <td width="10" style="line-height: 1.15; vertical-align: top; text-align: center;">:</td>
            <td style="line-height: 1.15; vertical-align: top;">{{ $letter_number }}</td>
            <td width="150" style="line-height: 1.15; vertical-align: top; text-align: right;">{{ $letter_date }}</td>
        </tr>
        <tr>
            <td width="70" style="line-height: 1.15; vertical-align: top;">Lampiran</td>
            <td width="10" style="line-height: 1.15; vertical-align: top; text-align: center;">:</td>
            <td style="line-height: 1.15; vertical-align: top;">1 (satu) lembar</td>
        </tr>
        <tr>
            <td width="70" style="line-height: 1.15; vertical-align: top;">Perihal</td>
            <td width="10" style="line-height: 1.15; vertical-align: top; text-align: center;">:</td>
            <td style="line-height: 1.15; vertical-align: top;">Surat Dispensasi Perkuliahan</td>
        </tr>
    </table>

    <div class="recipient-section">
        <div class="recipient-line">Yth. Dosen Pengampu Mata Kuliah</div>
        <div class="recipient-line">Fakultas Vokasi Universitas Brawijaya</div>
{{--        <div class="recipient-line">Malang</div>--}}
    </div>

    <div class="letter-body">
        Sehubungan dengan kegiatan {{ $nama_kegiatan }}, yang berlangsung, pada:
    </div>

    <table class="event-detail-table">
        <tr>
            <td class="label-col">Hari, tanggal</td>
            <td class="sep-col">:</td>
            <td>{{ $tanggal_kegiatan }} </td>
        </tr>
        <tr>
            <td class="label-col">Waktu</td>
            <td class="sep-col">:</td>
            <td>{{ $waktu_mulai }} s.d. {{ $waktu_selesai }} WIB</td>
        </tr>
        <tr>
            <td class="label-col">Tempat</td>
            <td class="sep-col">:</td>
            <td>{{ $tempat_kegiatan }} </td>
        </tr>
    </table>

    <div class="letter-body">
        bersama ini mohon untuk diberikan dispensasi kepada mahasiswa dengan nama terlampir berikut ini untuk tidak mengikuti perkuliahan pada Mata kuliah yang Bapak/Ibu ampu.
    </div>

    <div class="letter-body">
        Demikian surat dispensasi ini kami sampaikan, atas perhatian dan kerjasamanya kami ucapkan terima kasih.
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

    @if(isset($student_list) && count($student_list) > 0)
        <div class="page-break"></div>

        <div class="attachment-header">
            <div class="attachment-header-row">
                Lampiran Surat No. {{ $letter_number }}
            </div>
            <div class="attachment-header-row">
                Perihal: Surat Dispensasi
            </div>
        </div>

        <table class="attachment-table">
            <thead>
            <tr>
                <td width="20" style="text-align: center;">No</td>
                <td>Nama Lengkap</td>
                <td>Program Studi</td>
                <td width="120" style="text-align: center;">NIM</td>
            </tr>
            </thead>
            <tbody>
            @foreach($student_list as $index => $mhs)
                <tr>
                    <td style="text-align: center;">{{ $index + 1 }}</td>
                    <td>{{ $mhs['name'] }}</td>
                    <td>{{ $mhs['program'] }}</td>
                    <td style="text-align: center;">{{ $mhs['nim'] }}</td>
                </tr>
            @endforeach
            </tbody>
        </table>
    @endif
@endsection