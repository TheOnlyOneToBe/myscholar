<div class="toast-container position-fixed top-0 end-0 p-3" style="z-index: 1050;">
    @if($alerts['success'])
        @foreach($alerts['success'] as $alert)
            <div class="toast success-toast" role="alert" aria-live="assertive" aria-atomic="true" data-alert-id="{{ $alert['id'] }}">
                <div class="toast-header bg-success text-white">
                    <svg class="bi flex-shrink-0 me-2" width="20" height="20" fill="currentColor" viewBox="0 0 16 16">
                        <path d="M16 8A8 8 0 1 1 0 8a8 8 0 0 1 16 0zm-3.97-3.03a.75.75 0 0 0-1.08.022L7.477 9.417 5.384 7.323a.75.75 0 0 0-1.06 1.061L6.97 11.03a.75.75 0 0 0 1.079-.02l3.992-4.99a.75.75 0 0 0-.01-1.05z"/>
                    </svg>
                    <strong class="me-auto">{{ __('notifications.labels.success') }}</strong>
                    @if($alert['code'])
                        <small class="text-white-50">{{ $alert['code'] }}</small>
                    @endif
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="toast" aria-label="Close" wire:click="deleteAlert('{{ $alert['id'] }}')"></button>
                </div>
                <div class="toast-body">
                    {{ $alert['message'] }}
                </div>
            </div>
        @endforeach
    @endif

    @if($alerts['warning'])
        @foreach($alerts['warning'] as $alert)
            <div class="toast warning-toast" role="alert" aria-live="assertive" aria-atomic="true" data-alert-id="{{ $alert['id'] }}">
                <div class="toast-header bg-warning text-dark">
                    <svg class="bi flex-shrink-0 me-2" width="20" height="20" fill="currentColor" viewBox="0 0 16 16">
                        <path d="M8.982 1.566a1.13 1.13 0 0 0-1.96 0l-5.708 9.7a1.13 1.13 0 0 0 .98 1.734h11.396a1.13 1.13 0 0 0 .98-1.734L8.982 1.566zM8 5c.535 0 .954.462.954.999h-1.908C7.046 5.462 7.465 5 8 5zm.045 9h-1.09a.5.5 0 0 0 0 1h1.09a.5.5 0 0 0 0-1zm-.409-3h.818a.5.5 0 0 0 .5-.5v-2a.5.5 0 0 0-.5-.5h-.818a.5.5 0 0 0-.5.5v2a.5.5 0 0 0 .5.5z"/>
                    </svg>
                    <strong class="me-auto">{{ __('notifications.labels.warning') }}</strong>
                    @if($alert['code'])
                        <small class="text-warning-50">{{ $alert['code'] }}</small>
                    @endif
                    <button type="button" class="btn-close" data-bs-dismiss="toast" aria-label="Close" wire:click="deleteAlert('{{ $alert['id'] }}')"></button>
                </div>
                <div class="toast-body">
                    {{ $alert['message'] }}
                </div>
            </div>
        @endforeach
    @endif

    @if($alerts['error'])
        @foreach($alerts['error'] as $alert)
            <div class="toast error-toast" role="alert" aria-live="assertive" aria-atomic="true" data-alert-id="{{ $alert['id'] }}">
                <div class="toast-header bg-danger text-white">
                    <svg class="bi flex-shrink-0 me-2" width="20" height="20" fill="currentColor" viewBox="0 0 16 16">
                        <path d="M8 15A7 7 0 1 1 8 1a7 7 0 0 1 0 14zm0 1A8 8 0 1 0 8 0a8 8 0 0 0 0 16z"/>
                        <path d="m8.5 8-3.5.5 3.5.5V11l3.5-3 3.5 3V8l-3.5-.5L8.5 5V8z"/>
                    </svg>
                    <strong class="me-auto">{{ __('notifications.labels.error') }}</strong>
                    @if($alert['code'])
                        <small class="text-danger-50">{{ $alert['code'] }}</small>
                    @endif
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="toast" aria-label="Close" wire:click="deleteAlert('{{ $alert['id'] }}')"></button>
                </div>
                <div class="toast-body">
                    {{ $alert['message'] }}
                </div>
            </div>
        @endforeach
    @endif
</div>

<style>
.toast-container {
    --bs-toast-padding-x: 1rem;
    --bs-toast-padding-y: 0.75rem;
}

.toast {
    min-width: 300px;
    margin-bottom: 1rem;
    box-shadow: 0 0.5rem 1rem rgba(0, 0, 0, 0.15);
    animation: slideInRight 0.3s ease-out;
}

@keyframes slideInRight {
    from {
        transform: translateX(400px);
        opacity: 0;
    }
    to {
        transform: translateX(0);
        opacity: 1;
    }
}

.toast.success-toast {
    border-left: 4px solid #198754;
}

.toast.warning-toast {
    border-left: 4px solid #ffc107;
}

.toast.error-toast {
    border-left: 4px solid #dc3545;
}

.toast-header {
    border-bottom: 1px solid rgba(0, 0, 0, 0.05);
}

.btn-close {
    opacity: 0.7;
    transition: opacity 0.2s;
}

.btn-close:hover {
    opacity: 1;
}
</style>

<script>
document.addEventListener('livewire:navigated', function() {
    // Auto-dismiss toasts after 5 seconds
    const toasts = document.querySelectorAll('.toast');
    toasts.forEach(toast => {
        const bsToast = new bootstrap.Toast(toast, { delay: 5000 });
        bsToast.show();
    });
});
</script>
