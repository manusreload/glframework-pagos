<?php
/**
 * Created by PhpStorm.
 * User: manus
 * Date: 2018-12-27
 * Time: 16:39
 */

namespace GLApp;


use GLFramework\Controller;
use GLFramework\Modules\Cron\CronTaskBuilder;

class cron extends Controller
{

    public static $TIPO_EVENTO_NUEVO_PAGO = 'nuevo_pago.twig';

    private $timestamp;
    private function date($format) {
        return date($format, $this->timestamp);
    }
    /**
     * @param $builder CronTaskBuilder
     */
    public function getCronTasks($builder)
    {
        $builder->addTask($this->module, 'Generar Pagos', 'GLApp\cron->run');
    }

    /**
     * Implementar aqui el código que ejecutara nuestra aplicación
     *
     * @return mixed
     * @throws \Exception
     */
    public function run()
    {
        $this->setTemplate("json.twig");
        // TODO: Implement run() method.
        if(isset($_GET['date'])) {
            $this->timestamp = strtotime($_GET['date']);
        }
        if(!$this->timestamp) {
            $this->timestamp = time();
        }

        $pagosResult = [];
        $recurrentes = new \PagoRecurrente();
        $recurrentes = $recurrentes->get(['desactivar' => "0"]);
        foreach ($recurrentes as $recurrente) {
            if($recurrente instanceof \PagoRecurrente);

            $personasPagoRecurrente = $recurrente->getPersonasPagoRecurrente();
            $personas = [];
            foreach ($personasPagoRecurrente as $personaPagoRecurrente) {
                $persona = new \Persona($personaPagoRecurrente->persona_id);
                if(!$persona->baja) {
                    $personas[] = $persona;
                }
            }

            // Comprobamos que el pago no este creado en funcion del tipo de pago
            if($recurrente->tipo == \PagoRecurrente::$TIPO_PAGO_RECURRENTE_MENSUAL) {
                $day = $recurrente->primer_dia?$recurrente->primer_dia:0;
                if($day > $this->date("t")) $day = $this->date("t");
                $date = $this->date("Y-m");
                if($day == 0 || ($day == $this->date("d"))) {
                    foreach ($personas as $persona) {
                        $pagos = new \Pago();
                        $pagos = $pagos->select("SELECT * FROM pagos WHERE DATE_FORMAT(fecha_cargo, '%Y-%m') = '$date' AND persona_id = ? AND pago_recurrente_id = ?", [$persona->id, $recurrente->id]);
                        if($pagos->count() == 0) {
                            $pago = $this->generarPago($persona, $recurrente);
                            $pagosResult[] = $pago->id;
                        }
                    }
                }
            }

            if($recurrente->tipo == \PagoRecurrente::$TIPO_PAGO_RECURRENTE_SEMANAL) {
                $day = $recurrente->primer_dia?$recurrente->primer_dia:0;
                if($day > 7) $day = 0;
                $date = $this->date("Y-W");
                if($day == 0 || ($day == $this->date("N"))) {
                    foreach ($personas as $persona) {
                        $pagos = new \Pago();
                        $pagos = $pagos->select("SELECT * FROM pagos WHERE DATE_FORMAT(fecha_cargo, '%Y-%u') = '$date' AND persona_id = ? AND pago_recurrente_id = ?", [$persona->id, $recurrente->id]);
                        if($pagos->count() == 0) {
                            $pago = $this->generarPago($persona, $recurrente);
                            $pagosResult[] = $pago->id;
                        }
                    }
                }
            }
            if($recurrente->tipo == \PagoRecurrente::$TIPO_PAGO_RECURRENTE_DIARIO) {
                $date = $this->date("Y-m-d");
                foreach ($personas as $persona) {
                    $pagos = new \Pago();
                    $pagos = $pagos->select("SELECT * FROM pagos WHERE fecha_cargo = '$date' AND persona_id = ? AND pago_recurrente_id = ?", [$persona->id, $recurrente->id]);
                    if($pagos->count() == 0) {
                        $pago = $this->generarPago($persona, $recurrente);
                        $pagosResult[] = $pago->id;
                    }
                }

            }
        }

        return ['json' => ['result' => true, 'pagos' => $pagosResult]];
    }

    /**
     * @param $persona \Persona
     * @param $recurrente \PagoRecurrente
     * @throws \Exception
     */
    private function generarPago($persona, $recurrente) {
        $pago = new \Pago();
        $pago->persona_id = $persona->id;
        $pago->fecha_cargo = $this->date('Y-m-d');
        $pago->concepto = $recurrente->generarConcepto($this->timestamp);
        $pago->cantidad = $recurrente->cantidad;
        $pago->pago_recurrente_id = $recurrente->id;
        $pago->pagado = $recurrente->generar_pagado;
        if($pago->pagado) {
            $pago->fecha_abono = $this->date('Y-m-d');
        }
        if($pago->save(true) && !$pago->pagado) {
            self::sendEmail($this, $pago, self::$TIPO_EVENTO_NUEVO_PAGO);
        }
        return $pago;
    }

    /**
     * @param $pago \Pago
     * @throws \TijsVerkoyen\CssToInlineStyles\Exception
     */
    public static function sendEmail($controller, $pago, $tipo) {

        $persona = $pago->getPersona();
        if($persona->notificar_email) {
            $mail = new \GLFramework\Mail();
            $data = $mail->render($controller, "mail/$tipo", ['pago' => $pago, 'persona' => $persona]);
            $mail->send($persona->email, $pago->concepto, $data);
        }
    }
}