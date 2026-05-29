<?php declare(strict_types=1);

namespace App\Http\Responses;

use App\Data\Enums\HttpResponse;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Collection;
use App\Concerns\Responses\BuildsEnvelope;
use Illuminate\Pagination\LengthAwarePaginator;
use App\Concerns\Responses\NormalizesResponseData;

final class ApiResponse
{
    use BuildsEnvelope;
    use NormalizesResponseData;

    /**
     * @param  array<int|string, mixed>  $data
     * @param  array<int|string, mixed>  $errors
     * @param  array<string, mixed>  $meta
     */
    private function __construct(
        private readonly bool $success,
        private readonly string $message,
        private readonly HttpResponse $httpResponse,
        private readonly array $data = [],
        private readonly array $errors = [],
        private readonly array $meta = [],
    ) {}

    /**
     * @param  string|list<string>  $errors
     */
    public static function error(
        string|array $errors = [],
        string $message = 'Server Error',
        HttpResponse $httpResponse = HttpResponse::SERVER_ERROR,
    ): self {
        $general = is_array($errors) ? $errors : [$errors];

        return self::generate($httpResponse, $message, [], ['general' => $general]);
    }

    /**
     * @param  array<string, list<string>>  $errors  field-keyed errors
     */
    public static function fail(
        array $errors = [],
        string $message = 'Validation failed',
        HttpResponse $httpResponse = HttpResponse::UNPROCESSABLE,
    ): self {
        return self::generate($httpResponse, $message, [], $errors);
    }

    /**
     * @param  array<int|string, mixed>|Collection<int|string, mixed>|LengthAwarePaginator<int, mixed>  $data
     * @param  array<string, list<string>>|array<int, mixed>  $errors
     */
    public static function generate(
        HttpResponse $httpResponse,
        string $message = 'success',
        array|Collection|LengthAwarePaginator $data = [],
        array $errors = [],
    ): self {
        $normalized = self::normalizeData($data);

        return new self(
            success: self::resolveSuccess($httpResponse),
            message: $message,
            httpResponse: $httpResponse,
            data: $normalized['items'],
            errors: $errors,
            meta: $normalized['meta'],
        );
    }

    /**
     * @param  array<int|string, mixed>|Collection<int|string, mixed>|LengthAwarePaginator<int, mixed>  $data
     */
    public static function success(
        array|Collection|LengthAwarePaginator $data = [],
        string $message = 'success',
    ): self {
        return self::generate(HttpResponse::OK, $message, $data);
    }

    /**
     * @return array<string, mixed>
     */
    public function toArray(): array
    {
        return $this->buildEnvelope();
    }

    public function toResponse(): JsonResponse
    {
        return response()->json($this->buildEnvelope(), $this->httpResponse->value);
    }
}
