<?php

namespace App\Traits;

use App\Services\AlertService;

trait HasAlerts
{
    protected AlertService $alerts;

    public function initializeAlerts(): void
    {
        $this->alerts = app(AlertService::class);
    }

    public function success(string $message, ?string $code = null): self
    {
        $this->alerts->success($message, $code);
        return $this;
    }

    public function warning(string $message, ?string $code = null): self
    {
        $this->alerts->warning($message, $code);
        return $this;
    }

    public function error(string $message, ?string $code = null): self
    {
        $this->alerts->error($message, $code);
        return $this;
    }

    public function getAlerts(): array
    {
        return $this->alerts->all();
    }

    public function hasAlerts(): bool
    {
        return $this->alerts->hasAny();
    }

    public function clearAlerts(): self
    {
        $this->alerts->clear();
        return $this;
    }
}
