<?php
//CLASE PADRE
//herencia
//trabajo en esta capa con id's porque el objeto está mas a la mano, o sea los datos los estoy sacando de la fuente directamente, no estoy usando algo como intermediario
class Sensor
{
	//atributos (variables instancia)
	private $idSensor;
	private $codigoSensor;
	private $ubicacion;
	private $elementosResguardados;
	private $montoResguardado;
	private $mensajeBD;


	public function __construct()
	{
		$this->idSensor = "";
		$this->codigoSensor = "";
		$this->ubicacion = "";
		$this->elementosResguardados = "";
		$this->montoResguardado = "";
		$this->mensajeBD = "";
	}

	public function cargar($idSensor, $codigoSensor, $ubicacion, $elementosResguardados, $montoResguardado)
	{ //esto es como un constructor
		$this->setIdSensor($idSensor);
		$this->setCodigoSensor($codigoSensor);
		$this->setUbicacion($ubicacion);
		$this->setElementosResguardados($elementosResguardados);
		$this->setMontoResguardado($montoResguardado);
	}

	//getters y setters
	public function getIdSensor()
	{
		return $this->idSensor;
	}
	public function getUbicacion()
	{
		return $this->ubicacion;
	}
	public function getMontoResguardado()
	{
		return $this->montoResguardado;
	}
	public function getElementosResguardados()
	{
		return $this->elementosResguardados;
	}
	public function getCodigoSensor()
	{
		return $this->codigoSensor;
	}
	public function getmensajeoperacion()
	{
		return $this->mensajeBD;
	}


