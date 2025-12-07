<x-layouts.app title="Pusat Notifikasi">
    <x-ui.breadcrumb
            title="Pusat Notifikasi"
            :items="[
            ['label' => 'Notifikasi']
        ]"
    />

    <div class="mt-4 grid grid-cols-12 gap-4 sm:mt-5 sm:gap-5 lg:mt-6 lg:gap-6">
        <div class="col-span-12">
            <div class="flex items-center justify-between">
                <div>
                    <h2 class="text-base font-medium tracking-wide text-slate-700 line-clamp-1 dark:text-navy-100">
                        Semua Notifikasi
                    </h2>
                    <p class="mt-1 text-xs+ text-slate-400 dark:text-navy-300">
                        {{ $unreadCount }} notifikasi belum dibaca
                    </p>
                </div>

                <div class="flex items-center space-x-2">
                    {{-- Filter Buttons --}}
                    <div class="flex space-x-1">
                        <a href="{{ route('notifications.index') }}"
                           class="btn h-9 {{ !request('unread_only') && !request('category') ? 'bg-primary text-white' : 'border border-slate-300 text-slate-700 dark:border-navy-450 dark:text-navy-100' }}">
                            <i class="fa-solid fa-inbox mr-1"></i>
                            Semua
                        </a>
                        <a href="{{ route('notifications.index', ['unread_only' => 1]) }}"
                           class="btn h-9 {{ request('unread_only') ? 'bg-primary text-white' : 'border border-slate-300 text-slate-700 dark:border-navy-450 dark:text-navy-100' }}">
                            <i class="fa-solid fa-envelope mr-1"></i>
                            Belum Dibaca ({{ $unreadCount }})
                        </a>
                    </div>

                    {{-- Mark All as Read --}}
                    @if($unreadCount > 0)
                        <button
                            type="button"
                            data-toggle="modal"
                            data-target="#mark-all-notifications-read-modal"
                            class="btn h-9 bg-success font-medium text-white hover:bg-success-focus focus:bg-success-focus active:bg-success-focus/90"
                        >
                            <i class="fa-solid fa-check-double mr-1"></i>
                            Tandai Semua Dibaca
                        </button>
                    @endif

                    {{-- Settings --}}
                    <a href="{{ route('notifications.settings') }}"
                       class="btn h-9 border border-slate-300 text-slate-700 hover:bg-slate-150 dark:border-navy-450 dark:text-navy-100 dark:hover:bg-navy-500"
                       title="Pengaturan Notifikasi">
                        <i class="fa-solid fa-cog"></i>
                    </a>
                </div>
            </div>

            <div class="card mt-3">
                <div class="is-scrollbar-hidden min-w-full overflow-x-auto">
                    @forelse($notifications as $notification)
                        @php
                            $data = $notification->data;
                            $isRead = $notification->read_at !== null;
                        @endphp

                        <div class="flex items-start space-x-4 border-b border-slate-150 p-4 dark:border-navy-500 {{ !$isRead ? 'bg-primary/5 dark:bg-accent/10' : '' }}">
                            {{-- Icon --}}
                            <div class="flex size-10 shrink-0 items-center justify-center rounded-lg {{ !$isRead ? 'bg-primary/10 text-primary' : 'bg-slate-100 text-slate-400 dark:bg-navy-700 dark:text-navy-300' }}">
                                <i class="fa-solid {{ $data['icon'] ?? 'fa-solid fa-bell' }} text-lg"></i>
                            </div>

                            {{-- Content --}}
                            <div class="flex-1 min-w-0">
                                <div class="flex items-start justify-between">
                                    <div class="flex-1">
                                        <h3 class="font-medium text-slate-700 dark:text-navy-100 {{ !$isRead ? 'font-semibold' : '' }}">
                                            {{ $data['title'] ?? 'Notifikasi' }}
                                        </h3>
                                        <p class="mt-1 text-xs text-slate-600 dark:text-navy-200">
                                            {{ $data['message'] ?? '' }}
                                        </p>

                                        {{-- Action Button --}}
                                        @if(isset($data['action']['url']))
                                            <a href="{{ $data['action']['url'] }}"
                                               class="btn mt-3 h-8 bg-{{ $data['color'] ?? 'primary' }}/10 text-{{ $data['color'] ?? 'primary' }} hover:bg-{{ $data['color'] ?? 'primary' }}/20 text-xs">
                                                {{ $data['action']['text'] ?? 'Lihat Detail' }}
                                                <i class="fa-solid fa-arrow-right ml-1"></i>
                                            </a>
                                        @endif

                                        {{-- Timestamp --}}
                                        <p class="mt-2 text-xs text-slate-400 dark:text-navy-300">
                                            <i class="fa-solid fa-clock mr-1"></i>
                                            {{ $notification->created_at->diffForHumans() }}
                                        </p>
                                    </div>

                                    {{-- Mark as Read Button --}}
                                    @if(!$isRead)
                                        <button
                                            onclick="markAsRead('{{ $notification->id }}')"
                                            class="btn size-8 p-0 text-success hover:bg-success/20 focus:bg-success/20 active:bg-success/25"
                                            title="Tandai Dibaca">
                                            <i class="fa-solid fa-check"></i>
                                        </button>
{{--                                        <button--}}
{{--                                                type="button"--}}
{{--                                                data-toggle="modal"--}}
{{--                                                data-target="#mark-notification-read-modal-{{ $notification->id }}"--}}
{{--                                                class="btn size-8 p-0 text-success hover:bg-success/20 focus:bg-success/20 active:bg-success/25"--}}
{{--                                                title="Tandai Dibaca">--}}
{{--                                            <i class="fa-solid fa-check"></i>--}}
{{--                                        </button>--}}
                                    @else
                                        <div class="text-xs text-success flex items-center">
                                            <i class="fa-solid fa-check-circle mr-1"></i>
                                            Dibaca
                                        </div>
                                    @endif
                                </div>
                            </div>
                        </div>
                    @empty
                        <div class="p-8 text-center">
                            <div class="flex justify-center mb-4">
                                <i class="fa-solid fa-inbox text-6xl text-slate-300 dark:text-navy-500"></i>
                            </div>
                            <h3 class="text-base font-medium text-slate-700 dark:text-navy-100">
                                Tidak ada notifikasi
                            </h3>
                            <p class="text-xs-plus text-slate-500 dark:text-navy-300">
                                Notifikasi akan muncul di sini
                            </p>
                        </div>
                    @endforelse
                </div>

                @if ($notifications->hasPages())
                    <div class="flex justify-center p-4 border-t border-slate-150 dark:border-navy-500">
                        {{ $notifications->links() }}
                    </div>
                @endif
            </div>
        </div>
    </div>
    {{-- Modals --}}
    @if($unreadCount > 0)
        @include('notifications.modals._mark-all-read', ['unreadCount' => $unreadCount])
    @endif

    @foreach($notifications as $notification)
        @if($notification->read_at === null)
            @include('notifications.modals._mark-single-read', ['notification' => $notification])
        @endif
    @endforeach

    {{-- JavaScript for AJAX --}}
    <x-slot:scripts>
        <script>
            const csrfToken = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content');

            // Mark single notification as read
{{--            @foreach($notifications as $notification)--}}
{{--                @if($notification->read_at === null)--}}
{{--                    document.getElementById('mark-notification-read-form-{{ $notification->id }}')?.addEventListener('submit', function(e) {--}}
{{--                        e.preventDefault();--}}
{{--                        markAsRead('{{ $notification->id }}');--}}
{{--                    });--}}
{{--               @endif--}}
{{--            @endforeach--}}

            // Mark all notifications as read
            @if($unreadCount > 0)
                document.getElementById('mark-all-notifications-read-form')?.addEventListener('submit', function(e) {
                    e.preventDefault();
                    markAllAsRead();
                });
            @endif

            function markAsRead(notificationId) {
                fetch(`/api/notifications/${notificationId}/mark-as-read`, {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': csrfToken,
                        'Accept': 'application/json',
                        'X-Requested-With': 'XMLHttpRequest'
                    },
                    credentials: 'same-origin'
                })
                    .then(response => {
                        if (!response.ok) throw new Error(`HTTP ${response.status}`);
                        return response.json();
                    })
                    .then(data => {
                        if (data.success) {
                            // Show success notification
                            window.$notification({
                                variant: 'success',
                                text: 'Notifikasi berhasil ditandai sebagai dibaca!',
                                position: 'center-top',
                                duration: 2000
                            });

                            // Reload after animation
                            setTimeout(() => {
                                window.location.reload();
                            }, 2200);
                        } else {
                            window.$notification({
                                variant: 'error',
                                text: 'Gagal menandai notifikasi: ' + (data.message || 'Unknown error'),
                                position: 'center-top',
                                duration: 3000
                            });
                        }
                    })
                    .catch(error => {
                        window.$notification({
                            variant: 'error',
                            text: 'Terjadi kesalahan: ' + error.message,
                            position: 'center-top',
                            duration: 3000
                        });
                    });
            }

            function markAllAsRead() {
                fetch('/api/notifications/mark-all-as-read', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': csrfToken,
                        'Accept': 'application/json',
                        'X-Requested-With': 'XMLHttpRequest'
                    },
                    credentials: 'same-origin'
                })
                    .then(response => {
                        if (!response.ok) throw new Error(`HTTP ${response.status}`);
                        return response.json();
                    })
                    .then(data => {
                        if (data.success) {
                            // Show success notification
                            window.$notification({
                                variant: 'success',
                                text: 'Semua notifikasi berhasil ditandai sebagai dibaca!',
                                position: 'center-top',
                                duration: 2000
                            });

                            // Reload after animation
                            setTimeout(() => {
                                window.location.reload();
                            }, 2200);
                        } else {
                            window.$notification({
                                variant: 'error',
                                text: 'Gagal menandai semua notifikasi: ' + (data.message || 'Unknown error'),
                                position: 'center-top',
                                duration: 3000
                            });
                        }
                    })
                    .catch(error => {
                        window.$notification({
                            variant: 'error',
                            text: 'Terjadi kesalahan: ' + error.message,
                            position: 'center-top',
                            duration: 3000
                        });
                    });
            }
        </script>
    </x-slot:scripts>
</x-layouts.app>