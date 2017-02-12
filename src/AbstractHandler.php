<?php
namespace SlaxWeb\Output;

/**
 * Abstract Output Handler
 *
 * @package   SlaxWeb\Output
 * @author    Tomaz Lovrec <tomaz.lovrec@gmail.com>
 * @copyright 2016 (c) Tomaz Lovrec
 * @license   MIT <https://opensource.org/licenses/MIT>
 * @link      https://github.com/slaxweb/
 * @version   0.1
 */
abstract class AbstractHandler
{
    /**
     * Content-Type header value
     *
     * @var string
     */
    protected $contentType = "";

    /**
     * Status code
     *
     * @var int
     */
    protected $statusCode = 200;

    /**
     * Render
     *
     * Render the handlers contents and return them to be included in the response.
     *
     * @return strnig
     */
    abstract public function render(): string;

    /**
     * Get Content-Type
     *
     * @return string
     */
    public function getContentType(): string
    {
        return $this->contentType;
    }

    /**
     * Get response code
     *
     * @return int
     */
    public function getStatusCode(): int
    {
        return $this->statusCode;
    }
}
