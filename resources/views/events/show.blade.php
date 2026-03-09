<!doctype html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width,initial-scale=1">
    <title>{{ $event->name }}</title>
    @if (env('USE_TAILWIND_CDN'))
        <script src="https://cdn.tailwindcss.com"></script>
    @else
        @vite(['resources/css/app.css'])
    @endif
    <style>body{background-color:#f8fafc}</style>
</head>
<body class="min-h-screen py-10">
    <div class="max-w-7xl mx-auto px-4">
        <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
            <div class="md:col-span-2 bg-white p-6 rounded-lg shadow">
                <h1 class="text-2xl font-semibold text-gray-800">{{ $event->name }}</h1>

                <p class="mt-3 text-gray-600">
                    <strong class="text-gray-800">Date:</strong>
                    {{ $event->starts_at->format('j M Y H:i') }}
                    @if($event->ends_at)
                        - {{ $event->ends_at->format('j M Y H:i') }}
                    @endif
                </p>

                <p class="mt-2 text-gray-600"><strong class="text-gray-800">Location:</strong> {{ $event->location ?? '—' }}</p>

                <div class="mt-4 text-gray-700">
                    <p class="font-medium">Event Details</p>
                    <p class="mt-2 text-sm text-gray-600">Register to reserve your seat. After registration you'll get a ticket with a QR code to be scanned at the entrance.</p>
                </div>
            </div>

            <div class="bg-white p-6 rounded-lg shadow">
                <h2 class="text-lg font-medium text-gray-800">Register</h2>

                @if ($errors->any())
                    <div class="mt-3 p-3 rounded border border-red-200 bg-red-50 text-red-800 text-sm">
                        <strong>There were some problems with your input:</strong>
                        <ul class="mt-2 list-disc list-inside">
                            @foreach ($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif

                <form method="POST" action="{{ route('events.register', ['event' => $event->slug]) }}" class="mt-4 space-y-4">
                    @csrf
                    <div>
                        <p class="block text-sm font-medium text-gray-700">Tipe Pendaftar</p>
                        <div class="mt-2 flex items-center gap-6">
                            <label class="inline-flex items-center gap-2 text-sm text-gray-700">
                                <input type="radio" name="registrant_type" value="parent" {{ old('registrant_type', 'parent') === 'parent' ? 'checked' : '' }}>
                                <span>Orang Tua</span>
                            </label>
                            <label class="inline-flex items-center gap-2 text-sm text-gray-700">
                                <input type="radio" name="registrant_type" value="fasil" {{ old('registrant_type') === 'fasil' ? 'checked' : '' }}>
                                <span>Fasil</span>
                            </label>
                        </div>
                    </div>

                    <div id="parent-fields" class="grid grid-cols-3 gap-3">
                        <div id="parent-title-wrap">
                            <label for="parent_title" class="block text-sm font-medium text-gray-700">Ortu</label>
                            <select id="parent_title" name="parent_title" class="mt-1 block w-full px-4 py-3 text-base border border-gray-300 rounded-lg bg-white focus:outline-none focus:ring-2 focus:ring-indigo-200">
                                <option value="">-- Pilih --</option>
                                <option value="Ayah" {{ old('parent_title') == 'Ayah' ? 'selected' : '' }}>Ayah</option>
                                <option value="Bunda" {{ old('parent_title') == 'Bunda' ? 'selected' : '' }}>Bunda</option>
                            </select>
                        </div>
                        <div class="col-span-2">
                            <label id="parent-name-label" for="parent_name" class="block text-sm font-medium text-gray-700">Nama Orang Tua / Wali</label>
                            <input id="parent_name" name="parent_name" type="text" value="{{ old('parent_name') }}" class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-indigo-200">
                        </div>
                    </div>

                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <label for="email" class="block text-sm font-medium text-gray-700">Email</label>
                            <input id="email" name="email" type="email" value="{{ old('email') }}" class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-indigo-200">
                        </div>
                        <div>
                            <label for="phone" class="block text-sm font-medium text-gray-700">Phone</label>
                            <input id="phone" name="phone" type="text" value="{{ old('phone') }}" class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-indigo-200">
                        </div>
                    </div>

                    <div id="children-section">
                        <p class="text-sm font-medium text-gray-700">Anak</p>
                        <div id="children-list" class="space-y-3 mt-2">
                            <div class="child-item bg-gray-50 p-3 rounded-lg border border-gray-200">
                                <div class="grid grid-cols-2 gap-3">
                                    <div>
                                        <label class="block text-sm text-gray-600">Nama Anak</label>
                                        <input name="children[][name]" type="text" class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-lg">
                                    </div>
                                    <div>
                                        <label class="block text-sm text-gray-600">Kelas</label>
                                        @if(isset($classrooms) && count($classrooms))
                                            <select name="children[][class_room]" class="mt-1 block w-full px-4 py-3 text-base border border-gray-300 rounded-lg bg-white">
                                                <option value="">-- Pilih Kelas --</option>
                                                @foreach($classrooms as $cr)
                                                    @php
                                                        $class = $cr->name ?? (is_array($cr) && isset($cr['name']) ? $cr['name'] : $cr);
                                                    @endphp
                                                    <option value="{{ $class }}">{{ $class }}</option>
                                                @endforeach
                                            </select>
                                        @else
                                            <input name="children[][class_room]" type="text" class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-lg">
                                        @endif
                                    </div>
                                </div>
                                <div class="mt-2 text-right">
                                    <button type="button" class="remove-child inline-flex items-center px-2 py-1 text-sm text-red-600 hover:underline">Hapus</button>
                                </div>
                            </div>
                        </div>

                        <div class="mt-3">
                            <button id="add-child" type="button" class="inline-flex items-center px-3 py-2 bg-white border rounded text-sm text-indigo-600 hover:bg-indigo-50">+ Tambah Anak</button>
                        </div>
                    </div>

                    <div>
                        <button type="submit" class="w-full inline-flex items-center justify-center px-4 py-2 bg-indigo-600 text-white rounded-lg hover:bg-indigo-700">Register</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <script>
        (function(){
            const childrenList = document.getElementById('children-list');
            const addBtn = document.getElementById('add-child');
            const parentFields = document.getElementById('parent-fields');
            const parentTitleWrap = document.getElementById('parent-title-wrap');
            const parentTitle = document.getElementById('parent_title');
            const parentNameLabel = document.getElementById('parent-name-label');
            const childrenSection = document.getElementById('children-section');
            const typeRadios = document.querySelectorAll('input[name="registrant_type"]');

            function getRegistrantType() {
                const selected = document.querySelector('input[name="registrant_type"]:checked');
                return selected ? selected.value : 'parent';
            }

            function toggleParentChildFields() {
                const isFasil = getRegistrantType() === 'fasil';

                if (isFasil) {
                    parentTitleWrap.classList.add('hidden');
                    parentTitle.disabled = true;
                    childrenSection.classList.add('hidden');
                    addBtn.disabled = true;
                    addBtn.classList.add('opacity-50', 'cursor-not-allowed');
                    setChildrenFieldsState(true);
                    parentNameLabel.textContent = 'Name';
                } else {
                    parentTitleWrap.classList.remove('hidden');
                    parentTitle.disabled = false;
                    childrenSection.classList.remove('hidden');
                    addBtn.disabled = false;
                    addBtn.classList.remove('opacity-50', 'cursor-not-allowed');
                    setChildrenFieldsState(false);
                    parentNameLabel.textContent = 'Nama Orang Tua / Wali';
                }
            }

            function setChildrenFieldsState(disabled) {
                const items = childrenList.querySelectorAll('.child-item');
                items.forEach(function(item){
                    const nameInput = item.querySelector('input[name*="[name]"]');
                    const classInput = item.querySelector('select[name*="[class_room]"] , input[name*="[class_room]"]');

                    if (nameInput) {
                        nameInput.disabled = disabled;
                        nameInput.required = !disabled;
                    }

                    if (classInput) {
                        classInput.disabled = disabled;
                    }
                });
            }

            function makeChildNode() {
                const wrapper = document.createElement('div');
                wrapper.className = 'child-item bg-gray-50 p-3 rounded-lg border border-gray-200';
                wrapper.innerHTML = `
                    <div class="grid grid-cols-2 gap-3">
                        <div>
                            <label class="block text-sm text-gray-600">Nama Anak</label>
                            <input name="children[][name]" type="text" class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-lg">
                        </div>
                        <div>
                            <label class="block text-sm text-gray-600">Kelas</label>
                            ${generateClassRoomInput()}
                        </div>
                    </div>
                    <div class="mt-2 text-right">
                        <button type="button" class="remove-child inline-flex items-center px-2 py-1 text-sm text-red-600 hover:underline">Hapus</button>
                    </div>
                `;
                return wrapper;
            }

            function generateClassRoomInput(){
                // mirror the server-side classrooms rendering compactly
                const classrooms = `@json($classrooms ?? [])`;
                try {
                    const cls = JSON.parse(classrooms || '[]');
                    if (cls && cls.length) {
                        let options = '<select name="children[][class_room]" class="mt-1 block w-full px-4 py-3 text-base border border-gray-300 rounded-lg bg-white"><option value="">-- Pilih Kelas --</option>';
                        cls.forEach(function(c){
                            const name = (c.name) ? c.name : (typeof c === 'string' ? c : '');
                            options += '<option value="'+(name)+'">'+(name)+'</option>';
                        });
                        options += '</select>';
                        return options;
                    }
                } catch(e) {}
                return '<input name="children[][class_room]" type="text" class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-lg">';
            }

            addBtn.addEventListener('click', function(){
                const node = makeChildNode();
                childrenList.appendChild(node);
                reindexChildren();
                toggleParentChildFields();
            });

            // delegate remove
            childrenList.addEventListener('click', function(e){
                if (e.target && e.target.classList.contains('remove-child')){
                    const item = e.target.closest('.child-item');
                    if (item) {
                        item.remove();
                        reindexChildren();
                    }
                }
            });

            function reindexChildren(){
                const items = childrenList.querySelectorAll('.child-item');
                items.forEach(function(item, idx){
                    const nameInput = item.querySelector('input[name*="children"][type="text"]');
                    const classInput = item.querySelector('select[name*="children"] , input[name*="children"][type="text"]');
                    // find correct inputs by label or placeholder: first text input is name, second is class if it's input
                    const textInputs = item.querySelectorAll('input[type="text"]');
                    let nameEl = null;
                    let classEl = null;
                    if (textInputs.length) nameEl = textInputs[0];
                    // class may be a select or second text input
                    const selectEl = item.querySelector('select');
                    if (selectEl) classEl = selectEl;
                    else if (textInputs.length > 1) classEl = textInputs[1];

                    if (nameEl) nameEl.name = `children[${idx}][name]`;
                    if (classEl) classEl.name = `children[${idx}][class_room]`;
                });
            }

            // ensure initial inputs are indexed correctly
            reindexChildren();

            typeRadios.forEach(function(radio){
                radio.addEventListener('change', toggleParentChildFields);
            });

            toggleParentChildFields();
        })();
    </script>

</body>
</html>
