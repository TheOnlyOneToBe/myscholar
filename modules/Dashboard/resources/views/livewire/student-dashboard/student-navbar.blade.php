<nav class="bg-white shadow-md border-b border-gray-200">
    <div class="px-6 py-4">
        <div class="flex justify-between items-center">
            <!-- Left: School Logo/Name -->
            <div class="flex items-center space-x-3">
                <div class="h-10 w-10 bg-blue-600 rounded-lg flex items-center justify-center text-white font-bold">
                    <i class="fas fa-school"></i>
                </div>
                <div>
                    <h1 class="text-lg font-bold text-gray-800">MyScholar</h1>
                    <p class="text-xs text-gray-500">Portail Étudiant</p>
                </div>
            </div>

            <!-- Center: Breadcrumb (optional) -->
            <div class="hidden lg:flex items-center text-sm text-gray-600">
                <span>{{ $currentClass }}</span>
                <span class="mx-2">•</span>
                <span>{{ $studentMatricule }}</span>
            </div>

            <!-- Right: Actions -->
            <div class="flex items-center space-x-4">
                <!-- Notifications Bell -->
                <div class="relative">
                    <button
                        wire:click="toggleNotifications"
                        class="relative p-2 text-gray-600 hover:text-gray-900 hover:bg-gray-100 rounded-lg transition"
                    >
                        <i class="fas fa-bell w-6 h-6"></i>
                        @if($notificationCount > 0)
                            <span class="absolute top-0 right-0 inline-flex items-center justify-center px-2 py-1 text-xs font-bold leading-none text-white transform translate-x-1/2 -translate-y-1/2 bg-red-600 rounded-full">
                                {{ $notificationCount }}
                            </span>
                        @endif
                    </button>

                    <!-- Notifications Dropdown -->
                    @if($showNotifications)
                        <div class="absolute right-0 mt-2 w-80 bg-white rounded-lg shadow-xl z-50 border border-gray-200">
                            <div class="p-4 border-b border-gray-200">
                                <h3 class="font-semibold text-gray-800">Notifications</h3>
                            </div>
                            <div class="max-h-96 overflow-y-auto">
                                @if($notificationCount === 0)
                                    <div class="p-4 text-center text-gray-500">
                                        Aucune notification
                                    </div>
                                @else
                                    <!-- Notifications will be loaded from Notifications module -->
                                    <div class="p-4 text-sm text-gray-600">
                                        Les notifications s'afficheront ici
                                    </div>
                                @endif
                            </div>
                        </div>
                    @endif
                </div>

                <!-- Profile Dropdown -->
                <div class="relative">
                    <button
                        wire:click="toggleProfileDropdown"
                        class="flex items-center space-x-2 p-2 hover:bg-gray-100 rounded-lg transition"
                    >
                        <div class="h-8 w-8 bg-gradient-to-br from-blue-500 to-purple-600 rounded-full flex items-center justify-center text-white text-sm font-bold">
                            {{ substr($studentName, 0, 1) }}
                        </div>
                        <span class="hidden sm:inline text-sm font-medium text-gray-700">{{ $studentName }}</span>
                        <i class="fas fa-chevron-down w-4 h-4 text-gray-600"></i>
                    </button>

                    <!-- Profile Dropdown Menu -->
                    @if($showProfileDropdown)
                        <div class="absolute right-0 mt-2 w-48 bg-white rounded-lg shadow-xl z-50 border border-gray-200">
                            <div class="p-4 border-b border-gray-200">
                                <p class="text-sm font-semibold text-gray-800">{{ $studentName }}</p>
                                <p class="text-xs text-gray-500">{{ $studentMatricule }}</p>
                            </div>
                            <div class="py-2">
                                <a href="{{ route('student.profile') }}" class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100 transition">
                                    Mon Profil
                                </a>
                                <a href="{{ route('student.settings') }}" class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100 transition">
                                    Paramètres
                                </a>
                                <a href="{{ route('student.help') }}" class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100 transition">
                                    Aide
                                </a>
                            </div>
                            <div class="border-t border-gray-200 py-2">
                                <button
                                    wire:click="logout"
                                    class="block w-full text-left px-4 py-2 text-sm text-red-600 hover:bg-red-50 transition"
                                >
                                    Déconnexion
                                </button>
                            </div>
                        </div>
                    @endif
                </div>

                <!-- Mobile Menu Toggle (if sidebar is togglable) -->
                <button
                    class="lg:hidden p-2 text-gray-600 hover:text-gray-900 hover:bg-gray-100 rounded-lg transition"
                >
                    <i class="fas fa-bars w-6 h-6"></i>
                </button>
            </div>
        </div>
    </div>

    <!-- Role Badge -->
    @if($isChefClasse)
        <div class="bg-blue-50 border-t border-blue-200 px-6 py-2">
            <div class="flex items-center space-x-2">
                <span class="inline-block px-3 py-1 bg-blue-600 text-white text-xs font-semibold rounded-full">
                    <i class="fas fa-user-tie mr-1"></i>Chef de Classe
                </span>
                <span class="text-xs text-blue-700">Accès lecture aux données de votre classe</span>
            </div>
        </div>
    @endif
</nav>
