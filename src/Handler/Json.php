<?php
namespace SlaxWeb\Output\Handler;

use SlaxWeb\Output\AbstractHandler;

/**
 * SlaxWeb Json Output Handler
 *
 * The Json Output Handler encodes the data and added error messages as JSON when
 * rendering for output.
 *
 * @package   SlaxWeb\Output
 * @author    Tomaz Lovrec <tomaz.lovrec@gmail.com>
 * @copyright 2016 (c) Tomaz Lovrec
 * @license   MIT <https://opensource.org/licenses/MIT>
 * @link      https://github.com/slaxweb/
 * @version   0.1
 */
class Json extends AbstractHandler
{
    /**
     * Error container
     *
     * @var array
     */
    protected $errors = [];

    /**
     * Json data
     *
     * @var array
     */
    protected $data = [];

    /**
     * Content-Type header value
     *
     * @var string
     */
    protected $contentType = "application/json";

    /**
     * Set Status Code
     *
     * Sets the HTTP Response Status code and returns an instance of itself.
     *
     * @param int $code HTTP Status code for the response, default int(200)
     * @return self
     */
    public function setStatusCode(int $code = 200): self
    {
        $this->statusCode = $code;
        return $this;
    }

    /**
     * Add Data
     *
     * Adds an array of data to the internal container. New data is recursively
     * merged into the existing array, overwritting previously set data.
     *
     * @param array $data Data array to be merged with previsouly added data
     * @return self
     */
    public function add(array $data): self
    {
        $this->data = array_merge_recursive($this->data, $data);
        return $this;
    }

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
     *                  object, default int(500)
     * @return self
     */
    public function addError(string $error, int $code = 500): self
    {
        $this->errors[] = $error;
        $this->setStatusCode($code);
        return $this;
    }

    /**
     * @inheritDoc
     */
    public function render(): string
    {
        return json_encode([
            "data"      =>  $this->data,
            "errors"    =>  $this->errors
        ], JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE);
    }
}
