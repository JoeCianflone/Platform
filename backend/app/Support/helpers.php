<?php declare(strict_types=1);

use Inertia\Response;
use App\Support\MakeArray;
use App\Support\ModulePath;
use App\Support\ModuleNames;
use App\Data\Enums\HttpResponse;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Collection;
use App\Http\Responses\AppResponseFactory;
use Illuminate\Pagination\LengthAwarePaginator;

if (! function_exists('make_array')) {
    /**
     * @param  string|object|array<int|string, mixed>  $data
     * @return array<int|string, mixed>
     */
    function make_array(string|object|array $data): array
    {
        return MakeArray::get($data);
    }
}

if (! function_exists('module_path')) {
    function module_path(string $path = ''): string
    {
        return ModulePath::get($path);
    }
}

if (! function_exists('module_names')) {
    /**
     * @return array<int, string>
     */
    function module_names(): array
    {
        return ModuleNames::toArray();
    }
}

if (! function_exists('module_pages_path')) {

    function module_pages_path(string $module, string $filename): string
    {
        return $module.'/pages/'.$filename;
    }
}

if (! function_exists('app_response')) {
    /**
     * @param  array<int|string, mixed>|Collection<int|string, mixed>|LengthAwarePaginator<int, mixed>  $data
     * @param  array<string, mixed>  $errors
     */
    function app_response(
        ?string $page,
        array|Collection|LengthAwarePaginator $data = [],
        string $message = 'success',
        HttpResponse $httpResponse = HttpResponse::OK,
        array $errors = [],
    ): JsonResponse|Response {
        return AppResponseFactory::make($page, $data, $message, $httpResponse, $errors);
    }
}
