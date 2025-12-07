<?php
//CLASE HIJA DE SENSOR
//herencia
//trabajo en esta capa con id's porque el objeto está mas a la mano, o sea los datos los estoy sacando de la fuente directamente, no estoy usando algo como intermediario
class Sensor_Servidores extends Sensor
{
	//atributos
	private $porcentajePerdidas;

	public function __construct()
	{
		parent::__construct();
		$this->porcentajePerdidas = ""; //lo que va a entrar acá tiene que estar en decimal pero igual voy a hacer los calculos cuando sea necesario para no tener errores
	}

	public function cargar($idSensor, $codigoSensor, $ubicacion, $elementosResguardados, $montoResguardado)
	{ //es como un constructor masomeno
		parent::cargar($idSensor, $codigoSensor, $ubicacion, $elementosResguardados, $montoResguardado);
	}

	public function cargarServidores($porcentajePerdidas)
	{ //acá tuve que renombrar a esta funcion porque había problemas de incompatibilidad con la de la clase padre
		$this->setPorcentajePerdidas($porcentajePerdidas);
	}

	//getters y setters
	public function getPorcentajePerdidas()
	{
		return $this->porcentajePerdidas;
	}
	// public function getmensajeoperacion(){
	// 	return $this->mensajeoperacion ;
	// }

	public function setPorcentajePerdidas($porcentajePerdidas)
	{
		$this->porcentajePerdidas = $porcentajePerdidas;
	}
	// public function setmensajeoperacion($mensajeoperacion){
	// 	$this->mensajeoperacion=$mensajeoperacion;
	// }


	/**
	 * recibe un id como parametro y ejecuta la consulta del SELECT buscando lo que coincida con la informacion
	 * @return boolean
	 */
	public function Buscar($id)
	{
		$base = new BaseDatos(); //nueva instancia de la base de datos
		$resp = false;

		if (!parent::Buscar($id)) { //reviso que este id si existe en la clase sensor padre, o sea si esto da false es porque no está
			$this->setmensajeoperacion($base->getError());
		} else {
			$consulta = "Select * from w_temperaturasensorservidor where idtemperaturasensor=" . $id; //consulta sql q es un select, o sea que seleccione todo de la tabla donde el id sea el metido x param
			$resp = false;
			if ($base->Iniciar()) { //si se logró la conexion con la base de datos:
				if ($base->Ejecutar($consulta)) { //ejecuto la consulta del SELECT
					if ($row2 = $base->Registro()) { //se hace el registro q es devolver el sgte resultado de la consulta hecha arriba
						parent::Buscar($id); //busco el id que viene dela clase padre
						$this->setPorcentajePerdidas($row2['tssporcentajeperdida']); //seteo este dato
						$resp = true;
					}
				} else { //en caso de que no se pueda hacer el ejecutar
					$this->setmensajeoperacion($base->getError()); //string de error
				}
			} else { //en caso de que no se pueda iniciar conexion con la bd
				$this->setmensajeoperacion($base->getError());
			}
		}
		return $resp;
	}

	/**
	 * es como un select con una condición, devuelve el arreglo de esa consulta o null
	 * @return array|null
	 */
	public static function listar($condicion = "")
	{
		$arreglo = null;
		$base = new BaseDatos(); //new de base de datos
		$consulta = "Select * from w_temperaturasensorservidor "; //pido que haga un select todo de esta tabla
		if ($condicion != "") { //si viene una condición como parametro se la concatena al select
			$consulta = $consulta . ' where ' . $condicion;
		}
		if ($base->Iniciar()) { //iniciar conexion con la base de datos
			if ($base->Ejecutar($consulta)) { //que se ejecute la consulta de arriba	
				$arreglo = array(); //se sobreescribe esa variable y ahora es un array vacio
				while ($row2 = $base->Registro()) { //se hace el registro q es devolver el sgte resultado de la consulta hecha arriba
					$objSensorServ = new Sensor_Servidores(); //hago un new de esta clase
					$objSensorServ->Buscar($row2['idtemperaturasensor']); //y busco en el modelo que exista ese sensor
					array_push($arreglo, $objSensorServ); //voy pusheando cada objeto que cumpla con el select o la condición al arreglo que despues retorno
				}
			} else { //en caso de que no se pueda hacer el ejecutar
				self::setmensajeoperacion($base->getError()); //string de error
			}
		} else { //en caso de que no se pueda iniciar conexion con la bd
			self::setmensajeoperacion($base->getError());
		}
		return $arreglo; //retorno el arreglo de objetos listados o uno vacio/null
	}


