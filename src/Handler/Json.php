<?php
namespace SlaxWeb\Output\Handler;

use SlaxWeb\Output\AbstractHandler;
use SlaxWeb\Output\Interfaces\ErrorHandler;

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
class Json extends AbstractHandler implements ErrorHandler
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
     * @inheritDoc
     */
    public function addError(
        string $error,
        int $code = 500,
        array $errorData = []
    ): ErrorHandler {
        $this->errors[] = [
            "message"   =>  $error,
            "data"      =>  $errorData
        ];
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
