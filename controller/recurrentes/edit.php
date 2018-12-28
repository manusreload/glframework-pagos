<?php
/**
 * Created by PhpStorm.
 * User: manus
 * Date: 2018-12-27
 * Time: 16:13
 */

namespace GLApp\recurrentes;


use GLFramework\Controller\AuthController;

class edit extends AuthController
{
    public $recurrente;
    public $personas;
    public function run()
    {
        parent::run(); // TODO: Change the autogenerated stub
        $this->recurrente = new \PagoRecurrente($_GET['id']);
        $this->personas = new \Persona();
        $this->personas = $this->personas->get_all();
        if(isset($_POST['save'])) {
            $this->recurrente->setData($_POST['recurrente']);
            if($this->recurrente->save(true)) {
                $ppr = new \PersonaPagoRecurrente();
                $ppr->get(['pago_recurrente_id' => $this->recurrente->id])->delete();
                foreach ($_POST['personas'] as $id) {
                    $ppr = new \PersonaPagoRecurrente();
                    $ppr->pago_recurrente_id = $this->recurrente->id;
                    $ppr->persona_id = $id;
                    $ppr->save();
                }
                $this->addMessage("Se ha guardado correctamente", "success");
                if($_GET['id'] == 'add') {
                    $this->quit("?id=" . $this->recurrente->id);
                }
            } else {
                $this->addMessage("Error al guardar", "danger");
            }
        }
        if(isset($_POST['delete'])) {
            $this->recurrente->delete();
            $this->addMessage("Se ha eliminado correctamente");
            $this->quit("../");
        }
    }


}