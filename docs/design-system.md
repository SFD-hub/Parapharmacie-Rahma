# Design System — Rahmane Parapharmacie

Ce document est la référence unique du Design System du projet. Il décrit ce qui existe déjà dans l'application (audité, pas réinventé), ce qui a été formalisé/ajouté pour combler les incohérences, et les règles à suivre pour tout futur écran (public ou back-office).

**Portée de ce document** : il ne change aucun écran existant. Les tokens ajoutés dans `tailwind.config.js` sont additifs (aucune classe déjà utilisée n'a été retirée ou modifiée). Les composants `x-ds.*` décrits ici sont nouveaux et disponibles pour les prochains développements ; ils ne sont pas encore branchés sur les écrans actuels.

---

## 1. Audit des composants existants

Constat global : le projet a déjà un langage visuel cohérent en pratique (couleur de marque `primary` rouge/cramoisi, cartes `rounded-2xl border border-gray-100 bg-white`, badges de statut pilotés par les enums), mais ce langage n'était documenté nulle part et certains éléments sont dupliqués ou en conflit.

### Composants déjà présents et réutilisables tels quels
- `<x-icon name="...">` — bibliothèque d'icônes maison (voir section 6).
- `<x-pagination :paginator="...">` — pagination (support paginator standard + Livewire).
- `<x-empty-state title="..." description="..." icon="...">` — état vide.
- `<x-input-label>`, `<x-text-input>`, `<x-input-error>` — champs de formulaire (utilisés par le plugin `@tailwindcss/forms`).
- `<x-order-status-badge>`, `<x-review-status-badge>`, `<x-loyalty-movement-badge>`, `<x-stock-movement-badge>`, `<x-admin.status-badge>` — 5 composants **strictement identiques** (même structure `inline-flex items-center rounded-full px-2.5 py-1 text-xs font-medium`, seule la prop change), délégant la couleur à la méthode `color()` de l'enum correspondant. C'est la convention à suivre pour tout nouveau statut.

### Duplications / incohérences identifiées (non corrigées dans cette étape, à corriger lors d'un futur écran)
- **Deux langages de bouton "primaire" coexistent** : le composant Breeze `<x-primary-button>` (`bg-gray-800`, `uppercase`, `focus:ring-indigo-500`) utilisé dans les 12 formulaires admin, et un bouton "pilule" `rounded-full bg-primary-600 hover:bg-primary-700` copié-collé (sans composant) dans 10+ écrans (listes admin, ajout au panier). → formalisé en `<x-ds.button>` (section 5).
- **Carte `rounded-2xl border border-gray-100 bg-white`** copiée-collée dans 65 fichiers, sans composant. → formalisé en `<x-ds.card>`.
- **Tableau admin** (`overflow-hidden rounded-2xl ... <table class="min-w-full divide-y ...">`) copié à l'identique dans 23 écrans d'index. → formalisé en `<x-ds.table>`.
- **Résidus Breeze non alignés à la marque** : `<x-primary-button>`, `<x-secondary-button>`, `<x-danger-button>`, `<x-text-input>` (focus `indigo-500`), `nav-link`/`responsive-nav-link` utilisent des couleurs `indigo`/`gray-800` qui ne correspondent pas à la marque (`primary` = rouge, `emerald` = succès). Ils ne sont utilisés que dans les pages d'authentification/profil héritées de Breeze et les formulaires admin. À ne pas copier pour du nouveau code ; préférer `<x-ds.button>`.
- **`green-600`** (Breeze, pages auth/profil) coexiste avec **`emerald-600`** (couleur de succès réelle de l'app). `emerald` est la couleur à utiliser partout ailleurs.
- **`components/web/product-badge.blade.php`** a sa propre palette (couleurs pleines, texte blanc) séparée des badges de statut (fond pastel `-50/-700`). Les deux variantes sont légitimes et désormais documentées comme deux variantes officielles : `soft` (listes/tableaux) et `solid` (superposé sur une image produit) — voir `<x-ds.badge>`.
- **Message flash absent côté public** : `layouts/admin.blade.php` affiche `session('success')`/`session('error')`, mais `layouts/web.blade.php` n'a aucun équivalent. `<x-ds.alert>` comble ce vide pour un futur branchement (non fait ici, car cela modifierait un écran existant).
- **Incohérence de tooling** : `package.json` liste à la fois `tailwindcss ^3.1.0` (réellement actif, via `@tailwind` + `tailwind.config.js`) et `@tailwindcss/vite ^4.0.0` (non utilisé, aucun `@import "tailwindcss"` ni plugin Vite v4 configuré). À nettoyer lors d'une future itération d'infrastructure — non traité ici car hors périmètre "Design System".
- **Composants absents avant cette étape** : onglets, accordéon réutilisable, tooltip, avatar, skeleton loader, spinner — aucun n'existait (l'accordéon de `web/products/show.blade.php` était codé en dur, non réutilisable). Tous ajoutés en section 5.

---

## 2. Palette de couleurs

La palette de marque existante (`primary`, rouge/cramoisi) est conservée telle quelle. Le Design System ajoute des **alias sémantiques** dans `tailwind.config.js` — chacun reprend exactement les teintes Tailwind déjà utilisées dans le code (`emerald`, `amber`, `red`, `blue`, `gray`), sous un nom qui documente son rôle. Rien n'est retiré : `bg-emerald-600` reste utilisable directement, `bg-success-600` est un synonyme.

| Rôle | Token | Nuance dominante | Usage |
|---|---|---|---|
| **Primary** (marque) | `primary-50` → `primary-900` | `primary-600` (`#d71f35`) / `primary-700` hover | Actions principales, liens actifs, éléments de marque |
| **Secondary** (neutre) | `secondary-50` → `secondary-900` | `secondary-700`/`secondary-900` | Boutons secondaires, textes/actions neutres (alias de la rampe grise) |
| **Success** | `success-50` → `success-900` | `success-600`/`success-700` (= `emerald`) | Confirmation, livré, approuvé, gain de points |
| **Warning** | `warning-50` → `warning-900` | `warning-600` (= `amber`) | En attente, stock faible, expiration proche |
| **Danger** | `danger-50` → `danger-900` | `danger-600` (= `red`) | Erreurs, suppression, rupture de stock, remboursement |
| **Info** | `info-50` → `info-900` | `info-600` (= `blue`) | Confirmé, informatif, nouveauté |
| Accent décoratif | `blush-50` → `blush-200` | — | Fonds décoratifs légers uniquement (déjà existant, inchangé) |

### Couleurs neutres (texte / fond / bordure)
Convention déjà en usage dans tout le projet, désormais documentée :

| Usage | Classe |
|---|---|
| Titre / texte à forte emphase | `text-gray-900` |
| Texte courant / labels | `text-gray-700` / `text-gray-600` |
| Texte secondaire / meta (dates, compteurs) | `text-gray-500` / `text-gray-400` |
| Fond de page | `bg-gray-50` |
| Fond de carte | `bg-white` |
| Bordure de carte / séparateur | `border-gray-100` |
| Bordure de champ de formulaire | `border-gray-300` |
| Fond au survol (lignes de tableau, liens) | `hover:bg-gray-50` / `hover:bg-gray-100` |
| Lien | `text-primary-600 hover:text-primary-700` |
| État désactivé | `opacity-50 cursor-not-allowed` |

### Convention "badge de statut" (déjà en place, à reproduire pour tout nouveau statut)
Chaque enum de statut expose une méthode `color()` retournant une paire `bg-{couleur}-50 text-{couleur}-700` :

```php
public function color(): string
{
    return match ($this) {
        self::Delivered => 'bg-emerald-50 text-emerald-700', // succès
        self::Pending => 'bg-amber-50 text-amber-700',       // attente
        self::Cancelled => 'bg-gray-100 text-gray-500',      // neutre/terminé
        self::Refunded => 'bg-red-50 text-red-700',          // négatif
    };
}
```
Le composant `<x-ds.badge>` (section 5) offre la même palette prête à l'emploi sans dupliquer les classes.

### Préparation du mode sombre
Le mode sombre **n'est pas implémenté** (aucune vue n'utilise `dark:`), mais `tailwind.config.js` déclare désormais `darkMode: 'class'` : ceci n'active rien tant qu'aucune classe `dark:` n'est utilisée et qu'aucune classe `dark` n'est posée sur `<html>`/`<body>` — donc **zéro changement visuel actuel**. Quand le mode sombre sera implémenté, voici les équivalences prévues :

