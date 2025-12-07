<?php
//acá trabajo con objetos y claves-valor porque no estoy sacando la información directamente de la fuente porque el control es un intermediario, a diferencia de la capa del modelo
class ControlRegistroTemperaturas
{

    /**
     * Espera como parametro un arreglo asociativo donde las claves coinciden con los nombres de las variables instancias del objeto
     * crea al objeto completo y necesita toda la informacion. Lo uso más que nada para dar altas o modificar
     * retorna el objeto que se arma a partir de los parametros
     * @return Registro_Temperaturas
     */
    private function cargarObjeto($param)
    {
        $obj = null;
        if (isset($param['idtemperaturasensor']) && isset($param['tltemperatura']) && isset($param['tlfecharegistro'])) {//es lo que se espera recibir por parametro, son los atributos de la clase menos el id de esta clase xq es autoincremental entonces habria como una contradiccion ahi

            $objSensor = new Sensor(); //new de sensor
            $objSensor->setIdSensor($param['idtemperaturasensor']); //seteo el id con el que vino por param

            $id = $param['idtemperaturaregistro'] ?? null; // si no existe ese id null porque en realidad acá no viene xq es autoincremental

            if ($objSensor->Buscar($objSensor->getIdSensor())) {//si existen las clases referenciadas hago lo siguiente, porque no puedo agregar como atributos objetos que no existen
                $obj = new Registro_Temperaturas();//new de la clase del modelo
                $obj->cargar($id, $objSensor, $param['tltemperatura'], $param['tlfecharegistro']);//cargo todos los datos necesarios al crear la clase
            }
        }
        return $obj; //retorno el objeto nuevo creado o null en caso de que falle algo
    }


    /**
     * Espera como parametro un arreglo asociativo donde las claves coinciden con los nombres de las variables instancias del objeto que son claves
     * retorna el objeto creado pero solo necesitando su id, no necesita el resto de la info. Lo uso más que nada para dar bajas, verificar que exista el objeto solo buscando su id, donde no preciso del resto de los datos
     * @return Registro_Temperaturas
     */
    private function cargarObjetoConClave($param)
    {
        $obj = null;
        if (isset($param['idtemperaturaregistro'])) {//si está seteado ese id, o sea si contiene datos:
            $obj = new Registro_Temperaturas(); //new de esa clase
            $obj->cargar($param['idtemperaturaregistro'], null, null, null);//cargo al objeto pero sin la necesidad de poner los demás datos, por eso uso los null
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
        if (isset($param['idtemperaturaregistro']))
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
        $elObjtRegistro = $this->cargarObjeto($param);//cargo el objeto con los datos q entran x param
        //        verEstructura($elObjtRegistro);
        if ($elObjtRegistro != null and $elObjtRegistro->insertar()) {//si el objeto no es null y da true la inserción:
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
        if ($this->seteadosCamposClaves($param)) {//si los campos están correctamente seteados da true
            $elObjtRegistro = $this->cargarObjetoConClave($param);//cargo el objeto solo con su id porque necesito solamente borrarlo
            if ($elObjtRegistro != null and $elObjtRegistro->eliminar()) {//si el objeto no es nulo y se pudo borrar:
                $resp = true;
            }
        }
        return $resp;//true si se pudo borrar, false si no
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
            $elObjtRegistro = $this->cargarObjeto($param);//acá si uso cargarObjeto porque necesito toda la info del obj ya que tengo campos que modificar
            if ($elObjtRegistro != null and $elObjtRegistro->modificar()) {//si el obj no es nulo y se puede modificar:
                $resp = true;
            }
        }
        return $resp;
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
            if (isset($param['idtemperaturaregistro'])) {//si coincide el id de la tupla
                $where .= " AND idtemperaturaregistro = '" . $param['idtemperaturaregistro'] . "'";
            }
            //si coinciden el resto de los paramtros que recibe la tupla, los voy concatenando
            if (isset($param['idtemperaturasensor'])) {
                $where .= " AND idtemperaturasensor = '" . $param['idtemperaturasensor'] . "'";
            }
            if (isset($param['tltemperatura'])) {
                $where .= " AND tltemperatura = '" . $param['tltemperatura'] . "'";
            }
            if (isset($param['tlfecharegistro'])) {
                $where .= " AND tlfecharegistro = '" . $param['tlfecharegistro'] . "'";
            }
        }

        $arreglo = Registro_Temperaturas::listar($where);//armo un arreglo usando listar que recibe un parametro y en base a eso genera un arreglo, o sea creo el array si es que los datos coinciden
        return $arreglo;//retorno el array 
    }


