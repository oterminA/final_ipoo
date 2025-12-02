<?php
//CLASE PADRE
//herencia
class Aviso_Temperaturas{
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
    {
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

	
    public function Buscar($id){
		$base=new BaseDatos();
		$consultaAviso="Select * from w_temperaturaaviso where idtemperaturaaviso=". $id;
		$resp= false;
		if($base->Iniciar()){
			if($base->Ejecutar($consultaAviso)){
				if($row2=$base->Registro()){
				    $this->setIdAviso($id);
					$this->setActivo($row2['taactivo']);
					$this->setNombre($row2['tanombre']);
					$this->setEmail($row2['taemail']);
					$resp= true;
				}				
		 	}	else {
		 			$this->setmensajeoperacion($base->getError());
			}
		 }	else {
		 		$this->setmensajeoperacion($base->getError());
		 }		
		 return $resp;
	}	
    

	public static function listar($condicion=""){
	    $arregloAviso = null;
		$base=new BaseDatos();
		$consultaAviso="Select * from w_temperaturaaviso ";
		if ($condicion!=""){
		    $consultaAviso=$consultaAviso.' where '.$condicion;
		}
		$consultaAviso.=" order by idtemperaturaaviso ";
		//echo $consultaSensor;
		if($base->Iniciar()){
			if($base->Ejecutar($consultaAviso)){				
				$arregloAviso= array();
				while($row2=$base->Registro()){
				    $idAviso=$row2['idtemperaturaaviso'];
					$activo=$row2['taactivo'];
					$nombre=$row2['tanombre'];
					$email=$row2['taemail'];
				
					$objAviso=new Aviso_Temperaturas();
					$objAviso->cargar($idAviso, $activo, $nombre, $email);
					array_push($arregloAviso,$objAviso);
				}
		 	}	else {
		 			self::setmensajeoperacion($base->getError());
			}
		 }	else {
		 		self::setmensajeoperacion($base->getError());
		 }	
		 return $arregloAviso;
	}	

	
	public function insertar(){
		$base=new BaseDatos();
		$resp= false;
		$consultaInsertar="INSERT INTO w_temperaturaaviso(taactivo, tanombre, taemail) 
				VALUES (
                '".$this->getActivo()."',
                '".$this->getNombre()."',
                '".$this->getEmail()."')";
		
		if($base->Iniciar()){
			if($id = $base->devuelveIDInsercion($consultaInsertar)){
                $this->setIdAviso($id);
			    $resp=  true;
			}	else {
					$this->setmensajeoperacion($base->getError());
			}
		} else {
				$this->setmensajeoperacion($base->getError());
		}
		return $resp;
	}
	
	
	
	public function modificar(){
		$resp = false;
		$base = new BaseDatos();
	
		$consultaModifica = "UPDATE w_temperaturaaviso SET 
        taactivo='" . $this->getActivo() . "',
        tanombre='" . $this->getNombre() . "',
        taemail='" . $this->getEmail() . "'
    WHERE idtemperaturaaviso = " . $this->getIdAviso();

	
		if($base->Iniciar()){
			if($base->Ejecutar($consultaModifica)){
				$resp = true;
			} else {
				$this->setmensajeoperacion($base->getError());
			}
		} else {
			$this->setmensajeoperacion($base->getError());
		}
	
		return $resp;
	}
	
	

	public function eliminar(){
		$base=new BaseDatos();
		$resp=false;
		if($base->Iniciar()){
				$consultaBorra="DELETE FROM w_temperaturaaviso WHERE idtemperaturaaviso=".$this->getIdAviso();
				if($base->Ejecutar($consultaBorra)){
				    $resp=  true;
				}else{
						$this->setmensajeoperacion($base->getError());
				}
		}else{
				$this->setmensajeoperacion($base->getError());
			
		}
		return $resp; 
	}

    //redefinicion metodo __toString()
    public function __toString()
    {
        $mensaje = 
        "Id aviso: " . $this->getIdAviso() . "\n" . 
        "Activo: " . $this->getActivo() . "\n" . 
        "Nombre: " . $this->getNombre() . "\n" . 
        "Email: " . $this->getEmail() . "\n" ;
        return $mensaje;
    }

}