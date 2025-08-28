#!/bin/bash

# Este script automatiza la creacion de la base de datos, el usuario y la tabla.

# Mensaje para el usuario
echo "Este script creara la base de datos 'EEST-GEMINI' y el usuario 'EEST-GEM'."
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
CREATE USER 'EEST-GEM'@'localhost' IDENTIFIED BY 'UnaContraFuerte123!';
GRANT ALL PRIVILEGES ON \`EEST-GEMINI\`.* TO 'EEST-GEM'@'localhost';
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

echo "Configuracion de la base de datos completada."
echo "Puedes iniciar sesion en MySQL y verificar la creacion de la base de datos."
