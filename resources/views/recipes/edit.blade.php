@extends('layouts.app')

@section('title', 'Edit Recipe')

@section('content')
<div class="modal-overlay fixed inset-0 bg-black/40 z-200 items-center justify-center" id="deleteModal">
    <div class="bg-white rounded-2xl p-7 px-8 max-w-90 w-[90%] shadow-md text-center">
        <h3 class="text-[17px] font-extrabold text-gray-800 mb-2.5">Delete Recipe?</h3>
        <p class="text-sm text-gray-500 mb-6">This action cannot be undone. Your recipe will be permanently removed.</p>
        <div class="flex justify-center gap-3">
            <button onclick="closeModal()"
                    class="px-5 py-2 rounded-[20px] border-[1.5px] border-gray-300 bg-white text-sm font-bold text-gray-600 cursor-pointer hover:bg-gray-50 transition-colors">Cancel</button>
            <button onclick="confirmDelete()"
                    class="px-5 py-2 rounded-[20px] border-none bg-red-500 text-sm font-bold text-white cursor-pointer hover:bg-red-600 transition-colors">Delete</button>
        </div>
    </div>
</div>

<div class="toast fixed bottom-8 left-1/2 bg-gray-800 text-white px-5 py-2.5 rounded-3xl text-sm font-semibold pointer-events-none z-999 whitespace-nowrap" id="toast"></div>

