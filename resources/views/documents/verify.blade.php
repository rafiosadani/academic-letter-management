<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Verifikasi Dokumen - {{ $letter->letter_number }}</title>

    <link href="https://fonts.googleapis.com/css2?family=Open+Sans:wght@400;600;700&family=Source+Sans+Pro:wght@400;600;700&display=swap" rel="stylesheet">
    <link rel="icon" type="image/png" href="{{ setting('favicon') ? Storage::url(setting('favicon')) : asset('assets/images/favicon.png') }}"/>
    @vite('resources/css/app.css')

    <style>
        body {
            /* Font Utama */
            font-family: "Source Sans Pro", "Open Sans", "Helvetica Neue", Helvetica, Arial, sans-serif;
        }

        .font-narrow {
            /* Font Khusus untuk Info Kontak/Header */
            font-family: "Arial Narrow", Arial, sans-serif;
            font-stretch: condensed;
            letter-spacing: -0.01em;
        }

        .text-ub-blue {
            color: #285c82;
        }

        .text-ub-navy {
            color: #1e3c59;
        }

        .text-format-grey {
            color: rgb(86, 86, 86);
        }

        .watermark-bg {
            position: relative;
            overflow: hidden;
        }
        .watermark-bg::before {
            content: '{{ $hash_short }}';
            position: absolute;
            top: 50%;
            left: 50%;
            transform: translate(-50%, -50%) rotate(-45deg);
            font-size: 6rem;
            font-weight: 900;
            color: rgba(203, 213, 225, 0.15);
            letter-spacing: 0.3em;
            z-index: 0;
            pointer-events: none;
            white-space: nowrap;
        }
        .content-above-watermark {
            position: relative;
            z-index: 1;
        }
        @media print {
            .watermark-bg::before {
                color: rgba(203, 213, 225, 0.08);
            }
        }
    </style>
