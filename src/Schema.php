<?php
namespace ITHilbert\Schema;

use Illuminate\Support\Facades\Request;

class Schema{

    /**
     * Öffnet einen Schema.org Script
     *
     * @return string
     */
    public function open(){
        $schema = '<script type="application/ld+json">'. "\n";
        $schema .= "\t{\n";
        $schema .= "\t\t". '"@context": "https://schema.org/",'. "\n";

        return $schema;
    }

    /**
     * Schließt das Schema.org Script
     *
     * @return string
     */
    public function close(){
        $schema = "\t}\n";
        $schema .= "\t</script>\n";

        return $schema;
    }

    public function removeLastKomma($schema){
        //Letzte Komma wieder entfernen
        return substr($schema, 0, -2) . "\n";
    }
}
