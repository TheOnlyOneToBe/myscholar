@extends('app::layouts.app')

@section('content')
<div class="container-fluid py-4">
    <div class="row mb-4">
        <div class="col">
            <h1 class="h3 font-weight-bold text-gray-800">{{ __('dashboard::views.teacher.profile') }}</h1>
        </div>
    </div>

    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-body">
                    <p class="text-muted">{{ __('dashboard::views.teacher.profile_content') }}</p>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
