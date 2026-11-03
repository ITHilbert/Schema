<?php

declare(strict_types=1);

namespace ITHilbert\Schema\Tests\Feature;

use Illuminate\Support\Facades\File;
use Illuminate\Support\ServiceProvider;
use ITHilbert\Schema\Schema;
use ITHilbert\Schema\SchemaServiceProvider;
use ITHilbert\Schema\Tests\TestCase;
use ITHilbert\Schema\Types\AggregateRating;
use ITHilbert\Schema\Types\LocalBusiness;
use ITHilbert\Schema\Types\Offers;
use ITHilbert\Schema\Types\Product;
use ITHilbert\Schema\Types\Review;
use PHPUnit\Framework\Attributes\DataProvider;

/**
 * Spec schema-config-merge: Paket-Config wird zusammengefuehrt, mit neutralen Vorgaben
 * (keine erfundenen Bewertungen, keine Beispiel-Angaben).
 */
final class ConfigMergeTest extends TestCase
{
    private const GRUPPEN = ['breadcrumb', 'review', 'aggregateRating', 'product', 'localBusiness', 'offers'];

    /**
     * @return array<string, mixed>
     */
    private function jsonAus(string $ausgabe): array
    {
        $this->assertSame(1, preg_match('#<script type="application/ld\+json">(.*)</script>#s', $ausgabe, $m), 'Kein einzelner ld+json-Block');
        $json = json_decode($m[1], true);
        $this->assertIsArray($json, 'Block ist kein gueltiges JSON');

        return $json;
    }

    /**
     * @return array<string, mixed>
     */
    private function paketConfig(): array
    {
        return require __DIR__.'/../../src/Config/config.php';
    }

    private function readme(): string
    {
        return (string) file_get_contents(__DIR__.'/../../README.md');
    }

    private function alsSchema(Review $review): string
    {
        $schema = new Schema;
        $schema->review = $review;

        return $schema->getSchema();
    }

    // AK1
    public function test_ohne_veroeffentlichte_config_liefert_config_alle_gruppen_des_pakets(): void
    {
        $config = config('schemaOrg');

        $this->assertIsArray($config);
        foreach (self::GRUPPEN as $gruppe) {
            $this->assertArrayHasKey($gruppe, $config, "Gruppe {$gruppe} fehlt");
            $this->assertIsArray($config[$gruppe]);
        }
    }

    // AK1
    public function test_zusammengefuehrte_gruppen_entsprechen_der_paket_config(): void
    {
        $paket = $this->paketConfig();

        $this->assertSame(array_keys($paket), array_keys((array) config('schemaOrg')));
        foreach ($paket as $gruppe => $werte) {
            $this->assertSame(array_keys($werte), array_keys((array) config("schemaOrg.{$gruppe}")), "Schluessel von {$gruppe} weichen ab");
        }
    }

    /**
     * Setzt die Config des Hosts (nur diese Gruppen) und fuehrt danach die Paket-Config
     * erneut zusammen - so steht die Host-Config nachweislich VOR dem Zusammenfuehren.
     *
     * @param  array<string, array<string, mixed>>  $hostGruppen
     */
    private function hostConfigVorDemZusammenfuehren(array $hostGruppen): void
    {
        config()->set('schemaOrg', $hostGruppen);
        $this->assertSame(array_keys($hostGruppen), array_keys((array) config('schemaOrg')), 'Aufbau: nur Host-Gruppen vor dem Zusammenfuehren');

        (new SchemaServiceProvider($this->app))->register();
    }

    /**
     * @return array<string, array<string, mixed>>
     */
    private function hostGruppen(): array
    {
        return [
            'review' => ['ratingValue' => '4'],
            'aggregateRating' => ['ratingValue' => '4'],
            'localBusiness' => ['postalCode' => '99999'],
        ];
    }

    // AK2
    public function test_gruppe_des_hosts_ersetzt_die_paket_gruppe_vollstaendig(): void
    {
        $paket = $this->paketConfig();
        $this->assertArrayHasKey('bestRating', $paket['review'], 'Aufbau: Paket-Gruppe hat weitere Schluessel');
        $this->assertArrayHasKey('namePerson', $paket['review']);

        $this->hostConfigVorDemZusammenfuehren($this->hostGruppen());

        $this->assertSame(['ratingValue' => '4'], config('schemaOrg.review'));
        $this->assertSame(['ratingValue' => '4'], config('schemaOrg.aggregateRating'));
        $this->assertSame(['postalCode' => '99999'], config('schemaOrg.localBusiness'));
        $this->assertArrayNotHasKey('bestRating', (array) config('schemaOrg.review'));
        $this->assertArrayNotHasKey('namePerson', (array) config('schemaOrg.review'));
        $this->assertArrayNotHasKey('ratingCount', (array) config('schemaOrg.aggregateRating'));
        $this->assertArrayNotHasKey('url', (array) config('schemaOrg.localBusiness'));
    }

