#!/bin/bash

# Este script automatiza la creacion de la base de datos, el usuario y la tabla,
# y tambien configura el correo electronico en el archivo PHP.

# Cargar las variables del archivo de credenciales
if [ -f "usuario-contraseña" ]; then
    source "usuario-contraseña"
else
    echo "Error: El archivo 'usuario-contraseña' no existe. Por favor, creelo con sus credenciales."
    exit 1
fi

# Mensaje para el usuario
echo "Este script creara la base de datos 'EEST-GEMINI' y el usuario '$DB_USER'."
echo "Tambien configurara el correo de recepcion en el archivo 'enviar_correo.php' con la direccion '$RECIPIENT_EMAIL'."
read -p "¿Deseas continuar? (s/n): " -n 1 -r
echo
if [[ ! $REPLY =~ ^[Ss]$ ]]
then
    echo "Operacion cancelada. Saliendo."
    exit 1
fi

# Crea un archivo temporal con los comandos SQL
SQL_FILE=$(mktemp)
cat <<EOF > $SQL_FILE
CREATE DATABASE \`EEST-GEMINI\`;
USE \`EEST-GEMINI\`;
CREATE USER '$DB_USER'@'localhost' IDENTIFIED BY '$DB_PASS';
GRANT ALL PRIVILEGES ON \`EEST-GEMINI\`.* TO '$DB_USER'@'localhost';
CREATE TABLE mensajes (
    id INT AUTO_INCREMENT PRIMARY KEY,
    nombre VARCHAR(255) NOT NULL,
    email VARCHAR(255) NOT NULL,
    asunto VARCHAR(255),
    mensaje TEXT NOT NULL,
    fecha_envio TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);
FLUSH PRIVILEGES;
EOF

# Ejecuta los comandos SQL
echo "Iniciando sesion en MySQL para ejecutar los comandos..."
sudo mysql -u root -p < $SQL_FILE

# Remueve el archivo temporal
rm $SQL_FILE

# --- Seccion para configurar el correo del destinatario ---
echo "Configurando correo de recepcion en enviar_correo.php..."
# El siguiente comando reemplaza el correo en la linea 109 del archivo PHP
sed -i "s|mail->addAddress('experimentoiautn2@gmail.com', 'Experimento UTN');|mail->addAddress('$RECIPIENT_EMAIL', 'Experimento UTN');|" enviar_correo.php

echo "Configuracion de la base de datos y correo completada."
echo "Puedes iniciar sesion en MySQL y verificar la creacion de la base de datos."
