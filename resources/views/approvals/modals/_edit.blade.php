<div
    class="modal fixed inset-0 z-[100] flex flex-col items-center justify-center overflow-hidden px-4 py-6 sm:px-5"
    id="edit-content-modal-{{ $approval->id }}"
    data-open-on-error="{{ session('open_edit_modal_id') == $approval->id ? 'true' : 'false' }}"
    role="dialog"
>
    <div class="modal-overlay absolute inset-0 bg-slate-900/60 transition-opacity duration-300"></div>

    <div class="modal-content relative flex w-full max-w-3xl origin-top flex-col overflow-hidden rounded-lg bg-white dark:bg-navy-700">
        <div class="flex justify-between rounded-t-lg bg-slate-200 px-4 py-4 dark:bg-navy-800 sm:px-5">
            <div class="flex items-center space-x-2">
                <div class="flex size-7 items-center justify-center rounded-lg bg-warning/10 text-warning dark:bg-warning/15">
                    <i class="fa-solid fa-pen-to-square text-sm"></i>
                </div>
                <h4 class="text-lg font-medium text-slate-700 dark:text-navy-100">
                    Edit Konten Surat
                </h4>
            </div>
            <button
                data-close-modal
                class="btn size-7 rounded-full p-0 hover:bg-slate-300/20 focus:bg-slate-300/20 active:bg-slate-300/25"
            >
                <i class="fa-solid fa-xmark text-base"></i>
            </button>
        </div>
        <div class="scrollbar-sm overflow-y-auto px-4 py-4 sm:px-5">

            {{-- Info --}}
            <div class="rounded-lg border border-info/20 bg-info/10 p-3 mb-4">
                <div class="flex items-start space-x-3">
                    <div class="flex size-8 shrink-0 items-center justify-center rounded-lg bg-white shadow-sm dark:bg-navy-700">
                        <i class="fa-solid fa-info-circle text-lg text-info"></i>
                    </div>
                    <div class="flex flex-col min-w-0 text-left">
                        <span class="text-tiny font-bold uppercase tracking-wider text-info">
                            Informasi Editor
                        </span>
                        <p class="mt-1 text-xs text-justify text-slate-600 dark:text-navy-200">
                            Anda dapat mengedit konten surat sebelum menyetujui. Perubahan akan tersimpan dan mahasiswa akan melihat data yang telah diedit.
                        </p>
                    </div>
                </div>
            </div>

            {{-- Form --}}
            <form method="POST" action="{{ route('approvals.edit-content', $approval) }}">
                @csrf

                <div class="space-y-4 text-left">
                    @php
                        $letterType = $letter->letter_type;
                        $formFields = $letterType->formFields();
                        $currentData = $letter->data_input;
                    @endphp

                    @foreach($formFields as $fieldName => $config)
                        <div>
                            <label class="block">
                                <span class="text-xs font-medium text-slate-600 dark:text-navy-100">
                                    {{ $config['label'] }}
                                    @if($config['required'])
                                        <span class="text-error">*</span>
                                    @endif
                                </span>
                                @if($config['type'] === 'student_list')
                                    <div x-data="studentListEditor{{ $approval->id }}()"
                                         x-init="init()"
                                         @click.stop.prevent
                                         @mousedown.stop
                                         @touchstart.stop
                                         class="mt-1.5">

                                        <div class="flex items-center justify-between mb-2">
                                            <span class="text-[10px] font-bold uppercase tracking-wider text-slate-400 dark:text-navy-300">
                                                Data Kolektif Mahasiswa (<span x-text="students.length"></span>)
                                            </span>
                                            <button type="button"
                                                    @click.stop.prevent="addStudent()"
                                                    class="btn h-6 rounded bg-primary px-2 text-tiny font-medium text-white hover:bg-primary-focus">
                                                <i class="fa-solid fa-plus mr-1"></i> Tambah Data
                                            </button>
                                        </div>

                                        <div class="is-scrollbar-hidden min-w-full overflow-x-auto rounded border border-slate-200 dark:border-navy-500">
                                            <table class="w-full text-left">
                                                <thead>
                                                <tr class="bg-slate-50 dark:bg-navy-800">
                                                    <th class="w-8 border-r border-slate-200 px-2 py-2 text-tiny font-medium uppercase text-slate-600 dark:border-navy-500 dark:text-navy-100 text-center">#</th>
                                                    <th class="border-r border-slate-200 px-3 py-2 text-tiny font-medium uppercase text-slate-600 dark:border-navy-500 dark:text-navy-100">Nama</th>
                                                    <th class="w-40 border-r border-slate-200 px-3 py-2 text-tiny font-medium uppercase text-slate-600 dark:border-navy-500 dark:text-navy-100">NIM</th>
                                                    <th class="w-60 border-r border-slate-200 px-3 py-2 text-tiny font-medium uppercase text-slate-600 dark:border-navy-500 dark:text-navy-100">Prodi</th>
                                                    <th class="w-10 px-2 py-2 text-center text-[10px] font-medium uppercase text-slate-600 dark:text-navy-100">
                                                        <i class="fa-solid fa-trash-can"></i>
                                                    </th>
                                                </tr>
                                                </thead>
                                                <tbody class="bg-white dark:bg-navy-900">
                                                <template x-for="student in students" :key="student._id">
                                                    <tr class="border-t border-slate-200 dark:border-navy-500 hover:bg-slate-50/50" x-show="student && student._id">
                                                        <td class="border-r border-slate-200 px-2 py-2 text-center text-xs text-slate-500 dark:border-navy-500"
                                                            x-text="students.indexOf(student) + 1"></td>

                                                        <td class="border-r border-slate-200 px-2 py-2 dark:border-navy-500">
                                                            <input x-model="student.name"
                                                                   @click.stop
                                                                   class="form-input w-full rounded border border-slate-200 bg-white px-2 py-1 text-xs focus:border-primary dark:border-navy-450 dark:bg-navy-900"
                                                                   placeholder="Nama..."
                                                                   type="text" />
                                                        </td>

                                                        <td class="border-r border-slate-200 px-2 py-2 dark:border-navy-500">
                                                            <input x-model="student.nim"
                                                                   @click.stop
                                                                   class="form-input w-full rounded border border-slate-200 bg-white px-2 py-1 text-xs focus:border-primary dark:border-navy-450 dark:bg-navy-900 font-mono"
                                                                   placeholder="NIM..."
                                                                   type="text"
                                                                   maxlength="15" />
                                                        </td>

                                                        <td class="border-r border-slate-200 px-2 py-2 dark:border-navy-500">
                                                            <select x-model="student.program"
                                                                    @click.stop
                                                                    class="form-select w-full rounded border border-slate-200 bg-white px-2 py-1 text-xs focus:border-primary dark:border-navy-450 dark:bg-navy-900"
                                                                    style="background-position: right 0.4rem center; padding-right: 1.5rem;">
                                                                <option value="">Pilih Prodi</option>
                                                                <template x-for="prodi in studyPrograms" :key="prodi">
                                                                    <option :value="prodi" x-text="prodi" :selected="prodi === student.program"></option>
                                                                </template>
                                                            </select>
                                                        </td>

                                                        <td class="px-2 py-2 text-center">
                                                            <button type="button"
                                                                    @click.stop.prevent="removeStudent(student._id)"
                                                                    class="h-6 w-6 text-error hover:bg-error/10 rounded transition-colors">
                                                                <i class="fa-solid fa-xmark text-[10px]"></i>
                                                            </button>
                                                        </td>
                                                    </tr>
                                                </template>
                                                <tr x-show="students.length === 0">
                                                    <td colspan="5" class="px-3 py-2 text-center text-tiny-plus italic text-slate-400">
                                                        Belum ada data mahasiswa. Klik tombol "Tambah Data" untuk menambahkan.
                                                    </td>
                                                </tr>
                                                </tbody>
                                            </table>
                                        </div>

                                        <input type="hidden"
                                               :name="'{{ $fieldName }}'"
                                               :value="JSON.stringify(students.map(s => ({name: s.name, nim: s.nim, program: s.program})))">
                                    </div>

                                    <script>
                                        (function() {
                                            const editorId = 'studentListEditor{{ $approval->id }}';

                                            if (typeof window[editorId] === 'function') {
                                                return;
                                            }

                                            window[editorId] = function() {
                                                return {
                                                    students: [],
                                                    studyPrograms: @json($studyPrograms ?? []),
                                                    nextId: Date.now(),

                                                    init() {
                                                        let initialValue = null;

                                                        const oldInput = @json(old($fieldName));
                                                        if (oldInput) {
                                                            if (typeof oldInput === 'string') {
                                                                try {
                                                                    initialValue = JSON.parse(oldInput);
                                                                } catch (e) {
                                                                    initialValue = null;
                                                                }
                                                            } else {
                                                                initialValue = oldInput;
                                                            }
                                                        }

                                                        if (!initialValue) {
                                                            initialValue = @json($currentData[$fieldName] ?? []);
                                                        }

                                                        if (Array.isArray(initialValue) && initialValue.length > 0) {
                                                            this.students = initialValue.map(student => ({
                                                                _id: this.nextId++,
                                                                name: student.name || '',
                                                                nim: student.nim || '',
                                                                program: student.program || ''
                                                            }));
                                                        }
                                                    },

                                                    addStudent() {
                                                        this.students.push({
                                                            _id: this.nextId++,
                                                            name: '',
                                                            nim: '',
                                                            program: ''
                                                        });
                                                    },

                                                    removeStudent(studentId) {
                                                        const filtered = this.students.filter(s => s._id !== studentId);
                                                        this.students = [...filtered];
                                                    }
                                                }
                                            };
                                        })();
                                    </script>

                                @elseif($config['type'] === 'select')
                                    <select
                                            name="{{ $fieldName }}"
                                            class="form-select mt-1.5 w-full rounded-lg border border-slate-300 bg-white px-3 py-2 hover:border-slate-400 focus:border-primary dark:border-navy-450 dark:bg-navy-700 dark:hover:border-navy-400 dark:focus:border-accent"
                                            {{ $config['required'] ? 'required' : '' }}
                                    >
                                        <option value="">Pilih {{ $config['label'] }}</option>
                                        @foreach($config['options'] as $optValue => $optLabel)
                                            <option value="{{ $optValue }}" {{ old($fieldName, $currentData[$fieldName] ?? '') == $optValue ? 'selected' : '' }}>
                                                {{ $optLabel }}
                                            </option>
                                        @endforeach
                                    </select>
                                @elseif($config['type'] === 'select_or_other')
                                    <div x-data="{
                                        selectedValue: '{{ old($fieldName, $currentData[$fieldName] ?? '') }}',
                                        isOther: {{ (old($fieldName, $currentData[$fieldName] ?? '') && !in_array(old($fieldName, $currentData[$fieldName] ?? ''), array_keys($config['options']))) ? 'true' : 'false' }}
                                    }">
                                        {{-- SELECT --}}
                                        <select
                                                x-show="!isOther"
                                                x-model="selectedValue"
                                                name="{{ $fieldName }}"
                                                class="form-select mt-1.5 w-full rounded-lg border border-slate-300 bg-white px-3 py-2 hover:border-slate-400 focus:border-primary dark:border-navy-450 dark:bg-navy-700 dark:hover:border-navy-400 dark:focus:border-accent"
                                                {{ $config['required'] ? 'required' : '' }}
                                                @change="if (selectedValue === 'Lainnya') { isOther = true; selectedValue = ''; }"
                                        >
                                            <option value="">{{ $config['placeholder'] ?? '-- Pilih --' }}</option>
                                            @foreach($config['options'] as $optValue => $optLabel)
                                                <option value="{{ $optValue }}">{{ $optLabel }}</option>
                                            @endforeach
                                            <option value="Lainnya">Lainnya</option>
                                        </select>

                                        {{-- OTHER INPUT --}}
                                        <div x-show="isOther" x-cloak class="mt-1.5">
                                            <div class="flex gap-2">
                                                <input
                                                    type="text"
                                                    name="{{ $fieldName }}"
                                                    x-model="selectedValue"
                                                    class="form-input flex-1 rounded-lg border border-slate-300 bg-transparent px-3 py-2 placeholder:text-slate-400/70 hover:border-slate-400 focus:border-primary dark:border-navy-450 dark:hover:border-navy-400 dark:focus:border-accent"
                                                    placeholder="{{ $config['other_placeholder'] ?? 'Masukkan nilai lainnya' }}"
                                                    {{ $config['required'] ? 'required' : '' }}
                                                >
                                                <button
                                                    type="button"
                                                    @click="isOther = false; selectedValue = ''"
                                                    class="btn size-10 rounded-lg p-0 hover:bg-slate-300/20"
                                                >
                                                    <i class="fa-solid fa-xmark"></i>
                                                </button>
                                            </div>
                                        </div>
                                    </div>

                                @elseif($config['type'] === 'textarea')
                                        <textarea
                                            name="{{ $fieldName }}"
                                            rows="3"
                                            class="form-textarea mt-1.5 w-full rounded-lg border border-slate-300 bg-transparent px-3 py-2 placeholder:text-slate-400/70 hover:border-slate-400 focus:border-primary dark:border-navy-450 dark:hover:border-navy-400 dark:focus:border-accent"
                                            placeholder="{{ $config['placeholder'] ?? '' }}"
                                            {{--{{ $config['required'] ? 'required' : '' }}--}}
                                        >{{ old($fieldName, $currentData[$fieldName] ?? '') }}</textarea>

                                @elseif($config['type'] === 'date')
                                    <input
                                            type="date"
                                            name="{{ $fieldName }}"
                                            value="{{ old($fieldName, $currentData[$fieldName] ?? '') }}"
                                            class="form-input mt-1.5 w-full rounded-lg border border-slate-300 bg-transparent px-3 py-2 placeholder:text-slate-400/70 hover:border-slate-400 focus:border-primary dark:border-navy-450 dark:hover:border-navy-400 dark:focus:border-accent"
                                            {{--{{ $config['required'] ? 'required' : '' }}--}}
                                    >

                                @elseif($config['type'] === 'time')
                                    <input
                                            type="time"
                                            name="{{ $fieldName }}"
                                            value="{{ old($fieldName, $currentData[$fieldName] ?? '') }}"
                                            class="form-input mt-1.5 w-full rounded-lg border border-slate-300 bg-transparent px-3 py-2 placeholder:text-slate-400/70 hover:border-slate-400 focus:border-primary dark:border-navy-450 dark:hover:border-navy-400 dark:focus:border-accent"
                                            {{--{{ $config['required'] ? 'required' : '' }}--}}
                                    >

                                @else
                                    <input
                                            type="text"
                                            name="{{ $fieldName }}"
                                            value="{{ old($fieldName, $currentData[$fieldName] ?? '') }}"
                                            class="form-input mt-1.5 w-full rounded-lg border border-slate-300 bg-transparent px-3 py-2 placeholder:text-slate-400/70 hover:border-slate-400 focus:border-primary dark:border-navy-450 dark:hover:border-navy-400 dark:focus:border-accent"
                                            placeholder="{{ $config['placeholder'] ?? '' }}"
                                            {{--{{ $config['required'] ? 'required' : '' }}--}}
                                    >
                                @endif

                                @if(isset($config['helper']) && !$errors->has('student_list'))
                                    <span class="text-tiny-plus text-slate-400 dark:text-navy-300 ms-1 mt-1 block">
                                        {{ $config['helper'] }}
                                    </span>
                                @endif

                                @error($fieldName)
                                    <span class="text-tiny-plus text-error ms-1 mt-1 block">{{ $message }}</span>
                                @enderror
                            </label>
                        </div>
                    @endforeach
                </div>

                {{-- Actions --}}
                <div class="mt-6 flex space-x-2">
                    <button
                        type="button"
                        data-close-modal
                        class="btn flex-1 border border-slate-300 font-medium text-slate-800 hover:bg-slate-150 focus:bg-slate-150 active:bg-slate-150/80 dark:border-navy-450 dark:text-navy-50 dark:hover:bg-navy-500"
                    >
                        <i class="fa-solid fa-xmark mr-2"></i>
                        Batal
                    </button>
                    <button
                            type="submit"
                            class="btn flex-1 bg-warning font-medium text-white hover:bg-warning-focus focus:bg-warning-focus active:bg-warning-focus/90"
                    >
                        <i class="fa-solid fa-save mr-2"></i>
                        Simpan Perubahan
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>