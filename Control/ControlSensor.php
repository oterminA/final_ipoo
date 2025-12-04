<?php
class ControlSensor
{
    /**
     * Espera como parametro un arreglo asociativo donde las claves coinciden con los nombres de las variables instancias del objeto
     * @param array $param
     * @return Sensor
     */
    private function cargarObjeto($param)
    {
        $obj = null;

        if (isset($param['tscodigo']) && isset($param['tsubicacion']) && isset($param['tselementosresguardan']) && isset($param['tsmontoresguardado'])) {
            $id = $param['idtemperaturasensor'] ?? null; // si no existe, null
            $obj = new Sensor();
            $obj->cargar($id, $param['tscodigo'], $param['tsubicacion'], $param['tselementosresguardan'], $param['tsmontoresguardado']);
        }
        return $obj;
    }

    /**
     * Espera como parametro un arreglo asociativo donde las claves coinciden con los nombres de las variables instancias del objeto que son claves
     * @param array $param
     * @return Sensor
     */
    private function cargarObjetoConClave($param)
    {
        $obj = null;

        if (isset($param['idtemperaturasensor'])) { 
            $obj = new Sensor();
            $obj->cargar($param['idtemperaturasensor'], null, null, null, null);
        }
        return $obj;
    }


    /**
     * Corrobora que dentro del arreglo asociativo estan seteados los campos claves
     * @param array $param
     * @return boolean
     */

    private function seteadosCamposClaves($param)
    {
        $resp = false;
        if (isset($param['idtemperaturasensor'])) 
            $resp = true;
        return $resp;
    }

    /**
     * 
     * @param array $param
     */
    public function alta($param)
    {
        $resp = false;
        $elObjtSensor = $this->cargarObjeto($param);
        //        verEstructura($elObjtSensor);
        if ($elObjtSensor != null and $elObjtSensor->insertar()) {
            $resp = true;
        }
        return $resp;
    }


    /**
     * permite eliminar un objeto 
     * @param array $param
     * @return boolean
     */
    public function baja($param)
    {
        $resp = false;
        if ($this->seteadosCamposClaves($param)) {
            $elObjtSensor = $this->cargarObjetoConClave($param);
            if ($elObjtSensor != null and $elObjtSensor->eliminar()) {
                $resp = true;
            }
        }

        return $resp;
    }

    /**
     * permite modificar un objeto
     * @param array $param
     * @return boolean
     */
    public function modificacion($param)
    {
        $resp = false;
        if ($this->seteadosCamposClaves($param)) {
            $elObjtSensor = $this->cargarObjeto($param);
            if ($elObjtSensor != null and $elObjtSensor->modificar()) {
                $resp = true;
            }
        }
        return $resp;
    }


    /**
     * permite Buscar un objeto
     * @param array $param
     */
    public function Buscar($param)
    {
        $where = " true ";
        if ($param <> NULL) {
            if (isset($param['idtemperaturasensor']))
                $where .= " and idtemperaturasensor =" . $param['idtemperaturasensor'];
            if (isset($param['tscodigo']))
                $where .= " and tscodigo ='" . $param['tscodigo'] . "'";
            if (isset($param['tsubicacion']))
                $where .= " and tsubicacion ='" . $param['tsubicacion'] . "'";
            if (isset($param['tselementosresguardan']))
                $where .= " and tselementosresguardan ='" . $param['tselementosresguardan'] . "'";
            if (isset($param['tsmontoresguardado']))
                $where .= " and tsmontoresguardado ='" . $param['tsmontoresguardado'] . "'";
        }
        $arreglo = Sensor::listar($where);
        return $arreglo;
    }

    /** 
     * esta funcion busca un id sensor e indica si es un sensor heladera, servidores o generico
    */
    public function detectarTipoSensor($idSensor){
        $objSensor = new Sensor();
        $heladera = new Sensor_Heladeras();
        $servidor = new Sensor_Servidores();
        $resp = null;
        $existeServidor = $objSensor->Buscar($idSensor);
        $existeHeladera = $heladera->Buscar($idSensor);
        $existeServidor = $servidor->Buscar($idSensor);

        if($existeHeladera){
            $resp= $heladera;
        }elseif($existeServidor){
            $resp = $servidor;
        }else{
            $resp = $objSensor;
        }
        
        return $resp; //retorno ya tipo de sensor o el generico
    }

    /**
     * esta función lo que hace es usar otra q esta en el modelopara detectar el tipo de sensor y llamar a la funcion del modelo para estimar la perdida, así puedo llamarla despues en la interface
    */
    public function estimarPerdida($idSensor){
        $resp = null;
        $sensor = $this->detectarTipoSensor($idSensor); //acá traigo el obj sensor correcto para poder sar correctamente la funcion de la perdida
        if ($sensor !== null){
            $resp = $sensor->estimarPerdidaFallo(); //acá llamoa esa funcion que me devolvería la perdida especificadel sensor que busco
        }
        return $resp; //esto devuelve la perdida o null porque sucedió algo malo
    }

    /** 
     * funcion xra mostrar la info de los sensores
    */
    public function mostrarInfoSensores(){
        $objSensor = new Sensor();
        $listado = $objSensor::listar();
        return $listado; //retorno el array con la info del obj sensor
    }

    public function mostrarInfoID($idSensor){
        return $this->detectarTipoSensor($idSensor); //retorno el sensor que me entrega esa funcion, que es el que me esta pidiendo ver el usuario y así le muestro su toString especifico en el menu
    }
}
