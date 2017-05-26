<?php namespace Anomaly\PagesModule;

use Anomaly\PagesModule\Page\Contract\PageInterface;
use Anomaly\PagesModule\Page\Contract\PageRepositoryInterface;
use Anomaly\PagesModule\Page\PageCollection;
use Anomaly\PagesModule\Page\PageModel;
use Anomaly\PagesModule\Page\PageRepository;
use Anomaly\PagesModule\Type\Contract\TypeRepositoryInterface;
use Anomaly\PagesModule\Type\TypeModel;
use Anomaly\PagesModule\Type\TypeRepository;
use Anomaly\Streams\Platform\Addon\AddonServiceProvider;
use Anomaly\Streams\Platform\Model\Pages\PagesPagesEntryModel;
use Anomaly\Streams\Platform\Model\Pages\PagesTypesEntryModel;
use Illuminate\Http\Request;
use Route;

/**
 * Class PagesModuleServiceProvider
 *
 * @link          http://pyrocms.com/
 * @author        PyroCMS, Inc. <support@pyrocms.com>
 * @author        Ryan Thompson <ryan@pyrocms.com>
 */
class PagesModuleServiceProvider extends AddonServiceProvider
{

    /**
     * The addon plugins.
     *
     * @var array
     */
    protected $plugins = [
        PagesModulePlugin::class,
    ];

    /**
     * The class bindings.
     *
     * @var array
     */
    protected $bindings = [
        PagesPagesEntryModel::class => PageModel::class,
        PagesTypesEntryModel::class => TypeModel::class,
    ];

    /**
     * The singleton bindings.
     *
     * @var array
     */
    protected $singletons = [
        PageRepositoryInterface::class => PageRepository::class,
        TypeRepositoryInterface::class => TypeRepository::class,
    ];

    /**
     * The addon routes.
     *
     * @var array
     */
    protected $routes = [
        'admin/pages'                                    => 'Anomaly\PagesModule\Http\Controller\Admin\PagesController@index',
        'admin/pages/create'                             => 'Anomaly\PagesModule\Http\Controller\Admin\PagesController@create',
        'admin/pages/edit/{id}'                          => 'Anomaly\PagesModule\Http\Controller\Admin\PagesController@edit',
        'admin/pages/view/{id}'                          => 'Anomaly\PagesModule\Http\Controller\Admin\PagesController@view',
        'admin/pages/delete/{id}'                        => 'Anomaly\PagesModule\Http\Controller\Admin\PagesController@delete',
        'admin/pages/types'                              => 'Anomaly\PagesModule\Http\Controller\Admin\TypesController@index',
        'admin/pages/types/create'                       => 'Anomaly\PagesModule\Http\Controller\Admin\TypesController@create',
        'admin/pages/types/edit/{id}'                    => 'Anomaly\PagesModule\Http\Controller\Admin\TypesController@edit',
        'admin/pages/types/assignments/{type}'           => 'Anomaly\PagesModule\Http\Controller\Admin\AssignmentsController@index',
        'admin/pages/types/assignments/{type}/choose'    => 'Anomaly\PagesModule\Http\Controller\Admin\AssignmentsController@choose',
        'admin/pages/types/assignments/{type}/create'    => 'Anomaly\PagesModule\Http\Controller\Admin\AssignmentsController@create',
        'admin/pages/types/assignments/{type}/edit/{id}' => 'Anomaly\PagesModule\Http\Controller\Admin\AssignmentsController@edit',
        'admin/pages/fields'                             => 'Anomaly\PagesModule\Http\Controller\Admin\FieldsController@index',
        'admin/pages/fields/choose'                      => 'Anomaly\PagesModule\Http\Controller\Admin\FieldsController@choose',
        'admin/pages/fields/create'                      => 'Anomaly\PagesModule\Http\Controller\Admin\FieldsController@create',
        'admin/pages/fields/edit/{id}'                   => 'Anomaly\PagesModule\Http\Controller\Admin\FieldsController@edit',
        'admin/pages/ajax/choose_type'                   => 'Anomaly\PagesModule\Http\Controller\Admin\AjaxController@chooseType',
        'admin/pages/ajax/choose_field/{id}'             => 'Anomaly\PagesModule\Http\Controller\Admin\AjaxController@chooseField',
        'admin/pages/settings'                           => 'Anomaly\PagesModule\Http\Controller\Admin\SettingsController@index',
        'pages/preview/{id}'                             => 'Anomaly\PagesModule\Http\Controller\PagesController@preview',
    ];

    /**
     * Map additional routes.
     *
     * @param PageRepositoryInterface $pages
     * @param Request                 $request
     */
    public function map(PageRepositoryInterface $pages, Request $request)
    {
        // Don't map if in admin.
        if ($request->segment(1) == 'admin') {
            return;
        }

        $page = $pages->findByPath($request->path() == '/' ? '/' : '/' . $request->path());

        if ($page) {
            if ($page->isExact()) {
                Route::any(
                    $page->getPath() . '/{any?}',
                    [
                        'uses'                       => 'Anomaly\PagesModule\Http\Controller\PagesController@view',
                        'streams::addon'             => 'anomaly.module.pages',
                        'anomaly.module.pages::page' => $page->getId(),
                        'where'                      => [
                            'any' => '(.*)',
                        ],
                    ]
                );

                return;
            }

            Route::any(
                $page->getPath(),
                [
                    'uses'                       => 'Anomaly\PagesModule\Http\Controller\PagesController@view',
                    'streams::addon'             => 'anomaly.module.pages',
                    'anomaly.module.pages::page' => $page->getId(),
                ]
            );
        }
    }
}
