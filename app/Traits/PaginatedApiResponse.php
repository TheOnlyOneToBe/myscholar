<?php

namespace App\Traits;

use Illuminate\Pagination\Paginator;

trait PaginatedApiResponse
{
    /**
     * Return a paginated JSON response with consistent formatting
     */
    protected function paginatedResponse($items, $message = null, $code = 200): \Illuminate\Http\JsonResponse
    {
        $pagination = null;

        if ($items instanceof Paginator) {
            $pagination = [
                'total' => $items->total(),
                'per_page' => $items->perPage(),
                'current_page' => $items->currentPage(),
                'last_page' => $items->lastPage(),
                'from' => $items->firstItem(),
                'to' => $items->lastItem(),
                'path' => $items->path(),
            ];
            $items = $items->items();
        }

        $response = [
            'data' => $items,
        ];

        if ($message) {
            $response['message'] = $message;
        }

        if ($pagination) {
            $response['pagination'] = $pagination;
        }

        return response()->json($response, $code);
    }
}
