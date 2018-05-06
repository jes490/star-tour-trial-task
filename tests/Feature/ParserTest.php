<?php

namespace Tests\Feature;

use Tests\TestCase;

class ParserTest extends TestCase
{
    protected $basicRequest;

    protected function setUp()
    {
        parent::setUp();
        $this->basicRequest = '
        Lorem Ipsum is simply dummy text of the printing and typesetting industry. 
        Lorem Ipsum has been the industry\'s ' . route('test') . ' standard dummy 
        text ever since the 1500s, when an unknown printer id="main_content" took a galley 
        of type and scrambled it to make a type class="main_block_of_content" specimen book. It has survived not only class="mboc_text"
        five centuries, but also the leap into electronic ' . route('test') . ' typesetting, remaining id="secondary_content" essentially unchanged. 
        It was populari sed in the 1960s with the release of Letraset sheets containing Lorem Ipsum passages, 
        and more recently with desktop ' . route('test') . ' publishing software  id="main_content" like Aldus PageMaker 
        including versions of Lorem Ipsum ons of ets containing Lorem Ipsu id="delete_tag_test" 
        Lorem Ipsuand more recently with desktop ' . route('test') . ' including versions of Lorem publishing software  id="main_content" like 
        Aldus PageMaker ncluding versions of Lorem Ipsum ons of id="save_tag_test"  ' . route('test') . ' including versions of Lorem 
        publishing software  id="main_content" like including versions of Lorem Ipsum ons of id="inline_style_test" fd
        ' . route('test') . ' including versions of Lorem  1960s with the release of Lfdsdf class="mboc_text" class="main_block_of_content"
        t was populari sed in the 1960s with the release of Letraset sheetsem Ipsum ons of fdsf sdf sdf sdf sd
        http://task.com/not_existing_url lfj dslkfjsdlfjdl class="not_existing_selector"';
    }

    /**
     * A basic test example.
     *
     * @return void
     */
    public function testParserWorks()
    {
        $response = $this->json('POST', '/api/parse', ['test' => $this->basicRequest]);

        $response->assertStatus(200);
    }

    /**
     * Test simple response.
     *
     * @return void
     */
    public function testSimpleResponse()
    {
        $response = $this->json('POST', '/api/parse', ['text' => $this->basicRequest]);

        $parsedResponse = file_get_contents(base_path('tests/Feature/data/results/simple_test.txt'));
        $response->assertStatus(200);
        $response->assertJsonFragment([
            [
                'url' => route('test'),
                'status' => 'Success',
                'constraints' => [
                    ['constraint' => 'id="main_content"'],
                    ['constraint' => 'class="main_block_of_content"'],
                    ['constraint' => 'class="mboc_text"']
                ],
                'errors' => [],
                'results' => [
                    ['result' => $parsedResponse]
                ]
            ]
        ]);
    }

    /**
     * Test multiple results.
     */
    public function testMultipleResults()
    {
        $response = $this->json('POST', '/api/parse', ['text' => $this->basicRequest]);

        $parsedResponse = file_get_contents(base_path('tests/Feature/data/results/multiple_results.txt'));
        $response->assertStatus(200);
        $response->assertJsonFragment([
            [
                'url' => route('test'),
                'status' => 'Success',
                'constraints' => [
                    ['constraint' => 'id="secondary_content"'],
                ],
                'errors' => [],
                'results' => [
                    ['result' => $parsedResponse],
                    ['result' => $parsedResponse],
                ]
            ],
        ]);
    }

    /**
     * Test Tag Deletion
     */
    public function testTagDeletion()
    {
        $response = $this->json('POST', '/api/parse', ['text' => $this->basicRequest]);

        $parsedResponse = file_get_contents(base_path('tests/Feature/data/results/delete_tag_test.txt'));
        $response->assertStatus(200);
        $response->assertJsonFragment([
            [
                'url' => route('test'),
                'status' => 'Success',
                'constraints' => [
                    ['constraint' => 'id="main_content"'],
                    ['constraint' => 'id="delete_tag_test"'],
                ],
                'errors' => [],
                'results' => [
                    ['result' => $parsedResponse]
                ]
            ],
        ]);
    }

    /**
     * Test needed saved tags.
     */
    public function testSaveTag()
    {
        $response = $this->json('POST', '/api/parse', ['text' => $this->basicRequest]);

        $parsedResponse = file_get_contents(base_path('tests/Feature/data/results/save_tag_test.txt'));
        $response->assertStatus(200);
        $response->assertJsonFragment([
            [
                'url' => route('test'),
                'status' => 'Success',
                'constraints' => [
                    ['constraint' => 'id="main_content"'],
                    ['constraint' => 'id="save_tag_test"'],
                ],
                'errors' => [],
                'results' => [
                    ['result' => $parsedResponse]
                ]
            ],
        ]);
    }

    /**
     * Test inline styles deleted.
     */
    public function testInlineStylesDeleted()
    {
        $response = $this->json('POST', '/api/parse', ['text' => $this->basicRequest]);

        $parsedResponse = file_get_contents(base_path('tests/Feature/data/results/inline_style_test.txt'));
        $response->assertStatus(200);
        $response->assertJsonFragment([
            [
                'url' => route('test'),
                'status' => 'Success',
                'constraints' => [
                    ['constraint' => 'id="main_content"'],
                    ['constraint' => 'id="inline_style_test"'],
                ],
                'errors' => [],
                'results' => [
                    ['result' => $parsedResponse]
                ]
            ],
        ]);
    }

    /**
     * Test constraints used in right order.
     */
    public function testConstraintsOrder()
    {
        $response = $this->json('POST', '/api/parse', ['text' => $this->basicRequest]);
        
        $response->assertStatus(200);
        $response->assertJsonFragment([
            [
                'url' => route('test'),
                'status' => 'Success',
                'constraints' => [
                    ['constraint' => 'class="mboc_text"'],
                    ['constraint' => 'class="main_block_of_content"'],
                ],
                'errors' => [],
                'results' => []
            ],
        ]);
    }

    /**
     * Test if errors processed correctly.
     */
    public function testNotExistingUrl()
    {
        $response = $this->json('POST', '/api/parse', ['text' => $this->basicRequest]);

        $response->assertStatus(200);
        $response->assertJsonFragment([
            [
                'url' => 'http://task.com/not_existing_url',
                'status' => 'Error',
                'constraints' => [
                    ['constraint' => 'class="not_existing_selector"'],
                ],
                'errors' => [
                    ['message' => 'CURL: cannot get remote content.', 'HTTPCode' => '404'],
                    ['message' => 'PhantomJS: cannot get remote content.', 'HTTPCode' => '404']
                ],
                'results' => []
            ],
        ]);
    }
}
