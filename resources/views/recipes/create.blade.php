@extends('layouts.app')

@section('title', 'Create Recipe')

@section('content')
    <div class="toast fixed bottom-8 left-1/2 bg-gray-800 text-white px-5 py-2.5 rounded-3xl text-sm font-semibold pointer-events-none z-999 whitespace-nowrap" id="toast"></div>

    <div class="max-w-6xl mx-auto my-9 pb-5 pt-8">
        <form method="POST" action="{{ route('recipes.store') }}" enctype="multipart/form-data" id="createForm">
            @csrf
            <input type="hidden" name="ingredients" id="ingredientsInput">
            <input type="hidden" name="steps" id="stepsInput">
            <input type="hidden" name="status" id="statusInput">
            <div class="flex gap-8 items-start">
                <div class="w-sm shrink-0">
                    <div id="photoUpload"
                         onclick="document.getElementById('mainPhotoInput').click()"
                         class="photo-upload bg-white border-2 border-dashed border-gray-200 rounded-2xl flex flex-col items-center justify-center min-h-72 p-8 cursor-pointer transition-all hover:border-orange-600 hover:bg-orange-50 relative overflow-hidden mb-7">
                        <input type="file" name="image" id="mainPhotoInput" accept="image/*" style="display:none" onchange="handleMainPhoto(this)">
                        <div class="photo-placeholder flex flex-col items-center">
                            <div class="w-15 h-15 rounded-full bg-gray-100 flex items-center justify-center mb-3.5 transition-colors photo-icon">
                                <x-icons.camera class="w-8 h-8 text-gray-300"/>
                            </div>
                            <p class="text-md font-bold text-orange">Upload Recipe Photo</p>
                            <p class="text-xs text-gray-400 mt-1">Show others your finished dish</p>
                        </div>
                        <img class="preview w-full h-full object-cover rounded-xl absolute inset-0" id="mainPreview" style="display:none" alt="preview">
                        <div class="overlay absolute inset-0 bg-black/35 flex items-center justify-center rounded-xl">
                            <span class="text-white text-sm font-bold">Change Photo</span>
                        </div>
                    </div>

                    <div>
                        <h2 class="text-2xl font-extrabold text-orange mb-3.5">Ingredients</h2>
                        <div class="flex flex-col gap-1.5" id="ingredientList"></div>
                        <div class="flex flex-col gap-2 mt-6 items-center">
                            <button type="button" onclick="addSection()"
                                    class="flex items-center gap-1.5 bg-transparent border-[1.5px] border-gray-200 rounded-[20px] px-4 py-1.5 text-sm font-semibold text-gray-500 cursor-pointer transition-all hover:border-gray-400 hover:text-orange w-fit">
                                <x-icons.plus class="w-3.5 h-3.5"/>
                                Add Section
                            </button>
                            <button type="button" onclick="addIngredient()"
                                    class="flex items-center gap-1.5 bg-transparent border-[1.5px] border-gray-200 rounded-[20px] px-4 py-1.5 text-sm font-semibold text-gray-500 cursor-pointer transition-all hover:border-gray-400 hover:text-orange w-fit">
                                <x-icons.plus class="w-3.5 h-3.5"/>
                                Add Ingredients
                            </button>
                        </div>
                    </div>
                </div>

                <div class="flex-1">
                    <input type="text" id="recipeTitle" name="title" placeholder="Title" autofocus required
                           class="w-full bg-gray-100 border-none rounded-xl px-5 py-2 text-[28px] font-extrabold text-gray-700 outline-none transition-shadow focus:shadow-[0_0_0_2px_#f4b89a] mb-4 placeholder:text-gray-300">

                    <div class="flex items-center gap-2.5 my-4">
                        <img class="w-10 h-10 rounded-full object-cover border border-white"
                             src="{{ Storage::url(auth()->user()->avatar) }}" alt="{{ auth()->user()->name }}'s avatar">
                        <div>
                            <p class="text-md font-bold text-gray-800">{{ auth()->user()->name }}</p>
                            <p class="text-xs text-gray-400">{{ "@" . auth()->user()->username }}</p>
                        </div>
                    </div>

                    <div class="flex gap-3 mb-3.5 flex-wrap">
                        <div class="flex items-center gap-2 flex-1 min-w-40">
                            <span class="text-sm font-semibold text-gray-500 whitespace-nowrap">Cook time:</span>
                            <input type="text" id="cookTime" name="cook_time" placeholder="1 hr 30 mins" required
                                   class="flex-1 bg-gray-100 border-none rounded-lg px-3 py-2 text-sm text-gray-600 outline-none transition-shadow focus:shadow-[0_0_0_2px_#f4b89a] placeholder:text-gray-400">
                        </div>

                        <div class="flex items-center gap-2 flex-1 min-w-40">
                            <span class="text-sm font-semibold text-gray-500 whitespace-nowrap">Servings:</span>
                            <input type="text" id="servings" placeholder="1 serving" name="servings" required
                                   class="flex-1 bg-gray-100 border-none rounded-lg px-3 py-2 text-sm text-gray-600 outline-none transition-shadow focus:shadow-[0_0_0_2px_#f4b89a] placeholder:text-gray-400">
                        </div>
                    </div>
                    <div class="mb-4 flex items-center gap-4">
                        <div>
                            <label class="text-sm font-semibold text-gray-500 mb-1 block">Category</label>
                        </div>

                        <select
                            name="category_id"
                            required
                            class="w-full bg-gray-100 border-none rounded-xl px-4 py-2 text-sm text-gray-700 outline-none transition-shadow focus:shadow-[0_0_0_2px_#f4b89a]"
                        >
                            <option value="" disabled selected>Select category</option>

                            @foreach ($categories as $category)
                                <option value="{{ $category->id }}">
                                    {{ $category->name }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <textarea id="description" rows="2" placeholder="Share a little more about this dish." name="description"
                              class="w-full bg-gray-100 border-none rounded-xl px-4 py-3 text-sm text-gray-600 outline-none resize-none transition-shadow focus:shadow-[0_0_0_2px_#f4b89a] mb-5 placeholder:text-gray-400"></textarea>

                    <div>
                        <h2 class="text-2xl font-extrabold text-orange mb-3.5">Steps</h2>
                        <div class="flex flex-col gap-5" id="stepsList"></div>
                        <div class="flex justify-end mt-4">
                            <button type="button" onclick="addStep()"
                                    class="flex items-center gap-1.5 bg-transparent border-[1.5px] border-gray-200 rounded-[20px] px-4 py-1.5 text-sm font-semibold text-gray-500 cursor-pointer transition-all hover:border-gray-400 hover:text-orange w-fit">
                                <x-icons.plus class="w-3.5 h-3.5"/>
                                Add step
                            </button>
                        </div>
                    </div>

                    <div class="mt-6">
                        <h2 class="text-2xl font-extrabold text-orange mb-3.5">Tips</h2>
                        <textarea id="tips" rows="3" placeholder="Share your tips to recreate the dish here." name="tips"
                                  class="w-full bg-gray-100 border-none rounded-xl px-4 py-3 text-sm text-gray-600 outline-none resize-none transition-shadow focus:shadow-[0_0_0_2px_#f4b89a] placeholder:text-gray-400"></textarea>
                    </div>

                    <div class="flex flex-wrap justify-center gap-3 pt-5 pb-2">
                        <button type="button" onclick="saveRecipe('draft')"
                                class="inline-flex items-center gap-1.75 px-3 py-2 rounded-3xl text-sm font-bold cursor-pointer transition-all border-2 border-gray-300 text-gray-500 bg-white hover:border-gray-400 hover:text-gray-600">
                            <x-icons.save class="w-4 h-4"/>
                            Save and Close
                        </button>
                        <button type="button" onclick="saveRecipe('published')"
                                class="inline-flex items-center gap-1.75 px-5 py-2 rounded-3xl text-sm font-bold cursor-pointer transition-all border-2 border-orange-600 bg-orange-600 text-white shadow-pub hover:bg-[#d6541e] hover:border-orange-hover hover:shadow-pub-hover">
                            <x-icons.arrow-up-right class="w-4 h-4"/>
                            Publish
                        </button>
                    </div>

                </div>
            </div>
        </form>
    </div>

    <script>
        let ingredientCounter = 1;
        let stepCounter = 1;

        let ingredients = [
            { id: ingredientCounter++, name: '', amount: '', isSection: true },
            { id: ingredientCounter++, name: '', amount: '', isSection: false },
        ];

        let steps = [
            { id: stepCounter++, title: '', previewUrl: null },
        ];

        function renderIngredients() {
            const list = document.getElementById('ingredientList');
            list.innerHTML = '';
            ingredients.forEach((ing, i) => {
                const row = document.createElement('div');
                row.className = 'ingredient-row grid grid-cols-[auto_100px_1fr_auto] items-center gap-1.5 w-full';
                row.setAttribute('data-id', ing.id);
                row.draggable = true;

                row.innerHTML = `
                <button
                    type="button"
                    class="cursor-grab text-gray-300 shrink-0 p-1 bg-transparent rounded flex items-center hover:text-gray-400 active:cursor-grabbing transition-colors"
                    title="Drag"
                >
                    <x-icons.drag class="w-4 h-4"/>
                </button>

                ${ing.isSection ? `
                        <input
                            class="flex-1 bg-gray-100 col-span-2 border-none rounded-lg px-3 py-2 text-sm font-bold text-gray-700 outline-none transition-shadow focus:shadow-[0_0_0_2px_#f4b89a] placeholder:text-gray-400"
                            style="grid-column: 2 / 4;"
                            type="text"
                            value="${escHtml(ing.name)}"
                            placeholder="Section name"
                            oninput="updateIngredientName(${ing.id}, this.value)"
                        >
                        `
                        : `
                        <input
                            class="min-w-0 bg-gray-100 border-none rounded-lg px-3 py-2 text-sm text-gray-600 outline-none transition-shadow focus:shadow-[0_0_0_2px_#f4b89a] placeholder:text-gray-400"
                            type="text"
                            value="${escHtml(ing.amount)}"
                            placeholder="250g"
                            oninput="updateIngredientAmount(${ing.id}, this.value)"
                        >

                        <input
                            class="min-w-0 bg-gray-100 border-none rounded-lg px-3 py-2 text-sm text-gray-600 outline-none transition-shadow focus:shadow-[0_0_0_2px_#f4b89a] placeholder:text-gray-400"
                            type="text"
                            value="${escHtml(ing.name)}"
                            placeholder="Flour"
                            oninput="updateIngredientName(${ing.id}, this.value)"
                        >
                        `
                }

                <div class="relative">
                    <button
                        type="button"
                        class="bg-transparent border-none cursor-pointer text-gray-400 p-1 rounded flex items-center hover:text-gray-600 transition-colors more-btn"
                        onclick="toggleDropdown(this)"
                    >
                        <x-icons.three-dot class="w-3.5 h-3.5"/>
                    </button>

                    <div class="more-dropdown absolute right-0 top-[calc(100%+4px)] bg-white border border-gray-100 rounded-xl shadow-md py-1 min-w-[120px] z-50">
                        <button
                            type="button"
                            class="w-full text-left bg-transparent border-none px-3.5 py-2 text-sm cursor-pointer text-red-500 hover:bg-red-50 transition-colors"
                            onclick="removeIngredient(${i}); closeAllDropdowns()"
                        >
                            Delete
                        </button>
                    </div>
                </div>
                `;

                row.addEventListener('dragstart', (e) => {
                    row.classList.add('dragging');
                    e.dataTransfer.setData('text/plain', i);
                });
                row.addEventListener('dragend', () => row.classList.remove('dragging'));
                row.addEventListener('dragover', (e) => { e.preventDefault(); row.classList.add('drag-over'); });
                row.addEventListener('dragleave', () => row.classList.remove('drag-over'));
                row.addEventListener('drop', (e) => {
                    e.preventDefault();
                    row.classList.remove('drag-over');
                    const fromIdx = parseInt(e.dataTransfer.getData('text/plain'));
                    const toIdx = i;
                    if (fromIdx !== toIdx) {
                        const moved = ingredients.splice(fromIdx, 1)[0];
                        ingredients.splice(toIdx, 0, moved);
                        renderIngredients();
                    }
                });

                list.appendChild(row);
            });
        }

        function updateIngredient(id, value) {
            const item = ingredients.find(i => i.id === id);
            if (item) item.value = value;
        }

        function updateStep(id, value) {
            const item = steps.find(s => s.id === id);
            if (item) item.title = value;
        }

        function renderSteps() {
            const list = document.getElementById('stepsList');
            list.innerHTML = '';
            steps.forEach((step, i) => {
                const row = document.createElement('div');
                row.className = 'flex gap-3';
                row.innerHTML = `
      <div class="flex flex-col items-center gap-1.5 shrink-0 pt-2">
        <div class="w-8 h-8 rounded-full bg-orange flex items-center justify-center text-white text-sm font-extrabold">${i + 1}</div>
        <button type="button" class="cursor-grab text-gray-300 p-1 border-none bg-transparent rounded flex items-center hover:text-gray-400 transition-colors">
            <x-icons.drag class="w-4 h-4"/>
        </button>
      </div>
      <div class="flex-1">
        <div class="flex items-center gap-2 mb-2">
          <input
            type="text"
            class="flex-1 bg-gray-100 border-none rounded-lg px-3 py-2 text-sm text-gray-600 outline-none transition-shadow focus:shadow-[0_0_0_2px_#f4b89a] placeholder:text-gray-400"
            value="${escHtml(step.title)}"
            placeholder="Step ${i + 1}"
            oninput="updateStep(${step.id}, this.value)"
          >
          <div class="relative">
            <button type="button" class="bg-transparent border-none cursor-pointer text-gray-400 p-1 rounded flex items-center hover:text-gray-600 transition-colors more-btn" onclick="toggleDropdown(this)" type="button">
              <x-icons.three-dot class="w-3.5 h-3.5"/>
            </button>
            <div class="more-dropdown absolute right-0 top-[calc(100%+4px)] bg-white border border-gray-100 rounded-xl shadow-md py-1 min-w-[120px] z-50">
              <button type="button" type="button" class="w-full text-left bg-transparent border-none px-3.5 py-2 text-sm cursor-pointer text-red-500 hover:bg-red-50 transition-colors del-btn" onclick="removeStep(${i}); closeAllDropdowns()">Delete</button>
            </div>
          </div>
        </div>
        <div class="step-photo bg-gray-100 rounded-xl h-48 w-80 flex items-center justify-center cursor-pointer transition-colors hover:bg-orange-50 relative overflow-hidden" onclick="triggerStepPhoto(${i})" id="stepPhoto_${step.id}">
          <input type="file" name="step_images[${step.id}]" accept="image/*" style="display:none" id="stepInput_${step.id}" onchange="handleStepPhoto(this, ${step.id})">
          ${step.previewUrl
                    ? `<img src="${step.previewUrl}" alt="step photo" class="absolute inset-0 w-full h-full object-cover rounded-xl">
               <div class="step-overlay absolute inset-0 bg-black/30 flex items-center justify-center rounded-xl">
                 <span class="text-white text-[12px] font-bold">Change Photo</span>
               </div>`
                    : `<x-icons.camera class="w-8 h-8 text-gray-300"/>`
                }
        </div>
      </div>
    `;
                list.appendChild(row);
            });
        }

        function triggerStepPhoto(index) {
            document.getElementById('stepInput_' + steps[index].id).click();
        }
        function handleStepPhoto(input, stepId) {
            const file = input.files[0];
            if (!file) return;

            const step = steps.find(s => s.id === stepId);
            if (!step) return;

            const url = URL.createObjectURL(file);

            step.previewUrl = url;
            step.file = file;

            const container = document.getElementById(`stepPhoto_${step.id}`);

            let img = container.querySelector('img');
            if (!img) {
                img = document.createElement('img');
                img.className = 'absolute inset-0 w-full h-full object-cover rounded-xl';
                container.appendChild(img);
            }
            img.src = url;

            let overlay = container.querySelector('.step-overlay');
            if (!overlay) {
                overlay = document.createElement('div');
                overlay.className = 'step-overlay absolute inset-0 bg-black/30 flex items-center justify-center rounded-xl';
                overlay.innerHTML = `<span class="text-white text-[12px] font-bold">Change Photo</span>`;
                container.appendChild(overlay);
            }
        }

        function addSection() {
            if (ingredients.length > 0) {
                const last = ingredients[ingredients.length - 1];

                if (last.isSection) {
                    showToast('Add ingredient for the current section before adding a new one');
                    return;
                }
            }

            ingredients.push({
                id: ingredientCounter++,
                value: '',
                isSection: true
            });

            renderIngredients();
        }
        function addIngredient() {
            ingredients.push({
                id: ingredientCounter++,
                name: '',
                amount: '',
                isSection: false
            });
            renderIngredients();
        }
        function removeIngredient(i) { ingredients.splice(i, 1); renderIngredients(); }
        function addStep() { steps.push({ id: stepCounter++, title: '', previewUrl: null }); renderSteps(); }
        function removeStep(i) { steps.splice(i, 1); renderSteps(); }

        function updateIngredientName(id, value) {
            const item = ingredients.find(i => i.id === id);
            if (item) item.name = value;
        }

        function updateIngredientAmount(id, value) {
            const item = ingredients.find(i => i.id === id);
            if (item) item.amount = value;
        }

        function handleMainPhoto(input) {
            const file = input.files[0];
            if (!file) return;
            const url = URL.createObjectURL(file);
            const wrap = document.getElementById('photoUpload');
            const preview = document.getElementById('mainPreview');
            preview.src = url;
            preview.style.display = 'block';
            wrap.classList.add('has-image');
            wrap.querySelector('.photo-placeholder').style.display = 'none';
        }

        function toggleDropdown(btn) {
            const dd = btn.nextElementSibling;
            const isOpen = dd.classList.contains('open');
            closeAllDropdowns();
            if (!isOpen) dd.classList.add('open');
        }
        function closeAllDropdowns() {
            document.querySelectorAll('.more-dropdown.open').forEach(d => d.classList.remove('open'));
        }
        document.addEventListener('click', (e) => {
            if (!e.target.closest('.relative')) closeAllDropdowns();
        });

        function openModal() { document.getElementById('deleteModal').classList.add('open'); }
        function closeModal() { document.getElementById('deleteModal').classList.remove('open'); }
        function confirmDelete() {
            closeModal();
            document.getElementById('recipeTitle').value = '';
            document.getElementById('cookTime').value = '';
            document.getElementById('servings').value = '';
            document.getElementById('description').value = '';
            document.getElementById('tips').value = '';
            ingredients = [{ id: ingredientCounter++, value: '', isSection: true }];
            steps = [{ id: stepCounter++, title: '', previewUrl: null }];
            renderIngredients();
            renderSteps();
            const wrap = document.getElementById('photoUpload');
            document.getElementById('mainPreview').style.display = 'none';
            wrap.classList.remove('has-image');
            wrap.querySelector('.photo-placeholder').style.display = '';
            showToast('Recipe deleted');
        }

        function validateIngredients() {
            if (!ingredients.length) {
                showToast('Add at least 1 ingredient');
                return false;
            }

            for (let i = 0; i < ingredients.length; i++) {
                const current = ingredients[i];
                const next = ingredients[i + 1];

                if (current.isSection && !current.name.trim()) {
                    showToast('Section name cannot be empty');
                    return false;
                }

                if (!current.isSection && (!current.name.trim() || !current.amount.trim())) {
                    showToast('Ingredient must have amount and name');
                    return false;
                }

                if (current.isSection && (!next || next.isSection)) {
                    showToast('Each section must have at least 1 ingredient');
                    return false;
                }
            }

            return true;
        }

        function validateSteps() {
            if (!steps.length) {
                showToast('Tambahkan minimal 1 step');
                return false;
            }

            for (let i = 0; i < steps.length; i++) {
                if (!steps[i].title.trim()) {
                    showToast(`Step ${i + 1} cannot be empty`);
                    return false;
                }
            }

            return true;
        }

        function validateBasic() {
            const title = document.getElementById('recipeTitle').value.trim();
            const cookTime = document.getElementById('cookTime').value.trim();
            const servings = document.getElementById('servings').value.trim();

            if (!title) {
                showToast('Title is required');
                return false;
            }

            if (!cookTime) {
                showToast('Cook time is required');
                return false;
            }

            if (!servings) {
                showToast('Servings is required');
                return false;
            }

            return true;
        }

        function validateImage() {
            const input = document.getElementById('mainPhotoInput');

            if (!input.files.length) {
                showToast('Upload recipe picture first');
                return false;
            }

            return true;
        }

        function saveRecipe(status) {
            if (!validateBasic()) return;
            if (!validateIngredients()) return;
            if (!validateSteps()) return;
            if (!validateImage()) return;

            const form = document.getElementById('createForm');
            const formData = new FormData(form);

            formData.append('status', status);
            formData.append('ingredients', JSON.stringify(ingredients));
            formData.append('steps', JSON.stringify(steps));

            steps.forEach((step, i) => {
                if (step.file) {
                    formData.append(`step_images[${step.id}]`, step.file);
                }
            });

            fetch(form.action, {
                method: 'POST',
                body: formData
            }).then(() => {
                location.reload();
            });
        }

        function showToast(msg) {
            const t = document.getElementById('toast');
            t.textContent = msg;
            t.classList.add('show');
            setTimeout(() => t.classList.remove('show'), 2800);
        }

        function escHtml(str) {
            return (str || '').replace(/&/g,'&amp;').replace(/</g,'&lt;').replace(/>/g,'&gt;').replace(/"/g,'&quot;');
        }

        renderIngredients();
        renderSteps();
    </script>
@endsection
