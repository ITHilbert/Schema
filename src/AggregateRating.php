<?php
namespace ITHilbert\Schema;

class AggregateRating{

    public float $ratingValue;
    public float $bestRating;
    public int $ratingCount;

    public function __construct()
    {
        $this->ratingValue = config('schemaOrg.aggregateRating.ratingValue',5);
        $this->bestRating = config('schemaOrg.aggregateRating.bestRating',5);
        $this->ratingCount = config('schemaOrg.aggregateRating.ratingCount',0);
    }

    public function getSchema(){
        if( $this->ratingCount == 0){
            return '';
        }
        $ar = "\t\t\t". '"aggregateRating": {'."\n";
        $ar .= "\t\t\t\t".'"@type": "AggregateRating",'."\n";
        $ar .= "\t\t\t\t".'"ratingValue": "'. $this->ratingValue .'",'."\n";
        $ar .= "\t\t\t\t".'"bestRating": "'. $this->bestRating .'",'."\n";
        $ar .= "\t\t\t\t".'"ratingCount": "'. $this->ratingCount .'"'."\n";
        $ar .= "\t\t\t".'},'."\n";

        return $ar;
    }
}
