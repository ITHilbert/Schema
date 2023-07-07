<?php
namespace ITHilbert\Schema;

use Illuminate\Support\Facades\Request;

class Review{

    public $ratingValue;
    public $bestRating;
    public $namePerson;
    public $reviewBody;
    public $datePublished;
    public $itemReviewed;
    public $itemReviewedType;       //Organization / Product


    public function __construct()
    {
        $this->ratingValue = config('schemaOrg.review.ratingValue',5);
        $this->bestRating = config('schemaOrg.review.bestRating',5);
        $this->namePerson = config('schemaOrg.review.namePerson','');
        $this->reviewBody = config('schemaOrg.review.reviewBody','');
        $this->datePublished = config('schemaOrg.review.datePublished','');

    }

    public function getSchema(){
        $schema = "\t\t\t".'"review": {'."\n";
        $schema .= "\t\t\t\t".'"@type": "Review",'."\n";
        if($this->itemReviewed != ''){
            $schema .= "\t\t\t\t".'"itemReviewed": {'."\n";
            $schema .= "\t\t\t\t\t".'    "@type": "'.$this->itemReviewedType.'",'."\n";
            $schema .= "\t\t\t\t\t".'    "name": "'.$this->itemReviewed.'"'."\n";
            $schema .= "\t\t\t\t},\n";
        }

        if($this->reviewBody != '') $schema .= "\t\t\t".'"reviewBody": "'.$this->reviewBody.'",'."\n";
        $schema .= "\t\t\t\t".'"reviewRating": {'."\n";
        $schema .= "\t\t\t\t\t".'"@type": "Rating",'."\n";
        $schema .= "\t\t\t\t\t".'"ratingValue": "'.$this->ratingValue.'",'."\n";
        $schema .= "\t\t\t\t\t".'"bestRating": "'.$this->bestRating.'"'."\n";
        $schema .= "\t\t\t\t".'},'."\n";
        //Author
        if($this->namePerson != ''){
            $schema .= "\t\t\t\t".'"author": {'."\n";
            $schema .= "\t\t\t\t\t".'"@type": "Person",'."\n";
            $schema .= "\t\t\t\t\t".'"name": "'.$this->namePerson.'"'."\n";
            $schema .= "\t\t\t\t},\n";
        }

        if($this->datePublished != '') $schema .= "\t\t\t\t".'"datePublished": "'.$this->datePublished.'",'."\n";

        //Letzte Komma wieder entfernen
        $schema = substr($schema, 0, -2) . "\n";

        $schema .= "\t\t\t},\n";

        return $schema;
    }
}
