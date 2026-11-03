# ITHilbert Schema

**Beschreibung**: Erstellen von Schema.org Jsons.

## Dokumentation

Die ausführliche Dokumentation befindet sich im Ordner `docs/`:
- [Kontext & Zielsetzung](docs/00_README_Kontext.md)
- [Architekturübersicht](docs/01_Architekturübersicht.md)
- [Modulstruktur](docs/07_Modulstruktur.md)

## Installation

```bash
composer require ithilbert/schema
```

## Namespace
`ITHilbert\Schema`

## Konfiguration

Die Paket-Config (`src/Config/config.php`) wird per `mergeConfigFrom` unter `schemaOrg` zusammengeführt; eine veröffentlichte Datei ist nicht nötig. Veröffentlichen geht weiter mit:

```bash
php artisan vendor:publish --provider="ITHilbert\Schema\SchemaServiceProvider"
```

Setzt der Host in `config/schemaOrg.php` eine Gruppe (z. B. `review`), ersetzt diese Gruppe die Gruppe des Pakets ganz; nicht gesetzte Gruppen kommen aus dem Paket.

Die Vorgaben des Pakets sind neutral, es gibt keine erfundenen Werte.

Bewertungen (`review`, `aggregateRating`) muss der Host setzen, sonst gibt `useAggregateRating()` keinen Block aus und das Review hat keinen `author` und keinen Bewertungswert.

Marke (`product.brand`), Öffnungszeiten und Bild (`localBusiness.openingHours`, `localBusiness.image`) muss der Host setzen, sonst fehlen sie im JSON-LD.
