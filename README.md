INSTRUCTIVO

Instrucciones para la Configuración del Servidor y Prueba del Formulario

Este documento detalla los pasos necesarios para configurar un servidor Ubuntu 18.04.6 LTS y ejecutar la aplicación de formulario de contacto.

Requisitos del Sistema

Sistema Operativo: Ubuntu 18.04.6 LTS

Servidor Web: Apache 2.4.29

Base de Datos: MySQL 8.42 o superior

Parte 1: 

Instalación Automatizada de los Paquetes del Sistema

Para simplificar el proceso, se ha proporcionado un script de inicialización llamado inicializar.sh que automatiza la instalación de todos los paquetes necesarios.

Clonar el repositorio de GitHub en su máquina local.

Por ejemplo:

            git clone https://github.com/usuario/ProyectoIA-EEST.git
Ejecutar el script de inicialización:

Abra una terminal y navegue a la carpeta del repositorio. Convierta el script en ejecutable:

    chmod +x inicializar.sh
    
Ejecute el script:./inicializar.sh
El script instalará Apache, MySQL, PHP y sus módulos. También moverá los archivos de tu proyecto a la carpeta del servidor web (/var/www/html/).Parte 2: Configuración de la Base de DatosVerificar el archivo de credenciales:

El archivo usuario-contraseña ya se encuentra en el repositorio.Si desea cambiar las credenciales predeterminadas, edite el archivo usuario-contraseña con sus propios valores.

     DB_USER="EEST-GEM"
     DB_PASS="UnaContraFuerte123!"
Nota: El script de configuración de la base de datos leerá estos valores automáticamente.
Crear la base de datos, el usuario y la tabla necesarios:
En su repositorio, ejecute el script mysql-setup.sh para crear automáticamente la base de datos y la tabla.Convierta el script en ejecutable:

    chmod +x mysql-setup.sh
Ejecute el script:

    ./mysql-setup.sh
Cuando se le solicite, ingrese la contraseña de root de MySQL que estableció durante la instalación del servidor.

Nota: El script de configuración de la base de datos leerá estos valores automáticamente.

    $mail->addAddress('experimentoiautn2@gmail.com', 'Experimento UTN'); // La línea que debe cambiar.
Importante: La línea 106 ($mail->setFrom(...)) debe tener el mismo correo que la línea 99 ($mail->Username = ...) para que el envío funcione correctamente con Gmail.
Reinicie el servicio de Apache2 para aplicar los cambios:

    sudo systemctl restart apache2
Parte 4: 
Verificación del Funcionamiento
Abra un navegador web en el servidor y acceda a la siguiente URL:
http://localhost/ProyectoIA-EEST/contacto.html
Rellene el formulario de contacto con datos de prueba y haga clic en "Enviar".
Verifique la funcionalidad:
Correo Electrónico: Se debe enviar un correo electrónico con la información del formulario.
Base de Datos: Se debe insertar un nuevo registro en la tabla mensajes de la base de datos. Puede verificar esto iniciando sesión en MySQL y ejecutando la siguiente consulta:

    SELECT * FROM `EEST-GEMINI`.mensajes;
Si todos estos pasos se completan con éxito, el proyecto cumplirá con los requisitos de la consigna.
