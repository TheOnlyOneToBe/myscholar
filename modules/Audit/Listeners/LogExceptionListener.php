<?php

namespace Modules\Audit\Listeners;

use Illuminate\Foundation\Events\LocalizedExceptionRendered;
use Illuminate\Support\Facades\Request;
use Modules\Audit\Services\AuditService;

class LogExceptionListener
{
    protected AuditService $auditService;

    public function __construct(AuditService $auditService)
    {
        $this->auditService = $auditService;
    }

    public function handle(LocalizedExceptionRendered $event)
    {
        // Only log on certain conditions
        if (Request::isJson()) {
            $this->auditService->logError(
                $event->exception,
                'api_error',
                context: [
                    'endpoint' => Request::path(),
                    'method' => Request::method(),
                ]
            );
        }
    }
}
