<?php
//delegacion

class Registro_Temperaturas{
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
    {
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

	
    public function Buscar($id){
		$base=new BaseDatos();
		$consultaRegistro="Select * from w_temperaturaregistro where idtemperaturaregistro=". $id;
		$resp= false;
		if($base->Iniciar()){
			if($base->Ejecutar($consultaRegistro)){
				if($row2=$base->Registro()){

                    $objSensor = new Sensor();
                    $objSensor->Buscar($row2['idtemperaturasensor']);

				    $this->setIdRegistro($id);
					$this->setObjSensor($objSensor);
					$this->setTemperatura($row2['tltemperatura']);
					$this->setFecha($row2['tlfecharegistro']);
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
	    $arregloRegistro = null;
		$base=new BaseDatos();
		$consultaRegistro="Select * from w_temperaturaregistro ";
		if ($condicion!=""){
		    $consultaRegistro=$consultaRegistro.' where '.$condicion;
		}
		// $consultaRegistro.=" order by tlfecharegistro ";
		if($base->Iniciar()){
			if($base->Ejecutar($consultaRegistro)){				
				$arregloRegistro= array();
				while($row2=$base->Registro()){
				    $idRegistro=$row2['idtemperaturaregistro'];
					$temperatura=$row2['tltemperatura'];
					$fecha=$row2['tlfecharegistro'];

                    $objSensor = new Sensor();
					if($objSensor->Buscar($row2['idtemperaturasensor'])){
						$objRegistro=new Registro_Temperaturas();
					$objRegistro->cargar($idRegistro, $objSensor, $temperatura, $fecha);
					array_push($arregloRegistro,$objRegistro);
					}
				}
		 	}	
		 }	
		 return $arregloRegistro;
	}	


	
	public function insertar(){
		$base=new BaseDatos();
		$resp= false;
        $objSensor = $this->getObjSensor();
        if ($objSensor === null || !method_exists($objSensor, 'getIdSensor')){
            $this->setmensajeoperacion($base->getError());
            $resp = false;
        }else{
            $idSensor = $objSensor->getIdSensor();
		$consultaInsertar="INSERT INTO w_temperaturaregistro(idtemperaturasensor, tltemperatura, tlfecharegistro) 
				VALUES (
                '".$idSensor."',
                '".$this->getTemperatura()."',
                '".$this->getFecha()."')";
		
		if($base->Iniciar()){
			if($id = $base->devuelveIDInsercion($consultaInsertar)){
                $this->setIdRegistro($id);
			    $resp=  true;
			}	else {
					$this->setmensajeoperacion($base->getError());
			}
		} else {
				$this->setmensajeoperacion($base->getError());
		}
		return $resp;
	}
}
	
	public function modificar(){
	    $resp =false; 
	    $base=new BaseDatos();
		$idSensor = $this->getObjSensor()->getIdSensor(); //marca error pero esto tengo que ponerlo porque en sql yo tengo que pasar si o si un id, no puedo pasar un objeto porque así no funciona la bd
		$consultaModifica="UPDATE w_temperaturaregistro 
		SET idtemperaturasensor='".$idSensor."',
        tltemperatura='".$this->getTemperatura()."',
        tlfecharegistro='".$this->getFecha()."' WHERE idtemperaturaregistro=".$this->getIdRegistro();
		if($base->Iniciar()){
			if($base->Ejecutar($consultaModifica)){
			    $resp=  true;
			}else{
				$this->setmensajeoperacion($base->getError());
				
			}
		}else{
				$this->setmensajeoperacion($base->getError());
		}
		return $resp;
	}
	
	public function eliminar(){
		$base=new BaseDatos();
		$resp=false;
		if($base->Iniciar()){
				$consultaBorra="DELETE FROM w_temperaturaregistro WHERE idtemperaturaregistro=".$this->getIdRegistro();
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
        "Id registro: " . $this->getIdRegistro() . "\n" . 
        "Objeto sensor----\n " . $this->getObjSensor() . "\n" . 
        "Temperatura: " . $this->getTemperatura() .  "\n" . 
        "Fecha: " . $this->getFecha() . "\n" ;
        return $mensaje;
    }

}