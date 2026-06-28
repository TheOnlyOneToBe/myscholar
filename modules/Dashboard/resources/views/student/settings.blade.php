@extends('dashboard::student-dashboard-layout')

@section('content')
<div class="max-w-4xl mx-auto">
    <div class="mb-8">
        <h1 class="text-3xl font-bold text-gray-900">Paramètres</h1>
        <p class="text-gray-600 mt-2">Gérez vos préférences et paramètres de compte</p>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-4 gap-6">
        <!-- Sidebar Navigation -->
        <div class="lg:col-span-1">
            <div class="bg-white rounded-lg shadow p-4">
                <nav class="space-y-1">
                    <a href="#account" class="block px-4 py-2 text-sm font-medium text-blue-600 bg-blue-50 rounded-lg">
                        Compte
                    </a>
                    <a href="#privacy" class="block px-4 py-2 text-sm font-medium text-gray-600 hover:bg-gray-50 rounded-lg">
                        Confidentialité
                    </a>
                    <a href="#notifications" class="block px-4 py-2 text-sm font-medium text-gray-600 hover:bg-gray-50 rounded-lg">
                        Notifications
                    </a>
                    <a href="#security" class="block px-4 py-2 text-sm font-medium text-gray-600 hover:bg-gray-50 rounded-lg">
                        Sécurité
                    </a>
                </nav>
            </div>
        </div>

        <!-- Main Content -->
        <div class="lg:col-span-3 space-y-6">
            <!-- Account Settings -->
            <div id="account" class="bg-white rounded-lg shadow p-6">
                <h2 class="text-xl font-bold text-gray-900 mb-6">Paramètres de Compte</h2>

                <div class="space-y-6">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Nom Complet</label>
                        <input type="text" value="{{ $user->first_name }} {{ $user->last_name }}" readonly class="w-full px-4 py-2 border border-gray-300 rounded-lg bg-gray-50">
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Email</label>
                        <input type="email" value="{{ $user->email }}" readonly class="w-full px-4 py-2 border border-gray-300 rounded-lg bg-gray-50">
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Nom d'Utilisateur</label>
                        <input type="text" value="{{ $user->username }}" readonly class="w-full px-4 py-2 border border-gray-300 rounded-lg bg-gray-50">
                    </div>

                    <button class="px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700">
                        Changer le Mot de Passe
                    </button>
                </div>
            </div>

            <!-- Privacy Settings -->
            <div id="privacy" class="bg-white rounded-lg shadow p-6">
                <h2 class="text-xl font-bold text-gray-900 mb-6">Confidentialité</h2>

                <div class="space-y-4">
                    <label class="flex items-center">
                        <input type="checkbox" class="w-4 h-4 text-blue-600 rounded">
                        <span class="ml-3 text-sm text-gray-700">Rendre mon profil visible aux autres étudiants</span>
                    </label>

                    <label class="flex items-center">
                        <input type="checkbox" class="w-4 h-4 text-blue-600 rounded" checked>
                        <span class="ml-3 text-sm text-gray-700">Permettre aux parents de voir mes notes</span>
                    </label>
                </div>
            </div>

            <!-- Notification Settings -->
            <div id="notifications" class="bg-white rounded-lg shadow p-6">
                <h2 class="text-xl font-bold text-gray-900 mb-6">Notifications</h2>

                <div class="space-y-4">
                    <label class="flex items-center">
                        <input type="checkbox" class="w-4 h-4 text-blue-600 rounded" checked>
                        <span class="ml-3 text-sm text-gray-700">Notifications par email de nouvelles notes</span>
                    </label>

                    <label class="flex items-center">
                        <input type="checkbox" class="w-4 h-4 text-blue-600 rounded" checked>
                        <span class="ml-3 text-sm text-gray-700">Notifications de paiements en attente</span>
                    </label>

                    <label class="flex items-center">
                        <input type="checkbox" class="w-4 h-4 text-blue-600 rounded" checked>
                        <span class="ml-3 text-sm text-gray-700">Rappels d'absence</span>
                    </label>
                </div>
            </div>

            <!-- Security Settings -->
            <div id="security" class="bg-white rounded-lg shadow p-6">
                <h2 class="text-xl font-bold text-gray-900 mb-6">Sécurité</h2>

                <div class="space-y-4">
                    <div>
                        <h3 class="font-medium text-gray-900 mb-2">Sessions Actives</h3>
                        <p class="text-sm text-gray-600 mb-3">Gérez vos sessions connectées</p>
                        <button class="px-4 py-2 bg-red-600 text-white rounded-lg hover:bg-red-700">
                            Déconnecter Toutes les Sessions
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
