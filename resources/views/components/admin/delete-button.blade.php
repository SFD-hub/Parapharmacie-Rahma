@props(['action', 'confirm' => 'Êtes-vous sûr de vouloir supprimer cet élément ? Cette action est irréversible.'])

<form method="POST" action="{{ $action }}" onsubmit="return confirm('{{ $confirm }}');">
    @csrf
    @method('DELETE')
    <button type="submit" class="p-1.5 text-gray-400 hover:text-red-600" aria-label="Supprimer">
        <x-icon name="trash" class="h-4 w-4" />
    </button>
</form>
