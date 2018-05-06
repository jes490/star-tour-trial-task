<?php

namespace App\Services;

/**
 * Input parser.
 *
 * Class Parser
 * @package App\Services
 * @author Alexey Sesyolkin
 * @email aseselkin@gmail.com
 */
class InputParser
{
    /**
     * Urls block pattern.
     * @var string
     */

    static $urls = '/(https?:\/\/(?:www\.)?[-a-zA-Z0-9@:%._\+~#=]{2,256}\.[a-z]{2,6}\b(?:[-a-zA-Z0-9@:%_\+.~#?&\/\/=]*))+.*?(?=class="[^"]+"|id="[^"]+"|$)/m';
    /**
     * Constraint block pattern.
     * @var string
     */
    static $constraints = '/((?:class|id)="[^"]+").*?(?=https?:\/\/(?:www\.)?[-a-zA-Z0-9@:%._\+~#=]{2,256}\.[a-z]{2,6}\b(?:[-a-zA-Z0-9@:%_\+.~#?&\/\/=]*)|$)/m';

    /**
     * Url pattern.
     * @var string
     */
    static $url = '/(https?:\/\/(?:www\.)?[-a-zA-Z0-9@:%._\+~#=]{2,256}\.[a-z]{2,6}\b(?:[-a-zA-Z0-9@:%_\+.~#?&\/\/=]*))/m';

    /**
     * Constraint pattern.
     * @var string
     */
    static $constraint = '/((?:class|id)="[^"]+")/m';

    /**
     * Parses task text.
     *
     * @param $text
     * @return array
     */
    public function parse($text)
    {
        return $this->process($text);
    }

    /**
     * Breaks text for url and constraint blocks then parses them individually.
     *
     * @param $text
     * @return array
     */
    protected function process($text)
    {
        $output = [];

        $urlBlocks = $this->extractUrlsBlock($text);
        $constraintBlocks = $this->extractConstraintsBlock($text);

        foreach ($urlBlocks as $index => $urlBlock)
        {
            $urls = $this->extractUrls($urlBlock);

            $constraints = [];
            if ($this->constraintsExists($constraintBlocks, $index))
                $constraints = $this->extractConstraints($constraintBlocks[$index]);


            foreach ($urls as $url)
            {
                $output[] = ['url' => $url, 'constraints' => $constraints];
            }
        }

        return $output;
    }

    /**
     * Extracts url blocks from given text.
     *
     * @param $text
     * @return mixed
     */
    protected function extractUrlsBlock($text)
    {
        preg_match_all(self::$urls, $text, $urlBlocks);

        return $urlBlocks[0];
    }

    /**
     * Extracts constraint blocks from given text.
     *
     * @param $text
     * @return mixed
     */
    protected function extractConstraintsBlock($text)
    {
        preg_match_all(self::$constraints, $text, $constraintBlocks);

        return $constraintBlocks[0];
    }

    /**
     * Extracts urls from given text.
     *
     * @param $text
     * @return mixed
     */
    protected function extractUrls($text)
    {
        preg_match_all(self::$url, $text, $urls);

        return $urls[1];
    }

    /**
     * Extracts constraints from given text.
     *
     * @param $text
     * @return mixed
     */
    protected function extractConstraints($text)
    {
        preg_match_all(self::$constraint, $text, $constraints);

        return $constraints[1];
    }

    /**
     * Checks if constraints exists in given block.
     *
     * @param $block
     * @param $index
     * @return bool
     */
    protected function constraintsExists($block, $index)
    {
        if (isset($block[$index]))
            return true;

        return false;
    }

}
