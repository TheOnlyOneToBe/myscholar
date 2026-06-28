<div class="container-fluid">
    @if($teacher)
        <!-- Quick Stats Row -->
        <div class="row mb-4">
            <div class="col-md-3">
                <div class="card border-left-primary shadow h-100 py-2">
                    <div class="card-body">
                        <div class="text-primary text-uppercase mb-1 font-weight-bold text-xs">{{ __('dashboard::views.teacher.classes') }}</div>
                        <div class="h3 mb-0 font-weight-bold text-gray-800">{{ $classesCount }}</div>
                    </div>
                </div>
            </div>

            <div class="col-md-3">
                <div class="card border-left-success shadow h-100 py-2">
                    <div class="card-body">
                        <div class="text-success text-uppercase mb-1 font-weight-bold text-xs">{{ __('dashboard::views.teacher.students') }}</div>
                        <div class="h3 mb-0 font-weight-bold text-gray-800">{{ $studentsCount }}</div>
                    </div>
                </div>
            </div>

            <div class="col-md-3">
                <div class="card border-left-info shadow h-100 py-2">
                    <div class="card-body">
                        <div class="text-info text-uppercase mb-1 font-weight-bold text-xs">{{ __('dashboard::views.teacher.average_grade') }}</div>
                        <div class="h3 mb-0 font-weight-bold text-gray-800">{{ $averageClassGrade }}/20</div>
                    </div>
                </div>
            </div>

            <div class="col-md-3">
                <div class="card border-left-warning shadow h-100 py-2">
                    <div class="card-body">
                        <div class="text-warning text-uppercase mb-1 font-weight-bold text-xs">{{ __('dashboard::views.teacher.actions') }}</div>
                        <div class="btn-group btn-group-sm" role="group">
                            <a href="{{ route('teacher.classes') }}" class="btn btn-outline-primary btn-sm">{{ __('dashboard::views.teacher.view_classes') }}</a>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Classes List Row -->
        <div class="row">
            <div class="col-12">
                <div class="card shadow mb-4">
                    <div class="card-header py-3 d-flex flex-row align-items-center justify-content-between">
                        <h6 class="m-0 font-weight-bold text-primary">{{ __('dashboard::views.teacher.active_classes') }}</h6>
                    </div>
                    <div class="card-body">
                        @if(count($upcomingClasses) > 0)
                            <div class="table-responsive">
                                <table class="table table-hover">
                                    <thead>
                                        <tr>
                                            <th>{{ __('dashboard::views.teacher.class_name') }}</th>
                                            <th>{{ __('dashboard::views.teacher.level') }}</th>
                                            <th>{{ __('dashboard::views.teacher.students_count') }}</th>
                                            <th>{{ __('dashboard::views.teacher.actions') }}</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach($upcomingClasses as $class)
                                            <tr>
                                                <td><strong>{{ $class['name'] }}</strong></td>
                                                <td>{{ $class['level'] }}</td>
                                                <td><span class="badge badge-primary">{{ $class['students_count'] }}</span></td>
                                                <td>
                                                    <a href="{{ route('teacher.classes') }}" class="btn btn-sm btn-info">
                                                        <i class="fas fa-eye"></i> {{ __('dashboard::views.teacher.view') }}
                                                    </a>
                                                </td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                        @else
                            <div class="alert alert-info" role="alert">
                                {{ __('dashboard::views.teacher.no_classes') }}
                            </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>

        <!-- Teacher Info Row -->
        <div class="row">
            <div class="col-md-6">
                <div class="card shadow mb-4">
                    <div class="card-header py-3">
                        <h6 class="m-0 font-weight-bold text-primary">{{ __('dashboard::views.teacher.profile_info') }}</h6>
                    </div>
                    <div class="card-body">
                        <p><strong>{{ __('dashboard::views.labels.specialization') }}:</strong> {{ $teacher->specialization ?? 'N/A' }}</p>
                        <p><strong>{{ __('dashboard::views.labels.qualification_level') }}:</strong> {{ $teacher->qualification_level ?? 'N/A' }}</p>
                        <p><strong>{{ __('dashboard::views.labels.years_of_experience') }}:</strong> {{ $teacher->years_of_experience ?? 0 }} {{ __('dashboard::views.teacher.years') }}</p>
                        <p><strong>{{ __('dashboard::views.labels.office_location') }}:</strong> {{ $teacher->office_location ?? 'N/A' }}</p>
                    </div>
                </div>
            </div>

            <div class="col-md-6">
                <div class="card shadow mb-4">
                    <div class="card-header py-3">
                        <h6 class="m-0 font-weight-bold text-primary">{{ __('dashboard::views.teacher.contact_info') }}</h6>
                    </div>
                    <div class="card-body">
                        <p><strong>{{ __('dashboard::views.labels.email_office') }}:</strong> <a href="mailto:{{ $teacher->email_office }}">{{ $teacher->email_office ?? 'N/A' }}</a></p>
                        <p><strong>{{ __('dashboard::views.labels.phone_office') }}:</strong> {{ $teacher->phone_office ?? 'N/A' }}</p>
                        <p><strong>{{ __('dashboard::views.labels.status') }}:</strong>
                            @if($teacher->is_active)
                                <span class="badge badge-success">{{ __('dashboard::views.teacher.active') }}</span>
                            @else
                                <span class="badge badge-danger">{{ __('dashboard::views.teacher.inactive') }}</span>
                            @endif
                        </p>
                    </div>
                </div>
            </div>
        </div>
    @else
        <div class="alert alert-warning" role="alert">
            {{ __('dashboard::views.teacher.no_profile') }}
        </div>
    @endif
</div>
