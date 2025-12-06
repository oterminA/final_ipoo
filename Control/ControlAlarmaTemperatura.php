<?php
class ControlAlarmaTemperatura
{
    /**
     * Espera como parametro un arreglo asociativo donde las claves coinciden con los nombres de las variables instancias del objeto
     * @param array $param
     * @return Alarma_Temperaturas
     */
    private function cargarObjeto($param)
    {
        $obj = null;
        if (isset($param['idtemperaturasensor']) && isset($param['tasuperior']) && isset($param['tainferior']) && isset($param['tafechainicio']) && isset($param['tafechafin'])) {
            $fechaFin = $param['tafechafin'] ?? null; // si no existe, null
            $id = $param['idtemperaturaalarma'] ?? null; // si no existe, null
    
            $objSensor = new Sensor();
            $objSensor->setIdSensor($param['idtemperaturasensor']);
            if ($objSensor->Buscar($objSensor->getIdSensor())) {
                $obj = new Alarma_Temperaturas();
                $obj->cargar($id, $objSensor, $param['tasuperior'], $param['tainferior'], $param['tafechainicio'], $fechaFin); 
            }
        }
        return $obj;
    }
    

    /**
     * Espera como parametro un arreglo asociativo donde las claves coinciden con los nombres de las variables instancias del objeto que son claves
     * @param array $param
     * @return Alarma_Temperaturas
     */
    private function cargarObjetoConClave($param)
    {
        $obj = null;
        if (isset($param['idtemperaturaalarma'])) {
            $obj = new Alarma_Temperaturas();
            $obj->cargar($param['idtemperaturaalarma'], null, null, null, null, null);
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
        if (isset($param['idtemperaturaalarma']))
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
        $elObjtAlarma = $this->cargarObjeto($param);
        //        verEstructura($elObjtAlarma);
        if ($elObjtAlarma != null and $elObjtAlarma->insertar()) {
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
            $elObjtAlarma = $this->cargarObjetoConClave($param);
            if ($elObjtAlarma != null and $elObjtAlarma->eliminar()) {
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
        //echo "Estoy en modificacion";
        $resp = false;
        if ($this->seteadosCamposClaves($param)) {
            $elObjtAlarma = $this->cargarObjeto($param);
            if ($elObjtAlarma != null and $elObjtAlarma->modificar()) {
                $resp = true;
            }
        }
        return $resp;
    }

    /**
     * permite Buscar un objeto
     * @param array $param
     * @return array
     */
    public function Buscar($param)
    {
        $where = " true ";
        if ($param != null) {
            if (isset($param['idtemperaturaalarma'])) {
                $where .= " AND idtemperaturaalarma = '" . $param['idtemperaturaalarma'] . "'";
            }
            if (isset($param['idtemperaturasensor'])) {
                $where .= " AND idtemperaturasensor = '" . $param['idtemperaturasensor'] . "'";
            }    
            if (isset($param['tasuperior'])) {
                $where .= " AND tasuperior = '" . $param['tasuperior'] . "'";
            }
            if (isset($param['tainferior'])) {
                $where .= " AND tainferior = '" . $param['tainferior'] . "'";
            }
            if (isset($param['tafechainicio'])) {
                $where .= " AND tafechainicio = '" . $param['tafechainicio'] . "'";
            }
            if (isset($param['tafechafin'])) {
                $where .= " AND tafechafin = '" . $param['tafechafin'] . "'";
            }
        }
    
        $arreglo = Alarma_Temperaturas::listar($where);
        return $arreglo;
    }


    /**
     * funcion para saber si una alarma está activa, o sea si la alarma no tiene fecha de fin es porque lo está
    */
    public function alarmaActiva($idSensor){
        $listadoActivas = [];
        $sql = "idtemperaturasensor = " . $idSensor . " AND tafechafin IS NULL"; //o sea hago la consulta de que si coincide el id que entro por parametro Y no hay una fehca de fin, se entiende que está activa
        $listadoActivas = Alarma_Temperaturas::listar($sql); //hago que se busquen todas las alarmas que coincidan con esa query
 
        return $listadoActivas; //devuelvo un array de todas las alarmas activas de ese sensor o uno vacio
    }

    /** 
    * funcion xra mostrar la info de las alarmas
    */
    public function mostrarInfoAlarmas(){
        $objAlarma = new Alarma_Temperaturas();
        $listado = $objAlarma::listar();
        return $listado; //retorno el array con la info del obj
    }
}

?>