<?php
namespace ITHilbert\Schema\Types;


class FAQ{

    private $faqs = array();

    public function add($question, $answer){
        $quest['question'] = $question;
        $quest['answer'] = $answer;

        $this->faqs[] = $quest;
    }

    /**
     * Liefert ein FAQ Schema.org zurück
     *
     * @param string $faqType
     * @return void
     */
    public function getSchema(){
        $schema = "\t\t\t".'"FAQPage": {' ."\n";
        $schema .= "\t\t\t\t". '"@type": "FAQPage",'."\n";
        $schema .= "\t\t\t\t".'"mainEntity": ['."\n";

        foreach($this->faqs as $faq){
            $schema .= "\t\t\t\t\t".'{'."\n";
            $schema .= "\t\t\t\t\t\t".'"@type": "Question",'."\n";
            $schema .= "\t\t\t\t\t\t".'"name": "' . $faq['question'] .'",'."\n";
            $schema .= "\t\t\t\t\t\t".'"acceptedAnswer": {'."\n";
            $schema .= "\t\t\t\t\t\t\t".'"@type": "Answer",'."\n";
            $schema .= "\t\t\t\t\t\t\t".'"text": "'.$faq['answer'] .'"'."\n";
            $schema .= "\t\t\t\t\t\t".'}'."\n";
            $schema .= "\t\t\t\t\t".'},'."\n";
        }
        //Letztes Komma abschneiden
        $schema = substr($schema, 0, -2) . "\n";
        $schema .= "\t\t\t\t]\n";
        $schema .= "\t\t\t},\n";

        return $schema;
    }


}
