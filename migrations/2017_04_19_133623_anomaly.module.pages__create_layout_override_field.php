<?php

use Anomaly\Streams\Platform\Database\Migration\Migration;

class AnomalyModulePagesCreateLayoutOverrideField extends Migration
{

    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        if (!$layoutOverrideField = $this->fields()->findBySlugAndNamespace('layout_override', 'pages')) {
            $layoutOverrideField = $this->fields()
                ->create(
                    [
                        'slug'      => 'layout_override',
                        'namespace' => 'pages',
                        'type'      => 'anomaly.field_type.text',
                    ]
                );
        }

        $pagesStream = $this->streams()->findBySlugAndNamespace('pages', 'pages');

        $this->assignments()
            ->create(
                [
                    'label'    => 'Layout Override',
                    'field'    => $layoutOverrideField,
                    'stream'   => $pagesStream,
                    'required' => false,
                    'instructions' => 'Path to Twig Template.  Will override page type template.',
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
        // remove layout override assignment
        if ($field = $this->fields()->findBySlugAndNamespace('layout_override', 'pages')) {
            $field->delete();
        }
    }
}
