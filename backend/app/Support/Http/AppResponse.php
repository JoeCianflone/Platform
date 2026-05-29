<?php declare(strict_types=1);

namespace App\Support\Http;

use Illuminate\Contracts\Support\Responsable;
use Symfony\Component\HttpFoundation\Response;

class AppResponse implements Responsable
{
    protected ?string $force = null;

    /**
     * @param  array<string, mixed>  $props
     */
    public function __construct(
        protected string $component,
        protected array $props = [],
    ) {}

    public function forceInertia(): static
    {
        $this->force = 'inertia';

        return $this;
    }

    public function forceJson(): static
    {
        $this->force = 'json';

        return $this;
    }

    /**
     * @param  array<string, mixed>  $props
     */
    public static function make(string $component, array $props = []): static
    {
        // @phpstan-ignore-next-line new.static
        return new static($component, $props);
    }

    public function toResponse($request): Response
    {
        // Only bypass Inertia for explicit NativePHP background sync endpoints.
        // inertia() handles both initial page loads (renders blade) and
        // subsequent XHR requests (returns JSON) natively.
        if ($this->force === 'json') {
            return response()->json($this->props);
        }

        return inertia($this->component, $this->props)->toResponse($request);
    }
}
