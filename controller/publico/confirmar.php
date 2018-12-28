<?php
/**
 * Created by PhpStorm.
 * User: manus
 * Date: 2018-12-28
 * Time: 14:03
 */

namespace GLApp\publico;


use GLFramework\Controller;

class confirmar extends Controller
{

    public $pago;
    /**
     * Implementar aqui el código que ejecutara nuestra aplicación
     *
     * @return mixed
     * @throws \Exception
     */
    public function run()
    {
        // TODO: Implement run() method.

        $token = $this->params['token'];

        $pago = new \Pago();
        $pagos = $pago->get(['token' => $token]);

        if($pagos->count() == 1) {
            $pago = $pagos->getModel();


            if($pago instanceof \Pago);
            if(strtotime($pago->fecha_cargo) + (30 * 24 * 60 * 60) < time()) {
                $this->addMessage("Enlace caducado!", "danger");
            } else {
                $this->pago = $pago;
                if(!isset($_POST['abort'])) {
                    if(!$this->pago->persona_confirma_abono) {
                        $this->pago->persona_confirma_abono = 1;
                        $this->pago->persona_confirma_fecha = now();
                    }
                    if(isset($_POST['save'])) {
                        $this->addMessage("Mensaje enviado correctamente");
                        $this->pago->persona_confirma_mensaje = $_POST['mensaje'];
                    }
                    $this->pago->save();
                } else {
                    $this->abort = true;
                    $this->pago->persona_confirma_abono = 0;
                    $this->pago->save();
                }
            }

        } else {
            $this->addMessage("No se ha podido encontrar el pago! Contacte con el desarrollador...", "danger");
        }
    }
}