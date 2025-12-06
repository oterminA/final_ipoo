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
     * para saber el tipo de sensor de un id sensor xq no tengo "tipo" como atributo de los sensores
     * esta funcion busca un id sensor y me devuelve el objeto sensor con suinformacion especifica xq los servidores y las heladeras tienen atributos extras que no se comparten entre si
    */
    public function detectarTipoSensor($idSensor){
        $objSensor = new Sensor(); //hago un new de cada sensor para dsps poder usar la funcion buscar
        $heladera = new Sensor_Heladeras();
        $servidor = new Sensor_Servidores();
        $resp = null;
        $existeServidor = $objSensor->Buscar($idSensor); //busco si existe el sensor que entra x parametro
        $existeHeladera = $heladera->Buscar($idSensor);
        $existeServidor = $servidor->Buscar($idSensor);

        if($existeHeladera){ //si el id del parametro está en heladera me va a dar true
            $resp= $heladera;
        }elseif($existeServidor){ //si el id del param está en servidor me va a dar true
            $resp = $servidor;
        }else{ //si ese id no está en ninguna clase hija es porque no es un tipo de sensor y es generico
            $resp = $objSensor;
        }
        
        return $resp; //retorno ya tipo de sensor o el generico(o sea que no tiene especificacion de tipo)
    }

    /**
     * esta función lo que hace es usar otra q esta en el modelopara detectar el tipo de sensor y llamar a la funcion del modelo para estimar la perdida, así puedo llamarla despues en la interface
     * segun el sensor es la info que va a tener la funcion polimorfica de estimarFalloPerdida, por eso tengo que diferenciarlos
    */
    public function estimarPerdida($idSensor){
        $resp = null;
        $sensor = $this->detectarTipoSensor($idSensor); //acá traigo el obj sensor correcto para poder sar correctamente la funcion de la perdida
        if ($sensor !== null){
            $resp = $sensor->estimarPerdidaFallo(); //acá llamoa esa funcion del modelo que me devolvería la perdida especificadel sensor que busco
        }
        return $resp; //esto devuelve la perdida o null porque sucedió algo malo
    }

    /** 
     * funcion xra mostrar la info de los sensores, me limito a que solo muestrenla info generica porque despues si quieren ver más especificamente tengo otra forma de que se vea la info particular con los atributos extra de cada clase hija
    */
    public function mostrarInfoSensores(){
        $objSensor = new Sensor();
        $listado = $objSensor::listar(); //listar devuelve un array o null, es decir la info de ese sensor
        return $listado; //retorno el array con la info del obj sensor o null
    }

    /**
     * funcion que retorna un obj especificoo para luego poder mostrar su toString
    */
    public function mostrarInfoID($idSensor){
        return $this->detectarTipoSensor($idSensor); //retorno el sensor que me entrega esa funcion, que es el que me esta pidiendo ver el usuario y así le muestro su toString especifico en el menu. Esto funciona xq en detectarTipoSensor yo estoy devolviendo el obj sensor por medio del new que hice, por eso me devuelve toda la info de ese sensor en particular
    }
}
