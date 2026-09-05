<x-admin-layout :title="'Modifier '.$product->name" :back-url="route('admin.products.index')">
    <div class="space-y-6">
        <form method="POST" action="{{ route('admin.products.update', $product) }}">
            @csrf
            @method('PUT')
            <x-admin.products.form :product="$product" :categories="$categories" :brands="$brands" />
        </form>

        <livewire:admin.product-image-manager :product="$product" :key="'images-'.$product->id" />

        <div class="rounded-2xl border border-gray-100 bg-white p-5">
            <div class="flex items-center justify-between">
                <div>
                    <h2 class="text-sm font-semibold text-gray-900">Produits complémentaires</h2>
                    <p class="text-sm text-gray-500">Suggestions de vente additionnelle affichées sur la fiche produit.</p>
                </div>
                <a
                    href="{{ route('admin.products.complements.index', $product) }}"
                    class="inline-flex items-center gap-2 rounded-full border border-primary-600 px-4 py-2 text-sm font-semibold text-primary-600 hover:bg-primary-50"
                >
                    Gérer
                </a>
            </div>
        </div>
    </div>
</x-admin-layout>
