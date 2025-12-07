<?php
//acá trabajo con objetos y claves-valor porque no estoy sacando la información directamente de la fuente porque el control es un intermediario, a diferencia de la capa del modelo
class ControlSensorServidores
{
    /**
     * Espera como parametro un arreglo asociativo donde las claves coinciden con los nombres de las variables instancias del objeto
     * crea al objeto completo y necesita toda la informacion. Lo uso más que nada para dar altas o modificar
     * retorna el objeto que se arma a partir de los parametros
     * @return Sensor_Servidores
     */
    private function cargarObjeto($param)
    {
        $obj = null;

        if (isset($param['idtemperaturasensor']) && isset($param['tssporcentajeperdida'])) {//es lo que se espera recibir por parametro, son los atributos de la clase 
            $obj = new Sensor_Servidores();
            $obj->cargar($param['idtemperaturasensor'], null, null, null, null);//cargo solo el id y dejó como null los demás porque despues se llenan al tener la info de la clase padre
            $obj->cargarServidores($param['tssporcentajeperdida']);//cargo todos los datos necesarios que tiene la clase hija exclusivamente
        }
        return $obj;
    }

    /**
     * Espera como parametro un arreglo asociativo donde las claves coinciden con los nombres de las variables instancias del objeto que son claves
     * retorna el objeto creado pero solo necesitando su id, no necesita el resto de la info. Lo uso más que nada para dar bajas, verificar que exista el objeto solo buscando su id, donde no preciso del resto de los datos
     * @return Sensor_Servidores
     */
    private function cargarObjetoConClave($param)
    {
        $obj = null;
        if (isset($param['idtemperaturasensor'])) { //si está seteado ese id, o sea si contiene datos:
            $obj = new Sensor_Servidores();//new de esa clase
            $obj->cargar($param['idtemperaturasensor'], null, null, null, null);//cargo al objeto pero sin la necesidad de poner los demás datos, por eso uso los null yno hago el cargarServidores porque solo necesito el id para identificar
        }
        return $obj;
    }


    /**
     * Corrobora que dentro del arreglo asociativo estan seteados los campos claves
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
     * genera un INSERT basicamente, de lo pasado por parametro, o sea necesita de la funcion insertar() del modelo
     * @return boolean
     */
    public function alta($param)
    {
        $resp = false;
        $elObjtSensorS = $this->cargarObjeto($param);//cargo el objeto con los datos q entran x param
        if ($elObjtSensorS != null and $elObjtSensorS->insertar()) {//si el objeto no es null y da true la inserción:
            $resp = true;
        }
        return $resp;//revuelvo true si se pudo dar el alta, false si no
    }


    /**
     * permite eliminar un objeto mediante su ID usando una funcion que está en la capa de modelo y acá busco solamente borrar el obj hijo de la clase hija, no quiero que se borre del padre o algo así
     * @return boolean
     */
    public function baja($param)
    {
        $resp = false;
        if ($this->seteadosCamposClaves($param)) {//si los campos están correctamente seteados da true
            $elObjtSensorS = $this->cargarObjetoConClave($param);//cargo el objeto solo con su id porque necesito solamente borrarlo
            if ($elObjtSensorS != null and $elObjtSensorS->eliminar()) {//si el objeto no es nulo y se pudo borrar
                $resp = true;
            }
        }
        return $resp; //true si se pudo borrar, false si no
    }


    /**
     * permite modificar un objeto por la info que llega por paramentro, se ejecuta la funcion de la capa del modelo
     * @return boolean
     */
    public function modificacion($param)
    {
        $resp = false;
        if ($this->seteadosCamposClaves($param)) {//si los campos están correctamente seteados da true
            $elObjtSensorS = $this->cargarObjeto($param);//acá si uso cargarObjeto porque necesito toda la info del obj ya que tengo campos que modificar
            if ($elObjtSensorS != null and $elObjtSensorS->modificar()) {//si el obj no es nulo y se puede modificar:
                $resp = true;
            }
        }
        return $resp;//true si se pudo hacer la modificacion, false si no
    }


    /**
     * permite Buscar un objeto usando info que entra por parametro y acá tengo que usarlo así porque no puedo acceder directamente a la info sino que tengo q pasar por el modelo
     * usa una función que viene desde el modelo
     * @return array
     */
    public function Buscar($param)
    {
        $where = " true ";
        if ($param <> NULL) {//si el parametro no es nulo
            if (isset($param['idtemperaturasensor']))
                $where .= " and idtemperaturasensor =" . $param['idtemperaturasensor'];//si coincide el id de la tupla
             //si coinciden el resto de los paramtros que recibe la tupla, los voy concatenando
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
        $arreglo = Sensor_Servidores::listar($where);//armo un arreglo usando listar que recibe un parametro y en base a eso genera un arreglo, o sea creo el array si es que los datos coinciden
        return $arreglo;//retorno el array
    }
}
