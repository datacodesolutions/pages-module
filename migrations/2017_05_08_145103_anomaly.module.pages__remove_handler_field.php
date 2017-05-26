<?php

use Anomaly\Streams\Platform\Database\Migration\Migration;

class AnomalyModulePagesRemoveHandlerField extends Migration
{

    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        // remove handler field
        if ($field = $this->fields()->findBySlugAndNamespace('handler', 'pages')) {
            $field->delete();
        }
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        // add handler field back
        if (! $handlerField = $this->fields()->findBySlugAndNamespace('handler', 'pages')) {
            $handlerField = $this->fields()
                ->create(
                    [
                        'slug'      => 'handler',
                        'namespace' => 'pages',
                        'type'   => 'anomaly.field_type.addon',
                        'config' => [
                            'type'          => 'extension',
                            'search'        => 'anomaly.module.pages::handler.*',
                            'default_value' => 'anomaly.extension.default_page_handler',
                        ],
                    ]
                );
        }
    }
}