| Rôle clair | Équivalent sombre prévu |
|---|---|
| `bg-white` (carte) | `dark:bg-gray-800` |
| `bg-gray-50` (page) | `dark:bg-gray-900` |
| `text-gray-900` (titre) | `dark:text-gray-50` |
| `text-gray-600` (texte) | `dark:text-gray-300` |
| `border-gray-100` | `dark:border-gray-700` |
| `bg-primary-50` (badge doux) | `dark:bg-primary-900/40 dark:text-primary-300` |

---

## 3. Typographie

- **Police** : Figtree (Bunny Fonts), poids 400/500/600/700, `fontFamily.sans` dans `tailwind.config.js`. Chargée dans `layouts/web.blade.php` ; **absente de `layouts/admin.blade.php`** (remarque : à ajouter lors d'une future retouche de ce layout, non fait ici pour ne pas modifier un écran existant).
- **Hiérarchie de titres** (constatée dans le code, à respecter pour toute nouvelle vue) :

| Niveau | Classe | Exemple d'usage |
|---|---|---|
| Titre de page | `text-base font-semibold text-gray-900` | `<h1>` de `layouts/admin.blade.php` |
| Titre de section / carte | `text-sm font-semibold text-gray-900` | En-tête de panneau dashboard |
| Grand chiffre / prix | `text-2xl font-bold text-gray-900` (ou `text-primary-600` pour un prix) | Tuiles statistiques, prix produit |
| Corps de texte | `text-sm text-gray-700` | Contenu courant |
| Texte secondaire | `text-sm text-gray-500` | Description, sous-titre |
| Meta / légende | `text-xs text-gray-400` | Dates, compteurs |
| Lien | `text-primary-600 hover:text-primary-700 font-medium` | Liens inline |

- **Graisses** : `font-medium` = emphase par défaut (labels, liens, cellules) ; `font-semibold` = titres de section/CTA ; `font-bold` = valeurs à très forte emphase (prix, totaux, statistiques).
- **Paragraphes** : `text-sm text-gray-600 leading-relaxed` pour les blocs de texte longs (descriptions produit, articles).

---

## 4. Espacements

Un seul barème, basé sur les valeurs déjà utilisées de façon cohérente dans le code (aucune valeur arbitraire type `mt-[13px]` n'a été trouvée dans l'audit — à continuer d'éviter) :

| Contexte | Valeur | Équivalent px |
|---|---|---|
| Padding interne d'une carte | `p-5` | 20px |
| Padding interne compact (tuile produit/pack/routine) | `p-3` | 12px |
| Rythme vertical entre sections de page | `mt-6` | 24px |
| Rythme entre champs de formulaire | `mt-4` | 16px |
| Pile de blocs de formulaire | `space-y-6` | 24px |
| Groupe icône + texte / boutons | `gap-2` ou `gap-3` | 8px / 12px |
| Groupe très compact (fil d'ariane, note) | `gap-1` / `gap-1.5` | 4px / 6px |
| Grille de cartes/tuiles | `gap-4` | 16px |
| Grille de panneaux larges (formulaires 2 colonnes) | `gap-6` | 24px |
| Liste d'éléments (nav, filtres, notifications) | `space-y-1` (compact) / `space-y-3` (confortable) | 4px / 12px |

Règle : ne pas introduire de valeur d'espacement hors de ce barème sans raison documentée.

---

## 5. Composants

### Composants existants (inchangés, à continuer d'utiliser)
`<x-icon>`, `<x-pagination>`, `<x-empty-state>`, `<x-input-label>`, `<x-text-input>`, `<x-input-error>`, `<x-order-status-badge>` / `<x-review-status-badge>` / `<x-loyalty-movement-badge>` / `<x-stock-movement-badge>` / `<x-admin.status-badge>`, `<x-admin.sortable-th>`, `<x-admin.search-bar>`, `<x-admin.delete-button>`.

### Nouveaux composants de référence (`resources/views/components/ds/*`)
Créés pour ce Design System, disponibles sous le préfixe `<x-ds.*>`. Non branchés sur les écrans existants (voir portée en tête de document).

| Composant | Props principales | Rôle |
|---|---|---|
| `<x-ds.button>` | `variant` (primary/secondary/outline/danger/ghost), `size` (sm/md/lg), `href` | Formalise le bouton "pilule" `bg-primary-600` déjà dominant dans l'app, + variantes cohérentes avec la palette |
| `<x-ds.card>` | `padding` | Formalise `rounded-2xl border border-gray-100 bg-white` |
| `<x-ds.badge>` | `color` (primary/success/warning/danger/info/neutral), `variant` (soft/solid) | Généralise les 5 badges de statut existants + la variante "solid" de `product-badge` |
| `<x-ds.alert>` | `type` (success/error/warning/info), `icon` | Généralise le bloc flash-message de `layouts/admin.blade.php`, réutilisable côté public |
| `<x-ds.avatar>` | `name`, `size` (sm/md/lg) | Avatar à initiales (aucun avatar n'existait) |
| `<x-ds.spinner>` | `size` (sm/md/lg) | Indicateur de chargement (aucun n'existait ; à utiliser avec `wire:loading`) |
| `<x-ds.skeleton>` | `rounded` | Placeholder de chargement `animate-pulse` |
| `<x-ds.tooltip>` | `text` | Infobulle CSS pure (`group-hover`), aucune dépendance JS |
| `<x-ds.tabs>` + `<x-ds.tab>` | `items`, `active` / `name` | Onglets Alpine (aucun composant d'onglets n'existait) |
| `<x-ds.accordion>` + `<x-ds.accordion-item>` | `open` / `name`, `title` | Généralise le pattern Alpine codé en dur dans `web/products/show.blade.php` |
| `<x-ds.dropdown>` | `align`, `width` (slot nommé `trigger`) | Version stylée à la marque du menu déroulant (le `<x-dropdown>` Breeze reste en place pour les pages d'auth héritées, non touché) |
| `<x-ds.table>` | slot nommé `head` | Formalise le squelette de tableau admin dupliqué dans 23 écrans |

Exemple d'utilisation :
```blade
<x-ds.card>
    <div class="flex items-center justify-between">
        <h2 class="text-sm font-semibold text-gray-900">Dernières commandes</h2>
        <x-ds.badge color="success">À jour</x-ds.badge>
    </div>
</x-ds.card>

<x-ds.table>
    <x-slot:head>
        <x-admin.sortable-th field="name">Nom</x-admin.sortable-th>
        <th class="px-4 py-3">Statut</th>
    </x-slot:head>
    @foreach ($items as $item)
        <tr>
            <td class="px-4 py-3">{{ $item->name }}</td>
            <td class="px-4 py-3"><x-ds.badge color="success">{{ $item->status }}</x-ds.badge></td>
        </tr>
    @endforeach
</x-ds.table>
```

---

## 6. Icônes

Une seule bibliothèque : le composant maison `<x-icon name="...">` (`resources/views/components/icon.blade.php`), inspiré du style Heroicons outline, **sans dépendance externe** (pas de paquet npm). C'est la bibliothèque officielle du projet — ne pas introduire de police d'icônes ni de paquet externe.

**39 icônes disponibles** : `menu`, `x-mark`, `search`, `user`, `cart`, `bag`, `heart`, `home`, `chat`, `sparkles`, `chevron-right`, `chevron-left`, `chevron-down`, `chevron-up`, `phone`, `mail`, `map-pin`, `clock`, `minus`, `plus`, `trash`, `arrow-right`, `check`, `shield`, `clipboard`, `truck`, `tag`, `pencil`, `archive`, `photo`, `star-outline`, `filter`, `exclamation`, `newspaper`, `document-text`, `folder`, `bell`, `gift`, `box`.

### Échelle de tailles (formalisation de l'usage déjà observé)

| Alias | Classe | Usage |
|---|---|---|
| `icon-xs` | `h-3 w-3` / `h-3.5 w-3.5` | Glyphes très compacts (fil d'ariane, badge) |
| `icon-sm` | `h-4 w-4` | Icônes inline dans le texte, actions de tableau (crayon/corbeille), formulaires |
| `icon-md` | `h-5 w-5` / `h-6 w-6` | Navigation (sidebar admin, header, barre mobile) |
| `icon-lg` | `h-7 w-7` / `h-8 w-8` | Décoratif (tuile catégorie, état vide, logo) |
| `icon-xl` | `h-10 w-10` et plus | Placeholder image absente, illustration |

Ces alias ne sont pas des classes Tailwind ajoutées (pour ne pas complexifier l'existant) : ce sont des **conventions documentées** — continuer à écrire `class="h-4 w-4"` directement sur `<x-icon>`.

---

## 7. États

| État | Traitement |
|---|---|
| **Hover** | `hover:bg-{color}-700` (boutons pleins), `hover:bg-gray-50`/`hover:bg-gray-100` (lignes, liens neutres), `hover:text-primary-700` (liens texte) |
| **Focus** | `focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-primary-500 focus-visible:ring-offset-2` sur tout élément interactif (boutons `x-ds.button`, onglets `x-ds.tabs`) ; les champs de formulaire utilisent déjà `focus:border-primary-500 focus:ring-primary-500` via le plugin `@tailwindcss/forms` |
| **Active** | Couleur plus foncée que le hover (`-700` sur fond `-600`) ou fond teinté (`bg-primary-50 text-primary-600` pour un lien de nav actif) |
| **Disabled** | `disabled:opacity-50 disabled:cursor-not-allowed` (géré nativement par `<x-ds.button>`) |
| **Loading** | `<x-ds.spinner>` combiné à `wire:loading` / `x-show` ; `<x-ds.skeleton>` pour un placeholder de contenu |
| **Success / Error** | `<x-ds.alert type="success|error">` ; couleurs `success`/`danger` partout ailleurs (badges, textes de validation) |
| **Empty state** | `<x-empty-state title="..." description="..." icon="...">` (déjà existant, à continuer d'utiliser) |

---

## 8. Responsive

Le projet cible en pratique deux paliers au-delà du mobile : **`sm:`** (tablette, ~640px) et **`lg:`** (bureau, ~1024px). `md:` est marginal (un seul écran) et `xl:`/`2xl:` ne sont pas utilisés — ne pas en introduire sans besoin réel.

- **Palier de bascule navigation** : `lg:` partout (sidebar admin, barre de navigation basse mobile, menu hamburger, recherche desktop) — à respecter pour toute nouvelle navigation.
- **Grilles types déjà en usage** :
  - Statistiques / tuiles : `grid grid-cols-1 gap-4 sm:grid-cols-2 lg:grid-cols-4`
  - Cartes produit / contenu : `grid grid-cols-2 gap-4 sm:grid-cols-3 lg:grid-cols-{3|4|5}`
  - Formulaires deux colonnes : `grid grid-cols-1 gap-6 lg:grid-cols-3` (2 colonnes contenu + 1 colonne latérale)
- Mobile-first systématique : les classes de base (sans préfixe) définissent le rendu mobile ; `sm:`/`lg:` ajoutent les adaptations.

---

## 9. Accessibilité

Constat de l'audit : couverture présente mais inégale. Règles à respecter pour tout nouveau développement :

- **Tout élément interactif icône seule doit avoir un `aria-label`** (déjà la norme sur les boutons de suppression/édition/menu — à reproduire systématiquement, y compris pour les nouveaux composants comme `<x-ds.button>` sans texte visible).
- **Contraste** : les paires de couleurs `-50/-700` utilisées pour les badges, et `-600`/blanc pour les boutons pleins, respectent un contraste suffisant (WCAG AA) ; ne pas utiliser de texte `-400`/`-500` sur fond clair `-50` pour du texte porteur de sens.
- **Focus clavier visible** : tout composant interactif neuf doit exposer un anneau de focus (`focus-visible:ring-2`), comme posé sur `<x-ds.button>` et `<x-ds.tabs>`.
- **Taille minimale de zone cliquable** : viser au moins 40px (`p-2` autour d'une icône `h-5 w-5`/`h-6 w-6`) comme déjà pratiqué sur les boutons icône du header/sidebar.
- **Labels de formulaire** : toujours associer `<x-input-label>` à son champ (convention déjà respectée).
- **États décoratifs vs porteurs de sens** : une étoile de notation ou une icône purement décorative à côté d'un texte déjà explicite n'a pas besoin de `aria-label` supplémentaire (éviter la redondance) ; en revanche, un composant qui remplace un texte (comme `star-rating`) doit exposer `role="img" aria-label="..."` — modèle déjà bien implémenté dans `components/web/star-rating.blade.php`, à reproduire.
- **Overlays (menus, dropdowns)** : `<x-ds.dropdown>` ferme au clic extérieur et à la touche `Échap` ; toute nouvelle fenêtre modale/drawer doit au minimum fermer à l'`Échap` et au clic extérieur.

---

## 10. Documentation — conventions pour les futurs développeurs

1. **Avant de copier-coller une carte/un bouton/un tableau**, vérifier si `<x-ds.card>`, `<x-ds.button>` ou `<x-ds.table>` peut être utilisé à la place.
2. **Avant de créer un nouveau statut/badge**, suivre la convention `color()`/`label()` sur l'enum et utiliser `<x-ds.badge>` plutôt qu'un nouveau composant `*-badge` dédié.
3. **Ne jamais utiliser `indigo-*` ou `green-600`** dans du nouveau code — ce sont des résidus Breeze non alignés à la marque (`primary` = rouge, `success` = `emerald`).
4. **Utiliser les alias sémantiques (`success`/`warning`/`danger`/`info`/`secondary`) ou les couleurs Tailwind qu'ils reprennent (`emerald`/`amber`/`red`/`blue`/`gray`)** — les deux sont strictement équivalents, au choix du développeur pour la lisibilité du code.
5. **Respecter le barème d'espacement de la section 4** — pas de valeur arbitraire.
6. **Toute nouvelle icône** doit être ajoutée dans `resources/views/components/icon.blade.php` (pas de nouvelle bibliothèque).
7. **Le mode sombre n'est pas actif** — ne pas ajouter de classes `dark:` sur les écrans existants sans décision explicite de l'activer globalement ; `darkMode: 'class'` est prêt pour ce jour-là.
8. Ce document doit être mis à jour à chaque fois qu'un nouveau composant `x-ds.*` ou un nouveau token de couleur est ajouté.
