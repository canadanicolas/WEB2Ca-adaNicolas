<?php

require_once "./FinalCañadaNicolas/Model.php";
require_once "./FinalCañadaNicolas/View.php";

class Controller {

    private $view;
    private $model;

    function __construct(){
        $this->view = new View();
        $this->model = new Model();

    }

    private function checkLogeadoAdmin(){ //la tabla con los users debe tener un campo "rol" boolean 
        session_start();
        if(!isset($_SESSION["admin"])){
            $rol = "false";
        } elseif ($_SESSION["admin"] == 1){
            $rol = "admin";
        } else {
            $rol = "user";
        }
        return $rol;
    }

    function insertClienteYCuenta(){
        if ($this->CheckLoggedIn() == "admin"){
            $this->model->insertCliente($_POST["nombre"], $_POST["dni"], $_POST["telefono"], $_POST["direccion"], $_POST["premium"]);
            $this->model->insertCuenta($_POST["fecha_alta"], $_POST["nro_cuenta"], $_POST["tipo_cuenta"], $_POST["dni"], $_POST["premium"]);
        }
        $this->view->showHomeLocation();
    }

    function getCuentasPorCliente($params = null) {
        $id = $params[":ID"];
        $cuentas_cliente = $this->model->getCuentasPorCliente($id); 
        if ($cuentas_cliente = 0) {
            $this->View->response("El cliente no tiene cuentas asociadas o no existe el cliente indicado", 404); //el mensaje de error
        }
        $operaciones = $this->model->getOperaciones($cuentas_cliente); 
        $total = $this->model->getTotalCuenta($operaciones);
        $this->View->renderCuentasCliente($cuentas_cliente, $operaciones, $total);
    }

    function transferenciaRapida(){
        if ($this->CheckLoggedIn() != "false"){
            $this->model->transferenciaRapida($_POST["monto"], $_POST["dni_originario"], $_POST["dni_destinatario"]);
        }   
        $this->view->showHomeLocation(); 
    }

}