	public function setIdSensor($idSensor)
	{
		$this->idSensor = $idSensor;
	}
	public function setUbicacion($ubicacion)
	{
		$this->ubicacion = $ubicacion;
	}
	public function setMontoResguardado($montoResguardado)
	{
		$this->montoResguardado = $montoResguardado;
	}
	public function setElementosResguardados($elementosResguardados)
	{
		$this->elementosResguardados = $elementosResguardados;
	}
	public function setCodigoSensor($codigoSensor)
	{
		$this->codigoSensor = $codigoSensor;
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
		$base = new BaseDatos();//nueva instancia de la base de datos
		$consultaSensor = "Select * from w_temperaturasensor where idtemperaturasensor=" . $id;//consulta sql q es un select, o sea que seleccione todo de la tabla donde el id sea el metido x param
		$resp = false;
		if ($base->Iniciar()) {//si se logró la conexion con la base de datos:
			if ($base->Ejecutar($consultaSensor)) {//ejecuto la consulta del SELECT
				if ($row2 = $base->Registro()) {//se hace el registro q es devolver el sgte resultado de la consulta hecha arriba
					$this->setIdSensor($id);//seteo el id q entra x param 
					$this->setCodigoSensor($row2['tscodigo']);//lo mismo
					$this->setUbicacion($row2['tsubicacion']);
					$this->setElementosResguardados($row2['tselementosresguardan']);
					$this->setMontoResguardado($row2['tsmontoresguardado']);
					$resp = true;
				}
			} else {//en caso de que no se pueda hacer el ejecutar
				$this->setmensajeoperacion($base->getError());//string de error
			}
		} else {//en caso de que no se pueda iniciar conexion con la bd
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
		$arregloSensor = null;
		$base = new BaseDatos();//new de base de datos
		$consultaSensor = "Select * from w_temperaturasensor ";
		if ($condicion != "") {//pido que haga un select todo de esta tabla
			$consultaSensor = $consultaSensor . ' where ' . $condicion;
		}//si viene una condición como parametro se la concatena al select
		// $consultaSensor .= " order by idtemperaturasensor ";
		//echo $consultaSensor;
		if ($base->Iniciar()) {//iniciar conexion con la base de datos
			if ($base->Ejecutar($consultaSensor)) {//que se ejecute la consulta de arriba			
				$arregloSensor = array();//se sobreescribe esa variable y ahora es un array vacio
				while ($row2 = $base->Registro()) {//se hace el registro q es devolver el sgte resultado de la consulta hecha arriba
					$idSensor = $row2['idtemperaturasensor'];//acá a esa variable le asigno lo que contenga esa llave, lo mismo con las que están abajo
					$codigoSensor = $row2['tscodigo'];
					$ubicacion = $row2['tsubicacion'];
					$elementosResguardados = $row2['tselementosresguardan'];
					$montoResguardado = $row2['tsmontoresguardado'];

					$objSensor = new Sensor();//hago un new de esta clase
					$objSensor->cargar($idSensor, $codigoSensor, $ubicacion, $elementosResguardados, $montoResguardado);//cargo todos los datos que obtuve
					array_push($arregloSensor, $objSensor);//voy pusheando cada objeto que cumpla con el select o la condición al arreglo que despues retorno
				}
			} else {//en caso de que no se pueda hacer el ejecurar
				self::setmensajeoperacion($base->getError());//string de error
			}
		} else {//en caso de que no se pueda iniciar conexion con la bd
			self::setmensajeoperacion($base->getError());
		}
		return $arregloSensor;//retorno el arreglo de objetos listados o uno vacio/null
	}


	/**
    * crea una cadena SQL que corresponde a un INSERT
    * @return boolean
    */
	public function insertar()
	{
		$base = new BaseDatos();//new de base de datos
		$resp = false;
		$consultaInsertar = "INSERT INTO w_temperaturasensor(tscodigo, tsubicacion, tselementosresguardan, tsmontoresguardado) 
				VALUES (
                '" . $this->getCodigoSensor() . "',
                '" . $this->getUbicacion() . "',
                '" . $this->getElementosResguardados() . "',
                '" . $this->getMontoResguardado() . "')";//hago la query del insert

		if ($base->Iniciar()) {//inicio conexion con la bd
			if ($id = $base->devuelveIDInsercion($consultaInsertar)) {//acá la funcion esa ejecuta la consulta deltipo insert y devuelve el id de esa tupla incrementado
				$this->setIdSensor($id);//seteo el id incrementado como el id de ese obj alarma
				$resp =  true;
			} else {//si falla que devuelva el id incrementado
				$this->setmensajeoperacion($base->getError());
			}
		} else {//si falla la conexion con la bd
			$this->setmensajeoperacion($base->getError());//string de error
		}
		return $resp;//retorno true o false
	}


	/**
     * se crea una consulta SQL del tipo UPDATE
     * @return boolean
    */
	public function modificar()
	{
		$resp = false;
		$base = new BaseDatos();//new de base de datos
		$consultaModifica = "UPDATE w_temperaturasensor SET tscodigo='" . $this->getCodigoSensor() . "',
        tsubicacion='" . $this->getUbicacion() . "',
        tselementosresguardan='" . $this->getElementosResguardados() . "',
        tsmontoresguardado=" . $this->getMontoResguardado() . " WHERE idtemperaturasensor=" . $this->getIdSensor();// o sea que se modifique eso donde el id sea el de un objeto alarma
		if ($base->Iniciar()) {//inicio conexion con la bd
			if ($base->Ejecutar($consultaModifica)) {//que se ejecute la consulta
				$resp =  true;
			} else {//si no se puede ejecutar la consulta
				$this->setmensajeoperacion($base->getError());//string de error
			}
		} else {//si no se puede conectar con la bd
			$this->setmensajeoperacion($base->getError());
		}
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
			$consultaBorra = "DELETE FROM w_temperaturasensor WHERE idtemperaturasensor=" . $this->getIdSensor();//hago la consulta sql donde pido que se borre de la tabla la tupla que tenga ese id
			if ($base->Ejecutar($consultaBorra)) {///ejecuto esa consulta
				$resp =  true;
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
	 * el metodo despues lo redefino en las clase de los servidores porque en la de heladeras se usa lo mismo, entonces la redefinicion no es necesaria ahi
	 * no voy a pedir cosas x parametro xq la capa del modelo trabaja con sus propios atributos entonces creoque no es necesario
	 */
	public function estimarPerdidaFallo()
	{
		$cantidad = $this->getElementosResguardados();
		$costo = $this->getMontoResguardado();
		$perdida = $cantidad * $costo;
		return $perdida;
	}

	//redefinicion metodo __toString()
	public function __toString()
	{
		$mensaje =
			"Id sensor: " . $this->getIdSensor() . "\n" .
			"Codigo sensor: " . $this->getCodigoSensor() . "\n" .
			"Ubicación: " . $this->getUbicacion() . "\n" .
			"Cantidad de elementos resguardados: " . $this->getElementosResguardados() . "\n" .
			"Monto resguardado: $" . $this->getMontoResguardado() . "\n";
		return $mensaje;
	}
}
