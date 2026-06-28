<aside class="bg-gray-900 text-white w-64 min-h-screen fixed left-0 top-0 pt-20 shadow-lg" :class="{ 'hidden': !sidebarOpen }">
    <div class="p-6">
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

            <!-- Academics Section -->
            <div class="mb-6">
                <h3 class="text-xs font-semibold text-gray-400 uppercase tracking-wide mb-3">
                    Académique
                </h3>

                @if(in_array('Grades', $availableModules))
                    <a
                        href="#"
                        wire:click="selectTab('grades')"
                        class="flex items-center space-x-3 px-4 py-3 rounded-lg @if($activeTab === 'grades') bg-blue-600 @else hover:bg-gray-800 @endif transition"
                    >
                        <i class="fas fa-chart-bar w-5 h-5"></i>
                        <span>Mes Notes</span>
                    </a>
                @endif

                <!-- Attendance -->
                @if(in_array('Attendance', $availableModules))
                    <a
                        href="#"
                        wire:click="selectTab('attendance')"
                        class="flex items-center space-x-3 px-4 py-3 rounded-lg @if($activeTab === 'attendance') bg-blue-600 @else hover:bg-gray-800 @endif transition"
                    >
                        <i class="fas fa-calendar-check w-5 h-5"></i>
                        <span>Mes Absences</span>
                    </a>
                @endif
            </div>

            <!-- Financial Section -->
            <div class="mb-6">
                <h3 class="text-xs font-semibold text-gray-400 uppercase tracking-wide mb-3">
                    Finances
                </h3>

                @if(in_array('Billing', $availableModules))
                    <a
                        href="#"
                        wire:click="selectTab('billing')"
                        class="flex items-center space-x-3 px-4 py-3 rounded-lg @if($activeTab === 'billing') bg-blue-600 @else hover:bg-gray-800 @endif transition"
                    >
                        <i class="fas fa-money-bill-wave w-5 h-5"></i>
                        <span>Facturation</span>
                    </a>
                @endif
            </div>

            <!-- Class & Reports Section -->
            <div class="mb-6">
                <h3 class="text-xs font-semibold text-gray-400 uppercase tracking-wide mb-3">
                    Classe & Rapports
                </h3>

                @if(in_array('Classes', $availableModules))
                    <a
                        href="#"
                        wire:click="selectTab('class')"
                        class="flex items-center space-x-3 px-4 py-3 rounded-lg hover:bg-gray-800 transition"
                    >
                        <i class="fas fa-users w-5 h-5"></i>
                        <span>Ma Classe</span>
                    </a>
                @endif
            </div>

            <!-- Chef de Classe Section (if applicable) -->
            @if($isChefClasse)
                <div class="mb-6 pt-6 border-t border-gray-700">
                    <h3 class="text-xs font-semibold text-yellow-400 uppercase tracking-wide mb-3">
                        <i class="fas fa-user-tie mr-2"></i>Chef de Classe
                    </h3>

                    <!-- View Class Students -->
                    <a
                        href="#"
                        wire:click="selectTab('chef-classe-students')"
                        class="flex items-center space-x-3 px-4 py-3 rounded-lg hover:bg-gray-800 transition"
                    >
                        <i class="fas fa-user-friends w-5 h-5"></i>
                        <span>Élèves de la Classe</span>
                    </a>

                    <!-- Class Grades Analysis -->
                    <a
                        href="#"
                        wire:click="selectTab('chef-classe-grades')"
                        class="flex items-center space-x-3 px-4 py-3 rounded-lg hover:bg-gray-800 transition"
                    >
                        <i class="fas fa-chart-bar w-5 h-5"></i>
                        <span>Analyse des Notes</span>
                    </a>

                    <!-- Class Attendance Management -->
                    <a
                        href="#"
                        wire:click="selectTab('chef-classe-attendance')"
                        class="flex items-center space-x-3 px-4 py-3 rounded-lg hover:bg-gray-800 transition"
                    >
                        <i class="fas fa-calendar-times w-5 h-5"></i>
                        <span>Gestion des Absences</span>
                    </a>

                    <!-- Pending Justifications -->
                    <a
                        href="#"
                        wire:click="selectTab('chef-classe-justifications')"
                        class="flex items-center space-x-3 px-4 py-3 rounded-lg hover:bg-gray-800 transition"
                    >
                        <i class="fas fa-file-alt w-5 h-5"></i>
                        <span>Justifications en Attente</span>
                    </a>
                </div>
            @endif

            <!-- Profile & Settings Section -->
            <div class="mb-6 pt-6 border-t border-gray-700">
                <h3 class="text-xs font-semibold text-gray-400 uppercase tracking-wide mb-3">
                    Compte
                </h3>

                <a
                    href="{{ route('student.profile') }}"
                    class="flex items-center space-x-3 px-4 py-3 rounded-lg hover:bg-gray-800 transition"
                >
                    <i class="fas fa-user-circle w-5 h-5"></i>
                    <span>Mon Profil</span>
                </a>

                <a
                    href="{{ route('student.settings') }}"
                    class="flex items-center space-x-3 px-4 py-3 rounded-lg hover:bg-gray-800 transition"
                >
                    <i class="fas fa-cog w-5 h-5"></i>
                    <span>Paramètres</span>
                </a>

                <a
                    href="{{ route('student.help') }}"
                    class="flex items-center space-x-3 px-4 py-3 rounded-lg hover:bg-gray-800 transition"
                >
                    <i class="fas fa-question-circle w-5 h-5"></i>
                    <span>Aide</span>
                </a>
            </div>
        </nav>
    </div>

    <!-- Footer Section -->
    <div class="absolute bottom-0 left-0 right-0 p-6 border-t border-gray-700">
        <div class="bg-gray-800 rounded-lg p-4 mb-4">
            <p class="text-xs text-gray-400 mb-2">Version</p>
            <p class="text-sm font-semibold text-gray-200">MyScholar v2.0</p>
        </div>
    </div>
</aside>

<!-- Overlay for mobile (you might add this for mobile menu) -->
<div class="hidden lg:hidden fixed inset-0 bg-black bg-opacity-50 z-40" x-show="sidebarOpen"></div>
