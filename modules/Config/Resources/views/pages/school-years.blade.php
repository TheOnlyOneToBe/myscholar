<x-layouts.app>
    <div class="container-fluid py-4">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <div>
                <h1 class="h3">
                    <i class="fas fa-calendar"></i> Gestion des Années Scolaires
                </h1>
                <p class="text-muted">Configurez et gérez les années scolaires de votre établissement</p>
            </div>
        </div>

        @livewire('config::school-year-component')
    </div>

    <style>
        body {
            background-color: #f8f9fa;
        }

        .container-fluid {
            background-color: #ffffff;
            border-radius: 0.25rem;
        }
    </style>
</x-layouts.app>
