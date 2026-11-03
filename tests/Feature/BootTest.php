<?php

declare(strict_types=1);

namespace ITHilbert\Schema\Tests\Feature;

use Illuminate\Support\ServiceProvider;
use ITHilbert\Schema\SchemaServiceProvider;
use ITHilbert\Schema\Tests\TestCase;

/**
 * Charakterisierung des Boot-Verhaltens (Ist-Stand, keine Spec).
 */
final class BootTest extends TestCase
{
    // Charakterisierung
    public function test_provider_ist_registriert(): void
    {
        $this->assertArrayHasKey(SchemaServiceProvider::class, $this->app->getLoadedProviders());
    }

    // AK1 (schema-config-merge). Der frühere Test "Config ohne Veröffentlichung nicht gesetzt"
    // (Randfall 3 der Spec schema-plz-config, AK3 zurückgebaut) widerspricht AK1 der Spec
    // schema-config-merge: die Paket-Config wird jetzt zusammengeführt.
    public function test_config_schema_org_ist_ohne_veroeffentlichung_zusammengefuehrt(): void
    {
        $this->assertIsArray(config('schemaOrg'));
        $this->assertArrayHasKey('review', config('schemaOrg'));
    }

    // Charakterisierung
    public function test_config_wird_nach_config_schema_org_php_veroeffentlicht(): void
    {
        $pfade = ServiceProvider::pathsToPublish(SchemaServiceProvider::class);

        $this->assertSame([config_path('schemaOrg.php')], array_values($pfade));
        $this->assertFileExists((string) array_key_first($pfade));
    }

    // Charakterisierung: Die Config-Vorlage ist ohne weitere Dateien ladbar.
    public function test_config_vorlage_laesst_sich_laden(): void
    {
        $config = require __DIR__.'/../../src/Config/config.php';

        $this->assertIsArray($config);
        $this->assertArrayHasKey('breadcrumb', $config);
        $this->assertArrayHasKey('localBusiness', $config);
    }

    // Charakterisierung: Keine Routen, Views, Migrationen, Befehle
    public function test_paket_bringt_keine_routen_views_migrationen_oder_befehle(): void
    {
        $this->assertFalse(is_dir(__DIR__.'/../../src/Routes'));
        $this->assertFalse(is_dir(__DIR__.'/../../src/Views'));
        $this->assertFalse(is_dir(__DIR__.'/../../src/Database'));
        $this->assertFalse(is_dir(__DIR__.'/../../src/Commands'));
    }
}
