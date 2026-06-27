<?php

namespace App\Services;

use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Cache;

class AlertService
{
    protected AlertBag $alerts;
    protected string $cacheKey = 'alerts';
    protected string $sessionKey = '_alerts';

    public function __construct()
    {
        $this->alerts = new AlertBag();
        $this->loadFromSession();
    }

    /**
     * Add a success alert
     */
    public function success(string $message, ?string $code = null): self
    {
        $this->alerts->success($message, $code);
        $this->saveToSession();
        return $this;
    }

    /**
     * Add a warning alert
     */
    public function warning(string $message, ?string $code = null): self
    {
        $this->alerts->warning($message, $code);
        $this->saveToSession();
        return $this;
    }

    /**
     * Add an error alert
     */
    public function error(string $message, ?string $code = null): self
    {
        $this->alerts->error($message, $code);
        $this->saveToSession();
        return $this;
    }

    /**
     * Get all alerts
     */
    public function all(): array
    {
        return $this->alerts->all();
    }

    /**
     * Get success alerts
     */
    public function getSuccesses(): array
    {
        return $this->alerts->getSuccesses();
    }

    /**
     * Get warning alerts
     */
    public function getWarnings(): array
    {
        return $this->alerts->getWarnings();
    }

    /**
     * Get error alerts
     */
    public function getErrors(): array
    {
        return $this->alerts->getErrors();
    }

    /**
     * Check if there are any alerts
     */
    public function has(string $type): bool
    {
        return $this->alerts->has($type);
    }

    /**
     * Check if there are any alerts at all
     */
    public function hasAny(): bool
    {
        return $this->alerts->hasAny();
    }

    /**
     * Count alerts
     */
    public function count(): int
    {
        return $this->alerts->count();
    }

    /**
     * Count alerts by type
     */
    public function countByType(string $type): int
    {
        return $this->alerts->countByType($type);
    }

    /**
     * Delete an alert by ID
     */
    public function delete(string $id): self
    {
        $this->alerts->delete($id);
        $this->saveToSession();
        return $this;
    }

    /**
     * Delete a success alert
     */
    public function deleteSuccess(string $id): self
    {
        $this->alerts->deleteSuccess($id);
        $this->saveToSession();
        return $this;
    }

    /**
     * Delete a warning alert
     */
    public function deleteWarning(string $id): self
    {
        $this->alerts->deleteWarning($id);
        $this->saveToSession();
        return $this;
    }

    /**
     * Delete an error alert
     */
    public function deleteError(string $id): self
    {
        $this->alerts->deleteError($id);
        $this->saveToSession();
        return $this;
    }

    /**
     * Clear all alerts
     */
    public function clear(): self
    {
        $this->alerts->clear();
        $this->saveToSession();
        return $this;
    }

    /**
     * Clear alerts by type
     */
    public function clearType(string $type): self
    {
        $this->alerts->clearType($type);
        $this->saveToSession();
        return $this;
    }

    /**
     * Flash alerts (clear after retrieval)
     */
    public function flash(): array
    {
        $alerts = $this->alerts->all();
        $this->clear();
        return $alerts;
    }

    /**
     * Get alerts without clearing
     */
    public function peek(): array
    {
        return $this->alerts->all();
    }

    /**
     * Load alerts from session
     */
    protected function loadFromSession(): void
    {
        $stored = Session::get($this->sessionKey);
        if ($stored) {
            $this->alerts = new AlertBag();
            foreach ($stored as $type => $typeAlerts) {
                foreach ($typeAlerts as $alert) {
                    $this->alerts->addAlert(
                        $type,
                        $alert['message'],
                        $alert['code'] ?? null
                    );
                }
            }
        }
    }

    /**
     * Save alerts to session
     */
    protected function saveToSession(): void
    {
        Session::put($this->sessionKey, $this->alerts->all());
    }

    /**
     * Convert to JSON for API responses
     */
    public function toJson(): string
    {
        return json_encode($this->alerts->all());
    }

    /**
     * Get AlertBag instance
     */
    public function bag(): AlertBag
    {
        return $this->alerts;
    }
}
