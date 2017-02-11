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
     * Render
     *
     * Render the handlers contents and return them to be included in the response.
     *
     * @return strnig
     */
    abstract public function render(): string;
}
