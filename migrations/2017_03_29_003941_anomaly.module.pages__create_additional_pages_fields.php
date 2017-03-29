<?php

use Anomaly\Streams\Platform\Database\Migration\Migration;

class AnomalyModulePagesCreateAdditionalPagesFields extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        // remove theme_layout
        if ($field = $this->fields()->findBySlugAndNamespace('theme_layout', 'pages')) {
            $field->delete();
        }

        // remove layout
        if ($field = $this->fields()->findBySlugAndNamespace('layout', 'pages')) {
            $field->delete();
        }

        // // add layout back as text field typ
        if (! $layoutField = $this->fields()->findBySlugAndNamespace('layout', 'pages')) {
            $layoutField = $this->fields()
                ->create(
                    [
                        'slug'      => 'layout',
                        'namespace' => 'pages',
                        'type'      => 'anomaly.field_type.text',
                        'config' => [
                            'default_value' => 'theme::layouts.default',
                        ],
                    ]
                );
        }

        $pageTypesStream = $this->streams()->findBySlugAndNamespace('types', 'pages');

        $this->assignments()
            ->create(
                [
                    'label'    => 'Layout',
                    'field'    => $layoutField,
                    'stream'   => $pageTypesStream,
                    'required' => false,
                    'instructions' => 'Path to Twig Template',
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
        $pageTypesStream = $this->streams()->findBySlugAndNamespace('types', 'pages');

        // remove layout
        if ($field = $this->fields()->findBySlugAndNamespace('layout', 'pages')) {
            $field->delete();
        }

        // add layout back as editor type
        if (! $pageLayoutField = $this->fields()->findBySlugAndNamespace('layout', 'pages')) {
            $pageLayoutField = $this->fields()
                ->create(
                    [
                        'slug'      => 'layout',
                        'namespace' => 'pages',
                        'type'   => 'anomaly.field_type.editor',
                        'config' => [
                            'default_value' => '<h1>{{ page.title }}</h1>',
                            'mode'          => 'twig',
                        ],
                    ]
                );
        }

        // add theme_layout back
        if (! $themeLayoutField = $this->fields()->findBySlugAndNamespace('theme_layout', 'pages')) {
            $themeLayoutField = $this->fields()
                ->create(
                    [
                        'slug'      => 'theme_layout',
                        'namespace' => 'pages',
                        'type'   => 'anomaly.field_type.select',
                        'config' => [
                            'default_value' => 'theme::layouts/default.twig',
                            'handler'       => 'Anomaly\SelectFieldType\Handler\Layouts@handle',
                        ],
                    ]
                );
        }

        $this->assignments()
            ->create(
                [
                    'field'    => $themeLayoutField,
                    'stream'   => $pageTypesStream,
                    'required' => true,
                ]
            );

        $this->assignments()
            ->create(
                [
                    'field'    => $pageLayoutField,
                    'stream'   => $pageTypesStream,
                    'required' => true,
                ]
            );
    }
}
