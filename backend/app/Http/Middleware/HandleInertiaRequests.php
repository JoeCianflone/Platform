<?php declare(strict_types=1);

namespace App\Http\Middleware;

use Inertia\Middleware;
use Illuminate\Http\Request;
use Illuminate\Support\MessageBag;

class HandleInertiaRequests extends Middleware
{
    protected $rootView = 'app';

    /**
     * @return array<string, mixed>
     */
    public function share(Request $request): array
    {
        return [
            ...parent::share($request),
            'auth' => [
                'user' => $request->user(),
            ],
            'flash' => [
                'success' => $request->session()->get('success'),
            ],
            'errors' => $this->resolveErrors($request),
        ];
    }

    public function version(Request $request): ?string
    {
        return parent::version($request);
    }

    /**
     * @return array<string, array<int|string, string>>
     */
    private function resolveErrors(Request $request): array
    {
        $errors = [];

        $sessionErrors = $request->session()->get('errors');
        if ($sessionErrors instanceof MessageBag) {
            $errors = $sessionErrors->toArray();
        }

        $flashError = $request->session()->get('error');
        if ($flashError !== null) {
            $errors['general'] = is_array($flashError) ? $flashError : [$flashError];
        }

        return $errors;
    }
}
