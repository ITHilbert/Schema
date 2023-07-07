# Schema

Klassen um meine Schema.org Jsons zu erstellen


## Installation
In der composer.json folgendes einfügen:
```
"require": {
        "vendor/package-private": "*"
    },
    "repositories": [
        {
            "type": "vcs",
            "url": "<repository-url>"
        }
    ]
```
danach 
```
composer intsall
```
### copy config file
```
php artisan vendor:publish --provider="ITHilbert\Schema\SchemaServiceProvider" 
```


### config/schemaOrg.php
Hier kann alles konfiguriert werden.

## Beispiel:
```
use ITHilbert\Schema\Schema;

...

$schema = new Schema();
$schema->useBreadcrumb();
$schema->breadcrumb->add('Startseite', 'http://localhost:8000/');

$schema->useLocalBusiness();
$schema->localBusiness->useReview();
$schema->localBusiness->useAggregateRating();

$schema->localBusiness->useFAQ();
$schema->localBusiness->faq->add('macht Ihr das auch', 'ja');

echo $schema->getSchema();
```


## ToDo


## Author
IT-Hilbert GmbH

Access, Excel, VBA und Web Programmierungen

Homepage: [IT-Hilbert.com](https://www.IT-Hilbert.com) 
