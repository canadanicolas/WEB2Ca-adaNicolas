<?php

class Model {

    private $db;

    function __construct(){
        $this->db = new PDO('mysql:host=localhost;'.'dbname=BancoVVBA;charset=utf8', 'root', '');
        $this->db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    }

    function insertCliente($nombre, $dni, $telefono, $direccion, $premium){
        try {
            if ($this->existeDni($dni) == false && $nombre != null && $dni != null && $telefono != null && $direccion != null && $premium != null) {
                $sentencia = $this->db->prepare("INSERT INTO cliente(nombre, dni, telefono, direccion, premium) VALUES (?,?,?,?,?)"); 
                $sentencia->execute(array($nombre, $dni, $telefono, $direccion, $premium)); 
                return $sentencia->fetch(PDO::FETCH_OBJ);
            }
        } catch (PDOException $ex) {
            log $ex->getMessage());
        }

    }

    function existeDni($dni){
        $sentencia = $this->db->prepare("SELECT dni FROM cliente WHERE dni=?");
        $sentencia->execute();
        if ($sentencia->rowCount() != 0) {
            return true;
        } else return false;
    }

    function insertCuenta($fecha_alta, $nro_cuenta, $tipo_cuenta, $dni, $premium){
        try {
            if ($fecha_alta != null && $nro_cuenta != null && $tipo_cuenta != null) {
                $id_cliente = $this->getIdCliente($dni);
                $sentencia = $this->db->prepare("INSERT INTO cuenta(fecha_alta, nro_cuenta, tipo_cuenta, id_cliente) VALUES (?,?,?,?)"); 
                $sentencia->execute(array($fecha_alta, $nro_cuenta, $tipo_cuenta, $id_cliente)); 
                if($premium == 1){
                    $id_cuenta = $this->getIdCuenta($nro_cuenta);
                    $this->insertOperacion(10000, $fecha_alta, 2, $id_cuenta);
                }
                return $sentencia->fetch(PDO::FETCH_OBJ);
            } 
        }catch (PDOException $ex) {
            log $ex->getMessage());
        }
    }

    function getIdCliente($dni) {
        $sentencia = $this->db->prepare("SELECT id FROM cliente WHERE dni=?");
        $sentencia->execute(array($dni));
        return $sentencia->fetch(PDO::FETCH_NUM);
    }

    function getIdCuenta($nro_cuenta) {
        $sentencia = $this->db->prepare("SELECT id FROM cuenta WHERE nro_cuenta=?");
        $sentencia->execute(array($nro_cuenta));
        return $sentencia->fetch(PDO::FETCH_NUM);
    }

    function insertOperacion($monto, $fecha, $tipo_operacion, $id_cuenta){
        $sentencia = $this->db->prepare("INSERT INTO operacion(monto, fecha, tipo_operacion, id_cuenta) VALUES (?,?,?,?)"); 
        $sentencia->execute(array($monto, $fecha, $tipo_operacion, $id_cuenta)); 
        return $sentencia->fetch(PDO::FETCH_OBJ);  
    }

    function getCuentasPorCliente($id) {
        try {
            if ($this->getCliente($id) != null) {
                $sentencia = $this->db->prepare("SELECT * FROM cuenta WHERE id_cliente=?");
                $sentencia->execute(array($id));
                return $sentencia->fetchAll(PDO::FETCH_OBJ);
            }
        }catch (PDOException $ex) {
            log $ex->getMessage());
        }
    }

    function getCliente($id) {
        $sentencia = $this->db->prepare("SELECT * FROM cliente WHERE id=?");
        $sentencia->execute(array($id));
        return $sentencia->fetch(PDO::FETCH_OBJ);
    }

    function getOperaciones($id_cuenta){
        $sentencia = $this->db->prepare("SELECT * FROM operacion WHERE id_cuenta=?");
        $sentencia->execute(array($id_cuenta));
        return $sentencia->fetch(PDO::FETCH_OBJ);
    }

    function getMontoOperaciones($id_cuenta){
        $sentencia = $this->db->prepare("SELECT monto FROM operacion WHERE id_cuenta=?");
        $sentencia->execute(array($id_cuenta));
        return $sentencia->fetch(PDO::FETCH_NUM);
    }

    function getTotalCuenta($id_cuenta){
        $operaciones = $this->getMontoOperaciones();
        return array_sum($operaciones);
    }

    function transferenciaRapida($monto, $dni_originario, $dni_destinatario){
        $id_cliente_originario = $this->getIdCliente($dni_originario);
        $id_cliente_destinatario = $this->getIdCliente($dni_destinatario);
        $this->operacionRapidaExtraccion(($monto, $id_cliente_originario);
        $this->operacionRapidaDeposito(($monto, $id_cliente_originario);
    }

    function operacionRapidaExtraccion($monto, $id_cliente_originario){
        $cuenta = $this->getCuentasPorCliente($id_cliente_originario);
        if ($this->getTotalCuenta($cuenta) > $monto){
            $sentencia = $this->db->prepare("INSERT INTO operacion(monto, tipo_operacion, id_cliente_originario) VALUES (?,1,?)"); 
            $sentencia->execute(array($monto, 1, $id_cuenta)); 
            return $sentencia->fetch(PDO::FETCH_OBJ);  
        }
    }

    function operacionRapidaDeposito($monto, $id_cliente_destinatario){
        if ($this->getCliente($id_cliente_destinatario) != null){
            $sentencia = $this->db->prepare("INSERT INTO operacion(monto, tipo_operacion, id_cliente_destinatario) VALUES (?,2,?)"); 
            $sentencia->execute(array($monto, 2, $id_cuenta)); 
            return $sentencia->fetch(PDO::FETCH_OBJ);  
        }
    }
}
