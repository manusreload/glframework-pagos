<?php
/**
 * Created by PhpStorm.
 * User: manus
 * Date: 2018-12-28
 * Time: 16:05
 */

namespace GLApp\pagos;


use GLFramework\Controller\AuthController;

class view extends AuthController
{
    public $pago;
    public function run()
    {
        $this->pago = new \Pago($_GET['id']);

        if(isset($_GET['confirmar'])) {
            $this->pago->pagado = 1;
            $this->pago->fecha_abono = today();
            $this->pago->save();
            $this->addMessage("Se ha abonado correctamente");
        }
    }
}