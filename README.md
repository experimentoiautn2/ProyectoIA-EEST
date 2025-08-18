# ProyectoIA-EEST
#INSTRUCTIVO
#Instrucciones para la Configuración del Servidor y Prueba del Formulario
#Este documento detalla los pasos necesarios para configurar un servidor Ubuntu 18.04.6 LTS y ejecutar la aplicación de formulario de #contacto.
#Requisitos del Sistema
#•	Sistema Operativo: Ubuntu 18.04.6 LTS [cite: 1000401091.jpg]
#•	Servidor Web: Apache 2.4.29 [cite: 1000401091.jpg]
#•	Base de Datos: MySQL 8.42 o superior [cite: 1000401091.jpg]
#Parte 1: Instalación de los Paquetes del Sistema
#Abra la terminal de Ubuntu y ejecute los siguientes comandos.
#1.	Actualizar el sistema:
#2.	sudo apt update
#3.	sudo apt upgrade -y

#4.	Instalar Apache2:
#5.	sudo apt install apache2 -y

#6.	Instalar MySQL Server 8.0:
#o	Descargue el paquete de configuración del repositorio de MySQL:
#o	wget https://dev.mysql.com/get/mysql-apt-config_0.8.34-1_all.deb

#o	Instale el repositorio:
#o	sudo dpkg -i mysql-apt-config_0.8.34-1_all.deb

#	Nota: Durante la instalación interactiva, asegúrese de seleccionar mysql-8.0 cuando se le pida la versión del servidor [cite: #1000401091.jpg].
#o	Actualice el sistema de paquetes para que reconozca el nuevo repositorio:
#o	sudo apt update

#o	Instale MySQL Server:
#o	sudo apt install mysql-server -y

#	Nota: Durante la instalación, establezca una contraseña segura para el usuario root de MySQL y seleccione Use Strong Password #Encryption para el método de autenticación.
#7.	Instalar PHP y los módulos necesarios:
#o	Se han añadido módulos de PHP faltantes que son requeridos para tu código.
#8.	sudo apt install php libapache2-mod-php php-mysql php-mbstring php-gd php-curl php-json php-xml -y

#9.	Reiniciar el servicio de Apache:
#10.	sudo systemctl restart apache2

#Parte 2: Configuración de la Base de Datos
#Abra la terminal e inicie sesión en MySQL como usuario root con la contraseña que estableció.
#1.	Iniciar Sesión en MySQL:
#2.	sudo mysql -u root -p

#3.	Crear la base de datos, el usuario y la tabla necesarios:
#o	Ejecute las siguientes sentencias SQL dentro del cliente de MySQL, una por una.
#4.	CREATE DATABASE base_de_datos;
#5.	USE base_de_datos;
#6.	CREATE USER 'lsi'@'localhost' IDENTIFIED BY 'UnaContraFuerte123!';
#7.	GRANT ALL PRIVILEGES ON base_de_datos.* TO 'lsi'@'localhost';
#8.	CREATE TABLE mensajes (
#9.	    id INT AUTO_INCREMENT PRIMARY KEY,
#10.	    nombre VARCHAR(255) NOT NULL,
#11.	    email VARCHAR(255) NOT NULL,
#12.	    asunto VARCHAR(255),
#13.	    mensaje TEXT NOT NULL,
#14.	    fecha_envio TIMESTAMP DEFAULT CURRENT_TIMESTAMP
#15.	);
#16.	FLUSH PRIVILEGES;
#17.	EXIT;

#o	Nota: La contraseña para el usuario “nombre de usuario” debe coincidir con la que está configurada en el archivo enviar_correo.php del #proyecto.
Parte 3: Despliegue de la Aplicación
#1.	Copie la carpeta pcc del proyecto al directorio raíz del servidor web de Apache. La ubicación predeterminada es /var/www/html/.
#2.	sudo cp -r /ruta/del/proyecto/pcc /var/www/html/

#3.	Asigne los permisos correctos para que el servidor web (www-data) pueda acceder y ejecutar los archivos:
#4.	sudo chown -R www-data:www-data /var/www/html/pcc
#5.	sudo chmod -R 755 /var/www/html/pcc

#6.	Modificar el archivo enviar_correo.php:
#o	Abra el archivo enviar_correo.php para cambiar el correo del destinatario.
#o	Vaya a la línea 109 del código.
#o	Reemplace 'experimentoiautn2@gmail.com' con el correo electrónico del destinatario final.
#7.	// Destinatario del correo (A quién se le enviará el mensaje).
#8.	$mail->addAddress('experimentoiautn2@gmail.com', 'Experimento UTN'); // La línea que debe cambiar.

#o	Importante: La línea 106 ($mail->setFrom(...)) debe tener el mismo correo que la línea 99 ($mail->Username = ...) para que el envío #funcione correctamente con Gmail.
#9.	Reinicie el servicio de Apache2 para aplicar los cambios:
#10.	sudo systemctl restart apache2

#Parte 4: Verificación del Funcionamiento
#1.	Abra un navegador web en el servidor y acceda a la siguiente URL: http://localhost/pcc/contacto.html
#2.	Rellene el formulario de contacto con datos de prueba y haga clic en "Enviar".
#3.	Verifique la funcionalidad:
#o	Correo Electrónico: Se debe enviar un correo electrónico con la información del formulario.
#o	Base de Datos: Se debe insertar un nuevo registro en la tabla mensajes de la base de datos base_de_datos. Puede verificar esto #iniciando sesión en MySQL y ejecutando la siguiente consulta:
#o	SELECT * FROM base_de_datos.mensajes;

#o	Si todos estos pasos se completan con éxito, el proyecto cumplirá con los requisitos de la consigna.