    // AK2
    public function test_nicht_vom_host_gesetzte_gruppen_kommen_aus_dem_paket(): void
    {
        $this->hostConfigVorDemZusammenfuehren($this->hostGruppen());
        $paket = $this->paketConfig();

        $this->assertSame($paket['product'], config('schemaOrg.product'));
        $this->assertSame($paket['offers'], config('schemaOrg.offers'));
        $this->assertSame($paket['breadcrumb'], config('schemaOrg.breadcrumb'));
        $this->assertSame('EUR', config('schemaOrg.offers.priceCurrency'));
        // Gegenprobe: die Host-Gruppen sind nicht durch Paket-Gruppen ersetzt
        $this->assertSame(['postalCode' => '99999'], config('schemaOrg.localBusiness'));
    }

    // AK2
    public function test_host_werte_gelten_in_der_ausgabe(): void
    {
        $this->hostConfigVorDemZusammenfuehren($this->hostGruppen());

        $schema = new Schema;
        $schema->useAggregateRating();
        $schema->useLocalBusiness();
        $ausgabe = $schema->getSchema();

        $this->assertStringContainsString('"postalCode": "99999"', $ausgabe);
        // Gruppe aggregateRating des Hosts ersetzt die des Pakets: ohne ratingCount kein Block
        $this->assertArrayNotHasKey('aggregateRating', $this->jsonAus($ausgabe));
        // Gegenprobe: Gruppen aus dem Paket sind vorhanden
        $this->assertSame('EUR', config('schemaOrg.offers.priceCurrency'));
    }

    // AK3
    public function test_aggregate_rating_gibt_mit_paket_vorgaben_keinen_block_aus(): void
    {
        $this->assertArrayHasKey('ratingCount', (array) config('schemaOrg.aggregateRating'));
        $this->assertEmpty(config('schemaOrg.aggregateRating.ratingCount'));
        $schema = new Schema;
        $schema->useAggregateRating();
        $this->assertArrayNotHasKey('aggregateRating', $this->jsonAus($schema->getSchema()));

        // Gegenprobe: mit Host-Werten erscheint der Block
        config(['schemaOrg.aggregateRating.ratingCount' => 3, 'schemaOrg.aggregateRating.ratingValue' => '4']);
        $mit = new Schema;
        $mit->useAggregateRating();
        $json = $this->jsonAus($mit->getSchema());
        $this->assertSame('3', $json['aggregateRating']['ratingCount']);
        $this->assertSame('4', $json['aggregateRating']['ratingValue']);
    }

    // AK3
    public function test_review_hat_mit_paket_vorgaben_keinen_author(): void
    {
        $this->assertArrayHasKey('namePerson', (array) config('schemaOrg.review'));
        $this->assertEmpty(config('schemaOrg.review.namePerson'));
        $schema = new Schema;
        $schema->useReview('Firma X', 'Organization');
        $this->assertArrayNotHasKey('author', $this->jsonAus($schema->getSchema())['review']);

        // Gegenprobe: mit Host-Wert erscheint der author
        config(['schemaOrg.review.namePerson' => 'Anna']);
        $mit = new Schema;
        $mit->useReview('Firma X', 'Organization');
        $this->assertSame('Anna', $this->jsonAus($mit->getSchema())['review']['author']['name']);
    }

    // AK3
    public function test_review_hat_mit_paket_vorgaben_einen_leeren_rating_value(): void
    {
        $this->assertArrayHasKey('ratingValue', (array) config('schemaOrg.review'));
        $this->assertEmpty(config('schemaOrg.review.ratingValue'));
        $schema = new Schema;
        $schema->useReview('Firma X', 'Organization');
        $this->assertSame('', $this->jsonAus($schema->getSchema())['review']['reviewRating']['ratingValue']);

        // Gegenprobe: mit Host-Wert steht er in der Ausgabe
        config(['schemaOrg.review.ratingValue' => '4']);
        $mit = new Schema;
        $mit->useReview('Firma X', 'Organization');
        $this->assertSame('4', $this->jsonAus($mit->getSchema())['review']['reviewRating']['ratingValue']);
    }

