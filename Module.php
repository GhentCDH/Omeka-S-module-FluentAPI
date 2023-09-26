<?php
namespace FluentAPI;

use Laminas\Config\Factory;
use Omeka\Module\AbstractModule;


class Module extends AbstractModule
{
    const NAMESPACE = __NAMESPACE__;

    private array $config = [];

    public function getConfig(): array
    {
        if ($this->config) {
            return $this->config;
        }

        // Load our configuration.
        $this->config = Factory::fromFiles(
            glob(__DIR__ . '/config/*.config.php')
        );

        return $this->config;
    }
}
