@props([
    'name' => 'student_list',
    'required' => false,
    'value' => [],
    'studyPrograms' => [],
    'placeholder' => 'Klik tombol tambah data...',
    'helper' => '',
    'min_students' => 1,
    'max_students' => 50
])

<div x-data="studentListManager{{ $name }}()" x-init="init()" class="w-full">
    <div class="flex items-center justify-between mb-2">
        <div>
            <span class="font-medium text-slate-600 dark:text-navy-100">
                Daftar Mahasiswa @if($required)<span class="text-red-500">*</span>@endif
            </span>
            @if($helper)
                <p class="text-tiny-plus text-slate-500 dark:text-navy-300 mt-1">
                    {{ $helper }}
                </p>
            @endif
        </div>
        <button type="button"
                @click.stop.prevent="addStudent()"
{{--                @click="addStudent"--}}
                class="btn h-6 rounded bg-primary px-2.5 text-tiny-plus font-medium text-white hover:bg-primary-focus focus:bg-primary-focus active:bg-primary-focus/90 dark:bg-accent">
            <i class="fas fa-plus mr-1"></i> Tambah Data
        </button>
    </div>

    <div class="is-scrollbar-hidden min-w-full overflow-x-auto rounded-lg border border-slate-200 dark:border-navy-500">
        <table class="w-full text-left">
            <thead>
            <tr class="bg-slate-50 dark:bg-navy-800">
                <th class="w-8 border-r border-slate-200 px-2 py-2 text-tiny font-medium uppercase text-slate-600 dark:border-navy-500 dark:text-navy-100 text-center">
                    #
                </th>
                <th class="border-r border-slate-200 px-3 py-2 text-tiny font-medium uppercase text-slate-600 dark:border-navy-500 dark:text-navy-100">
                    Nama Mahasiswa
                </th>
                <th class="w-40 border-r border-slate-200 px-3 py-2 text-tiny font-medium uppercase text-slate-600 dark:border-navy-500 dark:text-navy-100">
                    NIM
                </th>
                <th class="w-60 border-r border-slate-200 px-3 py-2 text-tiny font-medium uppercase text-slate-600 dark:border-navy-500 dark:text-navy-100">
                    Program Studi
                </th>
                <th class="w-10 border-none px-2 py-2 text-center text-tiny font-medium uppercase text-slate-600 dark:text-navy-100">
                    <i class="fas fa-trash-alt"></i>
                </th>
            </tr>
            </thead>
            <tbody class="bg-white dark:bg-navy-900">
            <template x-for="student in students" :key="student._id">
                <tr class="border-t border-slate-200 dark:border-navy-500 hover:bg-slate-50/50 dark:hover:bg-navy-800">
                    <td class="border-r border-slate-200 px-2 py-2 text-center text-xs text-slate-500 dark:border-navy-500" x-text="students.indexOf(student) + 1"></td>

                    <td class="border-r border-slate-200 px-2 py-2 dark:border-navy-500">
                        <input x-model="student.name"
                               @click.stop
                               class="form-input w-full rounded border border-slate-200 bg-white px-2 py-1.5 text-xs focus:border-primary dark:border-navy-450 dark:bg-navy-900"
                               placeholder="Nama..." type="text" required />
                    </td>

                    <td class="border-r border-slate-200 px-2 py-2 dark:border-navy-500">
                        <input x-model="student.nim"
                               @click.stop
                               class="form-input w-full rounded border border-slate-200 bg-white px-2 py-1.5 text-xs focus:border-primary dark:border-navy-450 dark:bg-navy-900"
                               placeholder="NIM..." type="text" required />
                    </td>

                    <td class="border-r border-slate-200 px-2 py-2 dark:border-navy-500">
                        <select x-model="student.program"
                                @click.stop
                                class="form-select w-full rounded border border-slate-200 bg-white px-2 py-1.5 text-xs focus:border-primary dark:border-navy-450 dark:bg-navy-900"
                                required>
                            <option value="">Pilih Program Studi</option>
                            <template x-for="prodi in studyPrograms" :key="prodi">
                                <option :value="prodi" x-text="prodi" :selected="student.program === prodi"></option>
                            </template>
                        </select>
                    </td>

                    <td class="px-2 py-2 text-center">
                        <button type="button"
                                @click.stop.prevent="removeStudent(student._id)"
{{--                                @click="removeStudent(index)"--}}
                                class="btn h-6 w-6 p-0 text-error hover:bg-error/10">
                            <i class="fa fa-times text-[10px]"></i>
                        </button>
                    </td>
                </tr>
            </template>

            <tr x-show="students.length === 0">
                <td colspan="5" class="px-3 py-2 text-center text-tiny-plus italic text-slate-400">
                    {{ $placeholder ?? 'Belum ada data mahasiswa. Klik tombol "Tambah Data" untuk menambahkan.' }}
                </td>
            </tr>
            </tbody>
        </table>
    </div>

    <input type="hidden"
           :name="'{{ $name }}'"
           :value="JSON.stringify(students.map(s => ({name: s.name, nim: s.nim, program: s.program})))">


    @error($name)
        <span class="text-tiny-plus text-error mt-1 ms-1 block">
            {{ $message }}
        </span>
    @enderror

    @if(!$errors->has($name))
        <span class="text-tiny-plus text-slate-500 dark:text-navy-300 mt-1 ms-1 block">
            Total: <span x-text="students.length"></span> mahasiswa (Minimal 1 Mahasiswa, Maksimal 50 Mahasiswa)
        </span>
    @endif
</div>

<script>
    (function() {
        const componentId = 'studentListManager{{ $name }}';

        if (typeof window[componentId] === 'function') {
            return;
        }

        window[componentId] = function() {
            return {
                students: [],
                studyPrograms: @json($studyPrograms),
                nextId: Date.now(),

                init() {
                    let initialValue = null;

                    const oldInput = @json(old($name));
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

                    if (!initialValue && @json($value)) {
                        initialValue = @json($value);
                    }

                    if (Array.isArray(initialValue) && initialValue.length > 0) {
                        this.students = initialValue.map(student => ({
                            _id: this.nextId++,
                            name: student.name || '',
                            nim: student.nim || '',
                            program: (student.program || '').trim()
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