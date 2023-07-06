<?php
namespace ITHilbert\Schema;

use Illuminate\Support\Facades\Request;

class AggregateRating{

    public float $ratingValue;
    public float $bestRating;
    public int $ratingCount;

    public function __construct()
    {
        $this->ratingValue = config('schemaOrg.aggregateRating.ratingValue');
        $this->bestRating = config('schemaOrg.aggregateRating.bestRating');
        $this->ratingCount = config('schemaOrg.aggregateRating.ratingCount');
    }

    public function getSchema(){
        $ar = "\t\t". '"aggregateRating": {'."\n";
        $ar .= "\t\t\t".'"@type": "AggregateRating",'."\n";
        $ar .= "\t\t\t".'"ratingValue": "'. $this->ratingValue .'",'."\n";
        $ar .= "\t\t\t".'"bestRating": "'. $this->bestRating .'",'."\n";
        $ar .= "\t\t\t".'"ratingCount": "'. $this->ratingCount .'"'."\n";
        $ar .= "\t\t".'},'."\n";

        return $ar;
    }
}
