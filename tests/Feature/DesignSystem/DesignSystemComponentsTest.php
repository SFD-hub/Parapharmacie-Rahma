<?php

namespace Tests\Feature\DesignSystem;

use Illuminate\Support\Facades\Blade;
use Tests\TestCase;

class DesignSystemComponentsTest extends TestCase
{
    public function test_button_renders_variant_and_size_classes(): void
    {
        $html = Blade::render('<x-ds.button variant="danger" size="lg">Supprimer</x-ds.button>');

        $this->assertStringContainsString('bg-danger-600', $html);
        $this->assertStringContainsString('px-5 py-2.5', $html);
        $this->assertStringContainsString('Supprimer', $html);
    }

    public function test_button_renders_as_a_link_when_href_is_given(): void
    {
        $html = Blade::render('<x-ds.button href="/boutique">Voir la boutique</x-ds.button>');

        $this->assertStringContainsString('<a href="/boutique"', $html);
    }

    public function test_card_wraps_slot_content(): void
    {
        $html = Blade::render('<x-ds.card>Contenu</x-ds.card>');

        $this->assertStringContainsString('rounded-2xl border border-gray-100 bg-white p-5', $html);
        $this->assertStringContainsString('Contenu', $html);
    }

    public function test_badge_soft_and_solid_variants_use_the_documented_palette(): void
    {
        $soft = Blade::render('<x-ds.badge color="primary">Nouveau</x-ds.badge>');
        $solid = Blade::render('<x-ds.badge color="danger" variant="solid">Épuisé</x-ds.badge>');

        $this->assertStringContainsString('bg-primary-50 text-primary-700', $soft);
        $this->assertStringContainsString('bg-red-600 text-white', $solid);
    }

    public function test_alert_uses_the_matching_icon_and_colors_per_type(): void
    {
        $html = Blade::render('<x-ds.alert type="error">Une erreur est survenue.</x-ds.alert>');

        $this->assertStringContainsString('bg-red-50 text-red-700', $html);
        $this->assertStringContainsString('Une erreur est survenue.', $html);
    }

    public function test_avatar_derives_initials_from_the_name(): void
    {
        $html = Blade::render('<x-ds.avatar name="Jean Dupont" />');

        $this->assertStringContainsString('JD', $html);
    }

    public function test_avatar_falls_back_when_name_is_empty(): void
    {
        $html = Blade::render('<x-ds.avatar name="" />');

        $this->assertStringContainsString('?', $html);
    }

    public function test_spinner_has_an_accessible_status_role(): void
    {
        $html = Blade::render('<x-ds.spinner />');

        $this->assertStringContainsString('animate-spin', $html);
        $this->assertStringContainsString('role="status"', $html);
    }

    public function test_skeleton_renders_a_pulsing_placeholder(): void
    {
        $html = Blade::render('<x-ds.skeleton class="h-4 w-32" />');

        $this->assertStringContainsString('animate-pulse', $html);
        $this->assertStringContainsString('h-4 w-32', $html);
    }

    public function test_tooltip_renders_trigger_and_text(): void
    {
        $html = Blade::render('<x-ds.tooltip text="Ajouter aux favoris"><button>❤</button></x-ds.tooltip>');

        $this->assertStringContainsString('role="tooltip"', $html);
        $this->assertStringContainsString('Ajouter aux favoris', $html);
    }

    public function test_tabs_render_a_tablist_and_the_matching_panel(): void
    {
        $html = Blade::render(<<<'BLADE'
            <x-ds.tabs :items="['description' => 'Description', 'usage' => 'Utilisation']">
                <x-ds.tab name="description">Contenu description</x-ds.tab>
                <x-ds.tab name="usage">Contenu utilisation</x-ds.tab>
            </x-ds.tabs>
            BLADE);

        $this->assertStringContainsString('role="tablist"', $html);
        $this->assertStringContainsString('Description', $html);
        $this->assertStringContainsString("activeTab === 'description'", $html);
    }

    public function test_accordion_item_exposes_aria_expanded(): void
    {
        $html = Blade::render(<<<'BLADE'
            <x-ds.accordion>
                <x-ds.accordion-item name="ingredients" title="Ingrédients">Liste des ingrédients.</x-ds.accordion-item>
            </x-ds.accordion>
            BLADE);

        $this->assertStringContainsString('Ingrédients', $html);
        $this->assertStringContainsString(':aria-expanded="openItem === \'ingredients\'"', $html);
    }

    public function test_dropdown_renders_the_trigger_and_the_menu_slot(): void
    {
        $html = Blade::render(<<<'BLADE'
            <x-ds.dropdown>
                <x-slot:trigger>Mon compte</x-slot:trigger>
                <a href="/profil">Profil</a>
            </x-ds.dropdown>
            BLADE);

        $this->assertStringContainsString('Mon compte', $html);
        $this->assertStringContainsString('Profil', $html);
        $this->assertStringContainsString('x-data="{ open: false }"', $html);
    }

    public function test_table_renders_the_head_slot_and_rows(): void
    {
        $html = Blade::render(<<<'BLADE'
            <x-ds.table>
                <x-slot:head>
                    <th>Nom</th>
                </x-slot:head>
                <tr><td>Sérum Vitamine C</td></tr>
            </x-ds.table>
            BLADE);

        $this->assertStringContainsString('Nom', $html);
        $this->assertStringContainsString('Sérum Vitamine C', $html);
        $this->assertStringContainsString('divide-y divide-gray-100 text-sm', $html);
    }
}