	/**
	 * crea una cadena SQL que corresponde a un INSERT
	 * @return boolean
	 */
	public function insertar()
	{
		$base = new BaseDatos(); //new de base de datos
		$resp = false;
		// if (parent::insertar()) {//acá comento esto porque si lo dejo cuando hago un alta de una nueva instancia de la clase hija me genera un nuevo id y entiendo que debería ser el mismo que se creó en la clase padre
		$consultaInsertar = "INSERT INTO w_temperaturasensorservidor(idtemperaturasensor, tssporcentajeperdida)
				VALUES (" . parent::getIdSensor() . ",
                '" . $this->getPorcentajePerdidas() . "')"; //hago la query del insert
		if ($base->Iniciar()) { //inicio conexion con la bd
			if ($base->Ejecutar($consultaInsertar)) { //si se puede ejecutar esa consulta
				$resp =  true;
			} else { //si falla la consulta
				$this->setmensajeoperacion($base->getError()); //string de error
			}
		} else { //si falla la conexion con la bd
			$this->setmensajeoperacion($base->getError());
		}
		// }
		return $resp;
	}

	/**
	 * se crea una consulta SQL del tipo UPDATE
	 * @return boolean
	 */
	public function modificar()
	{
		$resp = false;
		$base = new BaseDatos(); //new de base de datos
		// if (parent::modificar()) {
		$consultaModifica = "UPDATE w_temperaturasensorservidor SET tssporcentajeperdida='" . $this->getPorcentajePerdidas() . "' WHERE idtemperaturasensor=" . parent::getIdSensor(); // o sea que se modifique eso donde el id sea el de un objeto alarma
		if ($base->Iniciar()) { //inicio conexion con la bd
			if ($base->Ejecutar($consultaModifica)) { //que se ejecute la consulta
				$resp =  true;
			} else { //si no se puede ejecutar la consulta
				$this->setmensajeoperacion($base->getError()); //string de error
			}
		} else { //si no se puede conectar con la bd
			$this->setmensajeoperacion($base->getError());
		}
		// }
		return $resp; //devuelvo true si se modificó y false si no
	}


	/**
	 * recibe una consulta SQL del tipo DELETE
	 * @return boolean
	 */
	public function eliminar()
	{
		$base = new BaseDatos();//new de base de datos
		$resp = false;
		if ($base->Iniciar()) {//inicio conexion con la bd
			$consultaBorra = "DELETE FROM w_temperaturasensorservidor WHERE idtemperaturasensor=" . parent::getIdSensor();//hago la consulta sql donde pido que se borre de la tabla la tupla que tenga ese id que viene de la clase padre
			if ($base->Ejecutar($consultaBorra)) {///ejecuto esa consulta
				// if (parent::eliminar()) {
				$resp =  true;
				// }
			} else {//por si no se puede ejecutar
				$this->setmensajeoperacion($base->getError());
			}
		} else {//por si falla la conexion
			$this->setmensajeoperacion($base->getError());
		}
		return $resp;//true si se pudo eliminar, false si no
	}


	/**
	 * retorna el importe final correspondiente a las pérdidas producidas en caso que el sensor (objSensor) falle
	 * este metodo va acá porque trabajo con comportamiento del objeto y la logica del mismo, lo que abarca la capa del modelo
	 * "Para el caso del sensor utilizado en la sala de servidores va a estar dada por la cantidad de servidores,
su costo y el porcentaje aplicado a la pérdida"
	 * el metodo está redefinido totalmente para esta clase por lo que la info del metodo en la clase padre no me sirve acá 
	 * no voy a pedir cosas x parametro xq la capa del modelo trabaja con sus propios atributos entonces creoque no es necesario
	 */
	public function estimarPerdidaFallo()
	{
		$montoPadre = parent::estimarPerdidaFallo(); //acá se guarda el valor que viene del metodo padre(que es costo*cantidad)
		$porcentaje = $this->getPorcentajePerdidas(); //tiene q estar en decimal
		$perdida = $montoPadre * $porcentaje;

		return $perdida;
	}


	//redefinicion del metodo toString
	public function __toString()
	{
		$mensaje =
			parent::__toString() .
			"Porcentaje perdidas: " . $this->getPorcentajePerdidas() . "%.\n";
		return $mensaje;
	}
}
