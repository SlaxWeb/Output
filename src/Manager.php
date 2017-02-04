<?php
namespace SlaxWeb\Output;

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
}
