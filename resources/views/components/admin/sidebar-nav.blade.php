<nav class="space-y-1 px-3 py-4">
    <x-admin.nav-link route="admin.dashboard" icon="home">Tableau de bord</x-admin.nav-link>

    <p class="px-3 pb-1 pt-5 text-[11px] font-semibold uppercase tracking-wider text-gray-500">Catalogue</p>
    <x-admin.nav-link route="admin.categories.index" icon="tag">Catégories</x-admin.nav-link>
    <x-admin.nav-link route="admin.brands.index" icon="sparkles">Marques</x-admin.nav-link>
    <x-admin.nav-link route="admin.products.index" icon="archive">Produits</x-admin.nav-link>
    <x-admin.nav-link route="admin.marketing.packs.index" icon="box">Packs</x-admin.nav-link>

    <p class="px-3 pb-1 pt-5 text-[11px] font-semibold uppercase tracking-wider text-gray-500">Commandes &amp; clients</p>
    <x-admin.nav-link route="admin.orders.pending" activePattern="admin.orders." icon="cart">Commandes</x-admin.nav-link>
    <x-admin.nav-link route="admin.customers.index" icon="user">Clients</x-admin.nav-link>
    <x-admin.nav-link route="admin.payments.index" icon="tag">Paiements</x-admin.nav-link>

    <p class="px-3 pb-1 pt-5 text-[11px] font-semibold uppercase tracking-wider text-gray-500">Contenu</p>
    <x-admin.nav-link route="admin.articles.index" icon="newspaper">Articles</x-admin.nav-link>
    <x-admin.nav-link route="admin.reviews.index" icon="star-outline">Avis clients</x-admin.nav-link>

    <p class="px-3 pb-1 pt-5 text-[11px] font-semibold uppercase tracking-wider text-gray-500">Support &amp; fidélité</p>
    <x-admin.nav-link route="admin.support.index" activePattern="admin.support." icon="chat">Messages clients</x-admin.nav-link>
    <x-admin.nav-link route="admin.loyalty.index" icon="gift">Fidélité</x-admin.nav-link>

</nav>
