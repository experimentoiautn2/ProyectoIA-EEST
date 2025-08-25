INSTRUCTIVOI
nstrucciones para la Configuración del Servidor y Prueba del Formulario


Este documento detalla los pasos necesarios para configurar un servidor Ubuntu 18.04.6 LTS y ejecutar la aplicación de formulario de contacto.

Requisitos del Sistema

Sistema Operativo: Ubuntu 18.04.6 LTS

Servidor Web: Apache 2.4.29

Base de Datos: MySQL 8.42 o superior

Parte 1:

Instalación Automatizada de los Paquetes del SistemaPara simplificar el proceso, se ha proporcionado un script de inicialización llamado inicializar.sh que automatiza la instalación de todos los paquetes necesarios.

Clonar el repositorio de GitHub en su máquina local.

Por ejemplo:

    git clone https://github.com/usuario/ProyectoIA-EEST.git
Ejecutar el script de inicialización:Abra una terminal y navegue a la carpeta del repositorio.

Convierta el script en ejecutable:

    chmod +x inicializar.sh
    
Ejecute el script:./inicializar.sh

El script instalará Apache, MySQL, PHP y sus módulos. También moverá los archivos de tu proyecto a la carpeta del servidor web (/var/www/html/).

Parte 2: 

Configuración de la Base de Datos

Abra la terminal e inicie sesión en MySQL como usuario root con la contraseña que estableció durante la instalación.

Iniciar Sesión en MySQL:

    sudo mysql -u root -p
Crear la base de datos, el usuario y la tabla necesarios:

Ejecute las siguientes sentencias SQL dentro del cliente de MySQL, una por una.

    CREATE DATABASE `EEST-GEMINI`;
    
    USE `EEST-GEMINI`;
    
    CREATE USER 'EEST-GEM'@'localhost' IDENTIFIED BY 'UnaContraFuerte123!';
    
    GRANT ALL PRIVILEGES ON `EEST-GEMINI`.* TO 'EEST-GEM'@'localhost';
    
    CREATE TABLE mensajes (
     
       id INT AUTO_INCREMENT PRIMARY KEY,
       nombre VARCHAR(255) NOT NULL,
       email VARCHAR(255) NOT NULL,
       asunto VARCHAR(255),
       mensaje TEXT NOT NULL,
       fecha_envio TIMESTAMP DEFAULT CURRENT_TIMESTAMP
    );
    FLUSH PRIVILEGES;
    EXIT;
Nota: La contraseña para el usuario EEST-GEM debe coincidir con la que está configurada en el archivo enviar_correo.php del proyecto.

Parte 3:

Despliegue y Configuración de la AplicaciónEl script de inicialización ya ha copiado la carpeta del proyecto a la ubicación del servidor web y ha asignado los permisos correctos.

Modificar el archivo enviar_correo.php:

Abra el archivo enviar_correo.php para cambiar el correo del destinatario.

La ruta del archivo es: /var/www/html/ProyectoIA-EEST/enviar_correo.php

Vaya a la línea 109 del código.Reemplace 'experimentoiautn2@gmail.com' con el correo electrónico del destinatario final.// 

Destinatario del correo (A quién se le enviará el mensaje).

$mail->addAddress('experimentoiautn2@gmail.com', 'Experimento UTN'); // La línea que debe cambiar.

Importante:
La línea 106 ($mail->setFrom(...)) debe tener el mismo correo que la línea 99 ($mail->Username = ...) para que el envío funcione correctamente con Gmail.Reinicie el servicio de Apache2 para aplicar los cambios:

    sudo systemctl restart apache2
Parte 4:
Verificación del FuncionamientoAbra un navegador web en el servidor y acceda a la siguiente URL:

http://localhost/ProyectoIA-EEST/contacto.html

Rellene el formulario de contacto con datos de prueba y haga clic en "Enviar".Verifique la funcionalidad:

Correo Electrónico: 

Se debe enviar un correo electrónico con la información del formulario.

Base de Datos: Se debe insertar un nuevo registro en la tabla mensajes de la base de datos. 

Puede verificar esto iniciando sesión en MySQL y ejecutando la siguiente consulta:

    SELECT * FROM `EEST-GEMINI`.mensajes;
    
Si todos estos pasos se completan con éxito, el proyecto cumplirá con los requisitos de la consigna.
