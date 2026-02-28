<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

/**
 * Monitoring Service — Health, Metrics, Service Discovery
 *
 * Provides observability endpoints for Prometheus/Grafana and
 * central health-checking for all microservices.
 *
 * Patterns: Service Discovery, Health Check, Prometheus Metrics
 */
class MonitoringServiceController extends Controller
{
    // ── Health endpoint ─────────────────────────────────────────

    public function health(): JsonResponse
    {
        $checks = [
            'database' => $this->checkDatabase(),
            'cache'    => $this->checkCache(),
            'storage'  => $this->checkStorage(),
        ];

        $healthy = !in_array(false, array_column($checks, 'ok'));

        return response()->json([
            'status'    => $healthy ? 'healthy' : 'degraded',
            'timestamp' => now()->toIso8601String(),
            'uptime_s'  => (int) (microtime(true) - LARAVEL_START),
            'checks'    => $checks,
        ], $healthy ? 200 : 503)
            ->header('X-Service', 'monitoring-service');
    }

    // ── Prometheus metrics (text/plain) ─────────────────────────

    public function metrics(): \Illuminate\Http\Response
    {
        $requestCount  = (int) Cache::get('metrics:request_count', 0);
        $errorCount    = (int) Cache::get('metrics:error_count', 0);
        $avgResponseMs = (float) Cache::get('metrics:avg_response_ms', 0);
        $activeUsers   = (int) Cache::get('metrics:active_users', 0);

        $dbOk    = $this->checkDatabase()['ok'] ? 1 : 0;
        $cacheOk = $this->checkCache()['ok'] ? 1 : 0;

        $lines = [
            '# HELP http_requests_total Total HTTP requests',
            '# TYPE http_requests_total counter',
            "http_requests_total {$requestCount}",
            '',
            '# HELP http_errors_total Total HTTP error responses',
            '# TYPE http_errors_total counter',
            "http_errors_total {$errorCount}",
            '',
            '# HELP http_response_time_ms Average response time in ms',
            '# TYPE http_response_time_ms gauge',
            "http_response_time_ms {$avgResponseMs}",
            '',
            '# HELP active_users Currently active users',
            '# TYPE active_users gauge',
            "active_users {$activeUsers}",
            '',
            '# HELP service_health Service health status (1=up, 0=down)',
            '# TYPE service_health gauge',
            "service_health{service=\"database\"} {$dbOk}",
            "service_health{service=\"cache\"} {$cacheOk}",
        ];

        return response(implode("\n", $lines), 200)
            ->header('Content-Type', 'text/plain; charset=UTF-8')
            ->header('X-Service', 'monitoring-service');
    }

    // ── Service Discovery status ────────────────────────────────

    public function services(): JsonResponse
    {
        $services = [
            'catalog-service'      => ['host' => 'catalog-service',      'port' => 8000],
            'order-service'        => ['host' => 'order-service',        'port' => 8000],
            'user-service'         => ['host' => 'user-service',         'port' => 8000],
            'notification-service' => ['host' => 'notification-service', 'port' => 8000],
        ];

        $results = [];
        foreach ($services as $name => $info) {
            $results[$name] = [
                'host'   => $info['host'],
                'port'   => $info['port'],
                'status' => $this->pingService($info['host'], $info['port']),
            ];
        }

        return response()->json([
            'success'  => true,
            'services' => $results,
        ])->header('X-Service', 'monitoring-service');
    }

    // ── Internals ───────────────────────────────────────────────

    private function checkDatabase(): array
    {
        try {
            DB::connection()->getPdo();
            return ['ok' => true, 'latency_ms' => 0];
        } catch (\Exception $e) {
            return ['ok' => false, 'error' => $e->getMessage()];
        }
    }

    private function checkCache(): array
    {
        try {
            Cache::put('health_check', true, 10);
            $ok = Cache::get('health_check') === true;
            return ['ok' => $ok];
        } catch (\Exception $e) {
            return ['ok' => false, 'error' => $e->getMessage()];
        }
    }

    private function checkStorage(): array
    {
        $path = storage_path('logs/laravel.log');
        return ['ok' => is_writable(dirname($path))];
    }

    private function pingService(string $host, int $port): string
    {
        try {
            $fp = @fsockopen($host, $port, $errno, $errstr, 2);
            if ($fp) {
                fclose($fp);
                return 'UP';
            }
            return 'DOWN';
        } catch (\Exception $e) {
            return 'DOWN';
        }
    }
}
