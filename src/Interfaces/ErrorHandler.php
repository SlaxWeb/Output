<?php
namespace SlaxWeb\Output\Interfaces;

/**
 * Error handler interface
 *
 * Handlers that implement this interface are capable of handling the display of
 * errors, and all errors and uncaught exceptions during the execution of the application
 * should be forwardded to that handler.
 *
 * @package   SlaxWeb\Output
 * @author    Tomaz Lovrec <tomaz.lovrec@gmail.com>
 * @copyright 2016 (c) Tomaz Lovrec
 * @license   MIT <https://opensource.org/licenses/MIT>
 * @link      https://github.com/slaxweb/
 * @version   0.1
 */
interface ErrorHandler
{
    /**
     * Add error to response
     *
     * When an error is added, the status code is automatically set to $code, which
     * defaults to int(500). This method only adds the error message to the local
     * error container. The output still needs to be written to response with the
     * Output Manager object.
     *
     * @param string $error Error message to add to container
     * @param int $code HTTP Status code that is automatically set to the response
     *                  object, default int(500),
     * @param array $errorData Additional error data, default []
     * @return self
     */
    public function addError(
        string $error,
        int $code = 500,
        array $errorData = []
    ): ErrorHandler;
}
