<?php
namespace SlaxWeb\Output\Tests\Unit;

use Mockery as m;

class JsonHandlerTest extends \Codeception\Test\Unit
{
    /**
     * @var \UnitTester
     */
    protected $tester;

    public function testRender()
    {
        $handler = new \SlaxWeb\Output\Handler\Json;
        $handler->add(["foo" => "bar"])->addError("baz", 400, ["data"]);
        $output = json_encode([
            "data"  =>  [
                "foo"   =>  "bar",
            ],
            "errors"    =>  [[
                "message"   =>  "baz",
                "data"      =>  ["data"]
            ]]
            
        ]);
        $this->assertEquals($output, $handler->render());
        $this->assertEquals(400, $handler->getStatusCode());
    }

    protected function _before()
    {
    }

    protected function _after()
    {
    }
}
