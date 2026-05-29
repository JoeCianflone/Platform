<?php declare(strict_types=1);

namespace App\Http\Responses;

use Inertia\Response;
use App\Data\Enums\HttpResponse;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Collection;
use Illuminate\Pagination\LengthAwarePaginator;

/**
 * Builds the correct response type based on request context.
 *
 * - page = null              → always ApiResponse (JSON)
 * - page set + Inertia req   → InertiaResponse
 * - page set + non-Inertia   → ApiResponse (page ignored)
 */
final class AppResponseFactory
{
    /**
     * @param  string|list<string>  $errors
     */
    public static function error(
        ?string $page = null,
        string|array $errors = [],
        string $message = 'Server Error',
        HttpResponse $httpResponse = HttpResponse::SERVER_ERROR,
    ): JsonResponse|Response {
        $general = is_array($errors) ? $errors : [$errors];

        return self::make($page, [], $message, $httpResponse, ['general' => $general]);
    }

    /**
     * @param  array<string, list<string>>  $errors  field-keyed errors
     */
    public static function fail(
        ?string $page = null,
        array $errors = [],
        string $message = 'Validation failed',
        HttpResponse $httpResponse = HttpResponse::UNPROCESSABLE,
    ): JsonResponse|Response {
        return self::make($page, [], $message, $httpResponse, $errors);
    }

    /**
     * @param  array<int|string, mixed>|Collection<int|string, mixed>|LengthAwarePaginator<int, mixed>  $data
     * @param  array<string, mixed>  $errors
     */
    public static function make(
        ?string $page = null,
        array|Collection|LengthAwarePaginator $data = [],
        string $message = 'success',
        HttpResponse $httpResponse = HttpResponse::OK,
        array $errors = [],
    ): JsonResponse|Response {
        if ($page !== null && request()->header('X-Inertia')) {
            return InertiaResponse::make($page, $httpResponse, $message, $data, $errors)
                ->toResponse();
        }

        return ApiResponse::generate($httpResponse, $message, $data, $errors)
            ->toResponse();
    }

    /**
     * @param  array<int|string, mixed>|Collection<int|string, mixed>|LengthAwarePaginator<int, mixed>  $data
     */
    public static function success(
        ?string $page = null,
        array|Collection|LengthAwarePaginator $data = [],
        string $message = 'success',
    ): JsonResponse|Response {
        return self::make($page, $data, $message, HttpResponse::OK);
    }
}
