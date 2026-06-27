<div class="card">
    <div class="card-header">
        <h5 class="card-title">Contact Form Example</h5>
    </div>
    <div class="card-body">
        <form wire:submit="submit">
            <div class="mb-3">
                <label for="name" class="form-label">Name</label>
                <input
                    type="text"
                    class="form-control @error('name') is-invalid @enderror"
                    id="name"
                    wire:model="name"
                    placeholder="Enter your name"
                >
                @error('name')
                    <div class="invalid-feedback d-block">{{ $message }}</div>
                @enderror
            </div>

            <div class="mb-3">
                <label for="email" class="form-label">Email</label>
                <input
                    type="email"
                    class="form-control @error('email') is-invalid @enderror"
                    id="email"
                    wire:model="email"
                    placeholder="Enter your email"
                >
                @error('email')
                    <div class="invalid-feedback d-block">{{ $message }}</div>
                @enderror
            </div>

            <div class="mb-3">
                <label for="message" class="form-label">Message</label>
                <textarea
                    class="form-control @error('message') is-invalid @enderror"
                    id="message"
                    wire:model="message"
                    rows="4"
                    placeholder="Enter your message"
                ></textarea>
                @error('message')
                    <div class="invalid-feedback d-block">{{ $message }}</div>
                @enderror
            </div>

            <button type="submit" class="btn btn-primary" wire:loading.attr="disabled">
                <span wire:loading.remove>Send Message</span>
                <span wire:loading>
                    <span class="spinner-border spinner-border-sm me-2" role="status" aria-hidden="true"></span>
                    Sending...
                </span>
            </button>
        </form>
    </div>
</div>
