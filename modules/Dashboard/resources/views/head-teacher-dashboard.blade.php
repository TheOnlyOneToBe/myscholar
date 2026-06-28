@extends('app::layouts.app')

@section('content')
<div class="container-fluid py-4">
    <div class="row mb-4">
        <div class="col">
            <h1 class="h3 font-weight-bold text-gray-800">{{ __('dashboard::views.head_teacher.title', ['name' => auth()->user()->full_name ?? 'Professeur Principal']) }}</h1>
        </div>
    </div>

    @livewire('dashboard::head-teacher-dashboard')
</div>
@endsection
