<x-layouts.app>
    <div class="row">
        <div class="col-md-8">
            <h1 class="mb-4">Alert System Demo</h1>

            <div class="row mb-4">
                <div class="col-md-6">
                    <div class="card">
                        <div class="card-header">
                            <h5 class="card-title">Success Alert</h5>
                        </div>
                        <div class="card-body">
                            <form action="/test/alert-success" method="POST">
                                @csrf
                                <button type="submit" class="btn btn-success">
                                    Trigger Success Alert
                                </button>
                            </form>
                        </div>
                    </div>
                </div>

                <div class="col-md-6">
                    <div class="card">
                        <div class="card-header">
                            <h5 class="card-title">Warning Alert</h5>
                        </div>
                        <div class="card-body">
                            <form action="/test/alert-warning" method="POST">
                                @csrf
                                <button type="submit" class="btn btn-warning">
                                    Trigger Warning Alert
                                </button>
                            </form>
                        </div>
                    </div>
                </div>
            </div>

            <div class="row mb-4">
                <div class="col-md-6">
                    <div class="card">
                        <div class="card-header">
                            <h5 class="card-title">Error Alert</h5>
                        </div>
                        <div class="card-body">
                            <form action="/test/alert-error" method="POST">
                                @csrf
                                <button type="submit" class="btn btn-danger">
                                    Trigger Error Alert
                                </button>
                            </form>
                        </div>
                    </div>
                </div>

                <div class="col-md-6">
                    <div class="card">
                        <div class="card-header">
                            <h5 class="card-title">Multiple Alerts</h5>
                        </div>
                        <div class="card-body">
                            <form action="/test/alert-multiple" method="POST">
                                @csrf
                                <button type="submit" class="btn btn-info">
                                    Trigger Multiple Alerts
                                </button>
                            </form>
                        </div>
                    </div>
                </div>
            </div>

            <div class="card">
                <div class="card-header">
                    <h5 class="card-title">Livewire Form Component</h5>
                </div>
                <div class="card-body">
                    <livewire:forms.simple-form />
                </div>
            </div>
        </div>

        <div class="col-md-4">
            <div class="card bg-light">
                <div class="card-header">
                    <h5 class="card-title">Alert System Features</h5>
                </div>
                <div class="card-body">
                    <ul>
                        <li>✅ Three types: Success, Warning, Error</li>
                        <li>✅ Auto-dismiss after 5 seconds</li>
                        <li>✅ Manual dismiss button</li>
                        <li>✅ Alert codes for identification</li>
                        <li>✅ Livewire integration</li>
                        <li>✅ API response injection</li>
                        <li>✅ Bootstrap toasts</li>
                        <li>✅ Smooth animations</li>
                    </ul>
                </div>
            </div>
        </div>
    </div>
</x-layouts.app>
