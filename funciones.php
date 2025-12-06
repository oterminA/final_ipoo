<?php
define('ROOT', __DIR__ . '/'); //defino la ruta raiz del trabajo que esería tpfinal_ipoo, DIR es el directorio actual

//////AUTOLOAD: esta funcion es para incluir 'dinamicamente' los objetos del control y del modelo, en lugar de hacer include_once todo el tiempo
spl_autoload_register(function ($className) {
    $directorios = [ //las sgtes son las rutas donde buscar las clases
        ROOT . 'Model/', //por ejemplo Sensor.php, Registro_Temperaturas.php, etc
        ROOT . 'Model/Conector/', //acá está el conector de la bd
        ROOT . 'Control/', //ejemplo: controlSensor.php, controlAviso.php, etc
    ];

    foreach ($directorios as $directorio) {
        $archivo = $directorio . $className . '.php';
        if (file_exists($archivo)) {
            require_once $archivo;
            return;
        }
    }

    error_log("Autoload: Clase '$className' no encontrada."); //msj por si falla la bd
});
