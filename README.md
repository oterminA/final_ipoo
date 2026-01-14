Sistema de Control de Temperatura - Final IPOO
Este repositorio contiene el trabajo práctico final para la asignatura Introducción a la Programación Orientada a Objetos (2025). El proyecto consiste en un sistema diseñado para gestionar y monitorear mediciones de temperatura a través de diferentes tipos de sensores. 

>Estructura del Proyecto
El sistema sigue una arquitectura organizada en capas:

/Model: Contiene las clases que representan las entidades del sistema (Sensores, Mediciones, etc.) y la lógica de interacción con la base de datos.

/Control: Incluye la lógica de negocio que coordina las operaciones entre la interfaz y el modelo.

/Interface: Contiene los scripts encargados de la interacción con el usuario.

IPOO - ControlTemperatura.pdf: Documentación con los requerimientos y el diseño del sistema.

mer_bdsensor.jpg: Diagrama de Entidad-Relación que detalla la estructura de la base de datos.

funciones.php: Scripts auxiliares y funciones generales del sistema.

Funcionalidades Principales
Gestión de Sensores: Registro y administración de diferentes dispositivos sensores.

Registro de Mediciones: Almacenamiento histórico de los valores de temperatura capturados.

Persistencia de Datos: El sistema está diseñado para conectarse a una base de datos (según el MER incluido) para mantener la información de forma permanente.

Lógica POO: Implementación de herencia y encapsulamiento para manejar distintos comportamientos de sensores.

>Tecnologías
Lenguaje: PHP (100%)

Base de Datos: SQL (según el diagrama de modelo relacional incluido).

>Interfaz de Usuario
El proyecto no posee una interfaz gráfica web; se visualiza y gestiona mediante un **menú interactivo por consola**. 
* **Entorno recomendado:** Terminal integrada de Visual Studio Code.
* **Interacción:** El usuario selecciona las opciones del menú ingresando los valores correspondientes por teclado.
<hr> <br>
Este proyecto fue desarrollado como examen final para demostrar conocimientos en arquitectura de capas y modelado de objetos para la materia 'Introducción a la Programación Orientada a Objetos' de la Tecnicatura Universitaria en Desarrollo Web (año 2025).
