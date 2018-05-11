<?php

namespace App\Services;

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
        $results = [];

        if (count ($constraints) > 0)
        {
            $dom = $this->loadDom($text);
            $nodes = $this->query($dom, $constraints);
            //return $nodes;
            foreach ($nodes as $result)
                $results[] = $this->innerHTML($result);

        }
        else
        {
            $results[] = $text;
        }

        return $results;
    }

    /**
     * Load DOM Document from text.
     *
     * @param $text
     * @return \DOMDocument
     */
    protected function loadDom($text)
    {
        $dom = new \DOMDocument;
        $dom->preserveWhiteSpace = false;
        //libxml_use_internal_errors(true);
        $tidy = tidy_parse_string($text, ['clean' => true, 'output-xhtml' => true, 'show-body-only' => true, 'wrap' => 0], 'UTF8');
        $tidy->cleanRepair();
        $dom->loadHTML( (string) $text);
        
        return $dom;
    }

    /**
     * Query DOM Document with XPath.
     *
     * @param $dom
     * @param $constraints
     * @return \DOMNodeList
     */
    protected function query($dom, $constraints)
    {
        $xpath = new \DOMXPath($dom);
        $query = $this->buildQuery($constraints);
        $nodes = $xpath->query('//' . $query);
        //return $query;
        return $nodes;
    }

    /**
     * Build query based on constraints.
     *
     * @param $constraints
     * @return null|string|string[]
     */
    protected function buildQuery($constraints)
    {
        $query = implode('/', $constraints);
        $query = preg_replace('/((?:class|id)="[^"]+")/m', '*[@$1]', $query);

        return $query;
    }

    /**
     * Get inner html from DOM Element.
     *
     * @param \DOMElement $element
     * @return string
     */
    protected function innerHTML(\DOMElement $element)
    {
        $innerHTML = "";
        $children  = $element->childNodes;

        foreach ($children as $child)
        {
            $innerHTML .= $element->ownerDocument->saveHTML($child);
        }

        return $innerHTML;
    }

}
