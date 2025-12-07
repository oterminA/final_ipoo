<?php
//delegacion
//trabajo en esta capa con id's porque el objeto está mas a la mano, o sea los datos los estoy sacando de la fuente directamente, no estoy usando algo como intermediario
class Alarma_Temperaturas
{
    //atributos (variables instancia)
    private $idAlarma;
    private $objSensor; //ref a clase Sensor
    private $superior;
    private $inferior;
    private $fechaInicio;
    private $fechaFin;
    private $mensajeBD;


    public function __construct()
    {
        $this->idAlarma = "";
        $this->objSensor = null; //?
        $this->superior = "";
        $this->inferior = "";
        $this->fechaInicio = "";
        $this->fechaFin = ""; //si es null creo q vendria a ser q la alarma está activa, si tiene un dato entonces está desactivada
        $this->mensajeBD = "";
    }

    public function cargar($idAlarma, $objSensor, $superior, $inferior, $fechaInicio, $fechaFin)
    { //es como un constructor masomeno
        $this->setIdAlarma($idAlarma);
        $this->setObjSensor($objSensor);
        $this->setSuperior($superior);
        $this->setInferior($inferior);
        $this->setFechaInicio($fechaInicio);
        $this->setFechaFin($fechaFin);
    }

    //getters y setters
    public function getIdAlarma()
    {
        return $this->idAlarma;
    }
    public function getObjSensor()
    {
        return $this->objSensor;
    }
    public function getSuperior()
    {
        return $this->superior;
    }
    public function getInferior()
    {
        return $this->inferior;
    }
    public function getFechaInicio()
    {
        return $this->fechaInicio;
    }
    public function getFechaFin()
    {
        return $this->fechaFin;
    }
    public function getmensajeoperacion()
    {
        return $this->mensajeBD;
    }


    public function setIdAlarma($idAlarma)
    {
        $this->idAlarma = $idAlarma;
    }
    public function setObjSensor($objSensor)
    {
        $this->objSensor = $objSensor;
    }
    public function setSuperior($superior)
    {
        $this->superior = $superior;
    }
    public function setInferior($inferior)
    {
        $this->inferior = $inferior;
    }
    public function setFechaInicio($fechaInicio)
    {
        $this->fechaInicio = $fechaInicio;
    }
    public function setFechaFin($fechaFin)
    {
        $this->fechaFin = $fechaFin;
    }
    public function setmensajeoperacion($mensajeBD)
    { //lo que se muestra si hay o no algun error xq es una variable que viene desde la bd
        $this->mensajeBD = $mensajeBD;
    }


    /**
     * recibe un id como parametro y ejecuta la consulta del SELECT buscando lo que coincida con la informacion
     * @return boolean
     */
    public function Buscar($id)
    {
        $base = new BaseDatos(); //nueva instancia de la base de datos
        $consultaAlarma = "Select * from w_temperaturaalarmas where idtemperaturaalarma=" . $id; //consulta sql q es un select, o sea que seleccione todo de la tabla donde el id sea el metido x param
        $resp = false;
        if ($base->Iniciar()) { //si se logró la conexion con la base de datos:
            if ($base->Ejecutar($consultaAlarma)) { //ejecuto la consulta del SELECT
                if ($row2 = $base->Registro()) { //se hace el registro q es devolver el sgte resultado de la consulta hecha arriba

                    $objSensor = new Sensor(); //hago un new para pasar el id despues
                    $objSensor->Buscar($row2['idtemperaturasensor']); //uso el buscar de esa clase para encontrar ese id

                    $this->setIdAlarma($id); //seteo el id q entra x param 
                    $this->setObjSensor($objSensor); //seteo el obj que está como atributo acá
                    $this->setSuperior($row2['tasuperior']); //seteo la temperatura superior
                    $this->setInferior($row2['tainferior']); //lo mismo con la inferior
                    $this->setFechaInicio($row2['tafechainicio']); //seteo la fecha de inicio
                    $this->setFechaFin($row2['tafechafin']); //lo mismo con la de fin
                    $resp = true; //si se pudo hacer todo el seteo pongo true
                }
            } else { //en caso de que no se pueda hacer el ejecurar
                $this->setmensajeoperacion($base->getError()); //string de error
            }
        } else { //en caso de que no se pueda iniciar conexion con la bd
            $this->setmensajeoperacion($base->getError());
        }
        return $resp;
    }


