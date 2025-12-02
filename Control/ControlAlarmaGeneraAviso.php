<?php
class ControlAlarmaGeneraAviso
{
    /**
     * Espera como parametro un arreglo asociativo donde las claves coinciden con los nombres de las variables instancias del objeto
     * @param array $param
     * @return AlarmaGeneraAviso
     */
    private function cargarObjeto($param)
    {
        $obj = null;
        if (isset($param['idavisoalarma']) && isset($param['idtemperaturaaviso']) && isset($param['idtemperaturaalarma'])) {

            $objAviso = new Aviso_Temperaturas();
            $objAviso->setIdAviso($param['idtemperaturaaviso']);

            $objAlarma = new Alarma_Temperaturas();
            $objAlarma->setIdAlarma($param['idtemperaturaalarma']);

            $id = $param['idavisoalarma']?? null; // si no existe, null

            if (
                $objAviso->Buscar($objAviso->getIdAviso()) &&
                $objAlarma->Buscar($objAlarma->getIdAlarma())
            ) {
                $obj = new AlarmaGeneraAviso();
                $obj->cargar($id, $objAviso, $objAlarma);
            }
        }
        return $obj;
    }


    /**
     * Espera como parametro un arreglo asociativo donde las claves coinciden con los nombres de las variables instancias del objeto que son claves
     * @param array $param
     * @return AlarmaGeneraAviso
     */
    private function cargarObjetoConClave($param)
    {
        $obj = null;
        if (isset($param['idavisoalarma'])) {
            $obj = new AlarmaGeneraAviso();
            $obj->cargar($param['idavisoalarma'], null, null);
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
        if (isset($param['idavisoalarma']))
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
        $elObjAA = $this->cargarObjeto($param);
        //        verEstructura($elObjAA);
        if ($elObjAA != null and $elObjAA->insertar()) {
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
            $elObjAA = $this->cargarObjetoConClave($param);
            if ($elObjAA != null and $elObjAA->eliminar()) {
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
            $elObjAA = $this->cargarObjeto($param);
            if ($elObjAA != null and $elObjAA->modificar()) {
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
            if (isset($param['idavisoalarma'])) {
                $where .= " AND idavisoalarma = '" . $param['idavisoalarma'] . "'";
            }
            if (isset($param['idtemperaturaaviso']) && $param['idtemperaturaaviso'] instanceof Aviso_Temperaturas) {
                $where .= " AND idtemperaturaaviso = '" . $param['idtemperaturaaviso']->getIdAviso() . "'";
            }
            if (isset($param['idtemperaturaalarma']) && $param['idtemperaturaalarma'] instanceof Alarma_Temperaturas) {
                $where .= " AND idtemperaturaalarma = '" . $param['idtemperaturaalarma']->getIdAlarma() . "'";
            }
        }

        $arreglo = AlarmaGeneraAviso::listar($where);
        return $arreglo;
    }
}
