<?php
namespace SlaxWeb\Output;

/**
 * Output Handler Interface
 *
 * @package   SlaxWeb\Output
 * @author    Tomaz Lovrec <tomaz.lovrec@gmail.com>
 * @copyright 2016 (c) Tomaz Lovrec
 * @license   MIT <https://opensource.org/licenses/MIT>
 * @link      https://github.com/slaxweb/
 * @version   0.1
 */
interface HandlerInterface
{
    /**
     * Render
     *
     * Render the handlers contents and return them to be included in the response.
     *
     * @return strnig
     */
    public function render(): string;
}
