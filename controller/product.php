<?php
/**
 * Created by PhpStorm.
 * User: mmunoz
 * Date: 3/09/18
 * Time: 9:34
 */

namespace GLApp;

use GLFramework\Controller;

class product extends Controller {

    public $products;
    /**
     * Implementar aqui el código que ejecutara nuestra aplicación
     *
     * @return mixed
     */
    public function run()
    {
        // TODO: Implement run() method.
        $product = new \Product();
        if(isset($_GET['add'])) {
            $product->name = "Test Product";
            $product->price = 25.50;
            $product->stock = 5;
            if($product->save(true)) {
                $this->addMessage("Product saved! #" . $product->id, "success");
            } else {
                $this->addMessage("Error saving product!", "danger");
            }
        }

        if(isset($_GET['list'])) {
            $this->products = $product->get_all();
        }
    }
}