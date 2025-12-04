<?php
class ControlRegistroTemperaturas
{
    /**
     * Espera como parametro un arreglo asociativo donde las claves coinciden con los nombres de las variables instancias del objeto
     * @param array $param
     * @return Registro_Temperaturas
     */
    private function cargarObjeto($param)
    {
        $obj = null;
        if (isset($param['idtemperaturasensor']) && isset($param['tltemperatura']) && isset($param['tlfecharegistro'])) {

            $objSensor = new Sensor();
            $objSensor->setIdSensor($param['idtemperaturasensor']);
            
            $id = $param['idtemperaturaregistro'] ?? null; // si no existe, null

            if ($objSensor->Buscar($objSensor->getIdSensor())) {
                $obj = new Registro_Temperaturas();
                $obj->cargar($id, $objSensor, $param['tltemperatura'], $param['tlfecharegistro']);
            }
        }
        return $obj;
    }


    /**
     * Espera como parametro un arreglo asociativo donde las claves coinciden con los nombres de las variables instancias del objeto que son claves
     * @param array $param
     * @return Registro_Temperaturas
     */
    private function cargarObjetoConClave($param)
    {
        $obj = null;
        if (isset($param['idtemperaturaregistro'])) {
            $obj = new Registro_Temperaturas();
            $obj->cargar($param['idtemperaturaregistro'], null, null, null);
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
        if (isset($param['idtemperaturaregistro']))
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
        $elObjtRegistro = $this->cargarObjeto($param);
        //        verEstructura($elObjtRegistro);
        if ($elObjtRegistro != null and $elObjtRegistro->insertar()) {
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
            $elObjtRegistro = $this->cargarObjetoConClave($param);
            if ($elObjtRegistro != null and $elObjtRegistro->eliminar()) {
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
            $elObjtRegistro = $this->cargarObjeto($param);
            if ($elObjtRegistro != null and $elObjtRegistro->modificar()) {
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
            if (isset($param['idtemperaturaregistro'])) {
                $where .= " AND idtemperaturaregistro = '" . $param['idtemperaturaregistro'] . "'";
            }
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

        $arreglo = Registro_Temperaturas::listar($where);
        return $arreglo;
    }


    /**
     * punto 6.2 del enunciado
     */
    public function registrosPorDebajo($idSensor)
    {
        //tomo el id q entra x parametro y lobusco acá, despues en alarma tomo el rango inferior y meto en un array todas las temperaturas inferiores de ese sensor que cumplan
        $objAlarma = new ControlAlarmaTemperatura(); //vreo un obj alarma para poder buscar la info
        $arrayXDebajo = []; //creo un array
        $activas = $objAlarma->alarmaActiva($idSensor); //busco si hay algina alarma activa o recibo null
        if ($activas !== null) {
            $alarmaActiva = $activas;
            $inferior = $alarmaActiva->getInferior(); //objetngo el rango inferior de alarmas
            $objRegistro = new Registro_Temperaturas();
            $registroXId = $objRegistro::listar("idtemperaturasensor =" . $idSensor); //o sea pido que me filtre todos los registros de temperaturas que sean del id ingresado
            if(is_array($registroXId)){
                 foreach ($registroXId as $registro) {
                $temp = $registro->getTemperatura(); //guardo la temperaura de ese objregistro
                if ($temp < $inferior) {
                    array_push($arrayXDebajo, $temp); //o sea si la temperatura es inferior, meto esa temp al array de registros x debajo
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
        $objAlarma = new ControlAlarmaTemperatura(); //vreo un obj alarma para poder buscar la info
        $activa = $objAlarma->alarmaActiva($idSensor); //busco si hay algina alarma activa o recibo null
        if ($activa <> null) {
            $alarma = new Alarma_Temperaturas();
            $superior = $alarma->getSuperior(); //objetngo el rango superior de alarmas
            $objRegistro = new Registro_Temperaturas();
            $registroXId = $objRegistro::listar("idtemperaturasensor =" . $idSensor); //o sea pido que me filtre todos los registros de temperaturas que sean del id ingresado
            $arrayXEncima = []; //creo un array
            foreach ($registroXId as $registro) {
                $temp = $registro->getTemperatura(); //guardo la temperaura de ese objregistro
                if ($temp < $superior) {
                    array_push($arrayXEncima, $registro); //o sea si la temperatura es sup$superior, meto ese objregistro al array de registros x encima
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
            if ($temperatura >
             $mayor) {
                $mayor = $temperatura; //voy guardando las temperaturas en $mayor para poder ir comparandolas y quedarme con la mayor de todas
            }
        }
        return $mayor; //devuelvo la mayor temperatura para ese id
    }
}
