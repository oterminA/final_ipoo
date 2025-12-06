<?php
//CLASE HIJA DE SENSOR
//herencia
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
	{
		parent::cargar($idSensor, $codigoSensor, $ubicacion, $elementosResguardados, $montoResguardado);
	}

	public function cargarHeladeras($marca, $modelo)
	{ //acá tuve que renombrar a esta funcion porque había problemas de incompatibilidad entre la de la clase padre
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


	public function Buscar($id)
	{
		$base = new BaseDatos();
		$resp = false;
		//reviso que este id si existe en la clase sensor padre, o sea si esto da false es porque no está
		if (!parent::Buscar($id)) {
			$this->setmensajeoperacion($base->getError());
		} else {
			$consulta = "Select * from w_temperaturasensorheladera where idtemperaturasensor=" . $id;
			if ($base->Iniciar()) {
				if ($base->Ejecutar($consulta)) {
					if ($row2 = $base->Registro()) {
						parent::Buscar($id);
						$this->setMarca($row2['marca']);
						$this->setModelo($row2['modelo']);

						$resp = true;
					}
				} else {
					$this->setmensajeoperacion($base->getError());
				}
			} else {
				$this->setmensajeoperacion($base->getError());
			}
		}

		return $resp;
	}


	public static function listar($condicion = "")
	{
		$arreglo = null;
		$base = new BaseDatos();
		$consulta = "Select * from w_temperaturasensorheladera ";
		if ($condicion != "") {
			$consulta = $consulta . ' where ' . $condicion;
		}
		$consulta .= " order by marca ";
		if ($base->Iniciar()) {
			if ($base->Ejecutar($consulta)) {
				$arreglo = array();
				while ($row2 = $base->Registro()) {
					$objSensorHel = new Sensor_Heladeras();
					$objSensorHel->Buscar($row2['idtemperaturasensor']);
					array_push($arreglo, $objSensorHel);
				}
			} else {
				self::setmensajeoperacion($base->getError());
			}
		} else {
			self::setmensajeoperacion($base->getError());
		}
		return $arreglo;
	}


	public function insertar()
	{
		$base = new BaseDatos();
		$resp = false;

		// if (parent::insertar()) {//acá comento esto porque si lo dejo cuando hago un alta de una nueva instancia de la clase hija me genera un nuevo id y entiendo que debería ser el mismo que se creó en la clase padre
			$consultaInsertar = "INSERT INTO w_temperaturasensorheladera(idtemperaturasensor, marca, modelo)
				VALUES (" . parent::getIdSensor() . ",
                '" . $this->getMarca() . "',
                '" . $this->getModelo() . "')";
			if ($base->Iniciar()) {
				if ($base->Ejecutar($consultaInsertar)) {
					$resp =  true;
				} else {
					$this->setmensajeoperacion($base->getError());
				}
			} else {
				$this->setmensajeoperacion($base->getError());
			}
		// }
		return $resp;
	}


	public function modificar()
	{
		$resp = false;
		$base = new BaseDatos();
		// if (parent::modificar()) {
			$consultaModifica = "UPDATE w_temperaturasensorheladera 
			SET marca='" . $this->getMarca() ."', 
			modelo='" . $this->getModelo() ."'
				 WHERE idtemperaturasensor=" . parent::getIdSensor();
			if ($base->Iniciar()) {
				if ($base->Ejecutar($consultaModifica)) {
					$resp =  true;
				} else {
					$this->setmensajeoperacion($base->getError());
				}
			} else {
				$this->setmensajeoperacion($base->getError());
			}
		// }

		return $resp;
	}

	public function eliminar()
	{
		$base = new BaseDatos();
		$resp = false;
		if ($base->Iniciar()) {
			$consultaBorra = "DELETE FROM w_temperaturasensorheladera WHERE idtemperaturasensor=" . parent::getIdSensor();
			if ($base->Ejecutar($consultaBorra)) {
				// if (parent::eliminar()) { //le pongo esto xq no se si quiero que se borre en el padre y creo que x las restricciones de integridad de la bd no puedo
					$resp =  true;
				// }
			} else {
				$this->setmensajeoperacion($base->getError());
			}
		} else {
			$this->setmensajeoperacion($base->getError());
		}
		return $resp;
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


	public function __toString()
	{
		$mensaje =
			parent::__toString() . 
			"Marca: " . $this->getMarca() . "\n" .
			"Modelo: " . $this->getModelo() . "\n";
	}
}
