<?php declare(strict_types=1);

namespace App\Concerns\Responses;

use App\Data\Enums\HttpResponse;
use Illuminate\Support\Collection;
use Illuminate\Pagination\LengthAwarePaginator;

trait NormalizesResponseData
{
    /**
     * @param  array<int|string, mixed>|Collection<int|string, mixed>|LengthAwarePaginator<int, mixed>  $data
     * @return array{items: array<int|string, mixed>, meta: array<string, mixed>}
     */
    private static function normalizeData(array|Collection|LengthAwarePaginator $data): array
    {
        if ($data instanceof LengthAwarePaginator) {
            return [
                'items' => $data->items(),
                'meta' => [
                    'current_page' => $data->currentPage(),
                    'last_page' => $data->lastPage(),
                    'per_page' => $data->perPage(),
                    'total' => $data->total(),
                    'from' => $data->firstItem(),
                    'to' => $data->lastItem(),
                ],
            ];
        }

        return [
            'items' => $data instanceof Collection ? $data->toArray() : $data,
            'meta' => [],
        ];
    }

    private static function resolveSuccess(HttpResponse $httpResponse): bool
    {
        return in_array($httpResponse, [HttpResponse::OK, HttpResponse::CREATED], true);
    }
}
