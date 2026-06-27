<?php

namespace App\Services;

use Illuminate\Support\Collection;

class AlertBag
{
    protected array $alerts = [];

    public function __construct()
    {
        $this->alerts = [
            'success' => [],
            'warning' => [],
            'error' => [],
        ];
    }

    public function success(string $message, ?string $code = null): self
    {
        $this->addAlert('success', $message, $code);
        return $this;
    }

    public function warning(string $message, ?string $code = null): self
    {
        $this->addAlert('warning', $message, $code);
        return $this;
    }

    public function error(string $message, ?string $code = null): self
    {
        $this->addAlert('error', $message, $code);
        return $this;
    }

    protected function addAlert(string $type, string $message, ?string $code = null): void
    {
        $this->alerts[$type][] = [
            'message' => $message,
            'code' => $code,
            'id' => uniqid(),
        ];
    }

    public function getSuccesses(): array
    {
        return $this->alerts['success'];
    }

    public function getWarnings(): array
    {
        return $this->alerts['warning'];
    }

    public function getErrors(): array
    {
        return $this->alerts['error'];
    }

    public function all(): array
    {
        return $this->alerts;
    }

    public function count(): int
    {
        return count($this->alerts['success']) + count($this->alerts['warning']) + count($this->alerts['error']);
    }

    public function countByType(string $type): int
    {
        return count($this->alerts[$type] ?? []);
    }

    public function has(string $type): bool
    {
        return !empty($this->alerts[$type]);
    }

    public function hasAny(): bool
    {
        return $this->count() > 0;
    }

    public function deleteSuccess(string $id): self
    {
        $this->deleteAlert('success', $id);
        return $this;
    }

    public function deleteWarning(string $id): self
    {
        $this->deleteAlert('warning', $id);
        return $this;
    }

    public function deleteError(string $id): self
    {
        $this->deleteAlert('error', $id);
        return $this;
    }

    public function delete(string $id): self
    {
        foreach (['success', 'warning', 'error'] as $type) {
            $this->deleteAlert($type, $id);
        }
        return $this;
    }

    protected function deleteAlert(string $type, string $id): void
    {
        $this->alerts[$type] = array_filter(
            $this->alerts[$type],
            fn($alert) => $alert['id'] !== $id
        );
    }

    public function clear(): self
    {
        $this->alerts = [
            'success' => [],
            'warning' => [],
            'error' => [],
        ];
        return $this;
    }

    public function clearType(string $type): self
    {
        $this->alerts[$type] = [];
        return $this;
    }

    public function toArray(): array
    {
        return $this->alerts;
    }

    public function toJson(): string
    {
        return json_encode($this->alerts);
    }
}
