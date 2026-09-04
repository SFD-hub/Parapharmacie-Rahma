

import Alpine from 'alpinejs';
import 'trix';

// Livewire bundles and starts its own Alpine instance via @livewireScripts.
// Only start ours on pages that don't load Livewire (guest/auth layouts),
// otherwise two Alpine instances end up racing over the same DOM.
if (! window.Alpine) {
    window.Alpine = Alpine;
    Alpine.start();
}
