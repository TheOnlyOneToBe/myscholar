<footer class="bg-gray-900 dark:bg-gray-950 text-gray-300 dark:text-gray-400 mt-12 border-t border-gray-700">
    <div class="container mx-auto px-4 py-8">
        <!-- Main Footer Content -->
        <div class="grid grid-cols-1 md:grid-cols-4 gap-8 mb-8">
            <!-- School Info -->
            @if ($schoolInfo)
                <div class="col-span-1">
                    @if ($schoolInfo->hasLogo())
                        <img src="{{ asset($schoolInfo->logo_path) }}"
                             alt="{{ $schoolInfo->name }}"
                             class="h-12 mb-4 brightness-0 invert">
                    @endif
                    <h3 class="text-white font-bold text-lg mb-2">{{ $schoolInfo->getFullName() }}</h3>
                    @if ($schoolInfo->motto)
                        <p class="text-sm italic text-gray-400">"{{ $schoolInfo->motto }}"</p>
                    @endif
                </div>
            @endif

            <!-- Address -->
            @if ($schoolInfo && $schoolInfo->getFullAddress())
                <div class="col-span-1">
                    <h4 class="text-white font-semibold mb-4"><i class="fas fa-map-marker-alt"></i> Adresse</h4>
                    <p class="text-sm text-gray-400">{{ $schoolInfo->getFullAddress() }}</p>
                    @if ($schoolInfo->po_box)
                        <p class="text-sm text-gray-400 mt-2">B.P. {{ $schoolInfo->po_box }}</p>
                    @endif
                </div>
            @endif

            <!-- Contact -->
            @if ($contactInfo)
                <div class="col-span-1">
                    <h4 class="text-white font-semibold mb-4"><i class="fas fa-phone"></i> Contact</h4>
                    <ul class="text-sm space-y-2">
                        @if ($contactInfo['phone'])
                            <li>
                                <a href="tel:{{ $contactInfo['phone'] }}" class="hover:text-white transition">
                                    {{ $contactInfo['phone'] }}
                                </a>
                            </li>
                        @endif
                        @if ($contactInfo['email'])
                            <li>
                                <a href="mailto:{{ $contactInfo['email'] }}" class="hover:text-white transition">
                                    {{ $contactInfo['email'] }}
                                </a>
                            </li>
                        @endif
                        @if ($contactInfo['website'])
                            <li>
                                <a href="{{ $contactInfo['website'] }}" target="_blank" class="hover:text-white transition">
                                    {{ str_replace(['http://', 'https://'], '', $contactInfo['website']) }}
                                </a>
                            </li>
                        @endif
                    </ul>
                </div>
            @endif

            <!-- Quick Links -->
            <div class="col-span-1">
                <h4 class="text-white font-semibold mb-4"><i class="fas fa-link"></i> Liens Utiles</h4>
                <ul class="text-sm space-y-2">
                    <li><a href="/" class="hover:text-white transition">Accueil</a></li>
                    <li><a href="/#about" class="hover:text-white transition">À Propos</a></li>
                    <li><a href="/#contact" class="hover:text-white transition">Contact</a></li>
                    <li><a href="/privacy" class="hover:text-white transition">Confidentialité</a></li>
                </ul>
            </div>
        </div>

        <!-- Divider -->
        <div class="border-t border-gray-700 my-8"></div>

        <!-- Bottom Footer -->
        <div class="flex flex-col md:flex-row justify-between items-center text-sm text-gray-500">
            <div>
                <p>© {{ $currentYear }} {{ $schoolInfo?->name ?? 'MyScholar' }}. Tous droits réservés.</p>
            </div>
            <div class="flex gap-4 mt-4 md:mt-0">
                <span>MyScholar v{{ $appVersion }}</span>
                <span>•</span>
                <span>Powered by <a href="#" class="hover:text-gray-300 transition">MyScholar Team</a></span>
            </div>
        </div>
    </div>
</footer>
