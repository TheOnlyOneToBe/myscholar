<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use App\Services\AlertService;

class AddAlertsToResponse
{
    public function __construct(protected AlertService $alertService) {}

    public function handle(Request $request, Closure $next): Response
    {
        $response = $next($request);

        // Only add alerts to JSON responses
        if ($request->is('api/*') && $response->headers->get('content-type') === 'application/json') {
            $alerts = $this->alertService->peek();

            if ($alerts['success'] || $alerts['warning'] || $alerts['error']) {
                $content = json_decode($response->getContent(), true) ?? [];

                if (is_array($content)) {
                    $content['alerts'] = $alerts;
                    $response->setContent(json_encode($content));
                }
            }
        }

        return $response;
    }
}
