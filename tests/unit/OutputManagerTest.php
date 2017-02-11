<?php
namespace SlaxWeb\Output\Tests\Unit;

use Mockery;

class OutputManagerTest extends \Codeception\Test\Unit
{
    /**
     * @var \UnitTester
     */
    protected $tester;

    /**
     * Response mock
     *
     * @var \Mockery\MockInterface
     */
    protected $response = null;

    protected function _before()
    {
        $this->response = Mockery::mock(\SlaxWeb\Router\Response::class);
    }

    protected function _after()
    {
    }

    public function testErrorHandling()
    {

    }
}
