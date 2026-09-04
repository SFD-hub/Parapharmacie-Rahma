<x-guest-layout>
    <div class="mb-6 text-center">
        <h1 class="text-xl font-bold text-gray-900">Espace administrateur</h1>
        <p class="mt-1 text-sm text-gray-500">Connectez-vous pour gérer la boutique.</p>
    </div>

    <x-auth-session-status class="mb-4" :status="session('status')" />

    <form method="POST" action="{{ route('admin.login') }}" class="space-y-4">
        @csrf

        <div>
            <x-input-label for="email" value="Email" />
            <input
                id="email"
                type="email"
                name="email"
                value="{{ old('email') }}"
                required
                autofocus
                autocomplete="username"
                class="mt-1 block w-full rounded-lg border-gray-300 text-sm shadow-sm focus:border-primary-500 focus:ring-primary-500"
            >
            <x-input-error :messages="$errors->get('email')" class="mt-2" />
        </div>

        <div>
            <x-input-label for="password" value="Mot de passe" />
            <input
                id="password"
                type="password"
                name="password"
                required
                autocomplete="current-password"
                class="mt-1 block w-full rounded-lg border-gray-300 text-sm shadow-sm focus:border-primary-500 focus:ring-primary-500"
            >
            <x-input-error :messages="$errors->get('password')" class="mt-2" />
        </div>

        <label for="remember_me" class="flex items-center">
            <input id="remember_me" type="checkbox" name="remember" class="rounded border-gray-300 text-primary-600 focus:ring-primary-500">
            <span class="ms-2 text-sm text-gray-600">Se souvenir de moi</span>
        </label>

        <button type="submit" class="flex w-full items-center justify-center rounded-full bg-primary-600 py-2.5 text-sm font-semibold text-white transition hover:bg-primary-700">
            Se connecter
        </button>
    </form>
</x-guest-layout>
