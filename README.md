-INSTRUCTIVO

Instrucciones para la Configuración del Servidor y Prueba del Formulario

Este documento detalla los pasos necesarios para configurar un servidor Ubuntu 18.04.6 LTS y ejecutar la aplicación de formulario de contacto.
Requisitos del Sistema

Sistema Operativo: Ubuntu 18.04.6 LTS

Servidor Web: Apache 2.4.29

Base de Datos: MySQL 8.42 o superior

Parte 1: Verificación e Instalación de los Paquetes del Sistema
 Antes de proceder, verifique si los paquetes necesarios ya están instalados.
 Verificación de Paquetes Instalados:
 Para verificar la versión de Apache, use: apache2 -v
 Para verificar la versión de MySQL, use: mysql -V
 Para verificar la versión de PHP, use: php -v
 
 Si los paquetes no están instalados, continúe con los siguientes pasos para instalarlos. Si ya están instalados, omita esta sección y pase a la Parte 2.
 Actualizar el sistema:
 sudo apt update
 sudo apt upgrade -y


 Instalar Apache2:
 sudo apt install apache2 -y


 Instalar MySQL Server 8.0:
 Descargue el paquete de configuración del repositorio de MySQL:
 wget https://dev.mysql.com/get/mysql-apt-config_0.8.34-1_all.deb


 Instale el repositorio:
 sudo dpkg -i mysql-apt-config_0.8.34-1_all.deb


 Nota: Durante la instalación interactiva, asegúrese de seleccionar mysql-8.0 cuando se le pida la versión del servidor.
 Actualice el sistema de paquetes para que reconozca el nuevo repositorio:
 sudo apt update


 Instale MySQL Server:
 sudo apt install mysql-server -y


 Nota: Durante la instalación, establezca una contraseña segura para el usuario root de MySQL y seleccione Use Strong Password Encryption para el método de autenticación.
 Instalar PHP y los módulos necesarios:
 sudo apt install php libapache2-mod-php php-mysql php-mbstring php-gd php-curl php-json php-xml -y


 Reiniciar el servicio de Apache:
 sudo systemctl restart apache2


 Parte 2: Configuración de la Base de Datos
 Abra la terminal e inicie sesión en MySQL como usuario root con la contraseña que estableció.
 Iniciar Sesión en MySQL:
 sudo mysql -u root -p


 Crear la base de datos, el usuario y la tabla necesarios:
 Ejecute las siguientes sentencias SQL dentro del cliente de MySQL, una por una.
 CREATE DATABASE base_de_datos;
 USE base_de_datos;
 CREATE USER 'lsi'@'localhost' IDENTIFIED BY 'UnaContraFuerte123!';
 GRANT ALL PRIVILEGES ON base_de_datos.* TO 'lsi'@'localhost';
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


 Nota: La contraseña para el usuario lsi debe coincidir con la que está configurada en el archivo enviar_correo.php del proyecto.
 Parte 3: Despliegue de la Aplicación
 Copie la carpeta pcc del proyecto al directorio raíz del servidor web de Apache. La ubicación predeterminada es /var/www/html/.
 sudo cp -r /ruta/del/proyecto/pcc /var/www/html/


 Asigne los permisos correctos para que el servidor web (www-data) pueda acceder y ejecutar los archivos:
 sudo chown -R www-data:www-data /var/www/html/pcc
 sudo chmod -R 755 /var/www/html/pcc


 Modificar el archivo enviar_correo.php:
 Abra el archivo enviar_correo.php para cambiar el correo del destinatario.
 Vaya a la línea 109 del código.
 Reemplace 'experimentoiautn2@gmail.com' con el correo electrónico del destinatario final.
 // Destinatario del correo (A quién se le enviará el mensaje).
 $mail->addAddress('experimentoiautn2@gmail.com', 'Experimento UTN'); // La línea que debe cambiar.


 Importante: La línea 106 ($mail->setFrom(...)) debe tener el mismo correo que la línea 99 ($mail->Username = ...) para que el envío funcione correctamente con Gmail.
 Reinicie el servicio de Apache2 para aplicar los cambios:
 sudo systemctl restart apache2


 Parte 4: Verificación del Funcionamiento
 Abra un navegador web en el servidor y acceda a la siguiente URL:
 http://localhost/pcc/contacto.html
 Rellene el formulario de contacto con datos de prueba y haga clic en "Enviar".
 Verifique la funcionalidad:
 Correo Electrónico: Se debe enviar un correo electrónico con la información del formulario.
 Base de Datos: Se debe insertar un nuevo registro en la tabla mensajes de la base de datos base_de_datos. Puede verificar esto iniciando sesión en MySQL y ejecutando la siguiente   consulta:
 SELECT * FROM base_de_datos.mensajes;


 Si todos estos pasos se completan con éxito, el proyecto cumplirá con los requisitos de la consigna.
