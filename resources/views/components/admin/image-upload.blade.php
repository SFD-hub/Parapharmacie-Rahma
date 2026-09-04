@props(['name', 'label' => 'Ajouter une image', 'current' => null])

<div
    x-data="{
        preview: @js($current),
        dragging: false,
        handleDrop(event) {
            const file = event.dataTransfer.files[0];
            if (! file) return;
            const transfer = new DataTransfer();
            transfer.items.add(file);
            $refs.input.files = transfer.files;
            this.preview = URL.createObjectURL(file);
        },
    }"
>
    <x-input-label :for="$name" :value="$label" />

    <label
        :for="'{{ $name }}'"
        x-show="! preview"
        x-cloak
        @dragover.prevent="dragging = true"
        @dragleave.prevent="dragging = false"
        @drop.prevent="dragging = false; handleDrop($event)"
        class="mt-2 flex cursor-pointer flex-col items-center justify-center gap-2 rounded-2xl border-2 border-dashed p-6 text-center transition"
        :class="dragging ? 'border-primary-400 bg-primary-50/60' : 'border-gray-200 bg-gray-50 hover:border-primary-300 hover:bg-primary-50/40'"
    >
        <span class="flex h-10 w-10 items-center justify-center rounded-full bg-white text-gray-400 shadow-sm">
            <x-icon name="photo" class="h-5 w-5" />
        </span>
        <span class="text-sm text-gray-500">Cliquez ou glissez une image ici</span>
    </label>

    <div x-show="preview" x-cloak class="relative mt-2 inline-block">
        <img :src="preview" alt="" class="h-32 w-32 rounded-2xl border border-gray-100 object-cover">
        <label
            :for="'{{ $name }}'"
            class="absolute -bottom-2 -right-2 flex h-8 w-8 cursor-pointer items-center justify-center rounded-full bg-white text-gray-600 shadow-md transition hover:text-primary-600"
            aria-label="Changer l'image"
        >
            <x-icon name="pencil" class="h-4 w-4" />
        </label>
    </div>

    <input
        id="{{ $name }}"
        x-ref="input"
        type="file"
        name="{{ $name }}"
        accept="image/*"
        class="sr-only"
        @change="preview = $event.target.files[0] ? URL.createObjectURL($event.target.files[0]) : preview"
    >

    <x-input-error :messages="$errors->get($name)" class="mt-2" />
</div>
