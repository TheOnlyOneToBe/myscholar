<div class="container-fluid">
    @if($teacher && $mainClass)
        <!-- Quick Stats Row -->
        <div class="row mb-4">
            <div class="col-md-3">
                <div class="card border-left-primary shadow h-100 py-2">
                    <div class="card-body">
                        <div class="text-primary text-uppercase mb-1 font-weight-bold text-xs">{{ __('dashboard::views.head_teacher.class_name') }}</div>
                        <div class="h3 mb-0 font-weight-bold text-gray-800">{{ $mainClass->name }}</div>
                    </div>
                </div>
            </div>

            <div class="col-md-3">
                <div class="card border-left-success shadow h-100 py-2">
                    <div class="card-body">
                        <div class="text-success text-uppercase mb-1 font-weight-bold text-xs">{{ __('dashboard::views.head_teacher.total_students') }}</div>
                        <div class="h3 mb-0 font-weight-bold text-gray-800">{{ $totalStudents }}</div>
                    </div>
                </div>
            </div>

            <div class="col-md-3">
                <div class="card border-left-info shadow h-100 py-2">
                    <div class="card-body">
                        <div class="text-info text-uppercase mb-1 font-weight-bold text-xs">{{ __('dashboard::views.head_teacher.attendance_rate') }}</div>
                        <div class="h3 mb-0 font-weight-bold text-gray-800">{{ $averageAttendance }}%</div>
                    </div>
                </div>
            </div>

            <div class="col-md-3">
                <div class="card border-left-warning shadow h-100 py-2">
                    <div class="card-body">
                        <div class="text-warning text-uppercase mb-1 font-weight-bold text-xs">{{ __('dashboard::views.head_teacher.pending_justifications') }}</div>
                        <div class="h3 mb-0 font-weight-bold text-gray-800">{{ $pendingJustifications }}</div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Quick Actions Row -->
        <div class="row mb-4">
            <div class="col-12">
                <div class="card shadow">
                    <div class="card-body">
                        <h6 class="font-weight-bold text-primary mb-4">{{ __('dashboard::views.head_teacher.quick_actions') }}</h6>
                        <div class="row">
                            <div class="col-md-3 mb-2">
                                <a href="{{ route('head-teacher.attendance') }}" class="btn btn-primary btn-block">
                                    <i class="fas fa-check-circle"></i> {{ __('dashboard::views.head_teacher.record_attendance') }}
                                </a>
                            </div>
                            <div class="col-md-3 mb-2">
                                <a href="{{ route('head-teacher.grades') }}" class="btn btn-success btn-block">
                                    <i class="fas fa-chart-line"></i> {{ __('dashboard::views.head_teacher.view_grades') }}
                                </a>
                            </div>
                            <div class="col-md-3 mb-2">
                                <a href="{{ route('head-teacher.communications') }}" class="btn btn-info btn-block">
                                    <i class="fas fa-envelope"></i> {{ __('dashboard::views.head_teacher.send_message') }}
                                </a>
                            </div>
                            <div class="col-md-3 mb-2">
                                <a href="{{ route('head-teacher.class') }}" class="btn btn-warning btn-block">
                                    <i class="fas fa-cogs"></i> {{ __('dashboard::views.head_teacher.manage_class') }}
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Attendance and Grades Overview Row -->
        <div class="row">
            <div class="col-md-6">
                <div class="card shadow mb-4">
                    <div class="card-header py-3">
                        <h6 class="m-0 font-weight-bold text-primary">{{ __('dashboard::views.head_teacher.attendance_overview') }}</h6>
                    </div>
                    <div class="card-body">
                        <div class="mb-3">
                            <label>{{ __('dashboard::views.head_teacher.overall_attendance') }}</label>
                            <div class="progress">
                                <div class="progress-bar bg-success" role="progressbar" style="width: {{ $averageAttendance }}%;" aria-valuenow="{{ $averageAttendance }}" aria-valuemin="0" aria-valuemax="100">{{ $averageAttendance }}%</div>
                            </div>
                        </div>
                        <p class="text-muted small">{{ __('dashboard::views.head_teacher.attendance_desc') }}</p>
                        @if($pendingJustifications > 0)
                            <div class="alert alert-warning small" role="alert">
                                <i class="fas fa-exclamation-triangle"></i> {{ $pendingJustifications }} {{ __('dashboard::views.head_teacher.pending_justifications_msg') }}
                            </div>
                        @endif
                    </div>
                </div>
            </div>

            <div class="col-md-6">
                <div class="card shadow mb-4">
                    <div class="card-header py-3">
                        <h6 class="m-0 font-weight-bold text-primary">{{ __('dashboard::views.head_teacher.class_info') }}</h6>
                    </div>
                    <div class="card-body">
                        <p><strong>{{ __('dashboard::views.labels.level') }}:</strong> {{ $mainClass->level ?? 'N/A' }}</p>
                        <p><strong>{{ __('dashboard::views.labels.total_students') }}:</strong> {{ $totalStudents }}</p>
                        <p><strong>{{ __('dashboard::views.labels.capacity') }}:</strong> {{ $mainClass->max_capacity ?? 'N/A' }}</p>
                        <p><strong>{{ __('dashboard::views.labels.year') }}:</strong> {{ \Carbon\Carbon::now()->year }}</p>
                    </div>
                </div>
            </div>
        </div>

        <!-- Teacher Info -->
        <div class="row">
            <div class="col-12">
                <div class="card shadow mb-4">
                    <div class="card-header py-3">
                        <h6 class="m-0 font-weight-bold text-primary">{{ __('dashboard::views.head_teacher.teacher_info') }}</h6>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-6">
                                <p><strong>{{ __('dashboard::views.labels.specialization') }}:</strong> {{ $teacher->specialization ?? 'N/A' }}</p>
                                <p><strong>{{ __('dashboard::views.labels.email_office') }}:</strong> <a href="mailto:{{ $teacher->email_office }}">{{ $teacher->email_office ?? 'N/A' }}</a></p>
                            </div>
                            <div class="col-md-6">
                                <p><strong>{{ __('dashboard::views.labels.phone_office') }}:</strong> {{ $teacher->phone_office ?? 'N/A' }}</p>
                                <p><strong>{{ __('dashboard::views.labels.years_of_experience') }}:</strong> {{ $teacher->years_of_experience ?? 0 }} {{ __('dashboard::views.head_teacher.years') }}</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    @else
        <div class="alert alert-warning" role="alert">
            @if(!$teacher)
                {{ __('dashboard::views.head_teacher.no_profile') }}
            @else
                {{ __('dashboard::views.head_teacher.no_class') }}
            @endif
        </div>
    @endif
</div>
