<?php
require_once __DIR__ . '/../funciones.php'; //con esto se agregarían dinamicamente todas las clases que yo llegue a necesitar acá
//////////// HAGO UN NEW DE CADA UNA DE LAS CLASES para despues no tener que llamarlas todo el tiempo////////////
$bd = new BaseDatos();
$objSensor = new ControlSensor();
$objSensorServidores = new ControlSensorServidores();
$objSensorHeladeras = new ControlSensorHeladeras();
$objAviso = new ControlAvisoTemperatura();
$objRegistro =  new ControlRegistroTemperaturas();
$objAlarma = new ControlAlarmaTemperatura();
$alarmaGeneraAviso = new ControlAlarmaGeneraAviso();

/**
 -Sensores
 -Registros
 -Alarmas
 -Avisos
 */


//////////// IMPLEMENTACIÓN DEL MENÚ
echo "****************************************************\n";
echo "**Bienvenido/a, ¿desea ingresar al menú? si-no**\n";
$rta = trim(fgets(STDIN));
while (strtolower($rta) === "si") {
    echo "***********MENÚ TEMPERATURAS**********\n";
    echo "¿Con qué elemento desea operar?\n";
    echo "a) Sensor de temperatura.\n";
    echo "b) Registros de temperatura.\n";
    echo "c) Alarma de temperaturas. \n";
    echo "d) Aviso/notificacion de temperaturas\n";
    $opcion = strtolower(trim(fgets(STDIN)));
    switch ($opcion) {
            ///////////----------------------------------------------------------------------------------------------------------/////////////
        case 'a': /////////SENSOR
            echo "Seleccione una opción para operar en SENSOR:\n";
            echo "1) Alta de un sensor.\n"; //pedido en el enunciado
            echo "2) Alta de un sensor de heladeras.\n"; //pedido en el enunciado
            echo "3) Alta de un sensor de sala de servidores.\n"; //pedido en el enunciado
            // echo "4) Baja de un sensor. \n"; //pedido en el enunciado pero acá lo saco xq por la restriccion de integridad creo q no úeden quedardatos huerfanos, lo q pasaria si borro a los padres y quedan los hijos, por eso decido solo borrar a los hijos y que quede como una suerte de registro historico en la tabla de sensor padre
            echo "5) Baja de un sensor de heladeras.\n"; //pedido en el enunciado
            echo "6) Baja de un sensor de sala de servidores.\n"; //pedido en el enunciado
            echo "7) Modificacion de un sensor.\n"; //pedido en el enunciado
            echo "8) Modificacion de un sensor de heladeras.\n"; //pedido en el enunciado
            echo "9) Modificacion de un sensor de sala de servidores.\n"; //pedido en el enunciado
            echo "10) Calcular perdidas monetarias de un sensor.\n";
            echo "11) Visualizar informacion de los sensores.\n"; //pedido en el enunciado
            echo "12) Visualizar información de un sensor por su ID.\n";
            $op = trim(fgets(STDIN));
            switch ($op) {
                case '1':
                    echo "Ingrese la siguiente información de SENSOR: codigo del sensor, ubicacion, elementos resguardados, monto resguardado.\n";
                    $codigo = trim(fgets(STDIN));
                    $ubicacion = trim(fgets(STDIN));
                    $elementos = trim(fgets(STDIN));
                    $monto = trim(fgets(STDIN));
                    $param = ['tscodigo' => $codigo, 'tsubicacion' => $ubicacion, 'tselementosresguardan' => $elementos, 'tsmontoresguardado' => $monto]; //hago un array asociativo con los datos
                    // $existeSensor = $objSensor->Buscar($datos); 
                    // if (is_array($existeSensor) && count($existeSensor) > 0) { 
                    // echo "Ese SENSOR ya existe. No pueden duplicarse los datos.\n";
                    // } else {
                    $darAlta = $objSensor->alta($param);  //hago el alta con los datos pasados por el user
                    if ($darAlta) {
                        echo "SENSOR creado con éxito.\n";
                    } else {
                        echo "Error al crear.\n";
                    }
                    // }
                    break;
                case '2':
                    echo "Ingrese la siguiente información de SENSOR HELADERAS: ID del SENSOR, marca y modelo.\n";
                    $idSensor = trim(fgets(STDIN)); //pido esto porque el sensor heladeras no puede tener un id independiente
                    $marca = trim(fgets(STDIN));
                    $modelo = trim(fgets(STDIN));
                    // $paramH = ['idtemperaturasensor' => $idSensor];
                    // $existeSensor = $objSensor->Buscar($paramH);
                    $tipo = $objSensor->detectarTipoSensor($idSensor); //esto me diria si es un obj de sala de servidores por ejemplo
                    if ($tipo instanceof Sensor_Servidores) { //o sea si es de servidores no puedo dar de alta como si fuera de heladeras
                        echo "Ese SENSOR ya existe en otro tipo.\n";
                    }else{
                        $param = ['idtemperaturasensor' => $idSensor, 'marca' => $marca, 'modelo' => $modelo]; //armo el arreglo para pasar
                        $darAlta = $objSensorHeladeras->alta($param);  //hago el alta con los datos del sensor
                        if ($darAlta) {
                            echo "SENSOR HELADERAS creado con éxito.\n";
                        } else {
                            echo "Error al crear.\n";
                        }
                    }
                    break;
                case '3':
                    echo "Ingrese la siguiente información de SENSOR SALA DE SERVIDORES: ID del SENSOR y porcentaje(en decimal) de pérdidas.\n";
                    $idSensor = trim(fgets(STDIN));
                    $perdidas = trim(fgets(STDIN));
                    $tipo = $objSensor->detectarTipoSensor($idSensor); //esto me diria si es un obj de heladeras por ejemplo
                    // $paramS = ['idtemperaturasensor' => $idSensor];
                    // $existeSensor = $objSensor->Buscar($paramS);
                    if ($tipo instanceof Sensor_Heladeras) { //o sea si ese id pertenece a heladeras 
                        echo "Ese SENSOR ya existe en otro tipo.\n";
                    } else {
                        $param = ['idtemperaturasensor' => $idSensor, 'tssporcentajeperdida' => $perdidas]; //armo el array
                        $darAlta = $objSensorServidores->alta($param);  //hago el alta con los datos
                        if ($darAlta) {
                            echo "SENSOR SALA DE SERVIDORES creado con éxito.\n";
                        } else {
                            echo "Error al crear.\n";
                        }
                    }
                    break;
                case '5':
                    echo "Ingrese el ID del SENSOR HELADERAS que desea dar de baja:\n";
                    $idSensor = trim(fgets(STDIN));
                    $paramH = ['idtemperaturasensor' => $idSensor];//armo el array con la clave y el valor
                    $existeSensor = $objSensorHeladeras->Buscar($paramH); //se lo paso a objetoHeladeras para que lo busque
                    if (is_array($existeSensor) && count($existeSensor) > 0) { //si existe ese sensor heladeras para borrar:
                        $param = ['idtemperaturasensor' => $idSensor]; //armo el array clave valor
                        $darBaja = $objSensorHeladeras->baja($param); //se lo paso a baja para que lo elimine
                        if ($darBaja) {
                            echo "SENSOR HELADERAS borrado con éxito.\n";
                        } else {
                            echo "Error al borrar.\n";
                        }
                    } else {
                        echo "Ese SENSOR HELADERAS no existe.\n";
                    }
                    break;
                case '6':
                    echo "Ingrese el ID del SENSOR SALA DE SERVIDORES que desea dar de baja:\n";
                    $idSensor = trim(fgets(STDIN));
                    $paramS = ['idtemperaturasensor' => $idSensor]; //armo el array clave valor
                    $existeSensor = $objSensorServidores->Buscar($paramS); //se lo paso a buscar
                    if (is_array($existeSensor) && count($existeSensor) > 0) { //si el existen esos datos en servidores
                        $param = ['idtemperaturasensor' => $idSensor];//armo el arreglo asociativo
                        $darBaja = $objSensorServidores->baja($param); //se lo paso a baja que usa eliminar para borrarlo
                        if ($darBaja) {
                            echo "SENSOR SALA DE SERVIDORES borrado con éxito.\n";
                        } else {
                            echo "Error al borrar.\n";
                        }
                    } else {
                        echo "Ese SENSOR SALA DE SERVIDORES no existe.\n";
                    }
                    break;
                case '7':
                    echo "Ingrese el ID del SENSOR que desea modificar:\n";
                    $idSensor = trim(fgets(STDIN));
                    $paramS = ['idtemperaturasensor' => $idSensor]; //armo el array para pasarlo como parametro en buscar
                    $existeSensor = $objSensor->Buscar($paramS); //paso el parametro para ver si ese id existe
                    if (is_array($existeSensor) && count($existeSensor) > 0) { //si existe pido datos
                        echo "Ingrese los datos nuevos: codigo del sensor, ubicacion, elementos resguardados, monto resguardado. \n";
                        $codigo = trim(fgets(STDIN));
                        $ubicacion = trim(fgets(STDIN));
                        $elementos = trim(fgets(STDIN));
                        $monto = trim(fgets(STDIN));
                        $param = ['idtemperaturasensor' => $idSensor, 'tscodigo' => $codigo, 'tsubicacion' => $ubicacion, 'tselementosresguardan' => $elementos, 'tsmontoresguardado' => $monto];
                        $modificar = $objSensor->modificacion($param);
                        if ($modificar) {
                            echo "SENSOR modificado con éxito.\n";
                        } else {
                            echo "Error al modificar.\n";
                        }
                    } else {
                        echo "Ese SENSOR no fue encontrado.\n";
                    }
                    break;
                case '8':
                    echo "Ingrese el ID del SENSOR HELADERAS que desea modificar:\n";
                    $idSensor = trim(fgets(STDIN));
                    $paramH = ['idtemperaturasensor' => $idSensor];
                    $existeSensor = $objSensorHeladeras->Buscar($paramH);
                    if (is_array($existeSensor) && count($existeSensor) > 0) {
                        echo "Ingrese los datos nuevos: marca y modelo\n";
                        $marca = trim(fgets(STDIN));
                        $modelo = trim(fgets(STDIN));
                        $param = ['idtemperaturasensor' => $idSensor, 'marca' => $marca, 'modelo' => $modelo];
                        $modificar = $objSensorHeladeras->modificacion($param);
                        if ($modificar) {
                            echo "SENSOR HELADERAS modificado con éxito.\n";
                        } else {
                            echo "Error al modificar.\n";
                        }
                    } else {
                        echo "Ese SENSOR HELADERAS no fue encontrado.\n";
                    }
                    break;
                case '9':
                    echo "Ingrese el ID del SENSOR SALA DE SERVIDORES que desea modificar:\n";
                    $idSensor = trim(fgets(STDIN));
                    $paramS = ['idtemperaturasensor' => $idSensor];
                    $existeSensor = $objSensorServidores->Buscar($paramS);
                    if (is_array($existeSensor) && count($existeSensor) > 0) {
                        echo "Ingrese los datos nuevos: porcentaje(en decimal) de pérdidas.\n";
                        $perdidas = trim(fgets(STDIN));
                        $param = ['idtemperaturasensor' => $idSensor, 'tssporcentajeperdida' => $perdidas];
                        $modificar = $objSensorServidores->modificacion($param);
                        if ($modificar) {
                            echo "SENSOR SALA DE SERVIDORES modificado con éxito.\n";
                        } else {
                            echo "Error al modificar.\n";
                        }
                    } else {
                        echo "Ese SENSOR SALA DE SERVIDORES no fue encontrado.\n";
                    }
                    break;
                case '10':
                    //acá preferí hacer un case para los tres metodos y hacer encontrol una funcion que los diferencie, se me hizo más cómodo así porque no pienso que es importante saber de qué es el sensor si ya se tiene su id
                    echo "Ingrese el ID del SENSOR cuyas perdidas quiere calcular:\n";
                    $idSensor = trim(fgets(STDIN));
                    $paramS = ['idtemperaturasensor' => $idSensor];
                    $existeSensor = $objSensor->Buscar($paramS);
                    if ($existeSensor) {
                        $perdidas = $objSensor->estimarPerdida($idSensor);
                        echo "Para el SENSOR con ID " . $idSensor . " las perdidas fueron de $" . $perdidas . "\n";
                    }
                    break;
                case '11':
                    $listado = $objSensor->mostrarInfoSensores();
                    if ($listado === null || $listado == 0) {
                        echo "No hay sensores cargados.\n";
                    } else {
                        foreach ($listado as $cadaUno) {
                            echo $cadaUno . "\n";
                        }
                    }
                    break;
                case '12':
                    echo "Ingrese el ID del SENSOR del que desea ver toda su información: \n";
                    $idSensor = trim(fgets(STDIN));
                    $info = $objSensor->mostrarInfoID($idSensor);
                    if ($info === null) {
                        echo "Ese SENSOR no existe.\n";
                    } else {
                        echo "La información del SENSOR con ID " . $idSensor . " es: \n";
                        echo $info . "\n";
                    }
                    break;
            }
            break;


            ///////////----------------------------------------------------------------------------------------------------------/////////////
        case 'b': /////////REGISTROS
            echo "Seleccione una opción para operar en REGISTROS:\n";
            echo "1) Alta de un registro.\n"; //pedido en el enunciado
            echo "2) Baja de un registro. \n"; //pedido en el enunciado
            echo "3) Modificacion de un registro.\n"; //pedido en el enunciado
            echo "4) Visualizar temperaturas de un sensor por debajo del rango.\n"; //pedido en el enunciado
            echo "5) Visualizar temperaturas de un sensor por encima del rango.\n"; //pedido en el enunciado
            echo "6) Visualizar temperatura más baja de un sensor.\n"; //pedido en el enunciado
            echo "7) Visualizar temperatura más alta de un sensor.\n"; //pedido en el enunciado
            echo "8) Mostrar registros de los sensores.\n";
            echo "9) Obtener registros de temperatura de un sensor por su ID.\n";
            $op = trim(fgets(STDIN));
            switch ($op) {
                case '1':
                    echo "Ingrese la siguiente información de REGISTRO: ID del SENSOR relacionado, temperatura, y fecha del registro.\n";
                    $idSensor = trim(fgets(STDIN));
                    $temperatura = trim(fgets(STDIN));
                    $fecha = trim(fgets(STDIN));
                    $paramS = ['idtemperaturasensor' => $idSensor];
                    $existeSensor = $objSensor->Buscar($paramS);
                    if (is_array($existeSensor) && count($existeSensor) > 0) {
                        $param = ['idtemperaturasensor' => $idSensor, 'tltemperatura' => $temperatura, 'tlfecharegistro' => $fecha];
                        $existeRegistro = $objRegistro->Buscar($param);
                        if (is_array($existeRegistro) && count($existeRegistro) > 0) {
                            echo "Ese REGISTRO ya existe. No pueden duplicarse los datos.\n";
                        } else {
                            $darAlta = $objRegistro->alta($param);  //hago el alta 
                            if ($darAlta) {
                                echo "REGISTRO creado con éxito.\n";
                            } else {
                                echo "Error al crear.\n";
                            }
                        }
                    } else {
                        echo "Ese SENSOR no existe.\n";
                    }
                    break;
                case '2':
                    echo "Ingrese el ID del REGISTRO que desea eliminar:\n";
                    $idRegistro = trim(fgets(STDIN));
                    $paramR = ['idtemperaturaregistro' => $idRegistro];
                    $existeRegistro = $objRegistro->Buscar($paramR);
                    if (is_array($existeRegistro) && count($existeRegistro) > 0) {
                        $darBaja = $objRegistro->baja(['idtemperaturaregistro' => $idRegistro]);
                        if ($darBaja) {
                            echo "REGISTRO eliminado con éxito.\n";
                        } else {
                            echo "Error al eliminar.\n";
                        }
                    } else {
                        echo "Ese REGISTRO no fue encontrado.\n";
                    }
                    break;
                case '3':
                    echo "Ingrese el ID del REGISTRO que desea modificar:\n";
                    $idRegistro = trim(fgets(STDIN));
                    $paramR = ['idtemperaturaregistro' => $idRegistro];
                    $existeRegistro = $objRegistro->Buscar($paramR);
                    if (is_array($existeRegistro) && count($existeRegistro) > 0) {
                        echo "Ingrese los datos nuevos: ID del SENSOR relacionado, temperatura y fecha del registro.\n";
                        $idSensor = trim(fgets(STDIN)); //esto se debería modificar?
                        $temperatura = trim(fgets(STDIN));
                        $fecha = trim(fgets(STDIN));
                        $param = ['idtemperaturaregistro' => $idRegistro, 'idtemperaturasensor' => $idSensor, 'tltemperatura' => $temperatura, 'tlfecharegistro' => $fecha];
                        $modificar = $objRegistro->modificacion($param);
                        if ($modificar) {
                            echo "REGISTRO modificado con éxito.\n";
                        } else {
                            echo "Error al modificar.\n";
                        }
                    } else {
                        echo "Ese REGISTRO no fue encontrado.\n";
                    }
                    break;
                case '4':
                    echo "Ingrese el ID de un SENSOR para visualizar todos sus registros de temperatura que están por debajo del rango permitido:\n";
                    $idSensor = trim(fgets(STDIN));
                    $paramS = ['idtemperaturasensor' => $idSensor];
                    $existeSensor = $objSensor->Buscar($paramS);
                    if (is_array($existeSensor) && count($existeSensor) > 0) {
                        $arrayTemperaturas = $objRegistro->registrosPorDebajo($idSensor);
                        if (count($arrayTemperaturas) > 0) {
                            echo "Para ese SENSOR el registro de temperaturas por debajo del registro es: \n";
                            foreach ($arrayTemperaturas as $temperatura) {
                                echo "> " . $temperatura . "° \n";
                            }
                        } else {
                            echo "No se encontraron tales temperaturas.\n";
                        }
                    } else {
                        echo "Ese SENSOR no existe.\n";
                    }
                    break;
                case '5':
                    echo "Ingrese el ID de un SENSOR para visualizar todos sus registros de temperatura que están por encima del rango permitido:\n";
                    $idSensor = trim(fgets(STDIN));
                    $paramS = ['idtemperaturasensor' => $idSensor];
                    $existeSensor = $objSensor->Buscar($paramS);
                    if (is_array($existeSensor) && count($existeSensor) > 0) {
                        $arrayTemperaturas = $objRegistro->registrosPorEncima($idSensor);
                        if (count($arrayTemperaturas) > 0) {
                            echo "Para ese SENSOR el registro de temperaturas por encima del registro es: \n";
                            foreach ($arrayTemperaturas as $temperatura) {
                                echo "> " . $temperatura . "° \n";
                            }
                        } else {
                            echo "No se encontraron tales temperaturas.\n";
                        }
                    } else {
                        echo "Ese SENSOR no existe.\n";
                    }
                    break;
                case '6':
                    echo "Ingrese el ID de un SENSOR para visualizar su temperatura más baja: \n";
                    $idSensor = trim(fgets(STDIN));
                    $paramS = ['idtemperaturasensor' => $idSensor];
                    $existeSensor = $objSensor->Buscar($paramS);
                    if (is_array($existeSensor) && count($existeSensor) > 0) {
                        $temperatura = $objRegistro->temperaturaMenor($idSensor);
                        if ($temperatura !== null) {
                            echo "La temperatura más baja registrada para el SENSOR con ID " . $idSensor . " es: " . $temperatura . "°. \n";
                        } else {
                            echo "No se encontró la temperatura.\n";
                        }
                    } else {
                        echo "Ese SENSOR no existe.\n";
                    }
                    break;
                case '7':
                    echo "Ingrese el ID de un SENSOR para visualizar su temperatura más alta: \n";
                    $idSensor = trim(fgets(STDIN));
                    $paramS = ['idtemperaturasensor' => $idSensor];
                    $existeSensor = $objSensor->Buscar($paramS);
                    if (is_array($existeSensor) && count($existeSensor) > 0) {
                        $temperatura = $objRegistro->temperaturaMayor($idSensor);
                        if ($temperatura !== null) {
                            echo "La temperatura más alta registrada para el SENSOR con ID " . $idSensor . " es: " . $temperatura . "°. \n";
                        } else {
                            echo "No se encontró la temperatura.\n";
                        }
                    } else {
                        echo "Ese SENSOR no existe.\n";
                    }
                    break;
                case '8':
                    $listado = $objRegistro->mostrarInfoRegistros();
                    if ($listado === null || $listado == 0) {
                        echo "No hay REGISTROS cargados.\n";
                    } else {
                        foreach ($listado as $cadaUno) {
                            echo "---------------------------\n";
                            echo $cadaUno . "\n";
                        }
                    }
                    break;
                case '9':
                    echo "Ingrese el ID del REGISTRO del que desea ver toda su información: \n";
                    $idRegistro = trim(fgets(STDIN));
                    $paramR = ['idtemperaturaregistro' => $idRegistro];
                    $info = $objRegistro->Buscar($paramR);
                    if (is_array($info) && count($info) > 0) {
                        foreach ($info as $datos) {
                            echo $datos . "\n";
                        }
                    } else {
                        echo "Ese REGISTRO no se encontró.\n";
                    }
                    break;
            }
            break;

            ///////////----------------------------------------------------------------------------------------------------------/////////////
        case 'c': /////////ALARMA
            echo "Seleccione una opción para operar en ALARMA:\n";
            echo "1) Alta de una alarma.\n"; //pedido en el enunciado
            echo "2) Baja de una alarma. \n"; //pedido en el enunciado
            echo "3) Modificacion de una alarma.\n"; //pedido en el enunciado
            echo "4) Vincular una alarma para generar un aviso.\n";
            echo "5) Desvincular una alarma que generó un aviso.\n";
            echo "6) Modificar la vinculación de una alarma que generó un aviso.\n";
            echo "7) Visualizar alarmas activas de un SENSOR por su ID.\n"; //o sea acá pido el id del sensor
            echo "8) Visualizar todas las alarmas.\n";
            echo "9) Visualizar una ALARMA por su ID.\n"; //acá pido el id de la alarma
            echo "10) Visualizar todas las alarmas que generaron avisos.\n";
            echo "11) Visualizar una alarma que genera aviso por su ID.\n";
            $op = trim(fgets(STDIN));
            switch ($op) {
                case '1':
                    echo "Ingrese la siguiente información de ALARMA: ID del SENSOR relacionado, rango de temperatura superior, rango de temperatura inferior, fecha inicio de la alarma, fecha fin de la alarma.\n";
                    $idSensor = trim(fgets(STDIN));
                    $superior = trim(fgets(STDIN));
                    $inferior = trim(fgets(STDIN));
                    $fechaInicio = trim(fgets(STDIN));
                    $fechaFin = trim(fgets(STDIN));
                    $paramS = ['idtemperaturasensor' => $idSensor];
                    $existeSensor = $objSensor->Buscar($paramS);
                    if (is_array($existeSensor) && count($existeSensor) > 0) {
                        $param = ['idtemperaturasensor' => $idSensor, 'tasuperior' => $superior, 'tainferior' => $inferior, 'tafechainicio' => $fechaInicio, 'tafechafin' => $fechaFin];
                        $existeAlarma = $objAlarma->Buscar($param);
                        if (is_array($existeAlarma) && count($existeAlarma) > 0) {
                            echo "Esa ALARMA ya existe. No pueden duplicarse los datos.\n";
                        } else {
                            $darAlta = $objAlarma->alta($param);  //hago el alta del aviso
                            if ($darAlta) {
                                echo "ALARMA creada con éxito.\n";
                            } else {
                                echo "Error al crear.\n";
                            }
                        }
                    } else {
                        echo "Ese SENSOR no existe.\n";
                    }
                    break;
                case '2':
                    echo "Ingrese el ID de la ALARMA que desea eliminar:\n";
                    $idAlarma = trim(fgets(STDIN));
                    $paramA = ['idtemperaturaalarma' => $idAlarma];
                    $existeAlarma = $objAlarma->Buscar($paramA);
                    if (is_array($existeAlarma) && count($existeAlarma) > 0) {
                        $darBaja = $objAlarma->baja(['idtemperaturaalarma' => $idAlarma]);
                        if ($darBaja) {
                            echo "ALARMA eliminada con éxito.\n";
                        } else {
                            echo "Error al eliminar. La ALARMA puede tener vinculaciones con AVISO o surgió otro problema.\n";
                        }
                    } else {
                        echo "Esa ALARMA no fue encontrada.\n";
                    }
                    break;
                case '3':
                    echo "Ingrese el ID de la ALARMA que desea modificar:\n";
                    $idAlarma = trim(fgets(STDIN));
                    $paramA = ['idtemperaturaalarma' => $idAlarma];
                    $existeAlarma = $objAlarma->Buscar($paramA);
                    if (is_array($existeAlarma) && count($existeAlarma) > 0) {
                        echo "Ingrese los datos nuevos: ID del SENSOR relacionado, rango de temperatura superior, rango de temperatura inferior, fecha inicio de la alarma, fecha fin de la alarma.\n";
                        $idSensor = trim(fgets(STDIN)); //esto se debería modificar?
                        $superior = trim(fgets(STDIN));
                        $inferior = trim(fgets(STDIN));
                        $fechaInicio = trim(fgets(STDIN));
                        $fechaFin = trim(fgets(STDIN));
                        $param = ['idtemperaturaalarma' => $idAlarma, 'idtemperaturasensor' => $idSensor, 'tasuperior' => $superior, 'tainferior' => $inferior, 'tafechainicio' => $fechaInicio, 'tafechafin' => $fechaFin];
                        $modificar = $objAlarma->modificacion($param);
                        if ($modificar) {
                            echo "ALARMA modificada con éxito.\n";
                        } else {
                            echo "Error al modificar.\n";
                        }
                    } else {
                        echo "Esa ALARMA no fue encontrada.\n";
                    }
                    break;
                case '4':
                    echo "Ingrese el ID del AVISO e ID de la ALARMA que quiere vincular:\n";
                    $idAviso =  trim(fgets(STDIN));
                    $idAlarma = trim(fgets(STDIN));
                    $paramAl = ['idtemperaturaalarma' => $idAlarma];
                    $paramAv = ['idtemperaturaaviso' => $idAviso];
                    $existeAviso = $objAviso->Buscar($paramAv);
                    $existeAlarma = $objAlarma->Buscar($paramAl);
                    if ((is_array($existeAlarma) && count($existeAlarma) > 0) && (is_array($existeAviso) && count($existeAviso) > 0)) {
                        $param = ['idtemperaturaaviso' => $idAviso, 'idtemperaturaalarma' => $idAlarma];
                        $darAlta = $alarmaGeneraAviso->alta($param);
                        if ($darAlta) {
                            echo "ALARMA generó AVISO con éxito.\n";
                        } else {
                            echo "Error al crear.\n";
                        }
                    } else {
                        echo "ALARMA y/o AVISO no fueron encontrados.\n";
                    }
                    break;

                case '5':
                    echo "Ingrese el ID de cuya relacion ALARMA-AVISO quiera desvincular:\n";
                    $idAA = trim(fgets(STDIN));
                    $paramA = ['idavisoalarma' => $idAA];
                    $existeAA = $alarmaGeneraAviso->Buscar($paramA);
                    if (is_array($existeAA) && count($existeAA) > 0) {
                        $darBaja = $alarmaGeneraAviso->baja(['idavisoalarma' => $idAA]);
                        if ($darBaja) {
                            echo "RELACION eliminada con éxito.\n";
                        } else {
                            echo "Error al eliminar.\n";
                        }
                    } else {
                        echo "Esa RELACION no fue encontrada.\n";
                    }
                    break;

                case '6':
                    echo "Ingrese el ID de la relación que vincula ALARMA-AVISO que desea modificar:\n";
                    $idAA = trim(fgets(STDIN));
                    $paramAA = ['idavisoalarma' => $idAA];
                    $existeAA = $alarmaGeneraAviso->Buscar($paramAA);
                    if (is_array($existeAA) && count($existeAA) > 0) {
                        echo "Ingrese los datos nuevos: ID del AVISO relacionado, e ID de la ALARMA relacionada.\n";
                        $idAviso = trim(fgets(STDIN));
                        $idAlarma = trim(fgets(STDIN));
                        $paramAl = ['idtemperaturaalarma' => $idAlarma];
                        $paramAv = ['idtemperaturaaviso' => $idAviso];
                        $existeAviso = $objAviso->Buscar($paramAv);
                        $existeAlarma = $objAlarma->Buscar($paramAl);
                        if ((is_array($existeAlarma) && count($existeAlarma) > 0) && (is_array($existeAviso) && count($existeAviso) > 0)) {
                            $param = ['idavisoalarma' => $idAA, 'idtemperaturaaviso' => $idAviso, 'idtemperaturaalarma' => $idAlarma];
                            $modificar = $alarmaGeneraAviso->modificacion($param);
                            if ($modificar) {
                                echo "RELACIÓN modificada con éxito.\n";
                            } else {
                                echo "Error al modificar.\n";
                            }
                        }
                    } else {
                        echo "Esa RELACIÓN no fue encontrada.\n";
                    }
                    break;

                case '7':
                    echo "Ingrese el ID del SENSOR cuyas alarmas activas desea visualizar:\n";
                    $idSensor = trim(fgets(STDIN));
                    $paramS = ['idtemperaturasensor' => $idSensor];
                    $existeSensor = $objSensor->Buscar($paramS);
                    if (is_array($existeSensor) && count($existeSensor) > 0) {
                        $listadoActivas = $objAlarma->alarmaActiva($idSensor);
                        if (is_array($listadoActivas) && count($listadoActivas) > 0) {
                            echo "El SENSOR con ID " . $idSensor . " tiene las siguientes alarmas activas:\n";
                            foreach ($listadoActivas as $activa) {
                                echo $activa . "\n";
                            }
                        } else {
                            echo "No hay ALARMAS activas para ese SENSOR.\n";
                        }
                    } else {
                        echo "Ese SENSOR no fue encontrado.\n";
                    }
                    break;

                case '8':
                    $listado = $objAlarma->mostrarInfoAlarmas();
                    if ($listado === null || $listado == 0) {
                        echo "No hay ALARMAS cargadas.\n";
                    } else {
                        foreach ($listado as $cadaUno) {
                            echo "---------------------------\n";
                            echo $cadaUno . "\n";
                        }
                    }
                    break;
                case '9':
                    echo "Ingrese el ID de la ALARMA de la que desea ver toda su información: \n";
                    $idAlarma = trim(fgets(STDIN));
                    $paramR = ['idtemperaturaalarma' => $idAlarma];
                    $info = $objAlarma->Buscar($paramR);
                    if (is_array($info) && count($info) > 0) {
                        foreach ($info as $datos) {
                            echo $datos . "\n";
                        }
                    } else {
                        echo "Esa ALARMA no se encontró.\n";
                    }
                    break;
                case '10':
                    $listado = $alarmaGeneraAviso->mostrarInfoAA();
                    if ($listado === null || $listado == 0) {
                        echo "No hay ALARMAS que generan AVISOS cargadas.\n";
                    } else {
                        foreach ($listado as $cadaUno) {
                            echo "---------------------------\n";
                            echo $cadaUno . "\n";
                        }
                    }
                    break;
                case '11':
                    echo "Ingrese el ID de la RELACION ALARMA-AVISO del que desea ver toda su información: \n";
                    $idAA = trim(fgets(STDIN));
                    $paramAA = ['idavisoalarma' => $idAA];
                    $info = $alarmaGeneraAviso->Buscar($paramAA);
                    if (is_array($info) && count($info) > 0) {
                        foreach ($info as $datos) {
                            echo $datos . "\n";
                        }
                    } else {
                        echo "Esa RELACIÓN no se encontró.\n";
                    }
                    break;
            }
            break;

            ///////////----------------------------------------------------------------------------------------------------------/////////////
        case 'd': /////////AVISO
            echo "Seleccione una opción para operar en AVISO: \n";
            echo "1) Alta de un aviso.\n"; //pedido en el enunciado
            echo "2) Baja de un aviso. \n"; //pedido en el enunciado
            echo "3) Modificacion de un aviso.\n"; //pedido en el enunciado
            echo "4) Visualizar la información de todos los avisos.\n";
            echo "5) Visualizar un aviso por su ID.\n";
            $op = trim(fgets(STDIN));
            switch ($op) {
                case '1':
                    echo "Ingrese la siguiente información de AVISO: fecha de activacion, nombre responsable, email responsable.\n";
                    $activo = trim(fgets(STDIN));
                    $nombre = trim(fgets(STDIN));
                    $email = trim(fgets(STDIN));
                    $param = ['taactivo' => $activo, 'tanombre' => $nombre, 'taemail' => $email];
                    $existeAviso = $objAviso->Buscar($param); //para fijarme si existe el aviso
                    if (is_array($existeAviso) && count($existeAviso) > 0) {
                        echo "Ese AVISO ya existe. No pueden duplicarse los datos.\n";
                    } else {
                        $darAlta = $objAviso->alta($param);  //hago el alta del aviso
                        if ($darAlta) {
                            echo "AVISO creado con éxito.\n";
                        } else {
                            echo "Error al crear.\n";
                        }
                    }
                    break;
                case '2':
                    echo "Ingrese el ID del AVISO que desea eliminar:\n";
                    $idAviso = trim(fgets(STDIN));
                    $paramA = ['idtemperaturaaviso' => $idAviso];
                    $existeAviso = $objAviso->Buscar($paramA);
                    if (is_array($existeAviso) && count($existeAviso) > 0) {
                        $darBaja = $objAviso->baja(['idtemperaturaaviso' => $idAviso]);
                        if ($darBaja) {
                            echo "AVISO eliminado con éxito.\n";
                        } else {
                            echo "Error al eliminar. El AVISO puede tener vinculaciones con ALARMA o se generó otro problema.\n";
                        }
                    } else {
                        echo "Ese AVISO no fue encontrado.\n";
                    }
                    break;

                case '3':
                    echo "Ingrese el ID del AVISO que desea modificar:\n";
                    $idAviso = trim(fgets(STDIN));
                    $paramA = ['idtemperaturaaviso' => $idAviso];
                    $existeAviso = $objAviso->Buscar($paramA);
                    if (is_array($existeAviso) && count($existeAviso) > 0) {
                        echo "Ingrese los datos nuevos: fecha de activacion, nombre responsable, email responsable.\n";
                        $activo = trim(fgets(STDIN));
                        $nombre = trim(fgets(STDIN));
                        $email = trim(fgets(STDIN));
                        $param = ['idtemperaturaaviso' => $idAviso, 'taactivo' => $activo, 'tanombre' => $nombre, 'taemail' => $email];
                        $modificar = $objAviso->modificacion($param);
                        if ($modificar) {
                            echo "AVISO modificado con éxito.\n";
                        } else {
                            echo "Error al modificar.\n";
                        }
                    } else {
                        echo "Ese AVISO no fue encontrado.\n";
                    }
                    break;
                case '4':
                    $listado = $objAviso->mostrarInfoAvisos();
                    if ($listado === null || $listado == 0) {
                        echo "No hay AVISOS cargados.\n";
                    } else {
                        foreach ($listado as $cadaUno) {
                            echo "---------------------------\n";
                            echo $cadaUno . "\n";
                        }
                    }
                    break;
                case '5':
                    echo "Ingrese el ID del AVISO del que desea ver toda su información: \n";
                    $idAviso = trim(fgets(STDIN));
                    $paramR = ['idtemperaturaaviso' => $idAviso];
                    $info = $objAviso->Buscar($paramR);
                    if (is_array($info) && count($info) > 0) {
                        foreach ($info as $datos) {
                            echo $datos . "\n";
                        }
                    } else {
                        echo "Ese AVISO no se encontró.\n";
                    }
                    break;
            }
            break;

        default:
            echo "Opción inválida.\n";
            break;
    }
    echo "¿Desea ingresar al menú principal? si-no\n";
    $rta = trim(fgets(STDIN));
}
echo "****Ha salido del menú.****\n";
