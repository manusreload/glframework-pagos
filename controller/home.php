<?php
/**
 * Created by PhpStorm.
 * User: mmunoz
 * Date: 3/09/18
 * Time: 9:34
 */

namespace GLApp;

use GLFramework\Bootstrap;
use GLFramework\Controller;
use Twig\Extension\StringLoaderExtension;

class home extends Controller {

    public $version;
    /**
     * Implementar aqui el código que ejecutara nuestra aplicación
     *
     * @return mixed
     */
    public function run()
    {
        // TODO: Implement run() method.
        $this->version = Bootstrap::getSingleton()->getVersion();
        $this->quit("/pagos");
    }

    /**
     * @param $twig \Twig_Environment
     */
    public function onViewCreated($twig) {
        $twig->addExtension(new StringLoaderExtension());
    }
}