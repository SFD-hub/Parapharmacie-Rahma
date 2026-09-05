<x-admin-layout title="Nouveau pack" :back-url="route('admin.marketing.packs.index')">
    <form method="POST" action="{{ route('admin.marketing.packs.store') }}" enctype="multipart/form-data">
        @csrf
        <x-admin.marketing.packs.form :products="$products" :badges="$badges" />
    </form>
</x-admin-layout>
