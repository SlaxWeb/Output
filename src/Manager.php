<?php
namespace SlaxWeb\Output;

use SlaxWeb\Router\Response;
use Psr\Log\LoggerInterface as Logger;
use SlaxWeb\Output\Interfaces\ErrorHandler;

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
     * Application environment
     *
     * @var string
     */
    protected $env = "";

    /**
     * Output Handler
     *
     * @var \SlaxWeb\Output\AbstractHandler
     */
    protected $handler = null;

    /**
     * Output Handler getter
     *
     * @var callable
     */
    protected $handlerGetter = null;

    /**
     * Response status code
     *
     * @var int
     */
    protected $statusCode = 0;

    /**
     * List of available error levels
     *
     * @var array
     */
    protected $levels = [
        E_ERROR             =>  "Error",
        E_WARNING           =>  "Warning",
        E_PARSE             =>  "Parsing Error",
        E_NOTICE            =>  "Notice",
        E_CORE_ERROR        =>  "Core Error",
        E_CORE_WARNING      =>  "Core Warning",
        E_COMPILE_ERROR     =>  "Compile Error",
        E_COMPILE_WARNING   =>  "Compile Warning",
        E_USER_ERROR        =>  "User Error",
        E_USER_WARNING      =>  "User Warning",
        E_USER_NOTICE       =>  "User Notice",
        E_STRICT            =>  "Runtime Notice"
    ];

    /**
     * Error template data
     *
     * Containing the style template which is loaded only once, and the error template.
     *
     * @var array
     */
    protected $errorTpl = [];

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
        $this->env = $settings["environment"] ?? "development";
        $this->errorTpl = $errorTpl;

        $this->init();

        $this->logger->info("Output manager initialized");
    }

    /**
     * Magic call
     *
     * Forward all calls to the Output Handler. Once this call has been made the
     * handler is set to the Manager and can no longer be changed.
     *
     * @param string $name Name of the method to call
     * @param array $args Arguments for the method call
     * @return mixed
     */
    public function __call(string $name, array $args)
    {
        return $this->getHandler()->{$name}(...$args);
    }

    /**
     * Set enabled
     *
     * Sets the enabled flag to on or off, depending on the input parameter. When
     * set to true, the output manager will be enabled.
     *
     * @param bool $enabled Enabled flag, default bool(true)
     * @return self
     */
    public function setEnabled(bool $enabled = true): self
    {
        $this->enabled = $enabled;
        return $this;
    }

    /**
     * Set Handler
     *
     * Sets the output handler instance to the Manager. The handler must implement
     * the \SlaxWeb\Output\AbstractHandler to be accepted by the method. Returns
     * an instance of itself.
     *
     * @param \SlaxWeb\Output\AbstractHandler $handler Handler instance
     * @return self
     */
    public function setHandler(AbstractHandler $handler): self
    {
        $this->handler = $handler;
        return $this;
    }

    /**
     * Set Handler Getter
     *
     * Sets a callable as the handler getter. The callable will be called the first
     * time when the handler is required. This can happen when an access to the
     * handler through magic '__call' method is made, or when the execution is shutting
     * down and the shutdown handler will try and render the handlers contents,
     * if enabled. Returns an instance of itself.
     *
     * @param callable $getter Handler getter callable
     * @return self
     */
    public function setHandlerGetter(callable $getter): self
    {
        $this->handlerGetter = $getter;
        return $this;
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
        if (($lastErr = error_get_last()) !== null && $lastErr["type"] === E_ERROR) {
            $this->errorHandler(
                $lastErr["type"],
                $lastErr["message"],
                $lastErr["file"],
                $lastErr["line"]
            );

            // discard PHP generated error output
            ob_end_clean();
        }

        if ($this->enabled === false || $this->allowOutput === true) {
            $this->response->addContent(ob_get_contents());
        }
        if (ob_get_status()) {
            ob_end_clean();
        }

        if ($this->enabled === true) {
            $handler = $this->handler ?: $this->getHandler();
            $this->statusCode = $this->statusCode ?: $handler->getStatusCode();
            $this->response->headers->set("Content-Type", $handler->getContentType());
            $this->response->addContent($handler->render());
        }

        $this->response->setStatusCode($this->statusCode ?: 200);
        $this->response->send();
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
        // set status code 500 on errors
        if ($code === ((E_ERROR | E_USER_ERROR | E_COMPILE_ERROR | E_CORE_ERROR | E_PARSE) & $code)) {
            $this->statusCode = ($responseCode = $this->response->getStatusCode()) >= 400
                ? $responseCode
                : 500;
        }

        // if we are not in dev environment, bail out
        if ($this->env !== "development") {
            return true;
        }

        // template not set, return false and let PHP handle this one
        if (empty($this->errorTpl["template"])) {
            return false;
        };

        // set readable severity for templating
        $severity = $this->levels[$code];

        // set the error to the handler if it implements the ErrorHandler interface
        $handler = $this->handler ?: $this->getHandler();
        if ($handler instanceof ErrorHandler) {
            $handler->addError(
                $error,
                $this->statusCode,
                [
                    "code"      =>  $this->statusCode,
                    "severity"  =>  $severity,
                    "file"      =>  $file,
                    "line"      =>  $line
                ]
            );
            return true;
        }

        // start output buffering
        ob_start();

        // require the error style template with require_once to ensure it is included
        // only once in the output
        if (empty($this->errorTpl["style"]) === false) {
            require_once $this->errorTpl["style"];
        }

        // load the error template
        require $this->errorTpl["template"];
        // and add it to response content
        $this->response->addContent(ob_get_contents());
        ob_end_clean();
        return true;
    }

    /**
     * Initialise manager
     *
     * Registers a shutdown handler function, and a error handler function for handling
     * execution termination, and allows for the desired output. After the handlers
     * are registered, output buffering is started.
     *
     * @return void
     */
    protected function init()
    {
        register_shutdown_function([$this, "shutdownHandler"]);
        set_error_handler([$this, "errorHandler"]);
        ob_start();

        $this->logger->info("Shutdown and error handlers have been registered");
    }

    /**
     * Get handler
     *
     * Tries to obtain an Output Handler, either directly from the '$handler' property,
     * or through the getter.
     *
     * @return \SlaxWeb\Output\AbstractHandler
     */
    protected function getHandler(): AbstractHandler
    {
        if ($this->handler === null
            && (
                is_callable($this->handlerGetter) === false
                || !(($this->handler = ($this->handlerGetter)()) instanceof \SlaxWeb\Output\AbstractHandler)
            )
        ) {
            throw new Exception\MissingOutputHandlerException(
                "Output Manager does not have a valid Output Handler nor an "
                . "Output Handler getter set, or it provided an invalid Handler."
            );
        }
        return $this->handler;
    }
}
