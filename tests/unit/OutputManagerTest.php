<?php
namespace SlaxWeb\Output\Tests\Unit;

use Mockery as m;

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
     * Logger mock
     *
     * @var \Mockery\MockInterface
     */
    protected $logger;

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
        $this->response->shouldReceive("send")->andReturn($this->response);
        $this->response
            ->shouldReceive("addContent")
            ->with("style template\nWarningTest warningTestfile.php1")
            ->times(1);

        $manager = new \SlaxWeb\Output\Manager(
            $this->logger,
            $this->response,
            [],
            [
                "style"     =>  $this->styleTplName,
                "template"  =>  $this->tplName
            ]
        );

        $manager->errorHandler(E_WARNING, "Test warning", "Testfile.php", 1);
    }

    protected function _before()
    {
        $this->response = m::mock(\SlaxWeb\Router\Response::class);
        $this->logger = m::mock(\Psr\Log\LoggerInterface::class);

        $this->logger->shouldReceive("info");

        $this->createErrorTemplates();
    }

    protected function _after()
    {
        $this->removeErrorTemplates();

        m::close();
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

        file_put_contents($this->tplName, "<?=\$severity;?><?=\$error;?><?=\$file;?><?=\$line;?>");
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