<div class="max-w-260 mx-auto my-9 px-5">
    <div class="flex gap-8 items-start">

        <div class="w-70 shrink-0">

            <div id="photoUpload"
                 onclick="document.getElementById('mainPhotoInput').click()"
                 class="photo-upload bg-white border-2 border-dashed border-gray-200 rounded-2xl flex flex-col items-center justify-center min-h-60 p-8 cursor-pointer transition-all hover:border-orange-600 hover:bg-orange-50 relative overflow-hidden mb-7">
                <input type="file" id="mainPhotoInput" accept="image/*" style="display:none" onchange="handleMainPhoto(this)">
                <div class="photo-placeholder flex flex-col items-center">
                    <div class="w-15 h-15 rounded-full bg-gray-100 flex items-center justify-center mb-3.5 transition-colors photo-icon">
                        <svg class="text-gray-400 transition-colors" width="30" height="30" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M6.827 6.175A2.31 2.31 0 015.186 7.23c-.38.054-.757.112-1.134.175C2.999 7.58 2.25 8.507 2.25 9.574V18a2.25 2.25 0 002.25 2.25h15A2.25 2.25 0 0021.75 18V9.574c0-1.067-.75-1.994-1.802-2.169a47.865 47.865 0 00-1.134-.175 2.31 2.31 0 01-1.64-1.055l-.822-1.316a2.192 2.192 0 00-1.736-1.039 48.774 48.774 0 00-5.232 0 2.192 2.192 0 00-1.736 1.039l-.821 1.316z"/>
                            <path stroke-linecap="round" stroke-linejoin="round" d="M16.5 12.75a4.5 4.5 0 11-9 0 4.5 4.5 0 019 0zM18.75 10.5h.008v.008h-.008V10.5z"/>
                        </svg>
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
                <h2 class="text-[19px] font-extrabold text-orange mb-3.5">Ingredients</h2>
                <div class="flex flex-col gap-1.5" id="ingredientList"></div>
                <div class="flex flex-col gap-2 mt-3.5">
                    <button onclick="addSection()"
                            class="flex items-center gap-1.5 bg-transparent border-[1.5px] border-gray-200 rounded-[20px] px-4 py-1.5 text-sm font-semibold text-gray-500 cursor-pointer transition-all hover:border-gray-400 hover:text-orange w-fit">
                        <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><path d="M12 4v16m8-8H4"/></svg>
                        Add Section
                    </button>
                    <button onclick="addIngredient()"
                            class="flex items-center gap-1.5 bg-transparent border-[1.5px] border-gray-200 rounded-[20px] px-4 py-1.5 text-sm font-semibold text-gray-500 cursor-pointer transition-all hover:border-gray-400 hover:text-orange w-fit">
                        <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><path d="M12 4v16m8-8H4"/></svg>
                        Add Ingredients
                    </button>
                </div>
            </div>
        </div>

        <div class="flex-1">
            <input type="text" id="recipeTitle" placeholder="Title" autofocus
                   class="w-full bg-gray-100 border-none rounded-xl px-5 py-2 text-[28px] font-extrabold text-gray-700 outline-none transition-shadow focus:shadow-[0_0_0_2px_#f4b89a] mb-4 placeholder:text-gray-300">

            <div class="flex items-center gap-2.5 my-4">
                <img class="w-10 h-10 rounded-full object-cover border-2 border-orange-200"
                     src="https://ui-avatars.com/api/?name=Natasya+Salsabila&background=e06c3a&color=fff&bold=true" alt="avatar">
                <div>
                    <p class="text-md font-bold text-gray-800">Natasya Salsabila</p>
                    <p class="text-xs text-gray-400">@natasyasalsabila</p>
                </div>
            </div>

            <div class="flex gap-3 mb-3.5 flex-wrap">
                <div class="flex items-center gap-2 flex-1 min-w-40">
                    <span class="text-sm font-semibold text-gray-500 whitespace-nowrap">Cook time:</span>
                    <input type="text" id="cookTime" placeholder="1 hr 30 mins"
                           class="flex-1 bg-gray-100 border-none rounded-lg px-3 py-2 text-sm text-gray-600 outline-none transition-shadow focus:shadow-[0_0_0_2px_#f4b89a] placeholder:text-gray-400">
                </div>
                <div class="flex items-center gap-2 flex-1 min-w-40">
                    <span class="text-sm font-semibold text-gray-500 whitespace-nowrap">Servings:</span>
                    <input type="text" id="servings" placeholder="1 serving"
                           class="flex-1 bg-gray-100 border-none rounded-lg px-3 py-2 text-sm text-gray-600 outline-none transition-shadow focus:shadow-[0_0_0_2px_#f4b89a] placeholder:text-gray-400">
                </div>
            </div>

            <textarea id="description" rows="2" placeholder="Share a little more about this dish."
                      class="w-full bg-gray-100 border-none rounded-xl px-4 py-3 text-sm text-gray-600 outline-none resize-none transition-shadow focus:shadow-[0_0_0_2px_#f4b89a] mb-5 placeholder:text-gray-400"></textarea>

            <div>
                <h2 class="text-[19px] font-extrabold text-orange mb-3.5">Steps</h2>
                <div class="flex flex-col gap-5" id="stepsList"></div>
                <div class="flex justify-end mt-4">
                    <button onclick="addStep()"
                            class="flex items-center gap-1.5 bg-transparent border-[1.5px] border-gray-200 rounded-[20px] px-4 py-1.5 text-sm font-semibold text-gray-500 cursor-pointer transition-all hover:border-gray-400 hover:text-orange w-fit">
                        <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><path d="M12 4v16m8-8H4"/></svg>
                        Add step
                    </button>
                </div>
            </div>

            <div class="mt-6">
                <h2 class="text-[19px] font-extrabold text-orange mb-3.5">Tips</h2>
                <textarea id="tips" rows="3" placeholder="Share your tips to recreate the dish here."
                          class="w-full bg-gray-100 border-none rounded-xl px-4 py-3 text-sm text-gray-600 outline-none resize-none transition-shadow focus:shadow-[0_0_0_2px_#f4b89a] placeholder:text-gray-400"></textarea>
            </div>

            <div class="flex flex-wrap justify-center gap-3 pt-5 pb-2">
                <button onclick="openModal()"
                        class="inline-flex items-center gap-1.75 px-5 py-2 rounded-3xl text-sm font-bold cursor-pointer transition-all border-2 border-orange-600 text-orange-600 bg-white hover:bg-orange-600 hover:text-white">
                    <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M14.74 9l-.346 9m-4.788 0L9.26 9m9.968-3.21c.342.052.682.107 1.022.166m-1.022-.165L18.16 19.673a2.25 2.25 0 01-2.244 2.077H8.084a2.25 2.25 0 01-2.244-2.077L4.772 5.79m14.456 0a48.108 48.108 0 00-3.478-.397m-12 .562c.34-.059.68-.114 1.022-.165m0 0a48.11 48.11 0 013.478-.397m7.5 0v-.916c0-1.18-.91-2.164-2.09-2.201a51.964 51.964 0 00-3.32 0c-1.18.037-2.09 1.022-2.09 2.201v.916m7.5 0a48.667 48.667 0 00-7.5 0"/>
                    </svg>
                    Delete
                </button>
                <button onclick="saveRecipe('draft')"
                        class="inline-flex items-center gap-1.75 px-3 py-2 rounded-3xl text-sm font-bold cursor-pointer transition-all border-2 border-gray-300 text-gray-500 bg-white hover:border-gray-400 hover:text-gray-600">
                    <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M3 16.5v2.25A2.25 2.25 0 005.25 21h13.5A2.25 2.25 0 0021 18.75V16.5M16.5 12L12 16.5m0 0L7.5 12m4.5 4.5V3"/>
                    </svg>
                    Save and Close
                </button>
                <button onclick="saveRecipe('published')"
                        class="inline-flex items-center gap-1.75 px-5 py-2 rounded-3xl text-sm font-bold cursor-pointer transition-all border-2 border-orange-600 bg-orange-600 text-white shadow-pub hover:bg-[#d6541e] hover:border-orange-hover hover:shadow-pub-hover">
                    <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M4.5 19.5l15-15m0 0H8.25m11.25 0v11.25"/>
                    </svg>
                    Publish
                </button>
            </div>

        </div>
    </div>
