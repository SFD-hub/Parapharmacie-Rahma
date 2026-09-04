<div class="rounded-2xl border border-gray-100 bg-white p-5">
    <h2 class="text-sm font-semibold text-gray-900">Images du produit</h2>
    <p class="mt-1 text-xs text-gray-500">La première image ajoutée devient automatiquement l'image principale.</p>

    <div class="mt-4">
        <input type="file" wire:model="newImages" multiple accept="image/*" class="block w-full text-sm text-gray-600">
        @error('newImages.*')
            <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
        @enderror

        <p wire:loading wire:target="newImages" class="mt-2 text-xs text-gray-400">Envoi en cours...</p>
    </div>

    <div class="mt-6 grid grid-cols-2 gap-4 sm:grid-cols-3 md:grid-cols-4">
        @forelse ($images as $image)
            <div wire:key="image-{{ $image->id }}" class="relative overflow-hidden rounded-xl border border-gray-100">
                <img src="{{ $image->path }}" alt="" class="aspect-square w-full object-cover">

                @if ($image->is_primary)
                    <span class="absolute left-2 top-2 rounded-full bg-primary-600 px-2 py-0.5 text-[10px] font-semibold text-white">
                        Principale
                    </span>
                @endif

                <div class="flex items-center justify-between gap-1 bg-white/95 p-1.5">
                    <button type="button" wire:click="moveUp({{ $image->id }})" class="p-1 text-gray-400 hover:text-gray-700" aria-label="Déplacer vers le haut">
                        <x-icon name="chevron-up" class="h-4 w-4" />
                    </button>
                    <button type="button" wire:click="moveDown({{ $image->id }})" class="p-1 text-gray-400 hover:text-gray-700" aria-label="Déplacer vers le bas">
                        <x-icon name="chevron-down" class="h-4 w-4" />
                    </button>
                    @unless ($image->is_primary)
                        <button type="button" wire:click="setPrimary({{ $image->id }})" class="p-1 text-gray-400 hover:text-primary-600" aria-label="Définir comme principale">
                            <x-icon name="star-outline" class="h-4 w-4" />
                        </button>
                    @endunless
                    <button
                        type="button"
                        wire:click="deleteImage({{ $image->id }})"
                        wire:confirm="Supprimer cette image ?"
                        class="p-1 text-gray-400 hover:text-red-600"
                        aria-label="Supprimer"
                    >
                        <x-icon name="trash" class="h-4 w-4" />
                    </button>
                </div>
            </div>
        @empty
            <div class="col-span-full">
                <x-empty-state title="Aucune image" description="Ajoutez au moins une image pour ce produit." icon="photo" />
            </div>
        @endforelse
    </div>
</div>
