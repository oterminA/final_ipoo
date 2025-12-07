<?php
//acá trabajo con objetos y claves-valor porque no estoy sacando la información directamente de la fuente porque el control es un intermediario, a diferencia de la capa del modelo
class ControlSensor
{
    /**
     * Espera como parametro un arreglo asociativo donde las claves coinciden con los nombres de las variables instancias del objeto
     * crea al objeto completo y necesita toda la informacion. Lo uso más que nada para dar altas o modificar
     * retorna el objeto que se arma a partir de los parametros
     * @return Sensor
     */
    private function cargarObjeto($param)
    {
        $obj = null;

        if (isset($param['tscodigo']) && isset($param['tsubicacion']) && isset($param['tselementosresguardan']) && isset($param['tsmontoresguardado'])) {//es lo que se espera recibir por parametro, son los atributos de la clase menos el id de esta clase xq es autoincremental entonces habria como una contradiccion ahi
            $id = $param['idtemperaturasensor'] ?? null; // si no existe ese id null porque en realidad acá no viene xq es autoincremental
            $obj = new Sensor(); //new de sensor
            $obj->cargar($id, $param['tscodigo'], $param['tsubicacion'], $param['tselementosresguardan'], $param['tsmontoresguardado']);//cargo todos los datos necesarios al crear la clase
        }
        return $obj;//retorno el objeto nuevo creado o null en caso de que falle algo
    }


    /**
     * Espera como parametro un arreglo asociativo donde las claves coinciden con los nombres de las variables instancias del objeto que son claves
     * retorna el objeto creado pero solo necesitando su id, no necesita el resto de la info. Lo uso más que nada para dar bajas, verificar que exista el objeto solo buscando su id, donde no preciso del resto de los datos
     * @return Sensor
     */
    private function cargarObjetoConClave($param)
    {
        $obj = null;

        if (isset($param['idtemperaturasensor'])) { //si está seteado ese id, o sea si contiene datos:
            $obj = new Sensor();//new de esa clase
            $obj->cargar($param['idtemperaturasensor'], null, null, null, null);//cargo al objeto pero sin la necesidad de poner los demás datos, por eso uso los null
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
        $elObjtSensor = $this->cargarObjeto($param);//cargo el objeto con los datos q entran x param
        //        verEstructura($elObjtSensor);
        if ($elObjtSensor != null and $elObjtSensor->insertar()) {//si el objeto no es null y da true la inserción:
            $resp = true;
        }
        return $resp;//revuelvo true si se pudo dar el alta, false si no
    }


    /**
     * permite eliminar un objeto 
     * @param array $param
     * @return boolean
     */
    public function baja($param)
    {
        $resp = false;
        if ($this->seteadosCamposClaves($param)) {//si los campos están correctamente seteados da true
            $elObjtSensor = $this->cargarObjetoConClave($param);//cargo el objeto solo con su id porque necesito solamente borrarlo
            if ($elObjtSensor != null and $elObjtSensor->eliminar()) {//si el objeto no es nulo y se pudo borrar:
                $resp = true;
            }
        }
        return $resp;
    }


    /**
     * permite modificar un objeto por la info que llega por paramentro, se ejecuta la funcion de la capa del modelo
     * @return boolean
     */
    public function modificacion($param)
    {
        $resp = false;
        if ($this->seteadosCamposClaves($param)) {//si los campos están correctamente seteados da true
            $elObjtSensor = $this->cargarObjeto($param);//acá si uso cargarObjeto porque necesito toda la info del obj ya que tengo campos que modificar
            if ($elObjtSensor != null and $elObjtSensor->modificar()) {//si el obj no es nulo y se puede modificar:
                $resp = true;
            }
        }
        return $resp;//true si se pudo hacer la modificacion, false si no
    }


    /**
     * permite Buscar un objeto usando info que entra por parametro y acá tengo que usarlo así porque no puedo acceder directamente a la info sino que tengo q pasar por el modelo
     * usa una función que viene desde el modelo
     * @param array $param
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
        }
        $arreglo = Sensor::listar($where);//armo un arreglo usando listar que recibe un parametro y en base a eso genera un arreglo, o sea creo el array si es que los datos coinciden
        return $arreglo;//retorno el array
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
        $existeSensor = $objSensor->Buscar($idSensor); //busco si existe el sensor que entra x parametro
        $existeHeladera = $heladera->Buscar($idSensor);
        $existeServidor = $servidor->Buscar($idSensor);

        if($existeHeladera){ //si el id del parametro está en heladera me va a dar true
            $resp= $heladera;
        }elseif($existeServidor){ //si el id del param está en servidor me va a dar true
            $resp = $servidor;
        }elseif($existeSensor){ //si ese id no está en ninguna clase hija es porque no es un tipo de sensor y es generico
            $resp = $objSensor;
        }
        
        return $resp; //retorno ya el obj tipo de sensor o el generico(o sea que no tiene especificacion de tipo)
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
