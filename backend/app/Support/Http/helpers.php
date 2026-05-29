<?php declare(strict_types=1);

use App\Support\Http\AppResponse;

if (! function_exists('app_response')) {
    /**
     * @param  array<string, mixed>  $props
     */
    function app_response(string $component, array $props = []): AppResponse
    {
        return AppResponse::make($component, $props);
    }
}
