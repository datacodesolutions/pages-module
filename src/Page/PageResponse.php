<?php namespace Anomaly\PagesModule\Page;

use Anomaly\PagesModule\Page\Contract\PageInterface;
use Illuminate\Routing\ResponseFactory;

/**
 * Class PageResponse
 *
 * @link          http://pyrocms.com/
 * @author        PyroCMS, Inc. <support@pyrocms.com>
 * @author        Ryan Thompson <ryan@pyrocms.com>
 */
class PageResponse
{

    /**
     * The response factory.
     *
     * @var ResponseFactory
     */
    protected $container;

    /**
     * Create a new PageResponse instance.
     *
     * @param ResponseFactory $response
     */
    function __construct(ResponseFactory $response)
    {
        $this->response = $response;
    }

    /**
     * Make the page response.
     *
     * @param PageInterface $page
     */
    public function make(PageInterface $page)
    {
        if (!$page->getResponse()) {
            if (!$layout = $page->layout_override) {
                $type = $page->getType();
                $layout = $type->getFieldType('layout')->getValue();
            }

            $page->setResponse(
                $this->response->view($layout, ['page' => $page])
            );
        }
    }
}