    // AK3
    public function test_review_behaelt_best_rating_5_als_skala(): void
    {
        $this->assertEquals(5, config('schemaOrg.review.bestRating'));
        $schema = new Schema;
        $schema->useReview('Firma X', 'Organization');

        $this->assertSame('5', $this->jsonAus($schema->getSchema())['review']['reviewRating']['bestRating']);
    }

    // AK3
    public function test_produkt_und_local_business_folgen_denselben_bewertungs_vorgaben(): void
    {
        $this->assertEmpty(config('schemaOrg.aggregateRating.ratingCount'));
        $this->assertEmpty(config('schemaOrg.review.namePerson'));
        $p = new Product('Tisch', 'Ein Tisch');
        $p->useReview();
        $p->useAggregateRating();
        $l = new LocalBusiness;
        $l->useReview();
        $l->useAggregateRating();

        foreach ([$p->getSchema(), $l->getSchema()] as $fragment) {
            $this->assertStringNotContainsString('"aggregateRating"', $fragment);
            $this->assertStringNotContainsString('"author"', $fragment);
            $this->assertStringContainsString('"ratingValue": ""', $fragment);
        }
    }

    // AK4
    public function test_ausgabe_enthaelt_mit_paket_vorgaben_keine_marke(): void
    {
        $this->assertArrayHasKey('brand', (array) config('schemaOrg.product'));
        $this->assertEmpty(config('schemaOrg.product.brand'));
        $schema = new Schema;
        $schema->useProduct('Tisch', 'Ein Tisch');
        $this->assertArrayNotHasKey('brand', $this->jsonAus($schema->getSchema())['Product']);

        // Gegenprobe: mit Host-Wert erscheint die Marke
        config(['schemaOrg.product.brand' => 'Acme']);
        $mit = new Schema;
        $mit->useProduct('Tisch', 'Ein Tisch');
        $this->assertSame('Acme', $this->jsonAus($mit->getSchema())['Product']['brand']['name']);
    }

    // AK4
    public function test_local_business_hat_mit_paket_vorgaben_keine_oeffnungszeiten(): void
    {
        $this->assertArrayHasKey('openingHours', (array) config('schemaOrg.localBusiness'));
        $this->assertEmpty(config('schemaOrg.localBusiness.openingHours'));
        $schema = new Schema;
        $schema->useLocalBusiness();
        $this->assertArrayNotHasKey('openingHours', $this->jsonAus($schema->getSchema())['LocalBusiness']);

        // Gegenprobe: mit Host-Wert erscheinen sie
        config(['schemaOrg.localBusiness.openingHours' => 'Mo-Fr 08:00-12:00']);
        $mit = new Schema;
        $mit->useLocalBusiness();
        $this->assertSame('Mo-Fr 08:00-12:00', $this->jsonAus($mit->getSchema())['LocalBusiness']['openingHours']);
    }

    // AK4
    public function test_local_business_hat_mit_paket_vorgaben_kein_bild(): void
    {
        $this->assertArrayHasKey('image', (array) config('schemaOrg.localBusiness'));
        $this->assertEmpty(config('schemaOrg.localBusiness.image'));
        $schema = new Schema;
        $schema->useLocalBusiness();
        $this->assertArrayNotHasKey('image', $this->jsonAus($schema->getSchema())['LocalBusiness']);

        // Gegenprobe: mit Host-Wert erscheint das Bild
        config(['schemaOrg.localBusiness.image' => '/img/eigen.png']);
        $mit = new Schema;
        $mit->useLocalBusiness();
        $this->assertStringEndsWith('/img/eigen.png', $this->jsonAus($mit->getSchema())['LocalBusiness']['image']);
    }

    // AK4
    public function test_config_hat_kein_festes_price_valid_until_und_der_code_default_greift(): void
    {
        $this->assertArrayHasKey('offers', (array) config('schemaOrg'));
        $this->assertArrayNotHasKey('priceValidUntil', (array) config('schemaOrg.offers'));

        $vorher = date('Y-m-d', strtotime('+2 years'));
        $schema = new Schema;
        $schema->useOffers();
        $datum = $this->jsonAus($schema->getSchema())['offers']['priceValidUntil'];
        $nachher = date('Y-m-d', strtotime('+2 years'));

        $this->assertContains($datum, [$vorher, $nachher]);

        // Gegenprobe: mit Host-Wert erscheint dieser
        config(['schemaOrg.offers.priceValidUntil' => '2031-05-05']);
        $mit = new Schema;
        $mit->useOffers();
        $this->assertSame('2031-05-05', $this->jsonAus($mit->getSchema())['offers']['priceValidUntil']);
    }

