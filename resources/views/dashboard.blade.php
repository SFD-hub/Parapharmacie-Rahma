<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Dashboard') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900">
                    {{ __("You're logged in!") }}
                </div>
                <div class="border-t border-gray-100 p-6 space-x-4">
                    <a href="{{ route('account.orders.index') }}" class="text-sm font-medium text-indigo-600 hover:text-indigo-800">
                        {{ __('Mes commandes') }} &rarr;
                    </a>
                    <a href="{{ route('account.loyalty.index') }}" class="text-sm font-medium text-indigo-600 hover:text-indigo-800">
                        {{ __('Ma fidélité') }} &rarr;
                    </a>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
