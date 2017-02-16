<?php
namespace SlaxWeb\Output\Exception;

/**
 * Missing Output Handler Exception
 *
 * Thrown when an attempt to retrieve to use the Output Handler was made but non
 * was available, neither directly nor through a getter.
 *
 * @package   SlaxWeb\Output
 * @author    Tomaz Lovrec <tomaz.lovrec@gmail.com>
 * @copyright 2016 (c) Tomaz Lovrec
 * @license   MIT <https://opensource.org/licenses/MIT>
 * @link      https://github.com/slaxweb/
 * @version   0.1
 */
class MissingOutputHandlerException extends \Exception
{
}
