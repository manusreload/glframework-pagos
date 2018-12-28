<?php
/**
 * Created by PhpStorm.
 * User: manus
 * Date: 2018-12-27
 * Time: 16:27
 */

class Persona extends \GLFramework\Model
{
    protected $table_name = 'personas';

    public $id;
    public $nombre;
    public $email;
    public $phone;
    public $baja;
    public $notificar_email;
    protected $definition = array(
        'index' => 'id',
        'fields' => array(
            'nombre' => "varchar(255)",
            'email' => "varchar(64)",
            'phone' => "varchar(64)",
            'baja' => "int(1)",
            'notificar_email' => "int(1)",
        )
    );
}