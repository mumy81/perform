<?php

namespace Perform\Tools;

/**
 * Combine multiple composer.json files into a single root file for easier
 * development and testing.
 **/
class ComposerConfigMerger
{
    private $require = [];
    private $requireDev = [
        'glynnforrest/temping'=> '^0.5.0',
        'phpdocumentor/reflection-docblock'=> '^3.1'
    ];
    private $autoload = [
        'Perform\\Tools\\' => 'src/Tools',
    ];

    public function loadFile(string $filename, string $namespace): void
    {
        $config = json_decode(file_get_contents($filename), true);

        try {
            if ($config === null) {
                throw new \Exception(json_last_error_msg());
            }
            $this->loadConfig($config, $namespace);
        } catch (\Exception $e) {
            throw new \Exception(sprintf('Error loading %s: %s', $filename, $e->getMessage()));
        }
    }

    public function loadConfig(array $config, string $namespace): void
    {
        foreach ($config['require'] ?? [] as $package => $version) {
            if (substr($package, 0, 8) === 'perform/') {
                continue;
            }
            if (isset($this->require[$package]) && $this->require[$package] !== $version) {
                throw new \Exception(sprintf('Package %s has mismatched versions: %s vs %s', $package, $this->require[$package], $version));
            }
            $this->require[$package] = $version;
        }

        foreach ($config['require-dev'] ?? [] as $package => $version) {
            if (substr($package, 0, 8) === 'perform/') {
                continue;
            }
            if (isset($this->requireDev[$package]) && $this->requireDev[$package] !== $version) {
                throw new \Exception(sprintf('Dev package %s has mismatched versions: %s vs %s', $package, $this->requireDev[$package], $version));
            }
            $this->requireDev[$package] = $version;
        }

        $this->autoload[sprintf('Perform\\%s\\', $namespace)] = 'src/'.$namespace;
    }

    public function generateConfig(): string
    {
        ksort($this->require);
        ksort($this->requireDev);
        ksort($this->autoload);

        $config = [
            'name' => 'perform/perform',
            'description' => 'This configuration is autogenerated and not available as a package',
            'require' => $this->require,
            'require-dev' => $this->requireDev,
            'autoload' => [
                'psr-4' => $this->autoload,
            ],
        ];

        return json_encode($config, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES).PHP_EOL;
    }

    public function dumpFile(string $filename): void
    {
        file_put_contents($filename, $this->generateConfig());
    }
}