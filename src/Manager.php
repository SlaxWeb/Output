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
     * Error template data
     *
     * Containing the style template which is loaded only once, and the error template.
     *
     * @var array
     */
    protected $errorTpl = [];
     */

    /**
     * Class constructor
     *
     * Copy dependencies to protected class properties, and parse the configuration
     * array.
     *
     * @param \Psr\Log\LoggerInterface $logger Logger instance
     * @param \SlaxWeb\Router\Response $response Response object
     * @param array $settings Output manager settings array
     * @param array $errorTpl Error templates for error output, default []
     */
    public function __construct(
        Logger $logger,
        Response $response,
        array $settings,
        array $errorTpl = []
    ) {
        $this->logger = $logger;
        $this->response = $response;

        $this->enabled = $settings["enabled"] ?? false;
        $this->allowOutput = $settings["allowOutput"] ?? true;
        $this->mode = $settings["mode"] ?? self::MODE_JSON;
        $this->allowModeChange = $settings["allowModeChange"] ?? true;
        $this->env = $settings["environment"] ?? "development";

        $this->errorTpl = $errorTpl;

        $this->registerHandlers();

        $this->logger->info("Output manager initialized");
    }

    /**
     * Shutdown handler
     *
     * Shutdown handler handles output for a specified mode in the Output Manager.
     * This method is called by PHP automatically at end of execution, and should
     * never be called directly. If the Output component is not enabled, it will
     * not attempt to generate output for the set mode.
     *
     * The method also catches fatal errors and forwards them to the error handler
     * method.
     *
     * @return void
     */
    public function shutdownHandler()
    {
        if (($lastErr = error_get_last())["type"] === E_ERROR) {
            $this->errorHandler(
                $lastErr["type"],
                $lastErr["message"],
                $lastErr["file"],
                $lastErr["line"]
            );
        }

        if ($this->enabled === false) {
            // Component not enabled, no need to render output.
            $this->logger->debug("Output component not enabled. Aborting output generation");
            return;
        }

        // @TODO: generate output for given mode
    }

    /**
     * Error handler
     *
     * The classic PHP error handler. It will load the "style" template set in the
     * "errorTpl" protected property only once on the first run, to ensure any styling
     * template is output only once. If the "template" item is not found in the
     * "errorTpl" protected property array, then the method will return bool(false)
     * and regular PHP error handling will proceed. If the environment is not set
     * to "development", method will return bool(true), halting any error output.
     * When set to "development", and the "template" item being set, the template
     * will be loaded with the error parameters.
     *
     * @param int $code Error code
     * @param string $error Error message
     * @param string $file File in which the error occured
     * @param int $line Line at which the error occured
     * @param array $context Error context pointing to the active symbol table
     * @return bool
     *
     * @todo log the error in appropriate level
     */
    public function errorHandler(
        int $code,
        string $error,
        string $file,
        int $line,
        array $context = []
    ): bool {
        // if we are not in dev environment, bail out
        if ($this->env !== "development") {
            return true;
        }

        // template not set, return false and let PHP handle this one
        if (isset($this->errorTpl["template"]) === false) {
            return false;
        }

        // start output buffering
        ob_start();

        // require the error style template with require_once to ensure it is included
        // only once in the output
        if (isset($this->errorTpl["style"])) {
            require_once $this->errorTpl["style"];
        }

        // load the error template
        require $this->errorTpl["template"];
        // and add it to response content
        $this->response->addContent(ob_get_clean());
        return true;
    }

    /**
     * Register handlers
     *
     * Registers a shutdown handler function, and a error handler function for handling
     * execution termination, and allows for the desired output.
     *
     * @return void
     */
    protected function registerHandlers()
    {
        register_shutdown_function([$this, "shutdownHandler"]);
        set_error_handler([$this, "errorHandler"]);
    }
}
