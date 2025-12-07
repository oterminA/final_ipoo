<?php
//trabajo en esta capa con id's porque el objeto está mas a la mano, o sea los datos los estoy sacando de la fuente directamente, no estoy usando algo como intermediario
class Aviso_Temperaturas
{
	//atributos (variables instancia)
	private $idAviso;
	private $activo;
	private $nombre;
	private $email;
	private $mensajeBD;


	public function __construct()
	{
		$this->idAviso = "";
		$this->activo = "";
		$this->nombre = "";
		$this->email = "";
		$this->mensajeBD = "";
	}

	public function cargar($idAviso, $activo, $nombre, $email)
	{ //es como un constructor masomeno
		$this->setIdAviso($idAviso);
		$this->setActivo($activo);
		$this->setNombre($nombre);
		$this->setEmail($email);
	}

	//getters y setters
	public function getIdAviso()
	{
		return $this->idAviso;
	}
	public function getActivo()
	{
		return $this->activo;
	}
	public function getNombre()
	{
		return $this->nombre;
	}
	public function getEmail()
	{
		return $this->email;
	}
	public function getmensajeoperacion()
	{
		return $this->mensajeBD;
	}


	public function setIdAviso($idAviso)
	{
		$this->idAviso = $idAviso;
	}
	public function setActivo($activo)
	{
		$this->activo = $activo;
	}
	public function setNombre($nombre)
	{
		$this->nombre = $nombre;
	}
	public function setEmail($email)
	{
		$this->email = $email;
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
		$consultaAviso = "Select * from w_temperaturaaviso where idtemperaturaaviso=" . $id; //consulta sql q es un select, o sea que seleccione todo de la tabla donde el id sea el metido x param
		$resp = false;
		if ($base->Iniciar()) { //si se logró la conexion con la base de datos:
			if ($base->Ejecutar($consultaAviso)) { //ejecuto la consulta del SELECT
				if ($row2 = $base->Registro()) { //se hace el registro q es devolver el sgte resultado de la consulta hecha arriba
					$this->setIdAviso($id); //seteo el id q entra x param 
					$this->setActivo($row2['taactivo']); //lo mismo
					$this->setNombre($row2['tanombre']);
					$this->setEmail($row2['taemail']);
					$resp = true; //si se pudo hacer todo el seteo pongo true
				}
			} else { //en caso de que no se pueda hacer el ejecutar
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
		$arregloAviso = null;
		$base = new BaseDatos(); //new de base de datos
		$consultaAviso = "Select * from w_temperaturaaviso ";
		if ($condicion != "") { //pido que haga un select todo de esta tabla
			$consultaAviso = $consultaAviso . ' where ' . $condicion;
		} //si viene una condición como parametro se la concatena al select
		// $consultaAviso.=" order by idtemperaturaaviso ";
		//echo $consultaSensor;
		if ($base->Iniciar()) { //iniciar conexion con la base de datos
			if ($base->Ejecutar($consultaAviso)) {	//que se ejecute la consulta de arriba				
				$arregloAviso = array(); //se sobreescribe esa variable y ahora es un array vacio
				while ($row2 = $base->Registro()) { //se hace el registro q es devolver el sgte resultado de la consulta hecha arriba
					$idAviso = $row2['idtemperaturaaviso']; //acá a esa variable le asigno lo que contenga esa llave, lo mismo con las que están abajo
					$activo = $row2['taactivo'];
					$nombre = $row2['tanombre'];
					$email = $row2['taemail'];

					$objAviso = new Aviso_Temperaturas(); //hago un new de esta clase
					$objAviso->cargar($idAviso, $activo, $nombre, $email); //cargo todos los datos que obtuve
					array_push($arregloAviso, $objAviso); //voy pusheando cada objeto que cumpla con el select o la condición al arreglo que despues retorno
				}
			} else { //en caso de que no se pueda hacer el ejecurar
				self::setmensajeoperacion($base->getError());
			} //string de error
		} else { //en caso de que no se pueda iniciar conexion con la bd
			self::setmensajeoperacion($base->getError());
		}
		return $arregloAviso; //retorno el arreglo de objetos listados o uno vacio/null
	}


	/**
	 * crea una cadena SQL que corresponde a un INSERT
	 * @return boolean
	 */
	public function insertar()
	{
		$base = new BaseDatos(); //new de base de datos
		$resp = false;
		$consultaInsertar = "INSERT INTO w_temperaturaaviso(taactivo, tanombre, taemail) 
				VALUES (
                '" . $this->getActivo() . "',
                '" . $this->getNombre() . "',
                '" . $this->getEmail() . "')"; //hago la query del insert

		if ($base->Iniciar()) { //inicio conexion con la bd
			if ($id = $base->devuelveIDInsercion($consultaInsertar)) { //acá la funcion esa ejecuta la consulta deltipo insert y devuelve el id de esa tupla incrementado
				$this->setIdAviso($id); //seteo el id incrementado como el id de ese obj alarma
				$resp =  true;
			} else { //si falla que devuelva el id incrementado
				$this->setmensajeoperacion($base->getError()); //string de error
			}
		} else { //si falla la conexion con la bd
			$this->setmensajeoperacion($base->getError());
		}
		return $resp; //retorno true o false
	}


	/**
	 * se crea una consulta SQL del tipo UPDATE
	 * @return boolean
	 */
	public function modificar()
	{
		$resp = false;
		$base = new BaseDatos(); //new de base de datos
		$consultaModifica = "UPDATE w_temperaturaaviso SET 
        taactivo='" . $this->getActivo() . "',
        tanombre='" . $this->getNombre() . "',
        taemail='" . $this->getEmail() . "'
    WHERE idtemperaturaaviso = " . $this->getIdAviso(); // o sea que se modifique eso donde el id sea el de un objeto alarma
		if ($base->Iniciar()) { //inicio conexion con la bd
			if ($base->Ejecutar($consultaModifica)) { //que se ejecute la consulta
				$resp = true;
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
		if ($this->hayHijos()) { //acá me fijo si avisos tiene algun hijo (o sea tiene vinculacion con alarmas x alarmas generan avisos)
			$this->setmensajeoperacion($base->getError());
		} else {
			if ($base->Iniciar()) { //inicio conexion con la bd
				$consultaBorra = "DELETE FROM w_temperaturaaviso WHERE idtemperaturaaviso=" . $this->getIdAviso(); //hago la consulta sql donde pido que se borre de la tabla la tupla que tenga ese id
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
	 * funcion que trata de gestionar el eliminar avisos que tengan hijos(o sea que esten vinculados con alarmas) porque no quiero que se borre un aviso y que queden registros huerfanos
	 * o sea quiero que esto revise si en la tabla q representa la relacion se está usando un id aviso por lo tanto hay avisos hijos y no puede borrarse
	 * @return boolean
	 */
	public function hayHijos()
	{
		$base = new BaseDatos(); //new de la base de datos
		$resp = false;

		if ($base->Iniciar()) { //inicio conexion con la bd
			$consulta = "SELECT COUNT(*) as total FROM w_temperaturasensortemperaturaaviso 
						 WHERE idtemperaturaaviso = " . $this->getIdAviso(); //hago la consulta donde quiero contar cuantos registros de la tabla intermedia donde usan un aviso

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
			"Id aviso: " . $this->getIdAviso() . "\n" .
			"Activo: " . $this->getActivo() . "\n" .
			"Nombre del responsable por el aviso: " . $this->getNombre() . "\n" .
			"Email del responsable por el aviso: " . $this->getEmail() . "\n";
		return $mensaje;
	}
}
