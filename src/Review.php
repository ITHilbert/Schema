<?php
namespace ITHilbert\Schema;

use Illuminate\Support\Facades\Request;

class Review{
    public function Review(){
        $rv = '"review": {';
        $rv .= '"@type": "Review",';
        $rv .= '"reviewRating": {';
        $rv .= '"@type": "Rating",';
        $rv .= '"ratingValue": "5",';
        $rv .= '"bestRating": "5"';
        $rv .= '},';
        $rv .= '"author": {';
        $rv .= '"@type": "Person",';
        $rv .= '"name": "Max"';
        $rv .= '}';
        $rv .= '}';

        return $rv;
    }
}
