<?php
namespace SlaxWeb\Output\Tests\Unit;

use Mockery as m;

class ViewHandlerTest extends \Codeception\Test\Unit
{
    /**
     * @var \UnitTester
     */
    protected $tester;

    public function testRender()
    {
        $view1 = m::mock(\SlaxWeb\View\Base::class);
        $view1->shouldReceive("render")->andReturn("view1");

        $view2 = m::mock(\SlaxWeb\View\Base::class);
        $view2->shouldReceive("render")->andReturn("view2");

        $handler = new \SlaxWeb\Output\Handler\View;
        $handler->add($view1)->add($view2);
        $output = $handler->render();
        $this->assertEquals("view1view2", $output);
    }

    public function testViewData()
    {
        $view = m::mock(\SlaxWeb\View\Base::class);
        $view->shouldReceive("render")->with(
            ["baz" => "qux"],
            \SlaxWeb\View\AbstractLoader::TPL_RETURN,
            \SlaxWeb\View\AbstractLoader::TPL_NO_CACHE_VARS
        );

        $handler = new \SlaxWeb\Output\Handler\View;
        $handler
            ->add($view)
            ->addData(["foo" => "bar"])
            ->addData(["baz" => "qux"], get_class($view));
        $handler->render();
    }

    protected function _before()
    {
    }

    protected function _after()
    {
    }
}
