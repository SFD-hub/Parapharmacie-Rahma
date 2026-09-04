# Changelog

Toutes les évolutions notables du projet Rahmane Parapharmacie sont documentées dans ce fichier.

## [2026-07-28] — Parcours d'achat (Panier → Checkout → Commandes)

### Ajouté

**Panier**
- Ajout, suppression, modification de quantité, vidage du panier (`CartController`, routes `cart.*`).
- Panier invité (identifié par un token de session) et panier client (persistant, lié au compte).
- Fusion automatique du panier invité dans le panier du client à la connexion (`MergeGuestCartOnLogin`).
- Mini-panier dans le header (`Livewire\Cart\MiniCart`) avec compteur dynamique, synchronisé en direct avec la page panier via l'événement `cart-updated`.
- Page panier complète (`Livewire\Cart\CartPage`) : quantités modifiables en direct, suppression, vidage, sous-total, remise, frais de livraison, barre de progression « livraison offerte ».
- Limitation automatique des quantités au stock disponible, à l'ajout comme à la modification.

**Checkout**
- Parcours complet : résumé de commande, sélection d'une adresse existante ou création d'une nouvelle adresse, choix du moyen de paiement, validation, page de confirmation.
- `CheckoutController` + `PlaceOrderRequest` + `StoreAddressRequest`, entièrement délégué à `CheckoutService`/`OrderService` (aucune logique métier dans le contrôleur).

**Commandes**
- Création de commande transactionnelle (`OrderService::createFromCart`) : snapshot des données produit (nom, SKU, prix), décrémentation atomique du stock (verrou de ligne), historique de statut initial, panier vidé — le tout annulé intégralement (rollback) en cas de rupture de stock détectée en cours de transaction.
- Génération de numéros de commande uniques (`OrderNumberService`).
- Statut de commande étendu (`OrderStatus`) : En attente, **Confirmée** *(nouveau)*, En préparation, Expédiée, Livrée, Annulée, Remboursée.
- Historique des changements de statut (`order_status_histories`), avec l'administrateur responsable et une note optionnelle.

**Compte client**
- Historique des commandes et détail d'une commande (`Account\OrderController`), strictement limités aux commandes du client connecté (Policy `OrderPolicy`).

**Administration**
- Gestion des commandes (`Admin\OrderController`) : liste, recherche (n° commande, client), filtre par statut, tri, pagination, détail complet, changement de statut avec historique.
- Lien « Commandes » ajouté au menu latéral admin.

**Paiement (architecture uniquement, aucune intégration réelle)**
- `PaymentMethod` (enum) : paiement à la livraison, Wave, Orange Money, carte bancaire, PayPal.
- `PaymentGatewayInterface` + `PaymentGatewayFactory` + `ManualPaymentGateway` — permet d'ajouter un vrai fournisseur de paiement plus tard sans modifier le flux de checkout.

**Base de données**
- Nouvelles tables : `carts`, `cart_items`, `order_status_histories`.
- Nouveaux modèles : `Cart`, `CartItem`, `OrderStatusHistory`.

**Tests**
- 50 nouveaux tests (ajout/suppression/modification/limitation de stock au panier, fusion et restauration du panier invité, checkout, création de commande, transaction SQL et rollback, décrémentation du stock, historique client, sécurité des commandes, administration, changement de statut).

### Corrigé (auto-revue)
- Un panier vide était créé en base à chaque simple visite du site (invité ou connecté), y compris sans aucune interaction — corrigé via `CartService::find()` (lecture sans création) utilisée par le mini-panier et le compteur.
- La sélection d'adresse au checkout n'était pas conservée après un échec de validation (retombait systématiquement sur la première adresse).
- Autorisation de consultation des commandes centralisée dans une Policy Laravel (`OrderPolicy`) plutôt que dupliquée dans deux contrôleurs.
- Duplication de blocs d'affichage (résumé des montants, carte d'adresse) éliminée via deux nouveaux composants partagés (`x-totals-summary`, `x-address-card`), désormais réutilisés dans le panier, le checkout, le compte client et l'administration.

## [Antérieur] — Itérations précédentes

- Architecture Laravel initiale, authentification séparée Client/Admin, layout public et composants Blade de base.
- Module Catalogue complet (catégories, marques, produits, images, recherche, filtres, tri, pagination côté public et administration).
