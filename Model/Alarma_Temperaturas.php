<?php
//delegacion
class Alarma_Temperaturas{
    //atributos (variables instancia)
    private $idAlarma;
    private $objSensor; //ref a clase Sensor
    private $superior;
    private $inferior;
    private $fechaInicio;
    private $fechaFin;
    private $mensajeBD;


    public function __construct()
    {
        $this->idAlarma = "";
        $this->objSensor = null; //?
        $this->superior = "";
        $this->inferior = "";
        $this->fechaInicio = "";
        $this->fechaFin = ""; //si es null creo q vendria a ser q la alarma está activa, si tiene un dato entonces está desactivada
        $this->mensajeBD = "";
    }

    public function cargar($idAlarma, $objSensor, $superior, $inferior, $fechaInicio, $fechaFin)
    {
        $this->setIdAlarma($idAlarma);
        $this->setObjSensor($objSensor);
        $this->setSuperior($superior);
        $this->setInferior($inferior);
        $this->setFechaInicio($fechaInicio);
        $this->setFechaFin($fechaFin);
    }

    //getters y setters
    public function getIdAlarma()
    {
        return $this->idAlarma;
    }
    public function getObjSensor()
    {
        return $this->objSensor;
    }
    public function getSuperior()
    {
        return $this->superior;
    }
	    public function getInferior()
    {
        return $this->inferior;
    }
    public function getFechaInicio()
    {
        return $this->fechaInicio;
    }
	    public function getFechaFin()
    {
        return $this->fechaFin;
    }
    public function getmensajeoperacion()
    {
        return $this->mensajeBD;
    }


    public function setIdAlarma($idAlarma)
    {
        $this->idAlarma = $idAlarma;
    }
    public function setObjSensor($objSensor)
    {
        $this->objSensor = $objSensor;
    }
    public function setSuperior($superior)
    {
        $this->superior = $superior;
    }
	    public function setInferior($inferior)
    {
        $this->inferior = $inferior;
    }
    public function setFechaInicio($fechaInicio)
    {
        $this->fechaInicio = $fechaInicio;
    }
	    public function setFechaFin($fechaFin)
    {
        $this->fechaFin = $fechaFin;
    }
    public function setmensajeoperacion($mensajeBD)
    { //lo que se muestra si hay o no algun error xq es una variable que viene desde la bd
        $this->mensajeBD = $mensajeBD;
    }

	
    public function Buscar($id){
		$base=new BaseDatos();
		$consultaAlarma="Select * from w_temperaturaalarmas where idtemperaturaalarma=". $id;
		$resp= false;
		if($base->Iniciar()){
			if($base->Ejecutar($consultaAlarma)){
				if($row2=$base->Registro()){

                    $objSensor = new Sensor();
                    $objSensor->Buscar($row2['idtemperaturasensor']);

				    $this->setIdAlarma($id);
					$this->setObjSensor($objSensor);
					$this->setSuperior($row2['tasuperior']);
					$this->setInferior($row2['tainferior']);
					$this->setFechaInicio($row2['tafechainicio']);
					$this->setFechaFin($row2['tafechafin']);
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
	    $arregloAlarma = null;
		$base=new BaseDatos();
		$consultaAlarma="Select * from w_temperaturaalarmas ";
		if ($condicion!=""){
		    $consultaAlarma=$consultaAlarma.' where '.$condicion;
		}
		$consultaAlarma.=" order by tafechainicio ";
		if($base->Iniciar()){
			if($base->Ejecutar($consultaAlarma)){				
				$arregloAlarma= array();
				while($row2=$base->Registro()){
				    $idAlarma=$row2['idtemperaturaalarma'];
					$superior=$row2['tasuperior'];
					$inferior=$row2['tainferior'];
					$fechaInicio=$row2['tafechainicio'];
					$fechaFin=$row2['tafechafin'];

                    $objSensor = new Sensor();
                    $objSensor->setIdSensor($row2['idtemperaturasensor']);
                    $objSensor->Buscar($row2['idtemperaturasensor']);
				
					$objAlarma=new Alarma_Temperaturas();
					$objAlarma->cargar($idAlarma, $objSensor, $superior, $inferior, $fechaInicio, $fechaFin);
					array_push($arregloAlarma,$objAlarma);
				}
		 	}	else {
		 			self::setmensajeoperacion($base->getError());
			}
		 }	else {
		 		self::setmensajeoperacion($base->getError());
		 }	
		 return $arregloAlarma;
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
            $consultaInsertar="INSERT INTO w_temperaturaalarmas(idtemperaturasensor, tasuperior, tainferior, tafechainicio, tafechafin) 
				VALUES (
                '".$idSensor."',
                '".$this->getSuperior()."',
                '".$this->getInferior()."',
                '".$this->getFechaInicio()."',
                '".$this->getFechaFin()."')";
		
		if($base->Iniciar()){
			if($id = $base->devuelveIDInsercion($consultaInsertar)){
                $this->setIdAlarma($id);
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
		$consultaModifica="UPDATE w_temperaturaalarmas 
        SET idtemperaturasensor='".$idSensor."',
        tasuperior='".$this->getSuperior()."',
        tainferior='".$this->getInferior()."',
        tafechainicio='".$this->getFechaInicio()."',
        tafechafin='".$this->getFechaFin()."' 
		WHERE idtemperaturaalarma=".$this->getIdAlarma();
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
				$consultaBorra="DELETE FROM w_temperaturaalarmas WHERE idtemperaturaalarma=".$this->getIdAlarma();
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
        "Id alarma: " . $this->getIdAlarma() . "\n" . 
        "Objeto sensor----\n " . $this->getObjSensor() . "\n" . 
        "Superior: " . $this->getSuperior() .  "\n" . 
        "Inferior: " . $this->getInferior() .  "\n" . 
        "Fecha inicio: " . $this->getFechaInicio() .  "\n" . 
        "Fecha fin: " . $this->getFechaFin() . "\n" ;
        return $mensaje;
    }

}