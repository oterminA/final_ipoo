<?php
class ControlSensorServidores
{
    /**
     * Espera como parametro un arreglo asociativo donde las claves coinciden con los nombres de las variables instancias del objeto
     * @param array $param
     * @return Sensor_Servidores
     */
    private function cargarObjeto($param)
    {
        $obj = null;

        if (isset($param['idtemperaturasensor']) && isset($param['tssporcentajeperdida'])) {
            $obj = new Sensor_Servidores();
            $obj->cargar($param['idtemperaturasensor'], null, null, null, null);
            $obj->cargarServidores($param['tssporcentajeperdida']);
        }
        return $obj;
    }

    /**
     * Espera como parametro un arreglo asociativo donde las claves coinciden con los nombres de las variables instancias del objeto que son claves
     * @param array $param
     * @return Sensor_Servidores
     */
    private function cargarObjetoConClave($param)
    {
        $obj = null;

        if (isset($param['idtemperaturasensor'])) {
            $obj = new Sensor_Servidores();
            $obj->cargar($param['idtemperaturasensor'], null, null, null, null);
            $obj->cargarServidores($param['tssporcentajeperdida']);
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
        $elObjtSensorS = $this->cargarObjeto($param);
        if ($elObjtSensorS != null and $elObjtSensorS->insertar()) {
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
            $elObjtSensorS = $this->cargarObjetoConClave($param);
            if ($elObjtSensorS != null and $elObjtSensorS->eliminar()) {
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
            $elObjtSensorS = $this->cargarObjeto($param);
            if ($elObjtSensorS != null and $elObjtSensorS->modificar()) {
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
            if (isset($param['tssporcentajeperdida']))
                $where .= " and tssporcentajeperdida ='" . $param['tssporcentajeperdida'] . "'";
        }
        $arreglo = Sensor_Servidores::listar($where);
        return $arreglo;
    }
}