    // AK5
    public function test_vendor_publish_registriert_die_paket_config_weiter_fuer_config_schema_org_php(): void
    {
        $pfade = ServiceProvider::pathsToPublish(SchemaServiceProvider::class);

        $this->assertSame(
            [realpath(__DIR__.'/../../src/Config/config.php') => config_path('schemaOrg.php')],
            array_combine(array_map('realpath', array_keys($pfade)), array_values($pfade))
        );
    }

    // AK5
    public function test_vendor_publish_kopiert_die_paket_config(): void
    {
        $ziel = config_path('schemaOrg.php');
        // Das Ziel liegt im eigenen Test-Verzeichnis, nie im geteilten Testbench-Skelett.
        $this->assertStringNotContainsString('testbench-core', $ziel);
        File::delete($ziel);

        try {
            $this->artisan('vendor:publish', ['--provider' => SchemaServiceProvider::class, '--force' => true])->assertExitCode(0);

            $this->assertFileExists($ziel);
            $this->assertSame(
                file_get_contents(__DIR__.'/../../src/Config/config.php'),
                file_get_contents($ziel)
            );
        } finally {
            File::delete($ziel);
        }
    }

    // AK6
    public function test_readme_nennt_dass_die_config_zusammengefuehrt_wird(): void
    {
        $this->assertMatchesRegularExpression('/zusammengeführt|mergeConfigFrom/iu', $this->readme());
    }

    // AK6
    public function test_readme_nennt_dass_eine_veroeffentlichte_gruppe_die_paket_gruppe_ganz_ersetzt(): void
    {
        $this->assertMatchesRegularExpression('/(Gruppe[^.]*ersetz|ersetz[^.]*Gruppe)/iu', $this->readme());
    }

    // AK6
    #[DataProvider('hostWerte')]
    public function test_readme_nennt_dass_der_host_den_wert_setzen_muss(string $begriff): void
    {
        $treffer = array_filter(
            preg_split('/\R\s*\R/u', $this->readme()),
            fn (string $absatz): bool => stripos($absatz, $begriff) !== false
                && stripos($absatz, 'Host') !== false
                && preg_match('/setz/iu', $absatz) === 1
        );

        $this->assertNotEmpty($treffer, "README nennt nicht, dass {$begriff} im Host gesetzt werden muss");
    }

    /**
     * @return array<string, array{string}>
     */
    public static function hostWerte(): array
    {
        return [
            'Bewertungen' => ['Bewertung'],
            'Marke' => ['Marke'],
            'Öffnungszeiten' => ['Öffnungszeiten'],
            'Bild' => ['Bild'],
        ];
    }

    // Randfall 1
    public function test_technische_vorgaben_bleiben_in_der_config_gesetzt(): void
    {
        $offers = config('schemaOrg.offers');

        $this->assertIsArray($offers);
        $this->assertSame('EUR', $offers['priceCurrency'] ?? null);
        $this->assertSame('InStock', $offers['availability'] ?? null);
        $this->assertSame('NewCondition', $offers['itemCondition'] ?? null);
        $this->assertFalse($offers['showShipping'] ?? null);
        $this->assertFalse($offers['showReturnPolicy'] ?? null);
        $this->assertSame('shippingSettings', $offers['shippingDetails'] ?? null);
        $this->assertSame('NextDay', $offers['deliveryTime'] ?? null);
        $this->assertSame('DACH', $offers['shippingDestination'] ?? null);
        $this->assertSame('MerchantReturnFreeShipping', $offers['hasMerchantReturnPolicy'] ?? null);
        $this->assertSame('DACH', $offers['applicableCountry'] ?? null);
        $this->assertSame('https://schema.org/NonRefundable', $offers['returnPolicyCategory'] ?? null);
    }

