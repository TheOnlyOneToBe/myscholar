<div class="chef-classe-section bg-white p-6 rounded-lg shadow">
    <h2 class="text-2xl font-bold mb-6"><i class="fas fa-user-tie mr-2"></i>{{ __('dashboard::views.chef_classe.title') }}</h2>

    @if(!$moduleAvailable)
        <div class="bg-yellow-50 border border-yellow-200 rounded-lg p-4">
            <p class="text-yellow-800">
                <strong>{{ __('dashboard::views.chef_classe.modules_missing') }}</strong> {{ $moduleError }}
            </p>
        </div>
    @else
        @if(!empty($chefClasseData))
            <!-- Class Overview -->
            <div class="bg-gradient-to-r from-purple-500 to-pink-500 text-white p-6 rounded-lg mb-6">
                <h3 class="text-xl font-bold mb-4">{{ $chefClasseData['class_name'] ?? 'N/A' }} - Gestion</h3>
                <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
                    <div>
                        <p class="text-sm opacity-90">{{ __('dashboard::views.chef_classe.attendance_to_record') }}</p>
                        <p class="text-3xl font-bold">{{ $chefClasseData['attendance_to_record_count'] ?? 0 }}</p>
                    </div>
                    <div>
                        <p class="text-sm opacity-90">{{ __('dashboard::views.chef_classe.pending_justifications') }}</p>
                        <p class="text-3xl font-bold">{{ $chefClasseData['pending_justifications'] ?? 0 }}</p>
                    </div>
                    <div>
                        <p class="text-sm opacity-90">{{ __('dashboard::views.chef_classe.class_average') }}</p>
                        <p class="text-3xl font-bold">{{ $chefClasseData['class_average'] ?? 0 }}/20</p>
                    </div>
                    <div>
                        <p class="text-sm opacity-90">{{ __('dashboard::views.chef_classe.attendance_rate') }}</p>
                        <p class="text-3xl font-bold">{{ $chefClasseData['attendance_rate_class'] ?? 0 }}%</p>
                    </div>
                </div>
            </div>

            <!-- Action Buttons for Chef de Classe -->
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-6">
                <div class="border-2 border-dashed border-gray-300 rounded-lg p-6 hover:border-blue-500 transition cursor-pointer">
                    <p class="font-bold text-lg mb-2">📝 {{ __('dashboard::views.chef_classe.record_attendance') }}</p>
                    <p class="text-sm text-gray-600">{{ __('dashboard::views.chef_classe.record_attendance_desc') }}</p>
                </div>

                <div class="border-2 border-dashed border-gray-300 rounded-lg p-6 hover:border-green-500 transition cursor-pointer">
                    <p class="font-bold text-lg mb-2"><i class="fas fa-check-circle"></i> {{ __('dashboard::views.chef_classe.approve_justifications') }}</p>
                    <p class="text-sm text-gray-600">{{ __('dashboard::views.chef_classe.approve_justifications_desc') }}</p>
                </div>

                <div class="border-2 border-dashed border-gray-300 rounded-lg p-6 hover:border-purple-500 transition cursor-pointer">
                    <p class="font-bold text-lg mb-2"><i class="fas fa-chart-bar"></i> {{ __('dashboard::views.chef_classe.view_statistics') }}</p>
                    <p class="text-sm text-gray-600">{{ __('dashboard::views.chef_classe.view_statistics_desc') }}</p>
                </div>

                <div class="border-2 border-dashed border-gray-300 rounded-lg p-6 hover:border-orange-500 transition cursor-pointer">
                    <p class="font-bold text-lg mb-2">📧 {{ __('dashboard::views.chef_classe.communicate') }}</p>
                    <p class="text-sm text-gray-600">{{ __('dashboard::views.chef_classe.communicate_desc') }}</p>
                </div>
            </div>

            <!-- Information Box -->
            <div class="bg-blue-50 border-l-4 border-blue-500 p-4 rounded">
                <p class="text-sm text-blue-800">
                    <strong>ℹ️ {{ __('dashboard::views.chef_classe.info_message') }}</strong>
                </p>
            </div>
        @else
            <div class="bg-gray-50 p-4 rounded-lg">
                <p class="text-gray-600">{{ __('dashboard::views.chef_classe.no_data') }}</p>
            </div>
        @endif
    @endif
</div>
