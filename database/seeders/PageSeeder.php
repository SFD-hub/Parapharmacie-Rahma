<?php

namespace Database\Seeders;

use App\Models\Page;
use Illuminate\Database\Seeder;

class PageSeeder extends Seeder
{
    /**
     * Seed the standard CMS pages.
     */
    public function run(): void
    {
        $pages = [
            [
                'slug' => 'a-propos',
                'title' => 'À propos',
                'content' => 'Rahmane Parapharmacie vous accompagne au quotidien avec une sélection de produits de parapharmacie de qualité, conseillés par des professionnels.',
            ],
            [
                'slug' => 'contact',
                'title' => 'Contact',
                'content' => 'Une question ? Contactez-nous par téléphone ou par email, notre équipe vous répond rapidement.',
            ],
            [
                'slug' => 'faq',
                'title' => 'FAQ',
                'content' => 'Retrouvez ici les réponses aux questions les plus fréquentes sur nos produits, la livraison et vos commandes.',
            ],
            [
                'slug' => 'conditions-generales',
                'title' => 'Conditions générales',
                'content' => "Conditions générales de vente et d'utilisation du site Rahmane Parapharmacie.",
            ],
            [
                'slug' => 'politique-de-confidentialite',
                'title' => 'Politique de confidentialité',
                'content' => 'Cette politique décrit comment Rahmane Parapharmacie collecte et protège vos données personnelles.',
            ],
        ];

        foreach ($pages as $data) {
            Page::updateOrCreate(
                ['slug' => $data['slug']],
                [
                    'title' => $data['title'],
                    'content' => $data['content'],
                    'is_active' => true,
                ]
            );
        }
    }
}
