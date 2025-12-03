<?php
require_once __DIR__ . '/../funciones.php'; //con esto se agregarían dinamicamente todas las clases que yo llegue a necesitar acá
//////////// HAGO UN NEW DE CADA UNA DE LAS CLASES ////////////
$bd = new BaseDatos();
$objSensor = new ControlSensor();
$objSensorServidores = new ControlSensorServidores();
$objSensorHeladeras = new ControlSensorHeladeras();
$objAviso = new ControlAvisoTemperatura();
$objRegistro =  new ControlRegistroTemperaturas();
$objAlarma = new ControlAlarmaTemperatura();
$alarmaGeneraAviso = new ControlAlarmaGeneraAviso();



//////////// IMPLEMENTACIÓN DEL MENÚ
echo "***********************************************\n";
echo "**Bienvenido, ¿desea ingresar al menú? si-no**\n";
$rta = trim(fgets(STDIN));
while (strtolower($rta) === "si") {
    echo "***********MENÚ SENSOR**********\n";
    echo "¿Con qué elemento desea operar?\n";
    echo "a) Sensor de temperatura.\n";
    echo "b) Registros de temperatura.\n";
    echo "c) Alarma de temperaturas. \n";
    echo "d) Aviso/notificacion de temperaturas\n";
    $opcion = strtolower(trim(fgets(STDIN)));
    switch ($opcion) {
        case 'a': /////////SENSOR
            echo "Seleccione una opción para operar en SENSOR:\n";
            echo "1) Alta de un sensor.\n"; //pedido en el enunciado
            echo "2) Alta de un sensor de heladeras.\n"; //pedido en el enunciado
            echo "3) Alta de un sensor de sala de servidores.\n"; //pedido en el enunciado
            // echo "4) Baja de un sensor. \n"; //pedido en el enunciado pero acá lo obvio xq por la restriccion de integridad creo q no úeden quedardatos huerfanos, lo q pasaria si borro a los padres y quedan los hijos
            echo "5) Baja de un sensor de heladeras.\n"; //pedido en el enunciado
            echo "6) Baja de un sensor de sala de servidores.\n"; //pedido en el enunciado
            echo "7) Modificacion de un sensor.\n"; //pedido en el enunciado
            echo "8) Modificacion de un sensor de heladeras.\n"; //pedido en el enunciado
            echo "9) Modificacion de un sensor de sala de servidores.\n"; //pedido en el enunciado
            echo "10) Visualizar informacion de los sensores.\n"; //pedido en el enunciado
            echo "11) Visualizar información de un sensor por su ID.\n";
            $op = trim(fgets(STDIN));
            switch ($op) {
                case '1':
                    echo "Ingrese la siguiente información de SENSOR: codigo del sensor, ubicacion, elementos resguardados, monto resguardado.\n";
                    $codigo = trim(fgets(STDIN));
                    $ubicacion = trim(fgets(STDIN));
                    $elementos = trim(fgets(STDIN));
                    $monto = trim(fgets(STDIN));
                    $param = ['tscodigo' => $codigo, 'tsubicacion' => $ubicacion, 'tselementosresguardan' => $elementos, 'tsmontoresguardado' => $monto];
                    $existeSensor = $objSensor->Buscar($param);
                    if (is_array($existeSensor) && count($existeSensor) > 0) {
                        echo "Ese SENSOR ya existe. No pueden duplicarse los datos.\n";
                    } else {
                        $darAlta = $objSensor->alta($param);  //hago el alta 
                        if ($darAlta) {
                            echo "SENSOR creado con éxito.\n";
                        } else {
                            echo "Error al crear.\n";
                        }
                    }
                    break;
                case '2':
                    echo "Ingrese la siguiente información de SENSOR HELADERAS: ID del SENSOR, marca y modelo.\n";
                    $idSensor = trim(fgets(STDIN));
                    $marca = trim(fgets(STDIN));
                    $modelo = trim(fgets(STDIN));
                    $param = ['idtemperaturasensor' => $idSensor, 'marca' => $marca, 'modelo' => $modelo];
                    $existeSensor = $objSensor->Buscar($idSensor);
                    if (is_array($existeSensor) && count($existeSensor) > 0) {
                        $darAlta = $objSensorHeladeras->alta($param);  //hago el alta 
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
                    $param = ['idtemperaturasensor' => $idSensor, 'tssporcentajeperdida' => $perdidas];
                    $existeSensor = $objSensor->Buscar($idSensor);
                    if (is_array($existeSensor) && count($existeSensor) > 0) {
                        $darAlta = $objSensorServidores->alta($param);  //hago el alta 
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
                    $existeSensor = $objSensorHeladeras->Buscar($idSensor);
                    if (is_array($existeSensor) && count($existeSensor) > 0) {
                        $param = ['idtemperaturasensor' => $idSensor, null, null];
                        $darBaja = $objSensorHeladeras->baja($param);
                        if ($darBaja) {
                            echo "SENSOR HELADERAS borrado con éxito.\n";
                        } else {
                            echo "Error al borrar.\n";
                        }
                    }
                    break;
                case '6':
                    echo "Ingrese el ID del SENSOR SALA DE SERVIDORES que desea dar de baja:\n";
                    $idSensor = trim(fgets(STDIN));
                    $existeSensor = $objSensorServidores->Buscar($idSensor);
                    if (is_array($existeSensor) && count($existeSensor) > 0) {
                        $param = ['idtemperaturasensor' => $idSensor, null];
                        $darBaja = $objSensorServidores->baja($param);
                        if ($darBaja) {
                            echo "SENSOR SALA DE SERVIDORES borrado con éxito.\n";
                        } else {
                            echo "Error al borrar.\n";
                        }
                    }
                    break;
                case '7':
                    echo "Ingrese el ID del SENSOR que desea modificar:\n";
                    $idSensor = trim(fgets(STDIN));
                    $existeSensor = $objSensor->Buscar($idSensor);
                    if (is_array($existeSensor) && count($existeSensor) > 0) {
                        echo "Ingrese los datos nuevos:\n";
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
                    $existeSensor = $objSensorHeladeras->Buscar($idSensor);
                    if (is_array($existeSensor) && count($existeSensor) > 0) {
                        echo "Ingrese los datos nuevos:\n";
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
                    $existeSensor = $objSensorServidores->Buscar($idSensor);
                    if (is_array($existeSensor) && count($existeSensor) > 0) {
                        echo "Ingrese los datos nuevos:\n";
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
            }
            break;

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
            break;

        case 'c': /////////ALARMA
            echo "Seleccione una opción para operar en ALARMA:\n";
            echo "1) Alta de una alarma.\n"; //pedido en el enunciado
            echo "2) Baja de una alarma. \n"; //pedido en el enunciado
            echo "3) Modificacion de una alarma.\n"; //pedido en el enunciado
            echo "4) Visualizar alarmas activas de un sensor.\n";
            $op = trim(fgets(STDIN));
            switch ($op) {
                case '1':
                    echo "Ingrese la siguiente información de ALARMA: ID del SENSOR relacionado, rango de temperatura superior, rango de temperatura inferior, fecha inicio de la alarma, fecha fin de la alarma.\n";
                    $idSensor = trim(fgets(STDIN));
                    $superior = trim(fgets(STDIN));
                    $inferior = trim(fgets(STDIN));
                    $fechaInicio = trim(fgets(STDIN));
                    $fechaFin = trim(fgets(STDIN));
                    $existeSensor = $objSensor->Buscar($idSensor);
                    if (is_array($existeSensor) && count($existeSensor) > 0) {
                        $param = ['idtemperaturasensor' => $objSensor, 'tasuperior' => $superior, 'tainferior' => $inferior, 'tafechainicio' => $fechaInicio, 'tafechafin' => $fechaFin];
                        $existeAlarma = $objAlarma->Buscar($param); //HELP, deberia revisar que el sensor existe? o que no estácon otra alarma?
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
                    }else{
                        echo "Ese SENSOR no existe.\n";
                    }
                    break;
                case '2':
                    echo "Ingrese el ID de la ALARMA que desea eliminar:\n";
                    $idAlarma = trim(fgets(STDIN));
                    $existeAlarma = $objAlarma->Buscar($idAlarma);
                    if (is_array($existeAlarma) && count($existeAlarma) > 0) {
                        $darBaja = $objAlarma->baja(['idtemperaturaalarma' => $idAlarma]);
                        if ($darBaja) {
                            echo "ALARMA eliminada con éxito.\n";
                        } else {
                            echo "Error al eliminar.\n";
                        }
                    } else {
                        echo "Esa ALARMA no fue encontrada.\n";
                    }
                    break;
                case '3':
                    echo "Ingrese el ID de la ALARMA que desea modificar:\n";
                    $idAlarma = trim(fgets(STDIN));
                    $existeAlarma = $objAlarma->Buscar($idAlarma);
                    if (is_array($existeAlarma) && count($existeAlarma) > 0) {
                        echo "Ingrese los datos nuevos:\n";
                        $idSensor = trim(fgets(STDIN));
                        $superior = trim(fgets(STDIN));
                        $inferior = trim(fgets(STDIN));
                        $fechaInicio = trim(fgets(STDIN));
                        $fechaFin = trim(fgets(STDIN));
                        $param = ['idtemperaturasensor' => $idSensor, 'tasuperior' => $superior, 'tainferior' => $inferior, 'tafechainicio' => $fechaInicio, 'tafechafin' => $fechaFin];
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
            }
            break;

        case 'd': /////////AVISO
            echo "Seleccione una opción para operar en AVISO: \n";
            echo "1) Alta de un aviso.\n"; //pedido en el enunciado
            echo "2) Baja de un aviso. \n"; //pedido en el enunciado
            echo "3) Modificacion de un aviso.\n"; //pedido en el enunciado
            echo "4) Visualizar avisos de una alarma.\n"; //HELP. revisar si es necesario ver los avisos de una alarma
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
                    $existeAviso = $objAviso->Buscar($idAviso);
                    if (is_array($existeAviso) && count($existeAviso) > 0) {
                        $darBaja = $objAviso->baja(['idtemperaturaaviso' => $idAviso]);
                        if ($darBaja) {
                            echo "AVISO eliminado con éxito.\n";
                        } else {
                            echo "Error al eliminar.\n";
                        }
                    } else {
                        echo "Ese AVISO no fue encontrado.\n";
                    }
                    break;

                case '3':
                    echo "Ingrese el ID del AVISO que desea modificar:\n";
                    $idAviso = trim(fgets(STDIN));
                    $existeAviso = $objAviso->Buscar($idAviso);
                    if (is_array($existeAviso) && count($existeAviso) > 0) {
                        echo "Ingrese los datos nuevos:\n";
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
            }
            break;

        default:
            echo "Opción inválida.\n";
            break;
    }
    echo "¿Desea ingresar al menú principal? si-no\n";
    $rta = trim(fgets(STDIN));
}
echo "**Ha salido del menú.**\n";