    // Randfall 1
    public function test_technische_vorgaben_erscheinen_nur_bei_use_offers(): void
    {
        $this->assertSame('EUR', config('schemaOrg.offers.priceCurrency'));
        $ohne = new Schema;
        $ohne->useProduct('Tisch', 'Ein Tisch');
        $ohne->useLocalBusiness();
        $ausgabe = $ohne->getSchema();
        $this->assertArrayNotHasKey('offers', $this->jsonAus($ausgabe));
        $this->assertStringNotContainsString('priceCurrency', $ausgabe);

        // Gegenprobe
        $mit = new Schema;
        $mit->useOffers();
        $offers = $this->jsonAus($mit->getSchema())['offers'];
        $this->assertSame('EUR', $offers['priceCurrency']);
        $this->assertSame('InStock', $offers['availability']);
        $this->assertSame('NewCondition', $offers['itemCondition']);
    }

    // Randfall 1
    public function test_versand_erscheint_nur_bei_eingeschaltetem_schalter(): void
    {
        $this->assertFalse(config('schemaOrg.offers.showShipping'));
        $schema = new Schema;
        $schema->useOffers();
        $this->assertArrayNotHasKey('shippingDetails', $this->jsonAus($schema->getSchema())['offers']);

        config(['schemaOrg.offers.showShipping' => true]);
        $mit = new Schema;
        $mit->useOffers();
        $offers = $this->jsonAus($mit->getSchema())['offers'];
        $this->assertSame('shippingSettings', $offers['shippingDetails']);
        $this->assertSame('NextDay', $offers['deliveryTime']);
        $this->assertSame('DACH', $offers['shippingDestination']);
    }

    // Randfall 1
    public function test_rueckgabe_erscheint_nur_bei_eingeschaltetem_schalter(): void
    {
        $this->assertFalse(config('schemaOrg.offers.showReturnPolicy'));
        $schema = new Schema;
        $schema->useOffers();
        $this->assertArrayNotHasKey('hasMerchantReturnPolicy', $this->jsonAus($schema->getSchema())['offers']);

        config(['schemaOrg.offers.showReturnPolicy' => true]);
        $mit = new Schema;
        $mit->useOffers();
        $offers = $this->jsonAus($mit->getSchema())['offers'];
        $this->assertSame('MerchantReturnFreeShipping', $offers['hasMerchantReturnPolicy']);
        $this->assertSame('https://schema.org/NonRefundable', $offers['returnPolicyCategory']);
        $this->assertSame('DACH', $offers['applicableCountry']);
    }

    // Randfall 2
    public function test_breadcrumb_bleibt_app_url_und_app_name(): void
    {
        $this->assertArrayHasKey('breadcrumb', (array) config('schemaOrg'));
        $this->assertSame(env('APP_URL'), config('schemaOrg.breadcrumb.rootURL'));
        $this->assertSame(env('APP_NAME'), config('schemaOrg.breadcrumb.rootName'));
        $quelle = (string) file_get_contents(__DIR__.'/../../src/Config/config.php');
        $this->assertMatchesRegularExpression("/'rootURL'\s*=>\s*env\('APP_URL'/", $quelle);
        $this->assertMatchesRegularExpression("/'rootName'\s*=>\s*env\('APP_NAME'/", $quelle);
    }

    // Randfall 2
    public function test_local_business_url_und_name_bleiben_app_url_und_app_name(): void
    {
        $this->assertArrayHasKey('localBusiness', (array) config('schemaOrg'));
        $this->assertSame(env('APP_URL', ''), config('schemaOrg.localBusiness.url'));
        $this->assertSame(env('APP_NAME', ''), config('schemaOrg.localBusiness.name'));
        $quelle = (string) file_get_contents(__DIR__.'/../../src/Config/config.php');
        $this->assertMatchesRegularExpression("/'url'\s*=>\s*env\('APP_URL'/", $quelle);
        $this->assertMatchesRegularExpression("/'name'\s*=>\s*env\('APP_NAME'/", $quelle);
    }

