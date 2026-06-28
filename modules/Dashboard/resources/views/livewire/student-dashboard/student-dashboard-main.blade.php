<div class="student-dashboard">
    <!-- Header with student info -->
    <div class="dashboard-header mb-6">
        <div class="flex justify-between items-center">
            <div>
                <h1 class="text-3xl font-bold">
                    Bienvenue, {{ $studentInfo['first_name'] ?? 'Élève' }}! 👋
                </h1>
                <p class="text-gray-600">Matricule: {{ $studentInfo['matricule'] ?? 'N/A' }}</p>
            </div>
            <div class="text-right">
                <p class="text-sm text-gray-500">Classe: {{ $studentInfo['current_class'] ?? 'N/A' }}</p>
            </div>
        </div>
    </div>

    <!-- Quick Stats Cards -->
    <div class="grid grid-cols-1 md:grid-cols-4 gap-4 mb-6">
        <div class="bg-blue-50 p-6 rounded-lg shadow">
            <div class="text-sm text-gray-600">Moyenne Générale</div>
            <div class="text-3xl font-bold text-blue-600">{{ $quickStats['current_average'] ?? 0 }}/20</div>
        </div>
        <div class="bg-green-50 p-6 rounded-lg shadow">
            <div class="text-sm text-gray-600">Présence</div>
            <div class="text-3xl font-bold text-green-600">{{ $quickStats['attendance_rate'] ?? 0 }}%</div>
        </div>
        <div class="bg-orange-50 p-6 rounded-lg shadow">
            <div class="text-sm text-gray-600">Solde Impayé</div>
            <div class="text-3xl font-bold text-orange-600">{{ number_format($quickStats['outstanding_balance'] ?? 0, 0) }} FCFA</div>
        </div>
        <div class="bg-red-50 p-6 rounded-lg shadow">
            <div class="text-sm text-gray-600">Factures Impayées</div>
            <div class="text-3xl font-bold text-red-600">{{ $quickStats['overdue_invoices'] ?? 0 }}</div>
        </div>
    </div>

    <!-- Tabs Navigation -->
    <div class="mb-6 border-b border-gray-200">
        <div class="flex space-x-8">
            <button
                wire:click="switchTab('overview')"
                class="py-4 px-1 border-b-2 @if($activeTab === 'overview') border-blue-500 text-blue-600 @else border-transparent text-gray-600 hover:text-gray-800 @endif"
            >
                Aperçu
            </button>
            <button
                wire:click="switchTab('grades')"
                class="py-4 px-1 border-b-2 @if($activeTab === 'grades') border-blue-500 text-blue-600 @else border-transparent text-gray-600 hover:text-gray-800 @endif"
            >
                Notes
            </button>
            <button
                wire:click="switchTab('attendance')"
                class="py-4 px-1 border-b-2 @if($activeTab === 'attendance') border-blue-500 text-blue-600 @else border-transparent text-gray-600 hover:text-gray-800 @endif"
            >
                Présences
            </button>
            <button
                wire:click="switchTab('billing')"
                class="py-4 px-1 border-b-2 @if($activeTab === 'billing') border-blue-500 text-blue-600 @else border-transparent text-gray-600 hover:text-gray-800 @endif"
            >
                Facturation
            </button>
            @if($isChefClasse)
            <button
                wire:click="switchTab('chef-classe')"
                class="py-4 px-1 border-b-2 @if($activeTab === 'chef-classe') border-blue-500 text-blue-600 @else border-transparent text-gray-600 hover:text-gray-800 @endif"
            >
                <i class="fas fa-user-tie mr-2"></i>Chef de Classe
            </button>
            @endif
        </div>
    </div>

    <!-- Tab Content -->
    <div class="tab-content">
        @if($activeTab === 'overview')
            <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
                <livewire:student-grades-section />
                <livewire:student-attendance-section />
                <livewire:student-class-section />
                <livewire:student-billing-section />
            </div>
        @elseif($activeTab === 'grades')
            <livewire:student-grades-section />
        @elseif($activeTab === 'attendance')
            <livewire:student-attendance-section />
        @elseif($activeTab === 'billing')
            <livewire:student-billing-section />
        @elseif($activeTab === 'chef-classe' && $isChefClasse)
            <livewire:chef-classe-section />
        @endif
    </div>
</div>
