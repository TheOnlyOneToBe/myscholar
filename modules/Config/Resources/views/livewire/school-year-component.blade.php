<div class="school-year-management">
    <!-- Header avec info années en cours et session -->
    <div class="row mb-4">
        <div class="col-md-6">
            <div class="card border-primary">
                <div class="card-header bg-primary text-white">
                    <h6 class="mb-0">📅 {{ __('config.labels.active_year') }}</h6>
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
                            <i class="fas fa-exclamation-circle"></i> {{ __('config.errors.no_school_year_available') }}
                        </p>
                    @endif
                </div>
            </div>
        </div>

        <div class="col-md-6">
            <div class="card border-info">
                <div class="card-header bg-info text-white">
                    <h6 class="mb-0">🎯 {{ __('config.labels.session_year') }}</h6>
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
                                {{ __('config.messages_ui.session_description') }}
                            </small>
                        </p>
                    @else
                        <p class="text-warning mb-0">
                            <i class="fas fa-exclamation-triangle"></i> {{ __('config.errors.no_school_year_available') }}
                        </p>
                    @endif
                </div>
            </div>
        </div>
    </div>

    <!-- Boutons d'action -->
    @if($canCreate)
        <div class="row mb-4">
            <div class="col-12">
                <button wire:click="toggleForm" class="btn btn-primary">
                    <i class="fas fa-plus-circle"></i> {{ $showForm ? __('config.labels.create_year') : __('config.labels.create_year') }}
                </button>
            </div>
        </div>
    @endif

    <!-- Formulaire de création/édition -->
    @if ($showForm)
        <div class="card mb-4 border-warning">
            <div class="card-header bg-warning text-dark">
                <h5 class="mb-0">
                    {{ $editingYear ? '✏️ ' . __('config.labels.edit_year') : '➕ ' . __('config.labels.create_year') }}
                </h5>
            </div>
            <div class="card-body">
                <form wire:submit.prevent="save">
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="form-label">{{ __('config.labels.school_year_name') }} *</label>
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
                            <label class="form-label">{{ __('config.labels.start_year') }} *</label>
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
                            <label class="form-label">{{ __('config.labels.end_year') }} *</label>
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
                            <label class="form-label">{{ __('config.labels.start_date') }} *</label>
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
                            <label class="form-label">{{ __('config.labels.end_date') }} *</label>
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
                        <label class="form-label">{{ __('config.labels.description') }}</label>
                        <textarea
                            wire:model="description"
                            class="form-control"
                            rows="2"
                            placeholder="{{ __('config.labels.description') }}..."
                        ></textarea>
                    </div>

                    <div class="d-flex gap-2">
                        <button type="submit" class="btn btn-success">
                            <i class="fas fa-save"></i> {{ $editingYear ? __('config.labels.edit_year') : __('config.labels.create_year') }}
                        </button>
                        <button type="button" wire:click="toggleForm" class="btn btn-secondary">
                            <i class="fas fa-times"></i> {{ __('auth.messages.cancel') ?? 'Annuler' }}
                        </button>
                    </div>
                </form>
            </div>
        </div>
    @endif

    <!-- Liste des années scolaires -->
    <div class="card">
        <div class="card-header">
            <h5 class="mb-0">📋 {{ __('config.labels.school_years') }}</h5>
        </div>
        <div class="card-body">
            @if ($schoolYears->count() > 0)
                <div class="table-responsive">
                    <table class="table table-hover">
                        <thead>
                            <tr>
                                <th>{{ __('config.labels.school_years') }}</th>
                                <th>{{ __('config.labels.period') }}</th>
                                <th>{{ __('config.labels.year_status') }}</th>
                                <th style="width: 250px;">{{ __('config.labels.actions') }}</th>
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
                                        @if ($year->is_locked)
                                            <span class="badge bg-secondary">{{ __('config.messages.archived') ?? 'Archivée' }}</span>
                                        @elseif ($year->is_active)
                                            <span class="badge bg-success">{{ __('config.messages.active') ?? 'En cours' }}</span>
                                        @else
                                            <span class="badge bg-light text-dark">{{ __('config.messages.available') ?? 'Disponible' }}</span>
                                        @endif
                                    </td>
                                    <td>
                                        <div class="btn-group btn-group-sm" role="group">
                                            @if (!$year->is_active && $canEdit)
                                                <button
                                                    wire:click="activateYear({{ $year->id }})"
                                                    class="btn btn-outline-success"
                                                    title="{{ __('config.labels.activate_year') }}"
                                                >
                                                    <i class="fas fa-play"></i> {{ __('config.labels.activate_year') }}
                                                </button>
                                            @endif

                                            @if ($sessionYear?->id !== $year->id && $canSwitch)
                                                <button
                                                    wire:click="switchSession({{ $year->id }})"
                                                    class="btn btn-outline-info"
                                                    title="{{ __('config.labels.switch_session') }}"
                                                >
                                                    <i class="fas fa-exchange"></i> {{ __('config.labels.switch_session') }}
                                                </button>
                                            @elseif ($sessionYear?->id === $year->id)
                                                <button
                                                    class="btn btn-outline-info disabled"
                                                    disabled
                                                    title="{{ __('config.labels.switch_session') }}"
                                                >
                                                    <i class="fas fa-check"></i> {{ __('config.labels.switch_session') }}
                                                </button>
                                            @endif

                                            @if ($canEdit)
                                                <button
                                                    wire:click="startEdit({{ $year->id }})"
                                                    class="btn btn-outline-primary"
                                                    title="{{ __('config.labels.edit_year') }}"
                                                >
                                                    <i class="fas fa-edit"></i> {{ __('config.labels.edit_year') }}
                                                </button>
                                            @endif

                                            @if (!$year->is_active && $canDelete)
                                                <button
                                                    wire:click="deleteYear({{ $year->id }})"
                                                    wire:confirm="{{ __('config.messages.confirm_delete') ?? 'Êtes-vous sûr?' }}"
                                                    class="btn btn-outline-danger"
                                                    title="{{ __('config.labels.delete_year') }}"
                                                >
                                                    <i class="fas fa-trash"></i> {{ __('config.labels.delete_year') }}
                                                </button>
                                            @elseif ($year->is_active)
                                                <button
                                                    class="btn btn-outline-danger disabled"
                                                    disabled
                                                    title="{{ __('config.alerts.cannot_delete_active') }}"
                                                >
                                                    <i class="fas fa-trash"></i> {{ __('config.labels.delete_year') }}
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
                    <i class="fas fa-info-circle"></i> {{ __('config.messages_ui.no_years_created') }}
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
