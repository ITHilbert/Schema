<?php
namespace ITHilbert\Schema;

use Illuminate\Support\Facades\Request;
use ITHilbert\Schema\AggregateRating;

class Product{

    public $product;
    public $description;
    public $image;
    public $url;
    public $brand;

    public function __construct()
    {
        $this->url = Request::url();
    }

    /**
     * Liefert ein Schema für ein Produkt
     *
     * @param string $produkt
     * @return string
     */
    public function getSchema(){
        $prod = "\t\t".'"@type": "Product",'."\n";
        $prod .= "\t\t".'"name": "'.$this->product.'",'."\n";
        $prod .= "\t\t".'"description": "'.$this->product.'",'."\n";
        $prod .= "\t\t".'"url": "'.$this->url.'",'."\n";
        $prod .= "\t\t".'"image": "'.$this->image.'",'."\n";

        //Brand
        $prod .= "\t\t".'"brand": {'."\n";
        $prod .= "\t\t\t".'"@type": "Brand",'."\n";
        $prod .= "\t\t\t".'"name": "'.$this->brand.'"'."\n";
        $prod .= "\t\t".'},'."\n";

        $ar = new AggregateRating;
        $prod .= $ar->getSchema();

        return $prod;
    }

}