</div>

<script>
    let idCounter = 10;
    let ingredients = [
        { id: 1, value: '', isSection: true },
        { id: 2, value: '250g flour', isSection: false },
        { id: 3, value: '100ml water', isSection: false },
    ];
    let steps = [
        { id: 1, title: '', previewUrl: null },
        { id: 2, title: '', previewUrl: null },
    ];

    function renderIngredients() {
        const list = document.getElementById('ingredientList');
        list.innerHTML = '';
        ingredients.forEach((ing, i) => {
            const row = document.createElement('div');
            row.className = 'ingredient-row flex items-center gap-1.5';
            row.setAttribute('data-id', ing.id);
            row.draggable = true;

            row.innerHTML = `
      <button class="cursor-grab text-gray-300 shrink-0 p-1 border-none bg-transparent rounded flex items-center hover:text-gray-400 active:cursor-grabbing transition-colors" type="button" title="Drag">
        <svg width="14" height="14" fill="currentColor" viewBox="0 0 24 24">
          <circle cx="9" cy="5" r="1.5"/><circle cx="15" cy="5" r="1.5"/>
          <circle cx="9" cy="12" r="1.5"/><circle cx="15" cy="12" r="1.5"/>
          <circle cx="9" cy="19" r="1.5"/><circle cx="15" cy="19" r="1.5"/>
        </svg>
      </button>
      <input
        class="flex-1 bg-gray-100 border-none rounded-lg px-3 py-2 text-sm text-gray-600 outline-none transition-shadow focus:shadow-[0_0_0_2px_#f4b89a] placeholder:text-gray-400${ing.isSection ? ' font-bold text-gray-700' : ''}"
        type="text"
        value="${escHtml(ing.value)}"
        placeholder="${ing.isSection ? 'Section name' : 'e.g. 250g flour'}"
        data-index="${i}"
        oninput="ingredients[${i}].value = this.value"
      >
      <div class="relative">
        <button class="bg-transparent border-none cursor-pointer text-gray-400 p-1 rounded flex items-center hover:text-gray-600 transition-colors more-btn" onclick="toggleDropdown(this)" type="button">
          <svg width="15" height="15" fill="currentColor" viewBox="0 0 20 20">
            <path d="M6 10a2 2 0 11-4 0 2 2 0 014 0zM12 10a2 2 0 11-4 0 2 2 0 014 0zM16 12a2 2 0 100-4 2 2 0 000 4z"/>
          </svg>
        </button>
        <div class="more-dropdown absolute right-0 top-[calc(100%+4px)] bg-white border border-gray-100 rounded-xl shadow-md py-1 min-w-[120px] z-50">
          <button class="w-full text-left bg-transparent border-none px-3.5 py-2 text-sm cursor-pointer text-red-500 hover:bg-red-50 transition-colors del-btn" onclick="removeIngredient(${i}); closeAllDropdowns()">Delete</button>
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
          <svg width="14" height="14" fill="currentColor" viewBox="0 0 24 24">
            <circle cx="9" cy="5" r="1.5"/><circle cx="15" cy="5" r="1.5"/>
            <circle cx="9" cy="12" r="1.5"/><circle cx="15" cy="12" r="1.5"/>
            <circle cx="9" cy="19" r="1.5"/><circle cx="15" cy="19" r="1.5"/>
          </svg>
        </button>
      </div>
      <div class="flex-1">
        <div class="flex items-center gap-2 mb-2">
          <input
            type="text"
            class="flex-1 bg-gray-100 border-none rounded-lg px-3 py-2 text-sm text-gray-600 outline-none transition-shadow focus:shadow-[0_0_0_2px_#f4b89a] placeholder:text-gray-400"
            value="${escHtml(step.title)}"
            placeholder="Step ${i + 1}"
            oninput="steps[${i}].title = this.value"
          >
          <div class="relative">
            <button class="bg-transparent border-none cursor-pointer text-gray-400 p-1 rounded flex items-center hover:text-gray-600 transition-colors more-btn" onclick="toggleDropdown(this)" type="button">
              <svg width="15" height="15" fill="currentColor" viewBox="0 0 20 20">
                <path d="M6 10a2 2 0 11-4 0 2 2 0 014 0zM12 10a2 2 0 11-4 0 2 2 0 014 0zM16 12a2 2 0 100-4 2 2 0 000 4z"/>
              </svg>
            </button>
            <div class="more-dropdown absolute right-0 top-[calc(100%+4px)] bg-white border border-gray-100 rounded-xl shadow-md py-1 min-w-[120px] z-50">
              <button class="w-full text-left bg-transparent border-none px-3.5 py-2 text-sm cursor-pointer text-red-500 hover:bg-red-50 transition-colors del-btn" onclick="removeStep(${i}); closeAllDropdowns()">Delete</button>
            </div>
          </div>
        </div>
        <div class="step-photo bg-gray-100 rounded-xl h-[140px] w-60 flex items-center justify-center cursor-pointer transition-colors hover:bg-orange-50 relative overflow-hidden" onclick="triggerStepPhoto(${i})" id="stepPhoto_${step.id}">
          <input type="file" accept="image/*" style="display:none" id="stepInput_${step.id}" onchange="handleStepPhoto(this, ${i})">
          ${step.previewUrl
                ? `<img src="${step.previewUrl}" alt="step photo" class="absolute inset-0 w-full h-full object-cover rounded-xl">
               <div class="step-overlay absolute inset-0 bg-black/30 flex items-center justify-center rounded-xl">
                 <span class="text-white text-[12px] font-bold">Change Photo</span>
               </div>`
                : `<svg class="text-gray-300 hover:text-orange transition-colors" width="32" height="32" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5">
                <path stroke-linecap="round" stroke-linejoin="round" d="M6.827 6.175A2.31 2.31 0 015.186 7.23c-.38.054-.757.112-1.134.175C2.999 7.58 2.25 8.507 2.25 9.574V18a2.25 2.25 0 002.25 2.25h15A2.25 2.25 0 0021.75 18V9.574c0-1.067-.75-1.994-1.802-2.169a47.865 47.865 0 00-1.134-.175 2.31 2.31 0 01-1.64-1.055l-.822-1.316a2.192 2.192 0 00-1.736-1.039 48.774 48.774 0 00-5.232 0 2.192 2.192 0 00-1.736 1.039l-.821 1.316z"/>
                <path stroke-linecap="round" stroke-linejoin="round" d="M16.5 12.75a4.5 4.5 0 11-9 0 4.5 4.5 0 019 0zM18.75 10.5h.008v.008h-.008V10.5z"/>
              </svg>`
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
    function handleStepPhoto(input, index) {
        const file = input.files[0];
        if (file) {
            steps[index].previewUrl = URL.createObjectURL(file);
            renderSteps();
        }
    }

    function addSection() { ingredients.push({ id: idCounter++, value: '', isSection: true }); renderIngredients(); }
    function addIngredient() { ingredients.push({ id: idCounter++, value: '', isSection: false }); renderIngredients(); }
    function removeIngredient(i) { ingredients.splice(i, 1); renderIngredients(); }
    function addStep() { steps.push({ id: idCounter++, title: '', previewUrl: null }); renderSteps(); }
    function removeStep(i) { steps.splice(i, 1); renderSteps(); }

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
        ingredients = [{ id: idCounter++, value: '', isSection: true }];
        steps = [{ id: idCounter++, title: '', previewUrl: null }];
        renderIngredients();
        renderSteps();
        const wrap = document.getElementById('photoUpload');
        document.getElementById('mainPreview').style.display = 'none';
        wrap.classList.remove('has-image');
        wrap.querySelector('.photo-placeholder').style.display = '';
        showToast('Recipe deleted');
    }

    function saveRecipe(status) {
        const title = document.getElementById('recipeTitle').value.trim();
        if (!title) {
            showToast('Please add a title first!');
            document.getElementById('recipeTitle').focus();
            return;
        }
        const msg = status === 'published' ? `"${title}" published! 🎉` : `"${title}" saved as draft`;
        showToast(msg);
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
