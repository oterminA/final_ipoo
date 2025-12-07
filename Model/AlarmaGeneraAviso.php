<?php
//delegacion
//tabla que muestra la relacion entre Aviso y Alamarma
//trabajo en esta capa con id's porque el objeto está mas a la mano, o sea los datos los estoy sacando de la fuente 
class AlarmaGeneraAviso
{
	//atributos (variables instancia)
	private $id;
	private $objAviso; //ref a clase Aviso
	private $objAlarma; //ref a clase Alarma
	private $mensajeBD;


	public function __construct()
	{
		$this->id = "";
		$this->objAviso = null; //?
		$this->objAlarma = null; //?
		$this->mensajeBD = "";
	}

	public function cargar($id, $objAviso, $objAlarma)
	{ //es como un constructor masomeno
		$this->setId($id);
		$this->setObjAviso($objAviso);
		$this->setObjAlarma($objAlarma);
	}

	//getters y setters
	public function getId()
	{
		return $this->id;
	}
	public function getObjAviso()
	{
		return $this->objAviso;
	}
	public function getObjAlarma()
	{
		return $this->objAlarma;
	}
	public function getmensajeoperacion()
	{
		return $this->mensajeBD;
	}


	public function setId($id)
	{
		$this->id = $id;
	}
	public function setObjAviso($objAviso)
	{
		$this->objAviso = $objAviso;
	}
	public function setObjAlarma($objAlarma)
	{
		$this->objAlarma = $objAlarma;
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
		$consultaAlarma = "Select * from w_temperaturasensortemperaturaaviso where idavisoalarma=" . $id; //consulta sql q es un select, o sea que seleccione todo de la tabla donde el id sea el metido x param
		$resp = false;
		if ($base->Iniciar()) { //si se logró la conexion con la base de datos:
			if ($base->Ejecutar($consultaAlarma)) { //ejecuto la consulta del SELECT
				if ($row2 = $base->Registro()) { //se hace el registro q es devolver el sgte resultado de la consulta hecha arriba

					$objAviso = new Aviso_Temperaturas(); //hago un new para pasar el id despues
					$objAviso->Buscar($row2['idtemperaturaaviso']); //uso el buscar de esa clase para encontrar ese id

					$objAlarma = new Alarma_Temperaturas(); //lo mismo de arriba
					$objAlarma->Buscar($row2['idtemperaturaalarma']);

					$this->setId($id); //seteo el id q entra x param 
					$this->setObjAviso($objAviso); //lo mismo 
					$this->setObjAlarma($objAlarma);
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
		$arreglo = null;
		$base = new BaseDatos(); //new de base de datos
		$consulta = "Select * from w_temperaturasensortemperaturaaviso "; //pido que haga un select todo de esta tabla
		if ($condicion != "") {
			$consulta = $consulta . ' where ' . $condicion;
		} //si viene una condición como parametro se la concatena al select
		// $consulta.=" order by idtemperaturaalarma ";
		if ($base->Iniciar()) {
			if ($base->Ejecutar($consulta)) {
				$arreglo = array(); //se sobreescribe esa variable y ahora es un array vacio
				while ($row2 = $base->Registro()) { //se hace el registro q es devolver el sgte resultado de la consulta hecha arriba
					$objAviso = new Aviso_Temperaturas(); //hago un new de esta clase
					$objAviso->Buscar($row2['idtemperaturaaviso']); //seteo como id de sensor lo que se guarde en esa llave

					$objAlarma = new Alarma_Temperaturas(); //mismo
					$objAlarma->Buscar($row2['idtemperaturaalarma']);

					$obj = new AlarmaGeneraAviso(); //hago un new de esa clase
					$obj->cargar($row2['idavisoalarma'], $objAviso, $objAlarma); //cargo todos los datos que obtuve
					array_push($arreglo, $obj); //voy pusheando cada objeto que cumpla con el select o la condición al arreglo que despues retorno
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
		$objAlarma = $this->getObjAlarma(); //traigo el atributo de sensor que está en esta clase
		$objAviso = $this->getObjAviso();
		if ($objAlarma === null || !method_exists($objAlarma, 'getIdAlarma') && ($objAviso === null || !method_exists($objAviso, 'getIdAviso'))) { //si el objeto es null o no existe un getIdSensor muestro mensajes de error
			$this->setmensajeoperacion($base->getError());
			$resp = false; //string con el mensaje de error
		} else {
			$idAviso = $objAviso->getIdAviso(); //obtengo el id de aviso xq la base de datos recibe id, no objetos como uso acá
			$idAlarma = $objAlarma->getIdAlarma(); //no puedo hacer una query pasando un objeto porque estas solo reciben id, por eso tengo que obtener el id de alguna forma para poder usarlo en la consulta
			$consultaInsertar = "INSERT INTO w_temperaturasensortemperaturaaviso(idtemperaturaaviso, idtemperaturaalarma) 
				VALUES (
                '" . $idAviso . "',
                '" . $idAlarma . "')"; //hago la query del insert
			if ($base->Iniciar()) { //inicio conexion con la bd
				if ($id = $base->devuelveIDInsercion($consultaInsertar)) { //acá la funcion esa ejecuta la consulta deltipo insert y devuelve el id de esa tupla incrementado
					$this->setId($id); //seteo el id incrementado como el id de ese obj alarma
					$resp =  true;
				} else {
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
		$base = new BaseDatos();//new de base de datos
		$idAviso = $this->getObjAviso()->getIdAviso();
		$idAlarma = $this->getObjAlarma()->getIdAlarma();
		$consultaModifica = "UPDATE w_temperaturasensortemperaturaaviso SET idtemperaturaaviso='" . $idAviso . "',
        idtemperaturaalarma=" . $idAlarma . " 
		WHERE idavisoalarma=" . $this->getId();// o sea que se modifique eso donde el id sea el de un objeto alarma
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
			$consultaBorra = "DELETE FROM w_temperaturasensortemperaturaaviso WHERE idavisoalarma=" . $this->getId(); //hago la consulta sql donde pido que se borre de la tabla la tupla que tenga ese id
			if ($base->Ejecutar($consultaBorra)) {///ejecuto esa consulta
				$resp =  true;
			} else {//por si no se puede ejecutar
				$this->setmensajeoperacion($base->getError());//string que muestra un msj de error
			}
		} else {
			$this->setmensajeoperacion($base->getError());
		}
		return $resp;//true si se pudo eliminar, false si no
	}

	//redefinicion metodo __toString()
	public function __toString()
	{
		$mensaje =
			"Id: " . $this->getId() . "\n" .
			"Aviso relacionado----\n " . $this->getObjAviso() .
			"Alarma relacionada----\n " . $this->getObjAlarma() . "\n";
		return $mensaje;
	}
}
