<?php
namespace ITHilbert\Schema;

use Illuminate\Support\Facades\Request;

class Breadcrumb{

    private $breadcrumbs = array();

    public function add($title, $url){
        $breadcrumb['name'] = $title;
        $breadcrumb['item'] = $url;
        $breadcrumb['position'] = (count($this->breadcrumbs)+1);

        $this->breadcrumbs[] = $breadcrumb;
    }

    public function getSchema(){
        $schema = "\t\t".'"BreadcrumbList": {'. "\n";
        $schema .= "\t\t\t".'"@type": "BreadcrumbList",'. "\n";
        $schema .= "\t\t\t".'"itemListElement": ['. "\n";
        foreach($this->breadcrumbs as $bc){
            $schema .= "\t\t\t\t".'{'. "\n";
            $schema .= "\t\t\t\t\t".'"@type": "ListItem",'. "\n";
            $schema .= "\t\t\t\t\t".'"position": '.$bc['position'].','. "\n";
            $schema .= "\t\t\t\t\t".'"name": "'.$bc['name'].'",'. "\n";
            $schema .= "\t\t\t\t\t".'"item": "'.$bc['item'].'"'. "\n";
            $schema .= "\t\t\t\t".'},'. "\n";
        }
        //Letzte Komma wieder entfernen
        $schema = substr($schema, 0, -2) . "\n";

        $schema .= "\t\t\t".']'. "\n";          // Ende @type": "BreadcrumbList
        $schema .= "\t\t".'},'. "\n";            // Ende BreadcrumbList


        return $schema;
    }
}
