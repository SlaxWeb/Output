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

    /**
     * Temp error template name
     *
     * @var string
     */
    protected $tplName = "";

    /**
     * Temp error style template name
     *
     * @var string
     */
    protected $styleTplName = "";

    public function testErrorHandling()
    {

    }

    protected function _before()
    {
        $this->response = Mockery::mock(\SlaxWeb\Router\Response::class);

        $this->createErrorTemplates();
    }

    protected function _after()
    {
        $this->removeErrorTemplates();
    }

    protected function createErrorTemplates()
    {
        if ($this->tplName !== "" && $this->styleTplName !== "") {
            // templates exist, re-use
            return;
        }

        $hash = sha1(time());
        $this->tplName = realpath(__DIR__) . "/errorTemplate_{$hash}.php";
        $this->styleTplName = realpath(__DIR__) . "/styleTemplate_{$hash}.html";

        file_put_contents($this->tplName, "<?=\$severity;?><?=\$message;?><?=\$file;?><?=\$line;?>");
        file_put_contents($this->styleTplName, "style template\n");
    }

    protected function removeErrorTemplates()
    {
        if (file_exists($this->tplName)) {
            unlink($this->tplName);
        }
        if (file_exists($this->styleTplName)) {
            unlink($this->styleTplName);
        }
        $this->tplName = "";
        $this->styleTplName = "";
    }
}
