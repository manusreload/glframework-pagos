<?php
/**
 * Created by PhpStorm.
 * User: manus
 * Date: 2018-12-27
 * Time: 16:07
 */

class Pago extends \GLFramework\Model
{
    protected $table_name = 'pagos';

    public $id;
    public $persona_id;
    public $cantidad;
    public $concepto;
    /**
     * @var Fecha que se genera el pago
     */
    public $fecha_cargo;
    /**
     * @var Fecha en la que se abona el pago
     */
    public $fecha_abono;
    public $pagado;
    public $pago_recurrente_id;
    public $persona_confirma_fecha;
    public $persona_confirma_abono;
    public $persona_confirma_mensaje;
    public $token;
    protected $definition = array(
        'index' => 'id',
        'fields' => array(
            'persona_id' => "int(11)",
            'cantidad' => "double(10,2)",
            'concepto' => "varchar(255)",
            'fecha_cargo' => "date",
            'fecha_abono' => "date",
            'pagado' => "int(1)",
            'pago_recurrente_id' => "int(11)",
            'token' => "varchar(16)",
            'persona_confirma_fecha' => "datetime",
            'persona_confirma_abono' => "int(1)",
            'persona_confirma_mensaje' => "varchar(255)",
        )
    );

    public function getPersona() {
        return new Persona($this->persona_id);
    }

    public function insert($data = null)
    {
        $this->token = substr(md5(time()), 0, 16);
        return parent::insert($data);
    }

    public function getPagoRecurrente() {
        return new PagoRecurrente($this->pago_recurrente_id);
    }
}