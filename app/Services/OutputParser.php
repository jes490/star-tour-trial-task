<?php

namespace App\Services;

use PHPHtmlParser\Dom;
use IvoPetkov\HTML5DOMDocument;

/**
 * Remote content parser.
 *
 * Class OutputParser
 * @author Alexey Sesyolkin
 * @email aseselkin@gmail.com
 */
class OutputParser
{
    /**
     * Parse html by XPath.
     *
     * @param $text
     * @param $constraints
     * @return array
     */
    public function parse($text, $constraints)
    {
        //$query = $this->buildQuery($constraints);
        
        $dom = new Dom;
        $dom->load(mb_convert_encoding($text, 'HTML-ENTITIES', 'UTF-8'));
 
        foreach ($constraints as $constraint)
            $dom = $dom->find($this->prepareSelector($constraint));
        
        $results = [];

        if (count ($constraints) > 0)
        {
            foreach ($dom as $element)
                $results[] = html_entity_decode($element->innerHTML);
        }
        else
        {
            $results[] = $text;
        }

        return $results;
    }

    /**
     * Query DOM Document with XPath.
     *
     * @param $constraints
     * @return \DOMNodeList
     */
    protected function buildQuery($constraints)
    {
        $query = [];
        foreach ($constraints as $constraint)
            $query[] = $this->prepareSelector($constraint);
            
        return implode(' ', $query);
    }

    /**
     * Prepare selector
     * 
     * @param $constraint
     * @return null|string|string[]
     */
    protected function prepareSelector($constraint)
    {
        $constraint = preg_replace('/id="([^"]+)"/', '#${1}', $constraint);
        $constraint = preg_replace('/class="([^"]+)"/', '.${1}', $constraint);
        
        return $constraint;
    }
    
}
