<?php
class BaseDatos {
    //atributos, informacion xra conectarme a la bd
    private $HOSTNAME;
    private $BASEDATOS;
    private $USUARIO;
    private $CLAVE;
    private $CONEXION;
    private $QUERY;
    private $RESULT;
    private $ERROR;

    /**
     * Constructor de la clase que inicia ls variables instancias de la clase vinculadas a la conexion con el Servidor de BD
     */
    public function __construct(){
        $this->HOSTNAME = "127.0.0.1";
        $this->BASEDATOS = "bd_sensor";
        $this->USUARIO = "root";
        $this->CLAVE="";
        $this->RESULT=0;
        $this->QUERY="";
        $this->ERROR="";

    }
    /**
     * Funcion que retorna una cadena con una peque�a descripcion del error si lo hubiera
     * @return string
     */
    public function getError(){
        return "\n".$this->ERROR;
        
    }
    
    /**
     * Inicia la coneccion con el Servidor y la  Base Datos Mysql.
     * Retorna true si la coneccion con el servidor se pudo establecer y false en caso contrario
     * @return boolean
     */
    public  function Iniciar(){
        $resp  = false;
        $conexion = mysqli_connect($this->HOSTNAME,$this->USUARIO,$this->CLAVE,$this->BASEDATOS);
        if ($conexion){
            if (mysqli_select_db($conexion,$this->BASEDATOS)){
                $this->CONEXION = $conexion; //si se hacer la conexion se guarda acá
                unset($this->QUERY);
                unset($this->ERROR);
                $resp = true;
            }  else {
                $this->ERROR = mysqli_errno($conexion) . ": " .mysqli_error($conexion); //si no se pudo conectar se guarda acá
            }
        }else{
            $this->ERROR =  mysqli_errno($conexion) . ": " .mysqli_error($conexion);
        }
        return $resp; //devuelve true o false si se pudo o no
    }
    
    /**
     * Recibe una consulta sql como cadena(como un select x ej) y la ejecuta
     * @return boolean
     */
    public function Ejecutar($consulta){
        $resp  = false;
        unset($this->ERROR);
        $this->QUERY = $consulta; //guarda el resultado acá creo
        if(  $this->RESULT = mysqli_query( $this->CONEXION,$consulta)){ 
            $resp = true;
        } else {
            $this->ERROR =mysqli_errno( $this->CONEXION).": ". mysqli_error( $this->CONEXION);
        }
        return $resp; //devuelve true o false si se pudo o no
    }
    
    /**
     * Devuelve el sgte resultado de la consulta ejecutada en EJECUTAR
     * @return array|null
     */
    public function Registro() {
        $resp = null; //no deberia ser un array vacio?
        if ($this->RESULT){
            unset($this->ERROR);
            if($temp = mysqli_fetch_assoc($this->RESULT)){ // mysqli_fetch_assoc devuelve una fila como arreglo asociativo
                $resp = $temp;
            }else{
                mysqli_free_result($this->RESULT);
            }
        }else{
            $this->ERROR = mysqli_errno($this->CONEXION) . ": " . mysqli_error($this->CONEXION);
        }
        return $resp ; //se devuelve la fila(arreglo) o null
    }
    
    /**
     * ejecuta una consulta del tipo INSERT INTO, si es exitosa se devuelve el ID incrementado x la base de datos
     * @return int id de la tupla insertada
     */
    public function devuelveIDInsercion($consulta){
        $resp = false; //debería ser un numero ya que con eso se trabaja??
    
        if ($this->Ejecutar($consulta)) {
            $resp = mysqli_insert_id($this->CONEXION); //mysqli_insert_id usado xra devolver el id incrementado
        }
    
        return $resp; //devuelve el id incrementado o false
    }
    
}
