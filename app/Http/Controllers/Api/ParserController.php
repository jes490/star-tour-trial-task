<?php

namespace App\Http\Controllers\Api;


use App\Http\Resources\TaskRequestCollection;
use App\Services\ParserInterface;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;


class ParserController extends Controller
{
    /**
     * Parse incoming request.
     *
     * @param Request $request
     * @param ParserInterface $parser
     * @return TaskRequestCollection
     */
    public function parse(Request $request, ParserInterface $parser)
    {
        $results = $parser->parse($request->text);

        return $results;
    }



}
