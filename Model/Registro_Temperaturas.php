<?php
//delegacion
//trabajo en esta capa con id's porque el objeto está mas a la mano, o sea los datos los estoy sacando de la fuente directamente, no estoy usando algo como intermediario
class Registro_Temperaturas
{
	//atributos (variables instancia)
	private $idRegistro;
	private $objSensor; //ref a clase Sensor
	private $temperatura;
	private $fecha;
	private $mensajeBD;


	public function __construct()
	{
		$this->idRegistro = "";
		$this->objSensor = null; //?
		$this->temperatura = "";
		$this->fecha = "";
		$this->mensajeBD = "";
	}

	public function cargar($idRegistro, $objSensor, $temperatura, $fecha)
	{ //es como un constructor masomeno
		$this->setIdRegistro($idRegistro);
		$this->setObjSensor($objSensor);
		$this->setTemperatura($temperatura);
		$this->setFecha($fecha);
	}

	//getters y setters
	public function getIdRegistro()
	{
		return $this->idRegistro;
	}
	public function getObjSensor()
	{
		return $this->objSensor;
	}
	public function getTemperatura()
	{
		return $this->temperatura;
	}
	public function getFecha()
	{
		return $this->fecha;
	}
	public function getmensajeoperacion()
	{
		return $this->mensajeBD;
	}


	public function setIdRegistro($idRegistro)
	{
		$this->idRegistro = $idRegistro;
	}
	public function setObjSensor($objSensor)
	{
		$this->objSensor = $objSensor;
	}
	public function setTemperatura($temperatura)
	{
		$this->temperatura = $temperatura;
	}
	public function setFecha($fecha)
	{
		$this->fecha = $fecha;
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
		$consultaRegistro = "Select * from w_temperaturaregistro where idtemperaturaregistro=" . $id; //consulta sql q es un select, o sea que seleccione todo de la tabla donde el id sea el metido x param
		$resp = false;
		if ($base->Iniciar()) { //si se logró la conexion con la base de datos:
			if ($base->Ejecutar($consultaRegistro)) { //ejecuto la consulta del SELECT
				if ($row2 = $base->Registro()) { //se hace el registro q es devolver el sgte resultado de la consulta hecha arriba

					$objSensor = new Sensor(); //hago un new para pasar el id despues
					$objSensor->Buscar($row2['idtemperaturasensor']); //uso el buscar de esa clase para encontrar ese id

					$this->setIdRegistro($id); //seteo el id q entra x param 
					$this->setObjSensor($objSensor); //seteo el obj que está como atributo acá
					$this->setTemperatura($row2['tltemperatura']); //mismo
					$this->setFecha($row2['tlfecharegistro']);
					$resp = true; //si se pudo hacer todo el seteo pongo true
				}
			} else { //en caso de que no se pueda hacer el ejecurar
				$this->setmensajeoperacion($base->getError()); //string de erro
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
	{ //new de base de datos
		$arregloRegistro = null;
		$base = new BaseDatos();
		$consultaRegistro = "Select * from w_temperaturaregistro "; //pido que haga un select todo de esta tabla
		if ($condicion != "") { //si viene una condición como parametro se la concatena al select
			$consultaRegistro = $consultaRegistro . ' where ' . $condicion;
		}
		// $consultaRegistro.=" order by tlfecharegistro ";
		if ($base->Iniciar()) { //iniciar conexion con la base de datos
			if ($base->Ejecutar($consultaRegistro)) {	//que se ejecute la consulta de arriba				
				$arregloRegistro = array(); //se sobreescribe esa variable y ahora es un array vacio
				while ($row2 = $base->Registro()) { //se hace el registro q es devolver el sgte resultado de la consulta hecha arriba
					$idRegistro = $row2['idtemperaturaregistro']; //acá a esa variable le asigno lo que contenga esa llave, lo mismo con las que están abajo
					$temperatura = $row2['tltemperatura'];
					$fecha = $row2['tlfecharegistro'];

					$objSensor = new Sensor(); //hago un new de esta clase
					if ($objSensor->Buscar($row2['idtemperaturasensor'])) {
						$objRegistro = new Registro_Temperaturas();
						$objRegistro->cargar($idRegistro, $objSensor, $temperatura, $fecha); //cargo todos los datos que obtuve
						array_push($arregloRegistro, $objRegistro); //voy pusheando cada objeto que cumpla con el select o la condición al arreglo que despues retorno
					}
				}
			} 
		}
		return $arregloRegistro;//retorno el arreglo de objetos listados o uno vacio/null
	}


	/**
     * crea una cadena SQL que corresponde a un INSERT
     * @return boolean
    */
	public function insertar()
	{
		$base = new BaseDatos();//new de base de datos
		$resp = false;
		$objSensor = $this->getObjSensor();//traigo el atributo de sensor que está en esta clase
		if ($objSensor === null || !method_exists($objSensor, 'getIdSensor')) {//si el objeto es null o no existe un getIdSensor muestro mensajes de error
			$this->setmensajeoperacion($base->getError());//string con el mensaje de error
			$resp = false;
		} else {//pero si el objeto no es null y existe ese getIdSensor
			$idSensor = $objSensor->getIdSensor();//obtengo el id de sensor xq la base de datos recibe id, no objetos como uso acá
			$consultaInsertar = "INSERT INTO w_temperaturaregistro(idtemperaturasensor, tltemperatura, tlfecharegistro) 
				VALUES (
                '" . $idSensor . "',
                '" . $this->getTemperatura() . "',
                '" . $this->getFecha() . "')";//hago la query del insert

			if ($base->Iniciar()) {//inicio conexion con la bd
				if ($id = $base->devuelveIDInsercion($consultaInsertar)) {//acá la funcion esa ejecuta la consulta deltipo insert y devuelve el id de esa tupla incrementado
					$this->setIdRegistro($id);//seteo el id incrementado como el id de ese obj 
					$resp =  true;
				} else {//si falla que devuelva el id incrementado
					$this->setmensajeoperacion($base->getError());//string de error
				}
			} else {//si falla la conexion con la bd
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
		$base = new BaseDatos();//new de base de datos
		$idSensor = $this->getObjSensor()->getIdSensor(); //marca error pero esto tengo que ponerlo porque en sql yo tengo que pasar si o si un id, no puedo pasar un objeto porque así no funciona la bd
		$consultaModifica = "UPDATE w_temperaturaregistro 
		SET idtemperaturasensor='" . $idSensor . "',
        tltemperatura='" . $this->getTemperatura() . "',
        tlfecharegistro='" . $this->getFecha() . 
		"' WHERE idtemperaturaregistro=" . $this->getIdRegistro();// o sea que se modifique eso donde el id sea el de un objeto registro
		if ($base->Iniciar()) {//inicio conexion con la bd
			if ($base->Ejecutar($consultaModifica)) {//que se ejecute la consulta
				$resp =  true;
			} else { //si no se puede ejecutar la consulta
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
			$consultaBorra = "DELETE FROM w_temperaturaregistro WHERE idtemperaturaregistro=" . $this->getIdRegistro();//hago la consulta sql donde pido que se borre de la tabla la tupla que tenga ese id
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

	
	//redefinicion metodo __toString()
	public function __toString()
	{
		$mensaje =
			"Id registro: " . $this->getIdRegistro() . "\n" .
			"Sensor relacionado----\n " . $this->getObjSensor() .
			"Temperatura: " . $this->getTemperatura() .  "\n" .
			"Fecha: " . $this->getFecha() . "\n";
		return $mensaje;
	}
}
