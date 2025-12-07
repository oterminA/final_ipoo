<?php
//acá trabajo con objetos y claves-valor porque no estoy sacando la información directamente de la fuente porque el control es un intermediario, a diferencia de la capa del modelo
class ControlAlarmaGeneraAviso
{
    /**
     * Espera como parametro un arreglo asociativo donde las claves coinciden con los nombres de las variables instancias del objeto
     * crea al objeto completo y necesita toda la informacion. Lo uso más que nada para dar altas o modificar
     * retorna el objeto que se arma a partir de los parametros
     * @return AlarmaGeneraAviso
     */
    private function cargarObjeto($param)
    {
        $obj = null;
        if (isset($param['idtemperaturaaviso']) && isset($param['idtemperaturaalarma'])) { //es lo que se espera recibir por parametro, son los atributos de la clase menos el id de esta clase xq es autoincremental entonces habria como una contradiccion ahi

            $objAviso = new Aviso_Temperaturas(); //new de aviso
            $objAviso->setIdAviso($param['idtemperaturaaviso']); //seteo el id con el que vino x param

            $objAlarma = new Alarma_Temperaturas(); //new de alarma
            $objAlarma->setIdAlarma($param['idtemperaturaalarma']); //seteo el id de alarma con el q vino x param

            $id = $param['idavisoalarma'] ?? null; // si no existe ese id null porque en realidad acá no viene xq es autoincremental

            if (
                $objAviso->Buscar($objAviso->getIdAviso()) &&
                $objAlarma->Buscar($objAlarma->getIdAlarma())
            ) { //si existen las clases referenciadas hago lo siguiente, porque no puedo agregar como atributos objetos que no existen
                $obj = new AlarmaGeneraAviso(); //new de la clase del modelo
                $obj->cargar($id, $objAviso, $objAlarma); //cargo todos los datos necesarios al crear la clase
            }
        }
        return $obj; //retorno el objeto nuevo creado o null en caso de que falle algo
    }


    /**
     * Espera como parametro un arreglo asociativo donde las claves coinciden con los nombres de las variables instancias del objeto que son claves
     * retorna el objeto creado pero solo necesitando su id, no necesita el resto de la info. Lo uso más que nada para dar bajas, verificar que exista el objeto solo buscando su id, donde no preciso del resto de los datos
     * @return AlarmaGeneraAviso
     */
    private function cargarObjetoConClave($param)
    {
        $obj = null;
        if (isset($param['idavisoalarma'])) { //si está seteado ese id, o sea si contiene datos:
            $obj = new AlarmaGeneraAviso(); //new de esa clase
            $obj->cargar($param['idavisoalarma'], null, null); //cargo al objeto pero sin la necesidad de poner los demás datos, por eso uso los null
        }
        return $obj; //retorno el objeto si pudo crearse o null si no
    }


    /**
     * Corrobora que dentro del arreglo asociativo estan seteados los campos claves
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
     * genera un INSERT basicamente, de lo pasado por parametro, o sea necesita de la funcion insertar() del modelo
     * @return boolean
     */
    public function alta($param)
    {
        $resp = false;
        $elObjAA = $this->cargarObjeto($param); //cargo el objeto con los datos q entran x param
        //        verEstructura($elObjAA);
        if ($elObjAA != null and $elObjAA->insertar()) { //si el objeto no es null y da true la inserción:
            $resp = true;
        }
        return $resp; //revuelvo true si se pudo dar el alta, false si no
    }


    /**
     * permite eliminar un objeto mediante su ID usando una funcion que está en la capa de modelo
     * @return boolean
     */
    public function baja($param)
    {
        $resp = false;
        if ($this->seteadosCamposClaves($param)) { //si los campos están correctamente seteados da true
            $elObjAA = $this->cargarObjetoConClave($param); //cargo el objeto solo con su id porque necesito solamente borrarlo
            if ($elObjAA != null and $elObjAA->eliminar()) { //si el objeto no es nulo y se pudo borrar:
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
        //echo "Estoy en modificacion";
        $resp = false;
        if ($this->seteadosCamposClaves($param)) {//si los campos están correctamente seteados da true
            $elObjAA = $this->cargarObjeto($param); //acá si uso cargarObjeto porque necesito toda la info del obj ya que tengo campos que modificar
            if ($elObjAA != null and $elObjAA->modificar()) { //si el obj no es nulo y se puede modificar:
                $resp = true;
            }
        }
        return $resp; //true si se pudo hacer la modificacion, false si no
    }

    /**
     * permite Buscar un objeto usando info que entra por parametro y acá tengo que usarlo así porque no puedo acceder directamente a la info sino que tengo q pasar por el modelo
     * usa una función que viene desde el modelo
     * @return array
     */
    public function Buscar($param)
    {
        $where = " true ";
        if ($param != null) { //si el parametro no es nulo
            if (isset($param['idavisoalarma'])) { //si coincide el id de la tupla
                $where .= " AND idavisoalarma = '" . $param['idavisoalarma'] . "'";
            }
            //si coinciden el resto de los paramtros que recibe la tupla, los voy concatenando
            if (isset($param['idtemperaturaaviso']) && $param['idtemperaturaaviso']){ 
                $where .= " AND idtemperaturaaviso = '" . $param['idtemperaturaaviso'] . "'";
            }
            if (isset($param['idtemperaturaalarma']) && $param['idtemperaturaalarma']){
                $where .= " AND idtemperaturaalarma = '" . $param['idtemperaturaalarma']. "'";
            }
        }

        $arreglo = AlarmaGeneraAviso::listar($where); //armo un arreglo usando listar que recibe un parametro y en base a eso genera un arreglo, o sea creo el array si es que los datos coinciden
        return $arreglo; //retorno el array 
    }


    /** 
     * funcion xra mostrar la info de esta tabla
     */
    public function mostrarInfoAA()
    {
        $objAA = new AlarmaGeneraAviso();
        $listado = $objAA::listar();
        return $listado; //retorno el array con la info del obj
    }
}
