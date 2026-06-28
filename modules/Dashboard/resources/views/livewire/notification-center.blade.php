<div class="relative">
    <!-- Notification Bell Button -->
    <button
        wire:click="toggleNotifications"
        class="relative p-2 text-gray-700 hover:text-indigo-600 transition-colors"
        title="Notifications"
    >
        <i class="fas fa-bell text-xl"></i>
        @if ($unreadCount > 0)
        <span class="absolute top-0 right-0 inline-flex items-center justify-center px-2 py-1 text-xs font-bold leading-none text-white transform translate-x-1/2 -translate-y-1/2 bg-red-500 rounded-full">
            {{ $unreadCount > 99 ? '99+' : $unreadCount }}
        </span>
        @endif
    </button>

    <!-- Notification Dropdown -->
    @if ($showNotifications)
    <div class="absolute right-0 mt-2 w-96 bg-white rounded-lg shadow-xl border border-gray-200 z-50 max-h-96 flex flex-col">
        <!-- Header -->
        <div class="flex items-center justify-between p-4 border-b border-gray-200 bg-gray-50 rounded-t-lg">
            <h3 class="text-lg font-bold text-gray-800"><i class="fas fa-bell mr-2"></i> Notifications</h3>
            <div class="flex gap-2">
                @if (count($notifications) > 0)
                <button
                    wire:click="clearAllNotifications"
                    class="text-sm text-red-600 hover:text-red-800 transition-colors"
                >
                    <i class="fas fa-trash mr-1"></i> Effacer tout
                </button>
                @endif
            </div>
        </div>

        <!-- Notifications List -->
        <div class="flex-1 overflow-y-auto">
            @if (count($notifications) === 0)
            <div class="p-8 text-center text-gray-500">
                <i class="fas fa-inbox text-3xl mb-2"></i>
                <p>Aucune notification</p>
            </div>
            @else
            <div class="divide-y">
                @foreach ($notifications as $notification)
                <div class="p-4 hover:bg-gray-50 transition-colors {{ !$notification['read'] ? 'bg-blue-50' : '' }}">
                    <div class="flex gap-3">
                        <!-- Icon -->
                        <div class="flex-shrink-0">
                            <div class="w-10 h-10 rounded-full flex items-center justify-center {{
                                $notification['color'] === 'danger' ? 'bg-red-100 text-red-600' :
                                ($notification['color'] === 'warning' ? 'bg-yellow-100 text-yellow-600' :
                                ($notification['color'] === 'success' ? 'bg-green-100 text-green-600' :
                                'bg-blue-100 text-blue-600'))
                            }}">
                                <i class="fas {{ $notification['icon'] }}"></i>
                            </div>
                        </div>

                        <!-- Content -->
                        <div class="flex-1 min-w-0">
                            <div class="flex items-start justify-between gap-2">
                                <div class="flex-1">
                                    <h4 class="font-semibold text-gray-800 text-sm">{{ $notification['title'] }}</h4>
                                    <p class="text-gray-600 text-xs mt-1">{{ $notification['message'] }}</p>
                                    <p class="text-gray-500 text-xs mt-2">
                                        <i class="fas fa-clock mr-1"></i>
                                        {{ \Carbon\Carbon::parse($notification['timestamp'])->diffForHumans() }}
                                    </p>
                                </div>
                                @if (!$notification['read'])
                                <button
                                    wire:click="markAsRead('{{ $notification['id'] }}')"
                                    class="text-blue-600 hover:text-blue-800 transition-colors"
                                    title="Marquer comme lu"
                                >
                                    <i class="fas fa-check"></i>
                                </button>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>
                @endforeach
            </div>
            @endif
        </div>

        <!-- Footer -->
        @if (count($notifications) > 0)
        <div class="p-4 border-t border-gray-200 bg-gray-50 rounded-b-lg text-center">
            <a href="#" class="text-sm text-indigo-600 hover:text-indigo-800 font-semibold">
                <i class="fas fa-eye mr-1"></i> Voir toutes les notifications
            </a>
        </div>
        @endif
    </div>
    @endif
</div>

<script>
    // Auto-dismiss notification dropdown when clicking outside
    document.addEventListener('click', function(event) {
        const notificationCenter = document.querySelector('[class*="notification-center"]');
        if (notificationCenter && !notificationCenter.contains(event.target)) {
            @this.set('showNotifications', false);
        }
    });

    // Listen for real-time notifications
    Livewire.on('studentNotified', (notification) => {
        // Show toast notification
        const toast = document.createElement('div');
        toast.className = 'fixed bottom-4 right-4 bg-white rounded-lg shadow-xl border border-gray-200 p-4 max-w-sm z-50 animate-slide-in';
        toast.innerHTML = `
            <div class="flex gap-3">
                <div class="w-10 h-10 rounded-full flex items-center justify-center ${
                    notification.color === 'danger' ? 'bg-red-100 text-red-600' :
                    (notification.color === 'warning' ? 'bg-yellow-100 text-yellow-600' :
                    (notification.color === 'success' ? 'bg-green-100 text-green-600' : 'bg-blue-100 text-blue-600'))
                }">
                    <i class="fas ${notification.icon}"></i>
                </div>
                <div class="flex-1">
                    <h4 class="font-semibold text-gray-800 text-sm">${notification.title}</h4>
                    <p class="text-gray-600 text-xs mt-1">${notification.message}</p>
                </div>
                <button onclick="this.parentElement.parentElement.remove()" class="text-gray-400 hover:text-gray-600">
                    <i class="fas fa-times"></i>
                </button>
            </div>
        `;
        document.body.appendChild(toast);

        // Auto-remove after 5 seconds
        setTimeout(() => {
            toast.remove();
        }, 5000);
    });
</script>
