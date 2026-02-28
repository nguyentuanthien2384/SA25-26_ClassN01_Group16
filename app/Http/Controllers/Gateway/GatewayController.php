<?php

namespace App\Http\Controllers\Gateway;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class GatewayController extends Controller
{
    private string $productServiceUrl;

    public function __construct()
    {
        $this->productServiceUrl = rtrim(
            env('PRODUCT_SERVICE_URL', 'http://127.0.0.1:5001'),
            '/'
        );
    }

    public function handle(Request $request, string $path = '')
    {
        // --- 1. ADMIN CHECK (write operations) ---
        $method = $request->method();
        $role   = $request->attributes->get('role', 'user');

        if (in_array($method, ['POST', 'PUT', 'DELETE']) && $role !== 'admin') {
            Log::warning('[GATEWAY] Forbidden: admin token required', [
                'method' => $method,
                'path'   => $request->path(),
                'role'   => $role,
            ]);

            return response()->json([
                'error'   => 'Forbidden',
                'details' => 'Admin token required for write operations',
            ], 403);
        }

        // --- 2. CONSTRUCT TARGET URL ---
        $targetUrl = $this->productServiceUrl . '/api/products';
        if (!empty($path)) {
            $targetUrl .= '/' . $path;
        }

        Log::info('[GATEWAY] Forwarding ' . $method . ' /' . $request->path()
            . ' -> ' . $targetUrl);

        // --- 3. FORWARD REQUEST TO BACKEND ---
        try {
            $headers = collect($request->headers->all())
                ->except(['host'])
                ->map(fn($v) => $v[0] ?? '')
                ->toArray();

            $response = Http::withHeaders($headers)
                ->timeout(5)
                ->send($method, $targetUrl, [
                    'query' => $request->query(),
                    'body'  => $request->getContent(),
                ]);

            // --- 4. RETURN BACKEND RESPONSE ---
            return response($response->body(), $response->status())
                ->header('Content-Type',
                    $response->header('Content-Type') ?? 'application/json')
                ->header('X-Gateway', 'ElectroShop-Gateway');

        } catch (\Illuminate\Http\Client\ConnectionException $e) {
            // --- 5. FAILURE HANDLING (503) ---
            Log::error('[GATEWAY] Product service unreachable', [
                'target' => $targetUrl,
                'error'  => $e->getMessage(),
            ]);

            return response()->json([
                'error'   => 'Service Unavailable',
                'details' => 'Product service is currently unavailable',
            ], 503);
        }
    }
}
