<?php
namespace SlaxWeb\Output;

use SlaxWeb\Router\Response;
use Psr\Log\LoggerInterface as Logger;
use SlaxWeb\Config\Container as Config;

/**
 * Output Manager
 *
 * The Output Manager class is the main class of the Output component. It registers
 * a shutdown function for outputing data from the Response object. If permitted
 * by configuration it will also render all gathered resources for the active mode
 * and append them to output.
 *
 * @package   SlaxWeb\Output
 * @author    Tomaz Lovrec <tomaz.lovrec@gmail.com>
 * @copyright 2016 (c) Tomaz Lovrec
 * @license   MIT <https://opensource.org/licenses/MIT>
 * @link      https://github.com/slaxweb/
 * @version   0.1
 */
class Manager
{
    /**
     * Ouput modes
     */
    const MODE_VIEW = 0;
    const MODE_JSON = 1;
    const MODE_FILE = 2;

    /**
     * Logger instance
     *
     * @var \Psr\Log\LoggerInterface
     */
    protected $logger = null;

    /**
     * Response object
     *
     * @var \SlaxWeb\Router\Response
     */
    protected $response = null;

    /**
     * Enabled
     *
     * @var bool
     */
    protected $enabled = false;

    /**
     * Permit direct output
     *
     * @var bool
     */
    protected $allowOutput = true;

    /**
     * Output mode
     *
     * @var int
     */
    protected $mode = 1;

    /**
     * Permit mode change
     *
     * @var bool
     */
    protected $allowModeChange = true;

    /**
     * Application environment
     *
     * @var string
     */
    protected $env = "";

    /**
     * Class constructor
     *
     * Copy dependencies to protected class properties, and parse the configuration
     * array.
     *
     * @param \Psr\Log\LoggerInterface $logger Logger instance
     * @param \SlaxWeb\Router\Response $response Response object
     * @param array $settings Output manager settings array
     */
    public function __construct(Logger $logger, Response $response, array $settings)
    {
        $this->logger = $logger;
        $this->response = $response;

        $this->enabled = $settings["enabled"] ?? false;
        $this->allowOutput = $settings["allowOutput"] ?? true;
        $this->mode = $settings["mode"] ?? self::MODE_JSON;
        $this->allowModeChange = $settings["allowModeChange"] ?? true;
        $this->env = $settings["environment"] ?? "development";

        $this->logger->info("Output manager initialized");
    }
}
