<?php
declare(strict_types=1);

namespace App\Config;

class AmazonApiConfig
{
    private static ?self $instance = null;
    private array $config;

    private function __construct()
    {
        $this->config = [
            'access_key' => 'your-access-key',
            'secret_key' => 'your-secret-key',
            'region' => 'eu-west-1',
        ];
    }

    public static function getInstance(): self
    {
        if (self::$instance === null) {
            self::$instance = new self();
        }

        return self::$instance;
    }

    public function getConfig(): array
    {
        return $this->config;
    }

    private function __clone()
    {
    }

    private function __wakeup()
    {
    }
}