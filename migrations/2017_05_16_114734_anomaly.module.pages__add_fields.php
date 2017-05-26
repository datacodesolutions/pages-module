<?php

use Anomaly\Streams\Platform\Database\Migration\Migration;

class AnomalyModulePagesAddFields extends Migration
{

    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        if (!$prePageHandlerField = $this->fields()->findBySlugAndNamespace('pre_page_handler', 'pages')) {
            $prePageHandlerField = $this->fields()
                ->create(
                    [
                        'slug'      => 'pre_page_handler',
                        'namespace' => 'pages',
                        'type'      => 'anomaly.field_type.text',
                    ]
                );
        }

        if (!$pageHandlerField = $this->fields()->findBySlugAndNamespace('page_handler', 'pages')) {
            $pageHandlerField = $this->fields()
                ->create(
                    [
                        'slug'      => 'page_handler',
                        'namespace' => 'pages',
                        'type'      => 'anomaly.field_type.text',
                        'instructions'      => 'Accepts any value resolvable by the container. If present, control will be given to handler.',
                    ]
                );
        }

        $pagesStream = $this->streams()->findBySlugAndNamespace('pages', 'pages');
        $pageTypesStream = $this->streams()->findBySlugAndNamespace('types', 'pages');

        // assign to pages stream
        $this->assignments()
            ->create(
                [
                    'label'    => 'Pre Page Handler',
                    'field'    => $prePageHandlerField,
                    'stream'   => $pagesStream,
                    'required' => false,
                    'instructions'      => 'Accepts any value resolvable by the container. Will run before the page loads and if it returns a response, the returned reponse will be used as the page response.',
                ]
            );

        $this->assignments()
            ->create(
                [
                    'label'    => 'Page Handler',
                    'field'    => $pageHandlerField,
                    'stream'   => $pagesStream,
                    'required' => false,
                    'instructions'      => 'Accepts any value resolvable by the container. If present, will be given full control of page response.',
                ]
            );

        // assign to page types stream
        $this->assignments()
            ->create(
                [
                    'label'    => 'Pre Page Handler',
                    'field'    => $prePageHandlerField,
                    'stream'   => $pageTypesStream,
                    'required' => false,
                    'instructions'      => 'Accepts any value resolvable by the container. Will run before the page loads and if it returns a response, the returned reponse will be used as the page response.',
                ]
            );

        $this->assignments()
            ->create(
                [
                    'label'    => 'Page Handler',
                    'field'    => $pageHandlerField,
                    'stream'   => $pageTypesStream,
                    'required' => false,
                    'instructions'      => 'Accepts any value resolvable by the container. If Present, will be given full control of page response.',
                ]
            );
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        // remove pre page handler assignment
        if ($field = $this->fields()->findBySlugAndNamespace('pre_page_handler', 'pages')) {
            $field->delete();
        }

        // remove page handler assignment
        if ($field = $this->fields()->findBySlugAndNamespace('page_handler', 'pages')) {
            $field->delete();
        }
    }
}
