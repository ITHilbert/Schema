<?php

declare(strict_types=1);

namespace ITHilbert\Schema\Tests;

use Illuminate\Foundation\Application;
use ITHilbert\Schema\SchemaServiceProvider;
use Orchestra\Testbench\TestCase as Orchestra;

/**
 * Basis aller Paket-Tests: eigene Testbench-App, unabhängig vom Host.
 */
abstract class TestCase extends Orchestra
{
    /** Eigenes Config-Verzeichnis je Test: Tests schreiben nie ins geteilte Testbench-Skelett. */
    private string $eigenerConfigPfad = '';

    protected function tearDown(): void
    {
        if ($this->eigenerConfigPfad !== '' && is_dir($this->eigenerConfigPfad)) {
            foreach (glob($this->eigenerConfigPfad.'/*') ?: [] as $datei) {
                @unlink($datei);
            }
            @rmdir($this->eigenerConfigPfad);
        }

        parent::tearDown();
    }

    /**
     * @param  Application  $app
     * @return array<int, class-string>
     */
    protected function getPackageProviders($app): array
    {
        return [
            SchemaServiceProvider::class,
        ];
    }

    /**
     * @param  Application  $app
     */
    protected function defineEnvironment($app): void
    {
        $this->eigenerConfigPfad = sys_get_temp_dir().'/schema-test-config-'.getmypid().'-'.bin2hex(random_bytes(4));
        mkdir($this->eigenerConfigPfad, 0777, true);
        $app->useConfigPath($this->eigenerConfigPfad);

        $app['config']->set('app.key', 'base64:'.base64_encode(str_repeat('a', 32)));
        $app['config']->set('database.default', 'testing');
        $app['config']->set('database.connections.testing', [
            'driver' => 'sqlite',
            'database' => ':memory:',
            'prefix' => '',
            'foreign_key_constraints' => true,
        ]);
        $app['config']->set('session.driver', 'array');
        $app['config']->set('cache.default', 'array');
        $app['config']->set('queue.default', 'sync');
    }
}
