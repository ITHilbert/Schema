<?php

declare(strict_types=1);

namespace ITHilbert\Schema\Tests\Feature;

use Illuminate\Foundation\Application;
use Illuminate\Support\ServiceProvider;
use ITHilbert\Schema\Schema;
use ITHilbert\Schema\SchemaServiceProvider;
use ITHilbert\Schema\Tests\TestCase;
use Orchestra\Testbench\Attributes\DefineEnvironment;

/**
 * PLZ im LocalBusiness (AK1, AK2) und Config-Randfälle (AK3 zurückgebaut).
 */
final class PlzUndConfigTest extends TestCase
{
    /**
     * @return array<string, mixed>|null
     */
    private function jsonAus(string $ausgabe): ?array
    {
        $this->assertSame(1, preg_match('#<script type="application/ld\+json">(.*)</script>#s', $ausgabe, $m), 'Kein einzelner ld+json-Block');

        return json_decode($m[1], true);
    }

    private function ausgabeMitPlz(string $plz): string
    {
        config(['schemaOrg.localBusiness.postalCode' => $plz]);
        $schema = new Schema;
        $schema->useLocalBusiness();

        return $schema->getSchema();
    }

    /**
     * @param  array<mixed>  $daten
     */
    private function plzAus(array $daten): mixed
    {
        foreach ($daten as $k => $v) {
            if ($k === 'postalCode') {
                return $v;
            }
            if (is_array($v) && ($f = $this->plzAus($v)) !== null) {
                return $f;
            }
        }

        return null;
    }

    // AK1
    public function test_plz_steht_ohne_angehaengtes_leerzeichen_im_json_ld(): void
    {
        $ausgabe = $this->ausgabeMitPlz('12345');

        $this->assertStringContainsString('"postalCode": "12345"', $ausgabe);
    }

    // AK1
    public function test_plz_ist_nach_json_decode_genau_der_konfigurierte_wert(): void
    {
        $json = $this->jsonAus($this->ausgabeMitPlz('12345'));

        $this->assertNotNull($json, 'Block ist kein gueltiges JSON');
        $this->assertSame('12345', $this->plzAus($json));
    }

    // AK2
    public function test_plz_mit_anfuehrungszeichen_bleibt_gueltiges_json(): void
    {
        $plz = '12"345';
        $json = $this->jsonAus($this->ausgabeMitPlz($plz));

        $this->assertNotNull($json, 'Anfuehrungszeichen in der PLZ brechen das JSON');
        $this->assertSame($plz, $this->plzAus($json));
    }

    // AK2
    public function test_plz_mit_script_ende_beendet_den_block_nicht_vorzeitig(): void
    {
        $plz = '</script><script>alert(1)</script>';
        $ausgabe = $this->ausgabeMitPlz($plz);

        $this->assertSame(1, substr_count($ausgabe, '</script>'), 'Zweites </script> in der Ausgabe');
        $this->assertSame(1, substr_count($ausgabe, '<script'), 'Zweites <script in der Ausgabe');
        $json = $this->jsonAus($ausgabe);
        $this->assertNotNull($json, 'Block-Inhalt ist kein gueltiges JSON');
        $this->assertSame($plz, $this->plzAus($json));
    }

    // Randfall: PLZ leer
    public function test_leere_plz_ergibt_leeren_wert(): void
    {
        $ausgabe = $this->ausgabeMitPlz('');

        $this->assertStringContainsString('"postalCode": ""', $ausgabe);
    }

    // Randfall: PLZ leer
    public function test_leere_plz_ergibt_gueltiges_json(): void
    {
        $json = $this->jsonAus($this->ausgabeMitPlz(''));

        $this->assertNotNull($json);
        $this->assertSame('', $this->plzAus($json));
    }

    // Randfall: vendor:publish bleibt wie bisher
    public function test_vendor_publish_veroeffentlicht_die_config_weiter(): void
    {
        $pfade = ServiceProvider::pathsToPublish(SchemaServiceProvider::class);

        $this->assertSame(
            [realpath(__DIR__.'/../../src/Config/config.php') => config_path('schemaOrg.php')],
            array_combine(array_map('realpath', array_keys($pfade)), array_values($pfade))
        );
    }

    /**
     * @param  Application  $app
     */
    protected function eigeneHostConfig($app): void
    {
        $app['config']->set('schemaOrg', ['localBusiness' => ['postalCode' => '99999']]);
    }

    // Randfall: eigene Werte des Hosts gelten
    #[DefineEnvironment('eigeneHostConfig')]
    public function test_eigene_werte_des_hosts_gelten(): void
    {
        $this->assertSame('99999', config('schemaOrg.localBusiness.postalCode'));
        $this->assertSame(['postalCode' => '99999'], config('schemaOrg.localBusiness'));
    }
}
