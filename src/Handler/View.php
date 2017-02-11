<?php
namespace SlaxWeb\Output\Handler;

use SlaxWeb\View\Base as AppView;
use SlaxWeb\Output\AbstractHandler;
use SlaxWeb\View\AbstractLoader as ViewLoader;

/**
 * SlaxWeb View Output Handler
 *
 * The View Output handler accepts the SlaxWeb View objects, and stores them internally.
 * It also stores view data that will be injected into the views, and renders them
 * in the order that they are received, when the call to render occurs.
 *
 * @package   SlaxWeb\Output
 * @author    Tomaz Lovrec <tomaz.lovrec@gmail.com>
 * @copyright 2016 (c) Tomaz Lovrec
 * @license   MIT <https://opensource.org/licenses/MIT>
 * @link      https://github.com/slaxweb/
 * @version   0.1
 */
class View extends AbstractHandler
{
    /**
     * Views container
     *
     * @var array
     */
    protected $container = [];

    /**
     * View data
     *
     * @var array
     */
    protected $viewData = [
        "all"   =>  []
    ];

    /**
     * Content-Type header value
     *
     * @var string
     */
    protected $contentType = "text/html";

    /**
     * Add View
     *
     * Adds a View instance to the Views container and returns an instance of itself.
     *
     * @param \SlaxWeb\View\Base $view View instance
     * @return self
     */
    public function add(AppView $view): self
    {
        $this->container[] = $view;
        return $this;
    }

    /**
     * Add view data
     *
     * Adds an array of data to the internal view data container. The second parameter,
     * $type, defines if the data received will be used for a specific View, or
     * all views. The $type needs to be set to the full class name of a view in
     * order to be recognized as view data for that specific view. If the second
     * parameters is omitted or value "all" is used, then data will be used for
     * all views.
     *
     * @param array $data View data
     * @param string $type View data type
     * @return self
     */
    public function addData(array $data, string $type = "all"): self
    {
        $this->viewData[$type] = array_merge($this->viewData[$type] ?? [], $data);
        return $this;
    }

    /**
     * @inheritDoc
     */
    public function render(): string
    {
        $output = "";
        // @var \SlaxWeb\View\Base $view
        foreach ($this->container as $view) {
            $dataType = get_class($view);
            if (isset($this->viewData[$dataType]) === false) {
                $dataType = "all";
            }
            $output .= $view->render(
                $this->viewData[$dataType],
                ViewLoader::TPL_RETURN,
                $dataType === "all"
                    ? ViewLoader::TPL_CACHE_VARS
                    : ViewLoader::TPL_NO_CACHE_VARS
            );
        }
        return $output;
    }
}