    // Randfall 3
    #[DataProvider('beispiele')]
    public function test_beispiele_der_alten_config_stehen_als_kommentar_nicht_als_wert(string $beispiel): void
    {
        $tokens = token_get_all((string) file_get_contents(__DIR__.'/../../src/Config/config.php'));
        $kommentare = '';
        $strings = [];
        foreach ($tokens as $i => $token) {
            if (! is_array($token)) {
                continue;
            }
            if (in_array($token[0], [T_COMMENT, T_DOC_COMMENT], true)) {
                $kommentare .= $token[1];
            }
            if ($token[0] === T_CONSTANT_ENCAPSED_STRING) {
                // Schluessel (gefolgt von =>) zaehlen nicht, nur Werte
                $n = $i + 1;
                while (isset($tokens[$n]) && is_array($tokens[$n]) && $tokens[$n][0] === T_WHITESPACE) {
                    $n++;
                }
                if (! (isset($tokens[$n]) && is_array($tokens[$n]) && $tokens[$n][0] === T_DOUBLE_ARROW)) {
                    $strings[] = trim($token[1], "'\"");
                }
            }
        }

        $this->assertStringContainsString($beispiel, $kommentare, 'Beispiel steht nicht als Kommentar');
        $this->assertNotContains($beispiel, $strings, 'Beispiel steht als Wert in der Config');
    }

    /**
     * @return array<string, array{string}>
     */
    public static function beispiele(): array
    {
        return [
            'Max' => ['Max'],
            'Marke brand' => ['brand'],
            'Oeffnungszeiten' => ['Mo-Fr 10:00-16:00'],
            'Bild' => ['/img/image.png'],
            'priceValidUntil' => ['2025-02-09'],
        ];
    }

    // Randfall 3
    public function test_beispielwerte_stehen_nicht_in_der_zusammengefuehrten_config(): void
    {
        $this->assertArrayHasKey('review', (array) config('schemaOrg'));
        $this->assertNotContains('Max', config('schemaOrg.review'));
        $this->assertNotContains('brand', config('schemaOrg.product'));
        $this->assertNotContains('/img/image.png', config('schemaOrg.localBusiness'));
        $this->assertNotContains('Mo-Fr 10:00-16:00', config('schemaOrg.localBusiness'));
    }

    // Randfall 4
    public function test_setter_ueberschreiben_bewertungen_der_config(): void
    {
        $this->assertEmpty(config('schemaOrg.aggregateRating.ratingCount'));
        $this->assertSame('', (new AggregateRating)->getSchema());
        $r = new AggregateRating;
        $r->setRatingValue(4.5);
        $r->setRatingCount(7);
        $this->assertStringContainsString('"ratingCount": "7"', $r->getSchema());
        $this->assertStringContainsString('"ratingValue": "4.5"', $r->getSchema());

        $review = new Review;
        $this->assertSame('', $this->jsonAus($this->alsSchema($review))['review']['reviewRating']['ratingValue']);
        $review->ratingValue(4);
        $review->namePerson('Anna');
        $json = $this->jsonAus($this->alsSchema($review))['review'];
        $this->assertSame('4', $json['reviewRating']['ratingValue']);
        $this->assertSame('Anna', $json['author']['name']);
    }

    // Randfall 4
    public function test_setter_ueberschreiben_marke_oeffnungszeiten_bild_und_gueltigkeit(): void
    {
        $this->assertArrayHasKey('brand', (array) config('schemaOrg.product'));
        $this->assertStringNotContainsString('"brand"', (new Product('Tisch', 'Ein Tisch'))->getSchema());
        $p = new Product('Tisch', 'Ein Tisch');
        $p->setBrand('Setter');
        $this->assertStringContainsString('"name": "Setter"', $p->getSchema());

        $l = new LocalBusiness;
        $l->setOpeningHours('Sa 10:00-12:00');
        $l->setImage('/img/setter.png');
        $ausgabe = $l->getSchema();
        $this->assertStringContainsString('"openingHours": "Sa 10:00-12:00"', $ausgabe);
        $this->assertStringContainsString('/img/setter.png"', $ausgabe);

        $o = new Offers;
        $o->setPriceValidUntil('2032-02-02');
        $this->assertStringContainsString('"priceValidUntil": "2032-02-02"', $o->getSchema());
    }

    // Randfall 4
    public function test_setter_ueberschreiben_auch_werte_aus_der_host_config(): void
    {
        config(['schemaOrg.product.brand' => 'Host', 'schemaOrg.offers.priceValidUntil' => '2030-01-01']);
        $p = new Product('Tisch', 'Ein Tisch');
        $p->setBrand('Setter');
        $o = new Offers;
        $o->setPriceValidUntil('2032-02-02');

        $this->assertStringContainsString('"name": "Setter"', $p->getSchema());
        $this->assertStringNotContainsString('Host', $p->getSchema());
        $this->assertStringContainsString('"priceValidUntil": "2032-02-02"', $o->getSchema());
    }
}
