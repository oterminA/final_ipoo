<?php
class ControlSensorHeladeras
{
    /**
     * Espera como parametro un arreglo asociativo donde las claves coinciden con los nombres de las variables instancias del objeto
     * @param array $param
     * @return Sensor_Heladeras
     */
    private function cargarObjeto($param)
    {
        $obj = null;

        if (isset($param['idtemperaturasensor']) && isset($param['marca']) && isset($param['modelo'])) {
            $obj = new Sensor_Heladeras();
            $obj->cargar($param['idtemperaturasensor'], null, null, null, null);
            $obj->cargarHeladeras($param['marca'], $param['modelo']);
        }
        return $obj;
    }

    /**
     * Espera como parametro un arreglo asociativo donde las claves coinciden con los nombres de las variables instancias del objeto que son claves
     * @param array $param
     * @return Sensor_Heladeras
     */
    private function cargarObjetoConClave($param)
    {
        $obj = null;

        if (isset($param['idtemperaturasensor'])) {
            $obj = new Sensor_Heladeras();
            $obj->cargar($param['idtemperaturasensor'], null, null, null, null);
            $obj->cargarHeladeras($param['marca'], $param['modelo']);
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
        $elObjtSensorH = $this->cargarObjeto($param);
        //        verEstructura($elObjtSensorH);
        if ($elObjtSensorH != null and $elObjtSensorH->insertar()) {
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
            $elObjtSensorH = $this->cargarObjetoConClave($param);
            if ($elObjtSensorH != null and $elObjtSensorH->eliminar()) {
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
            $elObjtSensorH = $this->cargarObjeto($param);
            if ($elObjtSensorH != null and $elObjtSensorH->modificar()) {
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
            if (isset($param['marca']))
                $where .= " and marca ='" . $param['marca'] . "'";
            if (isset($param['modelo']))
                $where .= " and modelo ='" . $param['modelo'] . "'";
        }
        $arreglo = Sensor_Heladeras::listar($where);
        return $arreglo;
    }
}
