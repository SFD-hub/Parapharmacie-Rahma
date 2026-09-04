<x-admin-layout :title="'Modifier '.$pack->name">
    <form method="POST" action="{{ route('admin.marketing.packs.update', $pack) }}" enctype="multipart/form-data">
        @csrf
        @method('PUT')
        <x-admin.marketing.packs.form :pack="$pack" :products="$products" :badges="$badges" />
    </form>
</x-admin-layout>
