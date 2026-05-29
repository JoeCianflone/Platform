<?php declare(strict_types=1);

namespace App\Http\Responses;

use Inertia\Response;
use App\Data\Enums\HttpResponse;
use Illuminate\Support\Collection;
use App\Concerns\Responses\BuildsEnvelope;
use Illuminate\Pagination\LengthAwarePaginator;
use App\Concerns\Responses\NormalizesResponseData;

final class InertiaResponse
{
    use BuildsEnvelope;
    use NormalizesResponseData;

    /**
     * @param  array<int|string, mixed>  $data
     * @param  array<string, mixed>  $errors
     * @param  array<string, mixed>  $meta
     */
    private function __construct(
        private readonly string $page,
        private readonly bool $success,
        private readonly string $message,
        private readonly HttpResponse $httpResponse,
        private readonly array $data = [],
        private readonly array $errors = [],
        private readonly array $meta = [],
    ) {}

    /**
     * @param  array<int|string, mixed>|Collection<int|string, mixed>|LengthAwarePaginator<int, mixed>  $data
     * @param  array<string, mixed>  $errors
     */
    public static function make(
        string $page,
        HttpResponse $httpResponse,
        string $message,
        array|Collection|LengthAwarePaginator $data = [],
        array $errors = [],
    ): self {
        $normalized = self::normalizeData($data);

        return new self(
            page: $page,
            success: self::resolveSuccess($httpResponse),
            message: $message,
            httpResponse: $httpResponse,
            data: $normalized['items'],
            errors: $errors,
            meta: $normalized['meta'],
        );
    }

    public function toResponse(): Response
    {
        return inertia(
            component: app()->basePath('frontend/experiences/'.$this->page),
            props: $this->buildEnvelope(),
        );
    }
}
