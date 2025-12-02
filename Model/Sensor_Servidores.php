<?php
//CLASE HIJA DE SENSOR
//herencia
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
	{
		parent::cargar($idSensor, $codigoSensor, $ubicacion, $elementosResguardados, $montoResguardado);
	}

	public function cargarServidores($porcentajePerdidas)
	{ //acá tuve que renombrar a esta funcion porque había problemas de incompatibilidad entre la de la clase padre
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


	public function Buscar($id)
	{
		$base = new BaseDatos();
		$resp = false;
		//reviso que este id si existe en la clase sensor padre, o sea si esto da false es porque no está
		if (!parent::Buscar($id)) {
			$this->setmensajeoperacion($base->getError());
		} else {
			$consulta = "Select * from w_temperaturasensorservidor where idtemperaturasensor=" . $id;
			$resp = false;
			if ($base->Iniciar()) {
				if ($base->Ejecutar($consulta)) {
					if ($row2 = $base->Registro()) {
						parent::Buscar($id);
						$this->setPorcentajePerdidas($row2['tssporcentajeperdida']);
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
		$consulta = "Select * from w_temperaturasensorservidor ";
		if ($condicion != "") {
			$consulta = $consulta . ' where ' . $condicion;
		}
		$consulta .= " order by tssporcentajeperdida ";
		if ($base->Iniciar()) {
			if ($base->Ejecutar($consulta)) {
				$arreglo = array();
				while ($row2 = $base->Registro()) {
					$objSensorServ = new Sensor_Servidores();
					$objSensorServ->Buscar($row2['idtemperaturasensor']);
					array_push($arreglo, $objSensorServ);
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

		if (parent::insertar()) {
			$consultaInsertar = "INSERT INTO w_temperaturasensorservidor(idtemperaturasensor tssporcentajeperdida)
				VALUES (" . parent::getIdSensor() . ",
                '" . $this->getPorcentajePerdidas() . "')";
			if ($base->Iniciar()) {
				if ($base->Ejecutar($consultaInsertar)) {
					$resp =  true;
				} else {
					$this->setmensajeoperacion($base->getError());
				}
			} else {
				$this->setmensajeoperacion($base->getError());
			}
		}
		return $resp;
	}


	public function modificar()
	{
		$resp = false;
		$base = new BaseDatos();
		if (parent::modificar()) {
			$consultaModifica = "UPDATE w_temperaturasensorservidor SET tssporcentajeperdida='" . $this->getPorcentajePerdidas() . "' WHERE idtemperaturasensor=" . parent::getIdSensor();
			if ($base->Iniciar()) {
				if ($base->Ejecutar($consultaModifica)) {
					$resp =  true;
				} else {
					$this->setmensajeoperacion($base->getError());
				}
			} else {
				$this->setmensajeoperacion($base->getError());
			}
		}

		return $resp;
	}

	public function eliminar()
	{
		$base = new BaseDatos();
		$resp = false;
		if ($base->Iniciar()) {
			$consultaBorra = "DELETE FROM w_temperaturasensorservidor WHERE idtemperaturasensor=" . parent::getIdSensor();
			if ($base->Ejecutar($consultaBorra)) {
				// if (parent::eliminar()) {
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
	 * "Para el caso del sensor utilizado en la sala de servidores va a estar dada por la cantidad de servidores,
su costo y el porcentaje aplicado a la pérdida"
	 * el metodo está redefinido totalmente para esta clase por lo que la info del metodo en la clase padre no me sirve acá 
	 */
	public function estimarPerdidaFallo($objSensor)
	{
		$cantidad = $this->getElementosResguardados();
		$costo = $this->getMontoResguardado();
		$porcentaje = $this->getPorcentajePerdidas(); //tiene q estar en decimal
		$perdida = $cantidad * $costo * $porcentaje;

		return $perdida;
	}


	public function __toString()
	{
		$mensaje =
			parent::__toString() . "\n" .
			"Porcentaje perdidas: " . $this->getPorcentajePerdidas();
	}
}
