<?php
namespace ITHilbert\Schema\Helper;


abstract class SchemaOrg{

    abstract public function getSchema();

    public function __toString()
    {
        return $this->getSchema();
    }

    protected function removeLastKomma($schema){
        //Letzte Komma wieder entfernen
        return substr($schema, 0, -2) . "\n";
    }
}