    /**
     * es como un select con una condición, devuelve el arreglo de esa consulta o null
     * @return array|null
     */
    public static function listar($condicion = "")
    {
        $arregloAlarma = null;
        $base = new BaseDatos(); //new de base de datos
        $consultaAlarma = "Select * from w_temperaturaalarmas "; //pido que haga un select todo de esta tabla
        if ($condicion != "") { //si viene una condición como parametro se la concatena al select
            $consultaAlarma = $consultaAlarma . ' where ' . $condicion;
        }
        if ($base->Iniciar()) { //iniciar conexion con la base de datos
            if ($base->Ejecutar($consultaAlarma)) { //que se ejecute la consulta de arriba	
                $arregloAlarma = array(); //se sobreescribe esa variable y ahora es un array vacio
                while ($row2 = $base->Registro()) { //se hace el registro q es devolver el sgte resultado de la consulta hecha arriba
                    $idAlarma = $row2['idtemperaturaalarma']; //acá a esa variable le asigno lo que contenga esa llave, lo mismo con las que están abajo
                    $superior = $row2['tasuperior'];
                    $inferior = $row2['tainferior'];
                    $fechaInicio = $row2['tafechainicio'];
                    $fechaFin = $row2['tafechafin'];

                    $objSensor = new Sensor(); //hago un new de esta clase
                    $objSensor->setIdSensor($row2['idtemperaturasensor']); //seteo como id de sensor lo que se guarde en esa llave
                    $objSensor->Buscar($row2['idtemperaturasensor']); //y busco en el modelo que exista ese sensor

                    $objAlarma = new Alarma_Temperaturas(); //hago un new de esa clase
                    $objAlarma->cargar($idAlarma, $objSensor, $superior, $inferior, $fechaInicio, $fechaFin); //cargo todos los datos que obtuve
                    array_push($arregloAlarma, $objAlarma); //voy pusheando cada objeto que cumpla con el select o la condición al arreglo que despues retorno
                }
            } else { //en caso de que no se pueda hacer el ejecurar
                self::setmensajeoperacion($base->getError()); //string de error
            } //en caso de que no se pueda iniciar conexion con la bd
        } else {
            self::setmensajeoperacion($base->getError());
        }
        return $arregloAlarma; //retorno el arreglo de objetos listados o uno vacio/null
    }


    /**
     * crea una cadena SQL que corresponde a un INSERT
     * @return boolean
     */
    public function insertar()
    {
        $base = new BaseDatos(); //new de base de datos
        $resp = false;
        $objSensor = $this->getObjSensor(); //traigo el atributo de sensor que está en esta clase
        if ($objSensor === null || !method_exists($objSensor, 'getIdSensor')) { //si el objeto es null o no existe un getIdSensor muestro mensajes de error
            $this->setmensajeoperacion($base->getError()); //string con el mensaje de error
            $resp = false;
        } else { //pero si el objeto no es null y existe ese getIdSensor
            $idSensor = $objSensor->getIdSensor(); //obtengo el id de sensor xq la base de datos recibe id, no objetos como uso acá
            $consultaInsertar = "INSERT INTO w_temperaturaalarmas(idtemperaturasensor, tasuperior, tainferior, tafechainicio, tafechafin)
				VALUES (
                '" . $idSensor . "',
                '" . $this->getSuperior() . "',
                '" . $this->getInferior() . "',
                '" . $this->getFechaInicio() . "',
                '" . $this->getFechaFin() . "')"; //hago la query del insert

            if ($base->Iniciar()) { //inicio conexion con la bd
                if ($id = $base->devuelveIDInsercion($consultaInsertar)) { //acá la funcion esa ejecuta la consulta deltipo insert y devuelve el id de esa tupla incrementado
                    $this->setIdAlarma($id); //seteo el id incrementado como el id de ese obj alarma
                    $resp =  true;
                } else { //si falla que devuelva el id incrementado
                    $this->setmensajeoperacion($base->getError()); //string de error
                }
            } else { //si falla la conexion con la bd
                $this->setmensajeoperacion($base->getError());
            }
            return $resp;
        }
    }


