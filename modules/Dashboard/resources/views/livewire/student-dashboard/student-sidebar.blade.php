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
                    <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20">
                        <path d="M10.707 2.293a1 1 0 00-1.414 0l-7 7a1 1 0 001.414 1.414L4 10.414V17a1 1 0 001 1h2a1 1 0 001-1v-2a1 1 0 011-1h2a1 1 0 011 1v2a1 1 0 001 1h2a1 1 0 001-1v-6.586l.293.293a1 1 0 001.414-1.414l-7-7z"></path>
                    </svg>
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
                        <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20">
                            <path d="M2 11a1 1 0 011-1h2a1 1 0 011 1v5a1 1 0 01-1 1H3a1 1 0 01-1-1v-5zM8 7a1 1 0 011-1h2a1 1 0 011 1v9a1 1 0 01-1 1H9a1 1 0 01-1-1V7zM14 4a1 1 0 011-1h2a1 1 0 011 1v12a1 1 0 01-1 1h-2a1 1 0 01-1-1V4z"></path>
                        </svg>
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
                        <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M6 2a1 1 0 00-1 1v1H4a2 2 0 00-2 2v10a2 2 0 002 2h12a2 2 0 002-2V6a2 2 0 00-2-2h-1V3a1 1 0 10-2 0v1H7V3a1 1 0 00-1-1zm0 5a1 1 0 000 2h8a1 1 0 100-2H6z" clip-rule="evenodd"></path>
                        </svg>
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
                        <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20">
                            <path d="M4 4a2 2 0 00-2 2v4a2 2 0 002 2V6h10a2 2 0 00-2-2H4zm2 6a2 2 0 012-2h8a2 2 0 012 2v4a2 2 0 01-2 2H8a2 2 0 01-2-2v-4zm6 4a2 2 0 100-4 2 2 0 000 4z"></path>
                        </svg>
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
                        <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20">
                            <path d="M13 6a3 3 0 11-6 0 3 3 0 016 0zM18 8a2 2 0 11-4 0 2 2 0 014 0zM14 15a4 4 0 00-8 0v1h8v-1zM6 8a2 2 0 11-4 0 2 2 0 014 0zM16 18v-3a5.972 5.972 0 00-.75-2.906A3.005 3.005 0 0119 15v3h-3zM4.75 12.094A5.973 5.973 0 004 15v3H1v-3a3 3 0 013.75-2.906z"></path>
                        </svg>
                        <span>Ma Classe</span>
                    </a>
                @endif
            </div>

            <!-- Chef de Classe Section (if applicable) -->
            @if($isChefClasse)
                <div class="mb-6 pt-6 border-t border-gray-700">
                    <h3 class="text-xs font-semibold text-yellow-400 uppercase tracking-wide mb-3">
                        👨‍💼 Chef de Classe
                    </h3>

                    <!-- View Class Students -->
                    <a
                        href="#"
                        wire:click="selectTab('chef-classe-students')"
                        class="flex items-center space-x-3 px-4 py-3 rounded-lg hover:bg-gray-800 transition"
                    >
                        <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20">
                            <path d="M9 6a3 3 0 11-6 0 3 3 0 016 0zM9 11a6 6 0 01-6-6v1a3 3 0 003 3h6V8a3 3 0 003-3v-1a3 3 0 00-3-3h-2.5A1.5 1.5 0 004 2.5V4a6 6 0 006 6v-1z"></path>
                        </svg>
                        <span>Élèves de la Classe</span>
                    </a>

                    <!-- Class Grades Analysis -->
                    <a
                        href="#"
                        wire:click="selectTab('chef-classe-grades')"
                        class="flex items-center space-x-3 px-4 py-3 rounded-lg hover:bg-gray-800 transition"
                    >
                        <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20">
                            <path d="M2 11a1 1 0 011-1h2a1 1 0 011 1v5a1 1 0 01-1 1H3a1 1 0 01-1-1v-5zM8 7a1 1 0 011-1h2a1 1 0 011 1v9a1 1 0 01-1 1H9a1 1 0 01-1-1V7zM14 4a1 1 0 011-1h2a1 1 0 011 1v12a1 1 0 01-1 1h-2a1 1 0 01-1-1V4z"></path>
                        </svg>
                        <span>Analyse des Notes</span>
                    </a>

                    <!-- Class Attendance Management -->
                    <a
                        href="#"
                        wire:click="selectTab('chef-classe-attendance')"
                        class="flex items-center space-x-3 px-4 py-3 rounded-lg hover:bg-gray-800 transition"
                    >
                        <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M6 2a1 1 0 00-1 1v1H4a2 2 0 00-2 2v10a2 2 0 002 2h12a2 2 0 002-2V6a2 2 0 00-2-2h-1V3a1 1 0 10-2 0v1H7V3a1 1 0 00-1-1zm0 5a1 1 0 000 2h8a1 1 0 100-2H6z" clip-rule="evenodd"></path>
                        </svg>
                        <span>Gestion des Absences</span>
                    </a>

                    <!-- Pending Justifications -->
                    <a
                        href="#"
                        wire:click="selectTab('chef-classe-justifications')"
                        class="flex items-center space-x-3 px-4 py-3 rounded-lg hover:bg-gray-800 transition"
                    >
                        <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M4 4a2 2 0 012-2h4.586A2 2 0 0112 2.586L15.414 6A2 2 0 0116 7.414V16a2 2 0 01-2 2H6a2 2 0 01-2-2V4zm2 6a1 1 0 011-1h6a1 1 0 110 2H7a1 1 0 01-1-1zm0 3a1 1 0 011-1h6a1 1 0 110 2H7a1 1 0 01-1-1z" clip-rule="evenodd"></path>
                        </svg>
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
                    <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M10 9a3 3 0 100-6 3 3 0 000 6zm-7 9a7 7 0 1114 0H3z" clip-rule="evenodd"></path>
                    </svg>
                    <span>Mon Profil</span>
                </a>

                <a
                    href="{{ route('student.settings') }}"
                    class="flex items-center space-x-3 px-4 py-3 rounded-lg hover:bg-gray-800 transition"
                >
                    <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M11.49 3.17c-.38-1.56-2.6-1.56-2.98 0a1.532 1.532 0 01-2.286.948c-1.372-.836-2.942.734-2.106 2.106.54.886.061 2.042-.947 2.287-1.561.379-1.561 2.6 0 2.978a1.532 1.532 0 01.947 2.287c-.836 1.372.734 2.942 2.106 2.106a1.532 1.532 0 012.287.947c.379 1.561 2.6 1.561 2.978 0a1.533 1.533 0 012.287-.947c1.372.836 2.942-.734 2.106-2.106a1.533 1.533 0 01.947-2.287c1.561-.379 1.561-2.6 0-2.978a1.532 1.532 0 01-.947-2.287c.836-1.372-.734-2.942-2.106-2.106a1.532 1.532 0 01-2.287-.947zM10 13a3 3 0 100-6 3 3 0 000 6z" clip-rule="evenodd"></path>
                    </svg>
                    <span>Paramètres</span>
                </a>

                <a
                    href="{{ route('student.help') }}"
                    class="flex items-center space-x-3 px-4 py-3 rounded-lg hover:bg-gray-800 transition"
                >
                    <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z" clip-rule="evenodd"></path>
                    </svg>
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