    /**
     * punto 6.2 del enunciado
     */
    public function registrosPorDebajo($idSensor)
    {
        //tomo el id q entra x parametro y lobusco acá, despues en alarma tomo el rango inferior y meto en un array todas las temperaturas inferiores de ese sensor que cumplan
        //o sea, la idea de esta funcion es: en la tabla de registros me fijo en un id especifico para tomar su temperatura inferior y a partir de ahi voy buscando las que estén por debajo de ella, o sea pej tomo el sensor con id 68 y si tiene registradas temperaturas en Registro Temperaturas, tomo la información del rango inferior que ese id tiene en Alarma Temperaturas y en base a ese rango yo voy a ir metiendo en un array todas las temperaturas registradas que estén por debajo
        $arrayXDebajo = []; //creo un array
        $objAlarma = new ControlAlarmaTemperatura(); //vreo un obj alarma para poder buscar la info
        $activas = $objAlarma->alarmaActiva($idSensor); //devuelve un array o vacio
        if (is_array($activas) && count($activas)>0) {
            $objRegistro = new Registro_Temperaturas();
            $registroXId = $objRegistro::listar("idtemperaturasensor =" . $idSensor); //o sea pido que me filtre todos los registros de temperaturas que sean del id ingresado
            if (is_array($registroXId)&& count($registroXId)>0) {
                foreach ($activas as $unaAlarma) {
                    $inferior = $unaAlarma->getInferior(); //objetngo el rango inferior de alarma

                    foreach ($registroXId as $registro) {
                        $temp = $registro->getTemperatura(); //guardo la temperaura de ese objregistro
                        if ($temp < $inferior) { //comparo la temperatura que hay en registro con el limite que tiene
                            array_push($arrayXDebajo, $temp); //o sea si la temperatura es inferior, meto esa temp al array de registros x debajo
                        }
                    }
                }
            }
        }
        return $arrayXDebajo; //retorno el array con las temperaturas que están x debajo del rango o un array vacio en caso de q no haya alguna
    }


    /**
     * punto 6.3 del enunciado
     */
    public function registrosPorEncima($idSensor)
    {
        //misma logica que la funcion de arriba solo que ahora busco lo que este por encima del rango
        //o sea, la idea de esta funcion es: en la tabla de registros me fijo en un id especifico para tomar su temperatura superior y a partir de ahi voy buscando las que estén por encima de ella, o sea pej tomo el sensor con id 68 y si tiene registradas temperaturas en Registro Temperaturas, tomo la información del rango superior que ese id tiene en Alarma Temperaturas y en base a ese rango yo voy a ir metiendo en un array todas las temperaturas registradas que estén por encima
        $arrayXEncima = []; //creo un array
        $objAlarma = new ControlAlarmaTemperatura(); //vreo un obj alarma para poder buscar la info
        $activas = $objAlarma->alarmaActiva($idSensor); //busco si hay algina alarma activa o recibo null
        if (is_array($activas) && count($activas)>0) {
            $objRegistro = new Registro_Temperaturas();
            $registroXId = $objRegistro::listar("idtemperaturasensor =" . $idSensor); //o sea pido que me filtre todos los registros de temperaturas que sean del id ingresado
            if (is_array($registroXId) && count($registroXId)>0) {
                foreach ($activas as $unaAlarma) {
                    $superior = $unaAlarma->getSuperior(); //objetngo el rango superior de alarma

                    foreach ($registroXId as $registro) {
                        $temp = $registro->getTemperatura(); //guardo la temperaura de ese objregistro
                        if ($temp > $superior) { //comparo la temperatura que hay en registro con el limite que tiene
                            array_push($arrayXEncima, $temp); //o sea si la temperatura es superior, meto esa temp al array de registros x encima
                        }
                    }
                }
            }
        }
        return $arrayXEncima; //retorno el array con las temperaturas que están x encima del rango o un array vacio en caso de q no haya alguna
    }

    /**
     * punto 6.4 del enunciado
     */
    public function temperaturaMenor($idSensor)
    {
        //el user ingresa un id y tengo que buscar en registro_temperaturas la menor ingresada para ese id
        $objRegistro = new Registro_Temperaturas();
        $menor = 9999999999; //le doy un valor super grande para comparar
        $registroXId = $objRegistro::listar("idtemperaturasensor =" . $idSensor); //o sea pido que me filtre todos los registros de temperaturas que sean del id ingresado
        foreach ($registroXId as $registro) {
            $temperatura = $registro->getTemperatura(); //guardo la temperatura que eso obtiene
            if ($temperatura < $menor) {
                $menor = $temperatura; //voy guardando las temperaturas en $menor para poder ir comparandolas y quedarme con la menor de todas
            }
        }
        return $menor; //devuelvo la menor temperatura para ese id
    }

    /**
     * punto 6.5 del enunciado
     */
    public function temperaturaMayor($idSensor)
    {
        //misma logica que la funcion de arriba solo que acá busco la mayor temperatura
        $objRegistro = new Registro_Temperaturas();
        $mayor = -9999999999; //le doy un valor super chico para comparar
        $registroXId = $objRegistro::listar("idtemperaturasensor =" . $idSensor); //o sea pido que me filtre todos los registros de temperaturas que sean del id ingresado
        foreach ($registroXId as $registro) {
            $temperatura = $registro->getTemperatura(); //guardo la temperatura que eso obtiene
            if (
                $temperatura >
                $mayor
            ) {
                $mayor = $temperatura; //voy guardando las temperaturas en $mayor para poder ir comparandolas y quedarme con la mayor de todas
            }
        }
        return $mayor; //devuelvo la mayor temperatura para ese id
    }


    /** 
     * funcion xra mostrar la info de los registros
     */
    public function mostrarInfoRegistros()
    {
        $objRegistro = new Registro_Temperaturas();
        $listado = $objRegistro::listar();
        return $listado; //retorno el array con la info del obj
    }
}
