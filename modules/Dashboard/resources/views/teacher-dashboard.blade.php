@extends('app::layouts.app')

@section('content')
<div class="container-fluid py-4">
    <div class="row mb-4">
        <div class="col">
            <h1 class="h3 font-weight-bold text-gray-800">{{ __('dashboard::views.teacher.title', ['name' => auth()->user()->full_name ?? 'Enseignant']) }}</h1>
        </div>
    </div>

    @livewire('dashboard::teacher-dashboard')
</div>
@endsection
