{{-- Formalise le squelette de tableau admin déjà copié-collé à l'identique
     dans les 23 écrans d'index (voir docs/design-system.md, section Composants). --}}
@props([])

<div class="overflow-hidden rounded-2xl border border-gray-100 bg-white">
    <div class="overflow-x-auto">
        <table class="min-w-full divide-y divide-gray-100 text-sm">
            <thead class="bg-gray-50 text-left text-xs font-medium uppercase tracking-wide text-gray-500">
                <tr>
                    {{ $head }}
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-100">
                {{ $slot }}
            </tbody>
        </table>
    </div>
</div>
