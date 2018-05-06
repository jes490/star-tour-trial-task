<?php

namespace App\Services;

use App\Http\Resources\TaskRequestCollection;


/**
 * ParserInterface.
 *
 * @author Alexey Sesyolkin
 * @email aseselkin@gmail.com
 */
interface ParserInterface
{
    /**
     * Returns parsed data.
     *
     * @param string $text
     * @return TaskRequestCollection
     */
    public function parse($text);
}
