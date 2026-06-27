@extends('layouts.app')

@section('content')
<div class="py-12">
    @livewire('classes.timetable', ['class' => app(\Modules\Classes\Models\ClassModel::class)->find(request()->route('class'))])
</div>
@endsection
