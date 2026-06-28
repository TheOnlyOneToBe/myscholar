<aside class="bg-gray-900 text-white w-64 min-h-screen fixed left-0 top-0 pt-20 shadow-lg" :class="{ 'hidden': !sidebarOpen }">
    <div class="p-6 overflow-y-auto h-screen pb-20">
        <!-- Main Navigation -->
        <nav class="space-y-2">
            <div class="mb-6">
                <h3 class="text-xs font-semibold text-gray-400 uppercase tracking-wide mb-3">
                    Général
                </h3>

                <!-- Dashboard/Home -->
                <a
                    href="#"
                    wire:click="selectTab('overview')"
                    class="flex items-center space-x-3 px-4 py-3 rounded-lg @if($activeTab === 'overview') bg-blue-600 @else hover:bg-gray-800 @endif transition"
                >
                    <i class="fas fa-home w-5 h-5"></i>
                    <span>Accueil</span>
                </a>
            </div>

            <!-- Children Section -->
            <div class="mb-6">
                <h3 class="text-xs font-semibold text-gray-400 uppercase tracking-wide mb-3">
                    Mes Enfants
                </h3>

                @if(!empty($children))
                    @foreach($children as $child)
                        <button
                            wire:click="selectTab('child-overview', {{ $child['id'] }})"
                            class="w-full flex items-center space-x-3 px-4 py-3 rounded-lg mb-2 @if($activeChild === $child['id'] && $activeTab === 'child-overview') bg-blue-600 @else hover:bg-gray-800 @endif transition"
                        >
                            <div class="h-8 w-8 bg-gradient-to-br from-purple-500 to-pink-600 rounded-full flex items-center justify-center text-white text-sm font-bold flex-shrink-0">
                                {{ substr($child['first_name'], 0, 1) }}
                            </div>
                            <div class="text-left flex-1">
                                <span class="block text-sm">{{ $child['first_name'] }}</span>
                                <span class="block text-xs text-gray-400">{{ $child['current_class'] ?? 'N/A' }}</span>
                            </div>
                        </button>
                    @endforeach
                @else
                    <p class="text-sm text-gray-400 px-4 py-3">Aucun enfant associé</p>
                @endif
            </div>

            <!-- Academic Section -->
            <div class="mb-6">
                <h3 class="text-xs font-semibold text-gray-400 uppercase tracking-wide mb-3">
                    Académique
                </h3>

                <a
                    href="#"
                    wire:click="selectTab('grades')"
                    class="flex items-center space-x-3 px-4 py-3 rounded-lg @if($activeTab === 'grades') bg-blue-600 @else hover:bg-gray-800 @endif transition"
                >
                    <i class="fas fa-chart-bar w-5 h-5"></i>
                    <span>Notes</span>
                </a>

                <a
                    href="#"
                    wire:click="selectTab('attendance')"
                    class="flex items-center space-x-3 px-4 py-3 rounded-lg mt-2 @if($activeTab === 'attendance') bg-blue-600 @else hover:bg-gray-800 @endif transition"
                >
                    <i class="fas fa-calendar-check w-5 h-5"></i>
                    <span>Présences</span>
                </a>

                <a
                    href="#"
                    wire:click="selectTab('bulletins')"
                    class="flex items-center space-x-3 px-4 py-3 rounded-lg mt-2 @if($activeTab === 'bulletins') bg-blue-600 @else hover:bg-gray-800 @endif transition"
                >
                    <i class="fas fa-file-pdf w-5 h-5"></i>
                    <span>Bulletins</span>
                </a>

                <a
                    href="#"
                    wire:click="selectTab('documents')"
                    class="flex items-center space-x-3 px-4 py-3 rounded-lg mt-2 @if($activeTab === 'documents') bg-blue-600 @else hover:bg-gray-800 @endif transition"
                >
                    <i class="fas fa-folder-open w-5 h-5"></i>
                    <span>Documents</span>
                </a>
            </div>

            <!-- Financial Section -->
            <div class="mb-6">
                <h3 class="text-xs font-semibold text-gray-400 uppercase tracking-wide mb-3">
                    Finances
                </h3>

                <a
                    href="#"
                    wire:click="selectTab('billing')"
                    class="flex items-center space-x-3 px-4 py-3 rounded-lg @if($activeTab === 'billing') bg-blue-600 @else hover:bg-gray-800 @endif transition"
                >
                    <i class="fas fa-money-bill-wave w-5 h-5"></i>
                    <span>Facturation</span>
                </a>
            </div>

            <!-- Alerts Section -->
            <div class="mb-6">
                <h3 class="text-xs font-semibold text-gray-400 uppercase tracking-wide mb-3">
                    Surveillance
                </h3>

                <a
                    href="#"
                    wire:click="selectTab('alerts')"
                    class="flex items-center space-x-3 px-4 py-3 rounded-lg @if($activeTab === 'alerts') bg-blue-600 @else hover:bg-gray-800 @endif transition"
                >
                    <i class="fas fa-bell w-5 h-5"></i>
                    <span>Alertes</span>
                </a>
            </div>

            <!-- Profile & Settings Section -->
            <div class="mb-6 pt-6 border-t border-gray-700">
                <h3 class="text-xs font-semibold text-gray-400 uppercase tracking-wide mb-3">
                    Compte
                </h3>

                <a
                    href="#"
                    class="flex items-center space-x-3 px-4 py-3 rounded-lg hover:bg-gray-800 transition"
                >
                    <i class="fas fa-user-circle w-5 h-5"></i>
                    <span>Mon Profil</span>
                </a>

                <a
                    href="#"
                    class="flex items-center space-x-3 px-4 py-3 rounded-lg hover:bg-gray-800 transition mt-2"
                >
                    <i class="fas fa-cog w-5 h-5"></i>
                    <span>Paramètres</span>
                </a>
            </div>
        </nav>
    </div>

    <!-- Footer Section -->
    <div class="absolute bottom-0 left-0 right-0 p-6 border-t border-gray-700 bg-gray-800">
        <div class="bg-gray-900 rounded-lg p-4">
            <p class="text-xs text-gray-400 mb-2">Version</p>
            <p class="text-sm font-semibold text-gray-200">MyScholar v2.0</p>
        </div>
    </div>
</aside>

<!-- Overlay for mobile -->
<div class="hidden lg:hidden fixed inset-0 bg-black bg-opacity-50 z-40" x-show="sidebarOpen"></div>
