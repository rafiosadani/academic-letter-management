<x-layouts.app title="Pengaturan Notifikasi">
    <x-ui.breadcrumb
            title="Pengaturan Notifikasi"
            :items="[
            ['label' => 'Notifikasi', 'url' => route('notifications.index')],
            ['label' => 'Pengaturan']
        ]"
    />

    <x-ui.page-header
            title="Pengaturan Notifikasi"
            description="Atur preferensi notifikasi Anda"
            :backUrl="route('notifications.index')"
    >
        <x-slot:icon>
            <svg xmlns="http://www.w3.org/2000/svg" class="size-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z" />
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
            </svg>
        </x-slot:icon>
    </x-ui.page-header>

    <form method="POST" action="{{ route('notifications.settings.update') }}" class="space-y-6">
        @csrf

        <div class="grid grid-cols-12 gap-4 sm:gap-5 lg:gap-6">
            {{-- Info Card --}}
            <div class="col-span-12">
                <div class="rounded-lg bg-info/10 border border-info/20 p-4">
                    <div class="flex items-start space-x-3">
                        <i class="fa-solid fa-info-circle text-info text-xl mt-0.5"></i>
                        <div class="text-sm text-slate-700 dark:text-navy-100">
                            <p class="font-medium mb-1">Tentang Pengaturan Notifikasi</p>
                            <ul class="list-disc list-inside space-y-1 text-xs text-slate-600 dark:text-navy-200">
                                <li>Notifikasi di aplikasi <strong>selalu aktif</strong> dan tidak dapat dimatikan</li>
                                <li>Email notifikasi bersifat <strong>opsional</strong> - Anda bisa mengaktifkan per kategori</li>
                                <li>Pilih <strong>"Kirim Langsung"</strong> untuk menerima email segera</li>
                                <li>Pilih <strong>"Ringkasan Harian"</strong> untuk menerima email kumpulan notifikasi setiap hari</li>
                            </ul>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Settings per Category --}}
            @foreach($categories as $category)
                <div class="col-span-12 lg:col-span-6">
                    <div class="card p-5">
                        <div class="flex items-center space-x-3 mb-4">
                            <div class="flex size-10 items-center justify-center rounded-lg bg-primary/10 text-primary dark:bg-accent-light/10 dark:text-accent-light">
                                <i class="fa-solid {{ $category === App\Enums\NotificationCategory::ACADEMIC_YEAR ? 'fa-calendar-alt' : ($category === App\Enums\NotificationCategory::SEMESTER ? 'fa-calendar-days' : ($category === App\Enums\NotificationCategory::LETTER_APPROVAL ? 'fa-file-signature' : 'fa-bell')) }}"></i>
                            </div>
                            <div>
                                <h3 class="text-base font-medium text-slate-700 dark:text-navy-100">
                                    {{ $category->label() }}
                                </h3>
                                <p class="text-xs text-slate-500 dark:text-navy-300">
                                    {{ $category->description() }}
                                </p>
                            </div>
                        </div>

                        <div class="space-y-3">
                            {{-- In-App Notification (Always ON) --}}
                            <label class="inline-flex items-center justify-between w-full p-3 rounded-lg bg-slate-100 dark:bg-navy-800">
                                <div class="flex items-center space-x-3">
                                    <i class="fa-solid fa-bell text-slate-500 dark:text-navy-300"></i>
                                    <div>
                                        <span class="text-sm font-medium text-slate-700 dark:text-navy-100">Notifikasi di Aplikasi</span>
                                        <p class="text-xs text-slate-500 dark:text-navy-300">Selalu aktif</p>
                                    </div>
                                </div>
                                <input
                                    type="checkbox"
                                    checked
                                    disabled
                                    class="form-switch h-5 w-10 rounded-full bg-success before:rounded-full before:bg-white"
                                />
                            </label>

                            {{-- Email Notification Toggle --}}
                            <label class="inline-flex items-center justify-between w-full p-3 rounded-lg border border-slate-200 dark:border-navy-500 hover:bg-slate-50 dark:hover:bg-navy-700 cursor-pointer">
                                <div class="flex items-center space-x-3">
                                    <i class="fa-solid fa-envelope text-slate-500 dark:text-navy-300"></i>
                                    <div>
                                        <span class="text-sm font-medium text-slate-700 dark:text-navy-100">Email Notifikasi</span>
                                        <p class="text-xs text-slate-500 dark:text-navy-300">Terima notifikasi via email</p>
                                    </div>
                                </div>
                                <input type="hidden" name="settings[{{ $category->value }}][channel_email]" value="0">
                                <input
                                    type="checkbox"
                                    name="settings[{{ $category->value }}][channel_email]"
                                    value="1"
                                    {{ $settings[$category->value]->channel_email ? 'checked' : '' }}
                                    class="form-switch h-5 w-10 rounded-full bg-slate-300 before:rounded-full before:bg-slate-50 checked:bg-primary checked:before:bg-white dark:bg-navy-900 dark:before:bg-navy-300 dark:checked:bg-accent dark:checked:before:bg-white"
                                    onchange="toggleEmailOptions('{{ $category->value }}', this.checked)"
                                />
                            </label>

                            {{-- Email Options (Show if channel_email is ON) --}}
                            <div id="email-options-{{ $category->value }}" class="ml-10 grid grid-cols-2 justify-center {{ !$settings[$category->value]->channel_email ? 'hidden' : '' }}">
                                {{-- Email Immediately --}}
                                <label class="inline-flex items-center space-x-2">
                                    <input type="hidden" name="settings[{{ $category->value }}][email_immediately]" value="0">
                                    <input
                                        type="checkbox"
                                        name="settings[{{ $category->value }}][email_immediately]"
                                        value="1"
                                        {{ $settings[$category->value]->email_immediately ? 'checked' : '' }}
                                        class="form-checkbox is-basic size-5 rounded border-slate-400/70 checked:border-primary checked:bg-primary hover:border-primary focus:border-primary dark:border-navy-400 dark:checked:border-accent dark:checked:bg-accent dark:hover:border-accent dark:focus:border-accent"
                                    />
                                    <span class="text-xs text-slate-600 dark:text-navy-200">Kirim email langsung</span>
                                </label>

                                {{-- Email Daily Digest --}}
                                <label class="inline-flex items-center space-x-2">
                                    <input type="hidden" name="settings[{{ $category->value }}][email_daily_digest]" value="0">
                                    <input
                                        type="checkbox"
                                        name="settings[{{ $category->value }}][email_daily_digest]"
                                        value="1"
                                        {{ $settings[$category->value]->email_daily_digest ? 'checked' : '' }}
                                        class="form-checkbox is-basic size-5 rounded border-slate-400/70 checked:border-primary checked:bg-primary hover:border-primary focus:border-primary dark:border-navy-400 dark:checked:border-accent dark:checked:bg-accent dark:hover:border-accent dark:focus:border-accent"
                                    />
                                    <span class="text-xs text-slate-600 dark:text-navy-200">Ringkasan harian (08:00 WIB)</span>
                                </label>
                            </div>
                        </div>
                    </div>
                </div>
            @endforeach
        </div>

        {{-- Action Buttons (Sticky Bottom) --}}
        <x-form.sticky-form-actions
            :cancelUrl="route('notifications.index')"
            submitText="Simpan Pengaturan"
            submitType="primary"
        />
    </form>
    <x-slot:scripts>
        <script>
            function toggleEmailOptions(category, isChecked) {
                const emailOptions = document.getElementById(`email-options-${category}`);
                if (isChecked) {
                    emailOptions.classList.remove('hidden');
                } else {
                    emailOptions.classList.add('hidden');
                }
            }
        </script>
    </x-slot:scripts>
</x-layouts.app>