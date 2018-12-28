<?php
/**
 * Created by PhpStorm.
 * User: manus
 * Date: 2018-12-27
 * Time: 16:55
 */

class PersonaPagoRecurrente extends \GLFramework\Model
{

    protected $table_name = 'personas_pagos_recurrentes';

    public $pago_recurrente_id;
    public $persona_id;
    protected $definition = array(
        'index' => 'id',
        'fields' => array(
            'pago_recurrente_id' => "int(11)",
            'persona_id' => "int(11)",
        )
    );
}