<?php

namespace App\Console\Commands;

use App\Services\ServiceDiscovery\ConsulClient;
use Illuminate\Console\Command;

class RegisterWithConsul extends Command
{
    protected $signature = 'consul:register {service-name?}';
    protected $description = 'Register application with Consul service registry';

    public function handle(ConsulClient $consul): int
    {
        $serviceName = $this->argument('service-name') ?? config('app.name', 'laravel-app');
        
        $config = [
            'id' => $serviceName . '-' . gethostname(),
            'host' => config('services.consul.service_host', 'host.docker.internal'),
            'port' => config('services.consul.service_port', 8000),
            'protocol' => 'http',
            'tags' => ['laravel', 'web-app'],
            'meta' => [
                'version' => config('app.version', '1.0.0'),
                'environment' => config('app.env', 'local'),
            ],
            'check_interval' => '10s',
            'check_timeout' => '5s',
        ];

        $this->info("Registering service '{$serviceName}' with Consul...");
        $this->table(
            ['Property', 'Value'],
            [
                ['Service Name', $serviceName],
                ['Host', $config['host']],
                ['Port', $config['port']],
                ['Health Check', "http://{$config['host']}:{$config['port']}/api/health"],
            ]
        );

        if ($consul->register($serviceName, $config)) {
            $this->info('✓ Service registered successfully!');
            return Command::SUCCESS;
        }

        $this->error('✗ Failed to register service with Consul');
        return Command::FAILURE;
    }
}