</head>
<body class="bg-slate-50">
<div class="min-h-screen py-5 sm:py-8 px-4 sm:px-6 lg:px-8 watermark-bg">
    <div class="max-w-4xl mx-auto content-above-watermark">

        {{-- Header with UB Logo --}}
        <div class="py-2 mb-5 border-b border-slate-200">
            <div class="flex flex-row items-start justify-between gap-6">
                {{-- Sisi Kiri: Logo & Nama Universitas --}}
                <div class="flex items-center gap-3 sm:gap-4">
                    {{-- Logo Dinamis --}}
                    <div class="h-16 w-16 sm:h-20 sm:w-20 flex items-center justify-center shrink-0">
                        @if(setting('header_logo'))
                            <img src="{{ Storage::url(setting('header_logo')) }}" alt="Logo" class="h-full w-full object-contain">
                        @else
                            <img src="{{ asset('assets/images/logo-ub.png') }}" alt="Logo UB" class="h-full w-full object-contain">
                        @endif
                    </div>

                    <div class="flex flex-col justify-start w-38 sm:w-72">
                        <span class="text-sm sm:text-lg font-narrow text-format-grey leading-4 sm:leading-5">
                            {{ setting('header_ministry', 'KEMENTERIAN PENDIDIKAN TINGGI, SAINS, DAN TEKNOLOGI') }}
                        </span>
                        <span class="text-base sm:text-lg font-bold font-narrow text-ub-blue leading-4 sm:leading-6">{{ setting('header_university', 'UNIVERSITAS BRAWIJAYA') }}</span>
                    </div>
                </div>

                {{-- Sisi Kanan: Detail Kontak --}}
                <div class="text-right text-tiny-plus sm:text-xs-plus font-narrow text-format-grey w-36">
                    <p class="leading-3.5 font-bold uppercase -tracking-wide">
                        {{ setting('header_faculty', 'Fakultas Vokasi') }}
                    </p>
                    <div class="flex flex-col sm:items-end leading-3.5 -tracking-wide">
                        <span>{{ setting('header_address', 'Jalan Veteran No 12-16, Malang 65145, Indonesia') }}</span>
                        <span>Telp: {{ setting('header_phone', '+62341 553240') }}</span>
                        <span>E-mail: {{ setting('header_email', 'vokasi@ub.ac.id') }}</span>
                        <span>{{ str_replace(['http://', 'https://'], '', setting('header_website', 'vokasi.ub.ac.id')) }}</span>
                    </div>
                </div>
            </div>
        </div>

        @php
            // DYNAMIC: Find Wakil Dekan approval based on position
            $wdApproval = $approvals->first(function($approval) {
                $positions = is_string($approval->required_positions)
                    ? json_decode($approval->required_positions, true)
                    : $approval->required_positions;

                return in_array('wakil_dekan_akademik', $positions ?? []);
            });

            $wdName = $wdApproval?->approver?->profile?->full_name
                ?? $wdApproval?->assignedApprover?->profile?->full_name
                ?? 'Wakil Dekan Bidang Akademik';
        @endphp

        <div class="mb-5">
            <h2 class="text-sm sm:text-xl text-ub-blue">
                {{ $letter->letter_type->label() }} {{ $profile->full_name }}
            </h2>
            <div class="flex flex-col sm:gap-0.5 text-sm text-format-grey">
                <span>
                    Naskah ini <span class="font-semibold">telah</span> ditandatangani oleh:
                </span>
                <span class="font-bold text-ub-blue uppercase">
                    {{ $wdName }}
                </span>
                <span>
                    {{ $wdApproval->position_labels }} {{ setting('header_faculty', 'Fakultas Vokasi') }}
                </span>
            </div>
        </div>

        {{-- Document Details Table --}}
        <div class="bg-transparent mb-5">
            <div class="grid grid-cols-1 gap-1.5 sm:gap-1 text-format-grey">
                <div class="grid sm:grid-cols-5">
                    <p class="text-sm sm:col-span-1">Tanggal Surat</p>
                    <p class="text-sm sm:col-span-4 text-ub-blue font-bold">{{ $letter->created_at_formatted }}</p>
                </div>
                <div class="grid sm:grid-cols-5">
                    <p class="text-sm sm:col-span-1">Nomor Surat</p>
                    <p class="text-sm sm:col-span-4 text-ub-blue font-semibold">{{ $letter->letter_number }}</p>
                </div>
                <div class="grid sm:grid-cols-5">
                    <p class="text-sm sm:col-span-1">Jenis Surat</p>
                    <p class="text-sm sm:col-span-4 font-semibold">{{ $letter->letter_type->label() }}</p>
                </div>
                <div class="grid sm:grid-cols-5">
                    <p class="text-sm sm:col-span-1">Jenis Tandatangan</p>
                    <p class="text-sm sm:col-span-4 font-semibold">Internal Fakultas Vokasi UB</p>
                </div>

                @php
                    $drafterApproval = $approvals->firstWhere('step', 1);

                    $pemarafApproval = $approvals->first(function($approval) {
                        $positions = is_string($approval->required_positions)
                            ? json_decode($approval->required_positions, true)
                            : $approval->required_positions;

                        return in_array('kasubbag_akademik', $positions ?? []);
                    });
                @endphp

                <div class="grid sm:grid-cols-5">
                    <p class="text-xs-plus sm:text-sm sm:col-span-1">Drafter</p>
                    <p class="text-xs-plus sm:text-sm sm:col-span-4 font-semibold">{{ $drafterApproval?->approver->profile->full_name ?? $drafterApproval?->position_labels }}</p>
                </div>
                <div class="grid sm:grid-cols-5">
                    <p class="text-xs-plus sm:text-sm sm:col-span-1">Pemaraf</p>
                    <p class="text-xs-plus sm:text-sm sm:col-span-4 font-semibold">
                        @if($pemarafApproval)
                            {{ $pemarafApproval->approver?->profile?->full_name ?? '-' }}
                            @if($pemarafApproval->status === 'approved')
                                <span class="text-error text-xs">*Disetujui</span>
                            @endif
                        @else
                            -
                        @endif
                    </p>
                </div>
                <div class="grid sm:grid-cols-5">
                    <p class="text-xs-plus sm:text-sm sm:col-span-1">Status Surat</p>
                    <div class="sm:col-span-4">
                        @if($is_valid)
                            <span class="text-xs-plus sm:text-sm text-ub-blue font-bold">Published</span>
                        @else
                            <span class="text-xs-plus sm:text-sm text-ub-blue font-bold">Draft</span>
                        @endif
                    </div>
                </div>
            </div>

            {{-- Download Button --}}
            {{--            @if($is_valid)--}}
            {{--                <div class="mt-6">--}}
            {{--                    <a href="{{ route('documents.download-verified', $hash) }}"--}}
            {{--                       class="btn bg-primary text-white font-medium hover:bg-primary-focus px-6 py-2.5 rounded-lg inline-flex items-center space-x-2">--}}
            {{--                        <i class="fa-solid fa-eye"></i>--}}
            {{--                        <span>Lihat Validitas Dokumen</span>--}}
            {{--                    </a>--}}
            {{--                </div>--}}
            {{--            @endif--}}
        </div>

        {{-- Approval Timeline (DESKRIPSI) --}}
        <div class="bg-transparent mb-5 border border-slate-200 overflow-hidden">
            <div class="border-b border-slate-200 p-3">
                <h3 class="text-xs-plus sm:text-sm font-bold text-format-grey uppercase tracking-wider">Deskripsi</h3>
            </div>

            <div class="overflow-x-auto">
            <table class="w-full border-collapse border-spacing-0">
                <tbody>
                @foreach($approvals->sortByDesc('step') as $approval)
                    @php
                        $passedPositions = [];
                        foreach ($approvals->where('step', '<', $approval->step)->where('status', 'approved') as $pastStep) {
                            $pastPositions = is_string($pastStep->required_positions)
                                ? json_decode($pastStep->required_positions, true)
                                : $pastStep->required_positions;
                            $passedPositions = array_merge($passedPositions, $pastPositions ?? []);
                        }

                        $positions = is_string($approval->required_positions)
                            ? json_decode($approval->required_positions, true)
                            : $approval->required_positions;

                        $isLastStep = $approval->step === $approvals->max('step');

                        if ($isLastStep) {
                            $actionLabel = $approval->status === 'approved' ? 'Published' : 'Pending Publish';
                            $actionColor = $approval->status === 'approved' ? 'bg-[#9fcc2e]' : 'bg-[#ff9800]';
                        } else {
                            $nextApproval = $approvals->firstWhere('step', $approval->step + 1);

                            if ($nextApproval && $approval->status === 'approved') {
                                $nextPositions = is_string($nextApproval->required_positions)
                                    ? json_decode($nextApproval->required_positions, true)
                                    : $nextApproval->required_positions;

                                // Check if next step has NEW positions (not passed before)
                                $hasNewKasubbag = in_array('kasubbag_akademik', $nextPositions ?? [])
                                    && !in_array('kasubbag_akademik', $passedPositions);

                                $hasNewWakilDekan = in_array('wakil_dekan_akademik', $nextPositions ?? [])
                                    && !in_array('wakil_dekan_akademik', $passedPositions);

                                if ($hasNewKasubbag) {
                                    $actionLabel = 'Kirim ke Pemaraf';
                                    $actionColor = 'bg-[#00c0ef]';
                                } elseif ($hasNewWakilDekan) {
                                    $actionLabel = 'Kirim Ke Penandatangan';
                                    $actionColor = 'bg-[#285c82]';
                                } else {
                                    // Next step is revisit or final publish step
                                    $actionLabel = 'Disetujui';
                                    $actionColor = 'bg-[#285c82]';
                                }
                            } else {
                                // Not approved yet or no next step
                                $actionLabel = 'Pending';
                                $actionColor = 'bg-[#ff9800]';
                            }
                        }
                    @endphp
                    <tr class="border-b border-slate-200 last:border-0">
                        <td class="whitespace-nowrap p-3 align-top border-r border-slate-200 w-[75%] sm:w-[70%]">
                            <div class="flex flex-wrap items-center gap-1.5 sm:gap-2">
                                <h4 class="text-sm text-ub-blue font-bold">
                                    {{ $approval->step_label }}
                                </h4>
                                <span class="{{ $actionColor }} text-white text-tiny px-1 py-0.5 rounded font-bold uppercase">
                                    {{ $actionLabel }}
                                </span>
                            </div>
                            <p class="text-xs-plus text-format-grey uppercase">
                                {{ $approval->approver?->profile?->full_name ?? $approval->assignedApprover?->profile?->full_name ?? 'Pending' }}
                            </p>
                        </td>

                        <td class="whitespace-nowrap p-3 align-middle text-right w-32">
                            <span class="text-xs-plus text-slate-600 font-medium whitespace-nowrap">
                                {{ $approval->approved_at ? $approval->approved_at_full : '-' }}
                            </span>
                        </td>
                    </tr>
                @endforeach
                </tbody>
            </table>
            </div>
        </div>

        <div class="flex flex-col bg-[#f5f5f5] p-4 mb-5 border-t border-slate-200">
            {{-- Disclaimer --}}
            <div>
                <h4 class="text-sm text-ub-blue font-bold mb-3 sm:mb-4">DISCLAIMER</h4>
                <p class="text-sm text-format-grey text-justify font-medium leading-5 sm:leading-relaxed">
                    The copyright in the material contained on this Website belongs to the University or its licensors.
                    The trademarks appearing on this Website are protected by the laws of Indonesia and by international trademark laws.
                </p>
            </div>
            {{-- Caution --}}
            <div>
                <h4 class="text-sm text-ub-blue font-bold my-3 sm:my-4">CAUTION</h4>
                <p class="text-sm text-format-grey text-justify font-medium leading-5 sm:leading-relaxed">
                    The information enclosed in this document (and any attachments) may be legally privileged and/or confidential
                    and is intended only for the use of the addressee(s). No addressee should forward, print, copy or otherwise
                    reproduce this document in any manner that would allow it to be viewed by any individual not originally listed
                    as a recipient. If the reader of this message is not the intended recipient, you are hereby notified that any
                    unauthorized disclosure, dissemination, distribution, copying or the taking of any action in reliance on the
                    information herein is strictly prohibited. If you have received this document in error, please immediately
                    notify the sender.
                </p>
            </div>
        </div>

        {{-- Footer --}}
        <div class="text-center text-xs sm:text-xs-plus text-slate-500">
            <p>Dokumen terverifikasi pada: {{ $verified_at->translatedFormat('d F Y, H:i') }} WIB</p>
            <p class="mt-1">Hash: {{ $hash_short }}</p>
        </div>
    </div>
</div>
</body>
</html>