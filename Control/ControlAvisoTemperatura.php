<?php
class ControlAvisoTemperatura
{
    /**
     * Espera como parametro un arreglo asociativo donde las claves coinciden con los nombres de las variables instancias del objeto
     * @param array $param
     * @return Aviso_Temperaturas
     */
    private function cargarObjeto($param)
    {
        $obj = null;
    
        if (isset($param['taactivo']) && isset($param['tanombre']) && isset($param['taemail'])) {
            $obj = new Aviso_Temperaturas();
    
            $id = $param['idtemperaturaaviso'] ?? null; // si no existe, null
            $obj->cargar($id, $param['taactivo'],$param['tanombre'], $param['taemail']);
        }
        return $obj;
    }

    /**
     * Espera como parametro un arreglo asociativo donde las claves coinciden con los nombres de las variables instancias del objeto que son claves
     * @param array $param
     * @return Aviso_Temperaturas
     */
    private function cargarObjetoConClave($param)
    {
        $obj = null;

        if (isset($param['idtemperaturaaviso'])) { 
            $obj = new Aviso_Temperaturas();
            $obj->cargar($param['idtemperaturaaviso'], null, null, null);
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
        if (isset($param['idtemperaturaaviso'])) 
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
        $elObjtAvisoT = $this->cargarObjeto($param);
        //        verEstructura($elObjtAvisoT);
        if ($elObjtAvisoT != null and $elObjtAvisoT->insertar()) {
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
            $elObjtAvisoT = $this->cargarObjetoConClave($param);
            if ($elObjtAvisoT != null and $elObjtAvisoT->eliminar()) {
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
            $elObjtAvisoT = $this->cargarObjeto($param);
            if ($elObjtAvisoT != null and $elObjtAvisoT->modificar()) {
                $resp = true;
            }
        }
        return $resp;
    }


    /**
     * permite Buscar un objeto
     * @param array $param
     * @return boolean
     */
    public function Buscar($param)
    {
        $where = " true ";
        if ($param <> NULL) {
            if (isset($param['idtemperaturaaviso']))
                $where .= " and idtemperaturaaviso =" . $param['idtemperaturaaviso'];
            if (isset($param['taactivo']))
                $where .= " and taactivo ='" . $param['taactivo'] . "'";
            if (isset($param['tanombre']))
                $where .= " and tanombre ='" . $param['tanombre'] . "'";
            if (isset($param['taemail']))
                $where .= " and taemail ='" . $param['taemail'] . "'";
        }
        $arreglo = Aviso_Temperaturas::listar($where);
        return $arreglo;
    }

    /** 
     * funcion xra mostrar la info de los avisos
    */
    public function mostrarInfoAvisos(){
        $objAviso = new Aviso_Temperaturas();
        $listado = $objAviso::listar();
        return $listado; //retorno el array con la info del obj
    }
}
