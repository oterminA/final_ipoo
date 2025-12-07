<?php
//acá trabajo con objetos y claves-valor porque no estoy sacando la información directamente de la fuente porque el control es un intermediario, a diferencia de la capa del modelo
class ControlAlarmaTemperatura
{
    /**
     * Espera como parametro un arreglo asociativo donde las claves coinciden con los nombres de las variables instancias del objeto
     * crea al objeto completo y necesita toda la informacion. Lo uso más que nada para dar altas o modificar
     * retorna el objeto que se arma a partir de los parametros
     * @return Alarma_Temperaturas
     */
    private function cargarObjeto($param)
    {
        $obj = null;
        if (isset($param['idtemperaturasensor']) && isset($param['tasuperior']) && isset($param['tainferior']) && isset($param['tafechainicio']) && isset($param['tafechafin'])) {//es lo que se espera recibir por parametro, son los atributos de la clase menos el id de esta clase xq es autoincremental entonces habria como una contradiccion ahi
            $fechaFin = $param['tafechafin'] ?? null; // si no existe, null porque puede ser que la fecha de fin no se ingrese
            $id = $param['idtemperaturaalarma'] ?? null; // si no existe ese id null porque en realidad acá no viene xq es autoincremental
    
            $objSensor = new Sensor(); //new de sensor
            $objSensor->setIdSensor($param['idtemperaturasensor']);  //seteo el id de sensor con el q vino x param
            if ($objSensor->Buscar($objSensor->getIdSensor())) {//si existen las clases referenciadas hago lo siguiente, porque no puedo agregar como atributos objetos que no existen
                $obj = new Alarma_Temperaturas();  //new de la clase del modelo
                $obj->cargar($id, $objSensor, $param['tasuperior'], $param['tainferior'], $param['tafechainicio'], $fechaFin); //cargo todos los datos necesarios al crear la clase
            }
        }
        return $obj;//retorno el objeto nuevo creado o null en caso de que falle algo
    }
    

   /**
     * Espera como parametro un arreglo asociativo donde las claves coinciden con los nombres de las variables instancias del objeto que son claves
     * retorna el objeto creado pero solo necesitando su id, no necesita el resto de la info. Lo uso más que nada para dar bajas, verificar que exista el objeto solo buscando su id, donde no preciso del resto de los datos
     * @return Alarma_Temperaturas
     */
    private function cargarObjetoConClave($param)
    {
        $obj = null;
        if (isset($param['idtemperaturaalarma'])) {//si está seteado ese id, o sea si contiene datos:
            $obj = new Alarma_Temperaturas();//new de esa clase
            $obj->cargar($param['idtemperaturaalarma'], null, null, null, null, null);//cargo al objeto pero sin la necesidad de poner los demás datos, por eso uso los null
        }
        return $obj;//retorno el objeto si pudo crearse o null si no
    }


    /**
     * Corrobora que dentro del arreglo asociativo estan seteados los campos claves
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
     * genera un INSERT basicamente, de lo pasado por parametro, o sea necesita de la funcion insertar() del modelo
     * @param array $param
     */
    public function alta($param)
    {
        $resp = false;
        $elObjtAlarma = $this->cargarObjeto($param);//cargo el objeto con los datos q entran x param
        //        verEstructura($elObjtAlarma);
        if ($elObjtAlarma != null and $elObjtAlarma->insertar()) {//si el objeto no es null y da true la inserción:
            $resp = true;
        }
        return $resp;//revuelvo true si se pudo dar el alta, false si no
    }


    /**
     * permite eliminar un objeto mediante su ID usando una funcion que está en la capa de modelo
     * @return boolean
     */
    public function baja($param)
    {
        $resp = false;
        if ($this->seteadosCamposClaves($param)) {//si los campos están correctamente seteados da true
            $elObjtAlarma = $this->cargarObjetoConClave($param);//cargo el objeto solo con su id porque necesito solamente borrarlo
            if ($elObjtAlarma != null and $elObjtAlarma->eliminar()) {//si el objeto no es nulo y se pudo borrar:
                $resp = true;
            }
        }
        return $resp;//true si se pudo borrar, false si no
    }


    /**
     * permite modificar un objeto por la info que llega por paramentro, se ejecuta la funcion de la capa del modelo
     * @return boolean
     * @return boolean
     */
    public function modificacion($param)
    {
        //echo "Estoy en modificacion";
        $resp = false;
        if ($this->seteadosCamposClaves($param)) {//si los campos están correctamente seteados da true
            $elObjtAlarma = $this->cargarObjeto($param);//acá si uso cargarObjeto porque necesito toda la info del obj ya que tengo campos que modificar
            if ($elObjtAlarma != null and $elObjtAlarma->modificar()) {//si el obj no es nulo y se puede modificar:
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
        if ($param != null) {//si el parametro no es nulo
            if (isset($param['idtemperaturaalarma'])) {
                $where .= " AND idtemperaturaalarma = '" . $param['idtemperaturaalarma'] . "'";//si coincide el id de la tupla
            }
            //si coinciden el resto de los paramtros que recibe la tupla, los voy concatenando
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
    
        $arreglo = Alarma_Temperaturas::listar($where);//armo un arreglo usando listar que recibe un parametro y en base a eso genera un arreglo, o sea creo el array si es que los datos coinciden
        return $arreglo;//retorno el array 
    }


    /**
     * funcion para saber si una alarma está activa, o sea si la alarma no tiene fecha de fin es porque lo está
    */
    public function alarmaActiva($idSensor){
        $listadoActivas = [];
        $sql = "idtemperaturasensor = " . $idSensor . " AND (tafechafin IS NULL OR tafechafin = '0000-00-00 00:00:00')"; //o sea hago la consulta de que si coincide el id que entro por parametro Y no hay una fehca de fin, se entiende que está activa
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