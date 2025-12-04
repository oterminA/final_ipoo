<?php
//CLASE PADRE
//herencia
class Sensor{
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
    {
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

	
    public function Buscar($id){
		$base=new BaseDatos();
		$consultaSensor="Select * from w_temperaturasensor where idtemperaturasensor=". $id;
		$resp= false;
		if($base->Iniciar()){
			if($base->Ejecutar($consultaSensor)){
				if($row2=$base->Registro()){
				    $this->setIdSensor($id);
					$this->setCodigoSensor($row2['tscodigo']);
					$this->setUbicacion($row2['tsubicacion']);
					$this->setElementosResguardados($row2['tselementosresguardan']);
					$this->setMontoResguardado($row2['tsmontoresguardado']);
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
	    $arregloSensor = null;
		$base=new BaseDatos();
		$consultaSensor="Select * from w_temperaturasensor ";
		if ($condicion!=""){
		    $consultaSensor=$consultaSensor.' where '.$condicion;
		}
		$consultaSensor.=" order by idtemperaturasensor ";
		//echo $consultaSensor;
		if($base->Iniciar()){
			if($base->Ejecutar($consultaSensor)){				
				$arregloSensor= array();
				while($row2=$base->Registro()){
				    $idSensor=$row2['idtemperaturasensor'];
					$codigoSensor=$row2['tscodigo'];
					$ubicacion=$row2['tsubicacion'];
					$elementosResguardados=$row2['tselementosresguardan'];
					$montoResguardado=$row2['tsmontoresguardado'];
				
					$objSensor=new Sensor();
					$objSensor->cargar($idSensor, $codigoSensor, $ubicacion, $elementosResguardados, $montoResguardado);
					array_push($arregloSensor,$objSensor);
				}
		 	}	else {
		 			self::setmensajeoperacion($base->getError());
			}
		 }	else {
		 		self::setmensajeoperacion($base->getError());
		 }	
		 return $arregloSensor;
	}	


	
	public function insertar(){
		$base=new BaseDatos();
		$resp= false;
		$consultaInsertar="INSERT INTO w_temperaturasensor(tscodigo, tsubicacion, tselementosresguardan, tsmontoresguardado) 
				VALUES (
                '".$this->getCodigoSensor()."',
                '".$this->getUbicacion()."',
                '".$this->getElementosResguardados()."',
                '".$this->getMontoResguardado()."')";
		
		if($base->Iniciar()){
			if($id = $base->devuelveIDInsercion($consultaInsertar)){
                $this->setIdSensor($id);
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
	    $resp =false; 
	    $base=new BaseDatos();
		$consultaModifica="UPDATE w_temperaturasensor SET tscodigo='".$this->getCodigoSensor()."',
        tsubicacion='".$this->getUbicacion()."',
        tselementosresguardan='".$this->getElementosResguardados()."',
        tsmontoresguardado=". $this->getMontoResguardado()." WHERE idtemperaturasensor=".$this->getIdSensor();
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
				$consultaBorra="DELETE FROM w_temperaturasensor WHERE idtemperaturasensor=".$this->getIdSensor();
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


	/**
     * retorna el importe final correspondiente a las pérdidas producidas en caso que el sensor (objSensor) falle
	 * este metodo va acá porque trabajo con comportamiento del objeto y la logica del mismo, lo que abarca la capa del modelo
	 * el metodo despues lo redefino en las clases hijas 
	 * no voy a pedir cosas x parametro xq la capa del modelo trabaja con sus propios atributos entonces creoque no es necesario
    */
    public function estimarPerdidaFallo(){
		return 0; //retorno cero porque los calculos se tienen que hacer en las redefiniciones del metodo
    }

    //redefinicion metodo __toString()
    public function __toString()
    {
        $mensaje = 
        "Id sensor: " . $this->getIdSensor() . "\n" . 
        "Codigo sensor: " . $this->getCodigoSensor() . "\n" . 
        "Ubicación: " . $this->getUbicacion() . "\n" . 
        "Elementos resguardados: " . $this->getElementosResguardados() . "\n" . 
        "Monto resguardado: $" . $this->getMontoResguardado() . "\n" ;
        return $mensaje;
    }

}