    /**
     * se crea una consulta SQL del tipo UPDATE
     * @return boolean
     */
    public function modificar()
    {
        $resp = false;
        $base = new BaseDatos(); //new de base de datos
        $idSensor = $this->getObjSensor()->getIdSensor(); //marca error pero esto tengo que ponerlo porque en sql yo tengo que pasar si o si un id, no puedo pasar un objeto porque así no funciona la bd
        $consultaModifica = "UPDATE w_temperaturaalarmas 
        SET idtemperaturasensor='" . $idSensor . "',
        tasuperior='" . $this->getSuperior() . "',
        tainferior='" . $this->getInferior() . "',
        tafechainicio='" . $this->getFechaInicio() . "',
        tafechafin='" . $this->getFechaFin() . "' 
		WHERE idtemperaturaalarma=" . $this->getIdAlarma(); // o sea que se modifique eso donde el id sea el de un objeto alarma
        if ($base->Iniciar()) { //inicio conexion con la bd
            if ($base->Ejecutar($consultaModifica)) { //que se ejecute la consulta
                $resp =  true;
            } else { //si no se puede ejecutar la consulta
                $this->setmensajeoperacion($base->getError()); //string de error
            }
        } else { //si no se puede conectar con la bd
            $this->setmensajeoperacion($base->getError());
        }
        return $resp; //devuelvo true si se modificó y false si no
    }


    /**
     * recibe una consulta SQL del tipo DELETE
     * @return boolean
     */
    public function eliminar()
    {
        $base = new BaseDatos(); //new de base de datos
        $resp = false;
        if ($this->hayHijos()) { //acá me fijo si alarmas tiene algun hijo (o sea tiene vinculacion con avisos x alarmas generan avisos)
            $this->setmensajeoperacion($base->getError());
        } else {
            if ($base->Iniciar()) { //inicio conexion con la bd
                $consultaBorra = "DELETE FROM w_temperaturaalarmas WHERE idtemperaturaalarma=" . $this->getIdAlarma(); //hago la consulta sql donde pido que se borre de la tabla la tupla que tenga ese id
                if ($base->Ejecutar($consultaBorra)) { ///ejecuto esa consulta
                    $resp =  true;
                } else { //por si no se puede ejecutar
                    $this->setmensajeoperacion($base->getError());
                }
            } else { //por si falla la conexion
                $this->setmensajeoperacion($base->getError()); //string que muestra un msj de error

            }
        }

        return $resp; //true si se pudo eliminar, false si no
    }


    /**
     * funcion que trata de gestionar el eliminar alarmas que tengan hijos(o sea que esten vinculados con avisos) porque no quiero que se borre una alarma y que queden registros huerfanos
     * o sea quiero que esto revise si en la tabla q representa la relacion se está usando un id alarma por lo tanto hay alarmas hijos y no puede borrarse
     * @return boolean
     */
    public function hayHijos()
    {
        $base = new BaseDatos(); //new de la base de datos
        $resp = false;

        if ($base->Iniciar()) { //inicio conexion con la bd
            $consulta = "SELECT COUNT(*) as total FROM w_temperaturasensortemperaturaaviso 
						 WHERE idtemperaturaalarma = " . $this->getIdAlarma(); //hago la consulta donde quiero contar cuantos registros de la tabla intermedia donde usan una alarma

            if ($base->Ejecutar($consulta)) { //ejecuto la consulta
                $row = $base->Registro();
                if ($row && $row['total'] > 0) { //si es mayor que cero es porque se encontró al menos una relaicon donde esa tabla usa
                    $resp = true;
                }
            }
        }
        return $resp; //devuelvo un boolean
    }


    //redefinicion metodo __toString()
    public function __toString()
    {
        $mensaje =
            "Id alarma: " . $this->getIdAlarma() . "\n" .
            "Sensor relacionado ----\n " . $this->getObjSensor() .
            "Superior: " . $this->getSuperior() .  "\n" .
            "Inferior: " . $this->getInferior() .  "\n" .
            "Fecha inicio: " . $this->getFechaInicio() .  "\n" .
            "Fecha fin: " . $this->getFechaFin() . "\n";
        return $mensaje;
    }
}
