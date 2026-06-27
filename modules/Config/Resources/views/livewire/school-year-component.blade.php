<div class="school-year-management">
    <!-- Header avec info années en cours et session -->
    <div class="row mb-4">
        <div class="col-md-6">
            <div class="card border-primary">
                <div class="card-header bg-primary text-white">
                    <h6 class="mb-0">📅 Année Scolaire en Cours</h6>
                </div>
                <div class="card-body">
                    @if ($activeYear)
                        <div class="d-flex justify-content-between align-items-start">
                            <div>
                                <h5 class="mb-2">{{ $activeYear->name }}</h5>
                                <p class="mb-1">
                                    <small class="text-muted">
                                        Du {{ $activeYear->start_date->format('d/m/Y') }}
                                        au {{ $activeYear->end_date->format('d/m/Y') }}
                                    </small>
                                </p>
                                @if ($activeYear->description)
                                    <p class="mb-0"><small>{{ $activeYear->description }}</small></p>
                                @endif
                            </div>
                            <span class="badge bg-success">Active</span>
                        </div>
                    @else
                        <p class="text-danger mb-0">
                            <i class="fas fa-exclamation-circle"></i> Aucune année scolaire active configurée
                        </p>
                    @endif
                </div>
            </div>
        </div>

        <div class="col-md-6">
            <div class="card border-info">
                <div class="card-header bg-info text-white">
                    <h6 class="mb-0">🎯 Année Scolaire en Session</h6>
                </div>
                <div class="card-body">
                    @if ($sessionYear)
                        <div class="d-flex justify-content-between align-items-start">
                            <div>
                                <h5 class="mb-2">{{ $sessionYear->name }}</h5>
                                <p class="mb-1">
                                    <small class="text-muted">
                                        Du {{ $sessionYear->start_date->format('d/m/Y') }}
                                        au {{ $sessionYear->end_date->format('d/m/Y') }}
                                    </small>
                                </p>
                            </div>
                            <span class="badge bg-info">Session</span>
                        </div>
                        <p class="mb-0 mt-2">
                            <small class="text-muted">
                                (Année utilisée pour les opérations courantes)
                            </small>
                        </p>
                    @else
                        <p class="text-warning mb-0">
                            <i class="fas fa-exclamation-triangle"></i> Aucune année sélectionnée en session
                        </p>
                    @endif
                </div>
            </div>
        </div>
    </div>

    <!-- Boutons d'action -->
    <div class="row mb-4">
        <div class="col-12">
            <button wire:click="toggleForm" class="btn btn-primary">
                <i class="fas fa-plus-circle"></i> {{ $showForm ? 'Annuler' : 'Nouvelle Année' }}
            </button>
        </div>
    </div>

    <!-- Formulaire de création/édition -->
    @if ($showForm)
        <div class="card mb-4 border-warning">
            <div class="card-header bg-warning text-dark">
                <h5 class="mb-0">
                    {{ $editingYear ? '✏️ Modifier une Année' : '➕ Créer une Nouvelle Année' }}
                </h5>
            </div>
            <div class="card-body">
                <form wire:submit.prevent="save">
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Nom de l'année *</label>
                            <input
                                type="text"
                                wire:model="name"
                                class="form-control @error('name') is-invalid @enderror"
                                placeholder="Ex: 2024-2025"
                            >
                            @error('name')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="col-md-3 mb-3">
                            <label class="form-label">Année de début *</label>
                            <input
                                type="number"
                                wire:model="start_year"
                                class="form-control @error('start_year') is-invalid @enderror"
                                min="1900"
                                max="2100"
                                placeholder="2024"
                            >
                            @error('start_year')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="col-md-3 mb-3">
                            <label class="form-label">Année de fin *</label>
                            <input
                                type="number"
                                wire:model="end_year"
                                class="form-control @error('end_year') is-invalid @enderror"
                                min="1900"
                                max="2100"
                                placeholder="2025"
                            >
                            @error('end_year')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Date de début *</label>
                            <input
                                type="date"
                                wire:model="start_date"
                                class="form-control @error('start_date') is-invalid @enderror"
                            >
                            @error('start_date')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="col-md-6 mb-3">
                            <label class="form-label">Date de fin *</label>
                            <input
                                type="date"
                                wire:model="end_date"
                                class="form-control @error('end_date') is-invalid @enderror"
                            >
                            @error('end_date')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Description</label>
                        <textarea
                            wire:model="description"
                            class="form-control"
                            rows="2"
                            placeholder="Notes ou description..."
                        ></textarea>
                    </div>

                    <div class="d-flex gap-2">
                        <button type="submit" class="btn btn-success">
                            <i class="fas fa-save"></i> {{ $editingYear ? 'Modifier' : 'Créer' }}
                        </button>
                        <button type="button" wire:click="toggleForm" class="btn btn-secondary">
                            <i class="fas fa-times"></i> Annuler
                        </button>
                    </div>
                </form>
            </div>
        </div>
    @endif

    <!-- Liste des années scolaires -->
    <div class="card">
        <div class="card-header">
            <h5 class="mb-0">📋 Liste des Années Scolaires</h5>
        </div>
        <div class="card-body">
            @if ($schoolYears->count() > 0)
                <div class="table-responsive">
                    <table class="table table-hover">
                        <thead>
                            <tr>
                                <th>Année</th>
                                <th>Période</th>
                                <th>Années</th>
                                <th>Statut</th>
                                <th style="width: 250px;">Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($schoolYears as $year)
                                <tr class="@if ($year->is_active) table-success @endif">
                                    <td>
                                        <strong>{{ $year->name }}</strong>
                                        @if ($year->is_active)
                                            <span class="badge bg-success ms-2">Active</span>
                                        @endif
                                        @if ($sessionYear?->id === $year->id)
                                            <span class="badge bg-info ms-1">Session</span>
                                        @endif
                                    </td>
                                    <td>
                                        <small class="text-muted">
                                            {{ $year->start_date->format('d/m/Y') }} - {{ $year->end_date->format('d/m/Y') }}
                                        </small>
                                    </td>
                                    <td>
                                        <small>{{ $year->start_year }}-{{ $year->end_year }}</small>
                                    </td>
                                    <td>
                                        @if ($year->is_locked)
                                            <span class="badge bg-secondary">Archivée</span>
                                        @elseif ($year->is_active)
                                            <span class="badge bg-success">En cours</span>
                                        @else
                                            <span class="badge bg-light text-dark">Disponible</span>
                                        @endif
                                    </td>
                                    <td>
                                        <div class="btn-group btn-group-sm" role="group">
                                            @if (!$year->is_active)
                                                <button
                                                    wire:click="activateYear({{ $year->id }})"
                                                    class="btn btn-outline-success"
                                                    title="Activer cette année"
                                                >
                                                    <i class="fas fa-play"></i> Activer
                                                </button>
                                            @endif

                                            @if ($sessionYear?->id !== $year->id)
                                                <button
                                                    wire:click="switchSession({{ $year->id }})"
                                                    class="btn btn-outline-info"
                                                    title="Sélectionner pour la session courante"
                                                >
                                                    <i class="fas fa-exchange"></i> Session
                                                </button>
                                            @else
                                                <button
                                                    class="btn btn-outline-info disabled"
                                                    disabled
                                                >
                                                    <i class="fas fa-check"></i> Session
                                                </button>
                                            @endif

                                            <button
                                                wire:click="startEdit({{ $year->id }})"
                                                class="btn btn-outline-primary"
                                                title="Modifier cette année"
                                            >
                                                <i class="fas fa-edit"></i> Modifier
                                            </button>

                                            @if (!$year->is_active)
                                                <button
                                                    wire:click="deleteYear({{ $year->id }})"
                                                    wire:confirm="Êtes-vous sûr de vouloir supprimer cette année?"
                                                    class="btn btn-outline-danger"
                                                    title="Supprimer cette année"
                                                >
                                                    <i class="fas fa-trash"></i> Supprimer
                                                </button>
                                            @else
                                                <button
                                                    class="btn btn-outline-danger disabled"
                                                    disabled
                                                    title="Impossible de supprimer l'année active"
                                                >
                                                    <i class="fas fa-trash"></i> Supprimer
                                                </button>
                                            @endif
                                        </div>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @else
                <div class="alert alert-info mb-0">
                    <i class="fas fa-info-circle"></i> Aucune année scolaire créée. Commencez par en créer une.
                </div>
            @endif
        </div>
    </div>
</div>

<style>
    .school-year-management .card {
        box-shadow: 0 0.125rem 0.25rem rgba(0, 0, 0, 0.075);
    }

    .school-year-management .badge {
        font-size: 0.75rem;
        padding: 0.35rem 0.6rem;
    }

    .school-year-management .btn-group-sm {
        flex-wrap: wrap;
    }

    .school-year-management table tbody tr {
        transition: background-color 0.2s ease;
    }

    .school-year-management table tbody tr:hover {
        background-color: rgba(0, 0, 0, 0.02);
    }
</style>
