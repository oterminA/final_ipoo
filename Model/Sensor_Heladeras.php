<?php
//CLASE HIJA DE SENSOR
//herencia
//trabajo en esta capa con id's porque el objeto está mas a la mano, o sea los datos los estoy sacando de la fuente directamente, no estoy usando algo como intermediario
class Sensor_Heladeras extends Sensor
{
	//atributos
	private $marca;
	private $modelo;


	public function __construct()
	{
		parent::__construct();
		$this->marca = "";
		$this->modelo = "";
	}

	public function cargar($idSensor, $codigoSensor, $ubicacion, $elementosResguardados, $montoResguardado)
	{//es como un constructor masomeno
		parent::cargar($idSensor, $codigoSensor, $ubicacion, $elementosResguardados, $montoResguardado);
	}

	public function cargarHeladeras($marca, $modelo)
	{ //acá tuve que renombrar a esta funcion porque había problemas de incompatibilidad con la de la clase padre
		$this->setMarca($marca);
		$this->setModelo($modelo);
	}

	//getters y setters
	public function getMarca()
	{
		return $this->marca;
	}
	public function getModelo()
	{
		return $this->modelo;
	}
	// public function getmensajeoperacion(){
	// 	return $this->mensajeoperacion ;
	// }

	public function setMarca($marca)
	{
		$this->marca = $marca;
	}
	public function setModelo($modelo)
	{
		$this->modelo = $modelo;
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
		$base = new BaseDatos();//nueva instancia de la base de datos
		$resp = false;
		if (!parent::Buscar($id)) {//reviso que este id si existe en la clase sensor padre, o sea si esto da false es porque no está
			$this->setmensajeoperacion($base->getError());
		} else {
			$consulta = "Select * from w_temperaturasensorheladera where idtemperaturasensor=" . $id;//consulta sql q es un select, o sea que seleccione todo de la tabla donde el id sea el metido x param
			if ($base->Iniciar()) {//si se logró la conexion con la base de datos:
				if ($base->Ejecutar($consulta)) {//ejecuto la consulta del SELECT
					if ($row2 = $base->Registro()) {//se hace el registro q es devolver el sgte resultado de la consulta hecha arriba
						parent::Buscar($id); //busco el id que viene dela clase padre
						$this->setMarca($row2['marca']); //seteo estos datos
						$this->setModelo($row2['modelo']);
						$resp = true;
					}
				} else {//en caso de que no se pueda hacer el ejecutar
					$this->setmensajeoperacion($base->getError());//string de error
				}
			} else {//en caso de que no se pueda iniciar conexion con la bd
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
		$base = new BaseDatos();//new de base de datos
		$consulta = "Select * from w_temperaturasensorheladera ";//pido que haga un select todo de esta tabla
		if ($condicion != "") {//si viene una condición como parametro se la concatena al select
			$consulta = $consulta . ' where ' . $condicion;
		}
		// $consulta .= " order by marca ";
		if ($base->Iniciar()) { //iniciar conexion con la base de datos
			if ($base->Ejecutar($consulta)) {//que se ejecute la consulta de arriba	
				$arreglo = array();//se sobreescribe esa variable y ahora es un array vacio
				while ($row2 = $base->Registro()) {//se hace el registro q es devolver el sgte resultado de la consulta hecha arriba
					$objSensorHel = new Sensor_Heladeras();//hago un new de esta clase
					$objSensorHel->Buscar($row2['idtemperaturasensor']);//y busco en el modelo que exista ese sensor
					array_push($arreglo, $objSensorHel);//voy pusheando cada objeto que cumpla con el select o la condición al arreglo que despues retorno
				}
			} else {//en caso de que no se pueda hacer el ejecutar
				self::setmensajeoperacion($base->getError());//string de error
			}
		} else {//en caso de que no se pueda iniciar conexion con la bd
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
		$base = new BaseDatos();//new de base de datos
		$resp = false;

		// if (parent::insertar()) {//acá comento esto porque si lo dejo cuando hago un alta de una nueva instancia de la clase hija me genera un nuevo id y entiendo que debería ser el mismo que se creó en la clase padre
			$consultaInsertar = "INSERT INTO w_temperaturasensorheladera(idtemperaturasensor, marca, modelo)
				VALUES (" . parent::getIdSensor() . ",
                '" . $this->getMarca() . "',
                '" . $this->getModelo() . "')";//hago la query del insert
			if ($base->Iniciar()) {//inicio conexion con la bd
				if ($base->Ejecutar($consultaInsertar)) {//si se puede ejecutar esa consulta
					$resp =  true;
				} else { //si falla la consulta
					$this->setmensajeoperacion($base->getError());//string de error
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
		$base = new BaseDatos();//new de base de datos
		// if (parent::modificar()) {
			$consultaModifica = "UPDATE w_temperaturasensorheladera 
			SET marca='" . $this->getMarca() ."', 
			modelo='" . $this->getModelo() ."'
				 WHERE idtemperaturasensor=" . parent::getIdSensor();// o sea que se modifique eso donde el id sea el de un objeto alarma
			if ($base->Iniciar()) {//inicio conexion con la bd
				if ($base->Ejecutar($consultaModifica)) {//que se ejecute la consulta
					$resp =  true;
				} else {//si no se puede ejecutar la consulta
					$this->setmensajeoperacion($base->getError());//string de error
				}
			} else {//si no se puede conectar con la bd
				$this->setmensajeoperacion($base->getError());
			}
		// }
		return $resp;//devuelvo true si se modificó y false si no
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
			$consultaBorra = "DELETE FROM w_temperaturasensorheladera WHERE idtemperaturasensor=" . parent::getIdSensor();//hago la consulta sql donde pido que se borre de la tabla la tupla que tenga ese id que viene de la clase padre
			if ($base->Ejecutar($consultaBorra)) {///ejecuto esa consulta
				// if (parent::eliminar()) { //le pongo esto xq no se si quiero que se borre en el padre y creo que x las restricciones de integridad de la bd no puedo
					$resp =  true;
				// }
			} else {//por si no se puede ejecutar
				$this->setmensajeoperacion($base->getError());
			}
		} else {//por si falla la conexion
			$this->setmensajeoperacion($base->getError());//string que muestra un msj de error
		}
		return $resp;//true si se pudo eliminar, false si no
	}


	/**
	 * retorna el importe final correspondiente a las pérdidas producidas en caso que el sensor (objSensor) falle
	 * este metodo va acá porque trabajo con comportamiento del objeto y la logica del mismo, lo que abarca la capa del modelo
	 * "En cambio, para el caso de los sensores de las heladeras, va a estar dado por el costo promedio de productos por la cantidad"
	 * el metodo NO está redefinido porque despues me di cuenta que la clase sensor padre y esta usan la misma forma y solo cambia en la de sensor servidores
	 * no voy a pedir cosas x parametro xq la capa del modelo trabaja con sus propios atributos entonces creoque no es necesario
	 */
	// public function estimarPerdidaFallo()
	// {
	// 	$cantidad = $this->getElementosResguardados();
	// 	$costo = $this->getMontoResguardado();
	// 	$perdida = $cantidad * $costo;
	// 	return $perdida;
	// }


	//redefinición metodo toString()
	public function __toString()
	{
		$mensaje =
			parent::__toString() . 
			"Marca: " . $this->getMarca() . "\n" .
			"Modelo: " . $this->getModelo() . "\n";
		return $mensaje;
	}
}
