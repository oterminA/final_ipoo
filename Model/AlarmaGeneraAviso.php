<?php
//delegacion
//tabla que muestra la relacion entre Aviso y Alamarma
class AlarmaGeneraAviso{
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
    {
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

	
    public function Buscar($id){
		$base=new BaseDatos();
		$consultaAlarma="Select * from w_temperaturasensortemperaturaaviso where idavisoalarma=". $id;
		$resp= false;
		if($base->Iniciar()){
			if($base->Ejecutar($consultaAlarma)){
				if($row2=$base->Registro()){

                    $objAviso = new Aviso_Temperaturas();
                    $objAviso->Buscar($row2['idtemperaturaaviso']);

                    $objAlarma = new Alarma_Temperaturas();
                    $objAlarma->Buscar($row2['idtemperaturaalarma']);

				    $this->setId($id);
					$this->setObjAviso($objAviso);
					$this->setObjAlarma($objAlarma);
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
	    $arreglo = null;
		$base=new BaseDatos();
		$consulta="Select * from w_temperaturasensortemperaturaaviso ";
		if ($condicion!=""){
		    $consulta=$consulta.' where '.$condicion;
		}
		// $consulta.=" order by idtemperaturaalarma ";
		if($base->Iniciar()){
			if($base->Ejecutar($consulta)){				
				$arreglo= array();
				while($row2=$base->Registro()){
					$objAviso = new Aviso_Temperaturas();
                    $objAviso->Buscar($row2['idtemperaturaaviso']);

                    $objAlarma = new Alarma_Temperaturas();
                    $objAlarma->Buscar($row2['idtemperaturaalarma']);
				
					$obj=new AlarmaGeneraAviso();
					$obj->cargar($row2['idavisoalarma'], $objAviso, $objAlarma);
					array_push($arreglo,$obj);
				}
		 	}	else {
		 			self::setmensajeoperacion($base->getError());
			}
		 }	else {
		 		self::setmensajeoperacion($base->getError());
		 }	
		 return $arreglo;
	}	


	
	public function insertar(){
		$base=new BaseDatos();
		$resp= false;
		$objAlarma = $this->getObjAlarma();
		$objAviso = $this->getObjAviso();
		if ($objAlarma === null || !method_exists($objAlarma, 'getIdAlarma')){
            $this->setmensajeoperacion($base->getError());
            $resp = false;
        }
		if ($objAviso === null || !method_exists($objAviso, 'getIdAviso')){
            $this->setmensajeoperacion($base->getError());
            $resp = false;
        }
		$idAviso = $objAviso->getIdAviso();
		$idAlarma = $objAlarma->getIdAlarma();
		$consultaInsertar="INSERT INTO w_temperaturasensortemperaturaaviso(idtemperaturaaviso, idtemperaturaalarma) 
				VALUES (
                '".$idAviso."',
                '".$idAlarma."')";
		
		if($base->Iniciar()){
			if($id = $base->devuelveIDInsercion($consultaInsertar)){
                $this->setId($id);
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
		$idAviso =$this->getObjAviso()->getIdAviso();
		$idAlarma = $this->getObjAlarma()->getIdAlarma();
		$consultaModifica="UPDATE w_temperaturasensortemperaturaaviso SET idtemperaturaaviso='".$idAviso."',
        idtemperaturaalarma". $idAlarma." 
		WHERE idavisoalarma=".$this->getId();
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
				$consultaBorra="DELETE FROM w_temperaturasensortemperaturaaviso WHERE idavisoalarma=".$this->getId();
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
        "Id: " . $this->getId() . "\n" . 
        "Objeto aviso----\n " . $this->getObjAviso() . "\n" . 
        "Objeto alarma----\n " . $this->getObjAlarma() . "\n" ;
        return $mensaje;
    }

}