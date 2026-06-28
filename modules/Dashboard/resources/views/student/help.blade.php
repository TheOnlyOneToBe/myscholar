@extends('dashboard::student-dashboard-layout')

@section('content')
<div class="max-w-4xl mx-auto">
    <div class="mb-8">
        <h1 class="text-3xl font-bold text-gray-900">Centre d'Aide</h1>
        <p class="text-gray-600 mt-2">Trouvez des réponses à vos questions</p>
    </div>

    <!-- Search Box -->
    <div class="mb-8">
        <div class="relative">
            <input type="text" placeholder="Rechercher dans l'aide..." class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent">
            <i class="fas fa-search absolute right-3 top-3.5 h-5 w-5 text-gray-400"></i>
        </div>
    </div>

    <!-- FAQ Categories -->
    <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-8">
        <!-- Grades -->
        <div class="bg-white rounded-lg shadow p-6 hover:shadow-lg transition">
            <div class="flex items-center mb-4">
                <div class="flex-shrink-0">
                    <i class="fas fa-chart-line h-6 w-6 text-blue-600"></i>
                </div>
                <h3 class="ml-3 text-lg font-medium text-gray-900">Notes et Grades</h3>
            </div>
            <p class="text-gray-600 text-sm mb-4">Comment consulter vos notes, faire appel d'une note...</p>
            <a href="#" class="text-blue-600 hover:text-blue-700 text-sm font-medium">Voir les articles →</a>
        </div>

        <!-- Attendance -->
        <div class="bg-white rounded-lg shadow p-6 hover:shadow-lg transition">
            <div class="flex items-center mb-4">
                <div class="flex-shrink-0">
                    <i class="fas fa-calendar-check h-6 w-6 text-green-600"></i>
                </div>
                <h3 class="ml-3 text-lg font-medium text-gray-900">Absences</h3>
            </div>
            <p class="text-gray-600 text-sm mb-4">Consulter vos présences, justifier une absence...</p>
            <a href="#" class="text-blue-600 hover:text-blue-700 text-sm font-medium">Voir les articles →</a>
        </div>

        <!-- Billing -->
        <div class="bg-white rounded-lg shadow p-6 hover:shadow-lg transition">
            <div class="flex items-center mb-4">
                <div class="flex-shrink-0">
                    <i class="fas fa-money-bill-wave h-6 w-6 text-purple-600"></i>
                </div>
                <h3 class="ml-3 text-lg font-medium text-gray-900">Facturation</h3>
            </div>
            <p class="text-gray-600 text-sm mb-4">Consulter vos factures, effectuer un paiement...</p>
            <a href="#" class="text-blue-600 hover:text-blue-700 text-sm font-medium">Voir les articles →</a>
        </div>

        <!-- Documents -->
        <div class="bg-white rounded-lg shadow p-6 hover:shadow-lg transition">
            <div class="flex items-center mb-4">
                <div class="flex-shrink-0">
                    <i class="fas fa-file-alt h-6 w-6 text-orange-600"></i>
                </div>
                <h3 class="ml-3 text-lg font-medium text-gray-900">Documents</h3>
            </div>
            <p class="text-gray-600 text-sm mb-4">Télécharger vos certificats, bulletins...</p>
            <a href="#" class="text-blue-600 hover:text-blue-700 text-sm font-medium">Voir les articles →</a>
        </div>
    </div>

    <!-- Common Questions -->
    <div class="bg-white rounded-lg shadow p-6">
        <h2 class="text-xl font-bold text-gray-900 mb-6">Questions Fréquemment Posées</h2>

        <div class="space-y-4">
            <details class="border-b border-gray-200 pb-4">
                <summary class="cursor-pointer font-medium text-gray-900 hover:text-blue-600">
                    Comment consulter mes notes ?
                </summary>
                <p class="mt-2 text-gray-600 text-sm">
                    Allez dans la section "Mes Notes" du dashboard pour consulter toutes vos notes par matière.
                </p>
            </details>

            <details class="border-b border-gray-200 pb-4">
                <summary class="cursor-pointer font-medium text-gray-900 hover:text-blue-600">
                    Comment justifier une absence ?
                </summary>
                <p class="mt-2 text-gray-600 text-sm">
                    Rendez-vous dans "Mes Absences" et cliquez sur le bouton "Justifier" pour soumettre votre justification.
                </p>
            </details>

            <details class="border-b border-gray-200 pb-4">
                <summary class="cursor-pointer font-medium text-gray-900 hover:text-blue-600">
                    Où télécharger mes certificats ?
                </summary>
                <p class="mt-2 text-gray-600 text-sm">
                    Allez à "Mon Profil" puis "Documents" pour télécharger tous vos certificats et bulletins.
                </p>
            </details>

            <details class="border-b border-gray-200 pb-4">
                <summary class="cursor-pointer font-medium text-gray-900 hover:text-blue-600">
                    Comment faire appel d'une note ?
                </summary>
                <p class="mt-2 text-gray-600 text-sm">
                    Dans "Mes Notes", cliquez sur une note et sélectionnez "Faire appel" pour soumettre votre appel.
                </p>
            </details>
        </div>
    </div>

    <!-- Contact Support -->
    <div class="mt-8 bg-blue-50 border border-blue-200 rounded-lg p-6">
        <h3 class="text-lg font-medium text-blue-900 mb-2">Vous avez besoin d'aide ?</h3>
        <p class="text-blue-700 mb-4">Si vous ne trouvez pas la réponse à votre question, contactez le support.</p>
        <button class="px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700">
            Contacter le Support
        </button>
    </div>
</div>
@endsection
