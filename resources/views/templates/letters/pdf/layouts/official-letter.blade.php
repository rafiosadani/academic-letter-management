<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8" />
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
    <title>{{ $title ?? 'Surat' }}</title>
    <style>
        /* ✅ KONFIGURASI HALAMAN DOMPDF */
        @page {
            size: A4;
            /* Margin ini memberikan ruang agar konten tidak menabrak header/footer fixed */
            margin-top: 48mm;
            /*margin-bottom: 10mm;*/
            margin-bottom: 0mm;
            margin-left: 21mm;
            margin-right: 21mm;
        }

        body {
            margin: 0;
            padding: 0;
            font-family: Arial, sans-serif;
            font-size: 11pt;
            line-height: 1.15;
            color: #000;
        }

        /* Header - muncul di setiap halaman */
        .main-header {
            position: fixed;
            top: -40mm; /* Naik ke area margin-top @page */
            left: 0;
            right: 0;
            height: 40mm;
        }

        .header-table {
            width: 100%;
            border-collapse: collapse;
        }

        .logo-box {
            width: 2.5cm;
            vertical-align: top;
        }

        .logo-box img {
            width: 2.5cm;
        }

        .institution-box {
            width: 62%;
            padding-left: 12px;
            padding-right: 5px;
            vertical-align: top;
        }

        .ministry-name {
            font-size: 16pt;
            margin: 5px 0 0 0;
            line-height: 1.15;
            letter-spacing: -1px;
            text-transform: uppercase;
        }

        .university-name {
            font-size: 18pt;
            color: #276285;
            font-weight: bold;
            margin: 5px 0 0 0;
            line-height: 1.15;
            letter-spacing: -0.8px;
            text-transform: uppercase;
        }

        .contact-info-box {
            width: auto;
            text-align: left;
            font-size: 11pt;
            line-height: 1.15;
            vertical-align: top;
        }

        .faculty-name {
            font-size: 11pt;
            font-weight: bold;
            display: block;
            margin-top: 5px;
            margin-bottom: 1px;
        }

        .contact-line {
            display: block;
            line-height: 1.15;
            letter-spacing: -1px;
        }

        /* Footer - muncul di setiap halaman */
        .legal-footer {
            position: fixed;
            bottom: -5mm; /* Turun ke area margin-bottom @page */
            left: 0;
            right: 0;
            height: 10mm;
            padding-top: 5px;
            border-top: 1px solid #000;
        }

        .legal-footer-table {
            width: 100%;
            border-collapse: collapse;
        }

        .legal-logo-cell {
            width: 2.6cm;
            vertical-align: top;
        }

        .legal-logo-cell img {
            width: 2.6cm;
        }

        .legal-notice-text {
            text-align: justify;
            font-size: 7pt;
            font-weight: bold;
            line-height: 1.15;
            padding-left: 10px;
        }

        .main-content {
            margin-top: 30px;
            width: 100%;
            min-height: 220mm;
            position: relative;
        }

        /* Prevent table break inside */
        table {
            page-break-inside: avoid;
        }

        @yield('extra-style')
    </style>
</head>
<body>
<header class="main-header">
    <table class="header-table">
        <tr>
            <td class="logo-box">
                @if(setting('header_logo'))
                    <img src="{{ public_path('storage/' . setting('header_logo')) }}" alt="Logo">
                @else
                    <img src="{{ public_path('assets/images/logo-ub.png') }}" alt="Logo UB">
                @endif
            </td>
            <td class="institution-box">
                <div class="ministry-name">
                    {{ setting('header_ministry', 'KEMENTERIAN PENDIDIKAN TINGGI, SAINS, DAN TEKNOLOGI') }}
                </div>
                <div class="university-name">
                    {{ setting('header_university', 'UNIVERSITAS BRAWIJAYA') }}
                </div>
            </td>
            <td class="contact-info-box">
                <div class="faculty-name">{{ setting('header_faculty', 'Fakultas Vokasi') }}</div>
                <div class="contact-line">{{ setting('header_address', 'Jalan. Veteran No 12-16, Malang 65145, Indonesia') }}</div>
                <div class="contact-line">Telp. {{ setting('header_phone', '+62341 553240') }}</div>
                <div class="contact-line">Fax. {{ setting('header_fax', '+62341 553448') }}</div>
                <div class="contact-line">{{ setting('header_email', 'vokasi@ub.ac.id') }}</div>
                <div class="contact-line">{{ setting('header_website', 'http://vokasi.ub.ac.id') }}</div>
            </td>
        </tr>
    </table>
</header>

{{--<footer class="legal-footer">--}}
{{--    <table class="legal-footer-table">--}}
{{--        <tr>--}}
{{--            <td class="legal-logo-cell">--}}
{{--                @if(setting('footer_logo'))--}}
{{--                    <img src="{{ public_path('storage/' . setting('footer_logo')) }}" alt="Logo BSRE">--}}
{{--                @else--}}
{{--                    <img src="{{ public_path('assets/images/logo-bsre.png') }}" alt="Logo BSRE">--}}
{{--                @endif--}}
{{--            </td>--}}
{{--            <td class="legal-notice-text">--}}
{{--                @foreach(explode('|', setting('footer_text', 'UU ITE No. 11 Tahun 2008 Pasal 5 Ayat 1 | "Informasi Elektronik dan/atau Dokumen Elektronik dan/atau hasil cetaknya merupakan alat bukti hukum yang sah." | Dokumen ini telah ditandatangani secara elektronik menggunakan sertifikat elektronik yang diterbitkan BSrE')) as $baris)--}}
{{--                    <div>--}}
{{--                        {{ trim($baris) }}--}}
{{--                    </div>--}}
{{--                @endforeach--}}
{{--            </td>--}}
{{--        </tr>--}}
{{--    </table>--}}
{{--</footer>--}}

<main class="main-content">
    @yield('content')
</main>
</body>
</html>