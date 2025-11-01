<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use App\Models\UserActivityLog;

class LogUserActivity
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle(Request $request, Closure $next)
    {
        $response = $next($request);

        // Log setelah request selesai
        if (auth()->check() && $this->shouldLog($request)) {
            $this->logActivity($request);
        }

        return $response;
    }

    /**
     * Tentukan apakah request perlu di-log
     */
    private function shouldLog(Request $request): bool
    {
        // Hanya log POST, PUT, DELETE (tidak log GET)
        if (!in_array($request->method(), ['POST', 'PUT', 'PATCH', 'DELETE'])) {
            return false;
        }

        // Exclude route tertentu (misal: heartbeat, ajax polling, livewire)
        $excludedRoutes = [
            'livewire.message',
            'livewire.upload',
            'livewire.preview-file',
        ];

        $routeName = $request->route()?->getName();
        return !in_array($routeName, $excludedRoutes);
    }

    /**
     * Log aktivitas user
     */
    private function logActivity(Request $request): void
    {
        try {
            $action = $this->determineAction($request);
            $description = $this->generateDescription($request, $action);

            UserActivityLog::log($action, $description, [
                'method' => $request->method(),
                'url' => $request->fullUrl(),
                'route' => $request->route()?->getName(),
                'params' => $request->except(['password', 'supervisor_pin', '_token', '_method'])
            ]);
        } catch (\Exception $e) {
            // Silent fail - jangan sampai log error crash aplikasi
            \Log::error('Failed to log user activity: ' . $e->getMessage());
        }
    }

    /**
     * Tentukan action dari request
     */
    private function determineAction(Request $request): string
    {
        $path = $request->path();
        $method = $request->method();

        // Mapping path ke action yang lebih readable
        if (str_contains($path, 'app/pos') && str_contains($path, 'sales') && $method == 'POST') {
            return 'create_invoice';
        }

        if (str_contains($path, 'sales') && $method == 'DELETE') {
            return 'delete_sale';
        }

        if (str_contains($path, 'void')) {
            return 'void_transaction';
        }

        if (str_contains($path, 'products') && $method == 'POST') {
            return 'create_product';
        }

        if (str_contains($path, 'products') && in_array($method, ['PUT', 'PATCH'])) {
            return 'edit_product';
        }

        if (str_contains($path, 'product-seconds') && $method == 'POST') {
            return 'create_product_second';
        }

        // Default: method + route name
        $routeName = $request->route()?->getName();
        return strtolower($method) . '_' . str_replace('.', '_', $routeName ?? 'unknown');
    }

    /**
     * Generate deskripsi yang readable
     */
    private function generateDescription(Request $request, string $action): string
    {
        $user = auth()->user()->name;
        $actionReadable = ucwords(str_replace('_', ' ', $action));

        return "{$user} melakukan: {$actionReadable}";
    }
}
