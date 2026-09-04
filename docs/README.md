# Rahmane Parapharmacie

## Présentation

Rahmane Parapharmacie est un site web e-commerce moderne développé avec Laravel 12.

Le site permet aux visiteurs de consulter les produits, rechercher des produits, obtenir des conseils beauté grâce à un assistant IA, lire des articles et passer une commande.

Il ne s'agit PAS d'une application SaaS.

Le projet dispose de deux authentifications totalement indépendantes :

- les clients utilisent la table `users` ;
- les administrateurs utilisent la table `admins`.

Le compte client est conservé afin de gérer l'historique des commandes, les adresses, les promotions, la fidélité et les futures fonctionnalités marketing.

L'authentification de l'administrateur permet de gérer le contenu du site.

---

# Technologies

Le projet doit utiliser uniquement les technologies suivantes :

- Laravel 12
- PHP 8+
- MySQL
- Blade
- Livewire
- Tailwind CSS
- Alpine.js
- JavaScript uniquement lorsque nécessaire

---

# Hébergement

Le projet sera déployé sur un hébergement mutualisé LWS.

Toutes les décisions techniques doivent être compatibles avec cet environnement.

Éviter toute dépendance nécessitant :

- Redis
- Docker
- Node.js en production
- Serveur dédié

Le projet doit rester facilement déployable sur LWS.

---

# Architecture

Le projet doit respecter une architecture Laravel propre.

La logique métier doit être séparée des vues.

Le code doit être organisé, lisible et facilement maintenable.

Éviter la duplication de code.

Créer des composants réutilisables lorsque cela est pertinent.

Toujours privilégier les bonnes pratiques Laravel.

---

# Interface utilisateur

Le design doit respecter les maquettes validées.

L'objectif est de proposer une interface :

- moderne
- rapide
- simple
- élégante
- responsive

Chaque page doit avoir un objectif clair.

Ne jamais ajouter des fonctionnalités ou sections qui n'ont pas été demandées.

---

# Fonctionnement

Le site contient notamment :

- Accueil
- Boutique
- Produits
- Catégories
- Marques
- Conseils Beauté
- Assistant IA
- Panier
- Paiement
- Notifications
- Administration

---

# Développement

Avant chaque modification :

- Lire ce document.
- Respecter l'architecture existante.
- Ne jamais modifier une fonctionnalité sans raison.
- Ne jamais prendre d'initiative qui change le fonctionnement du projet.
- En cas de doute, toujours demander confirmation.

Le développement se fera étape par étape.

Une seule fonctionnalité sera développée à la fois.

Ne jamais générer plusieurs modules dans une même demande.

Toujours privilégier un code propre, lisible, maintenable et professionnel.
