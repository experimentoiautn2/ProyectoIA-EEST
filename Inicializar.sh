#!/bin/bash

# --- Mensaje inicial y confirmación ---
echo "Este script instalará Apache, MySQL, PHP y configurará tu proyecto."
read -p "¿Deseas continuar? (s/n): " -n 1 -r
echo
if [[ ! $REPLY =~ ^[Ss]$ ]]
then
    echo "Operación cancelada. Saliendo."
    exit 1
fi

# --- Parte 1: Instalación de los Paquetes ---
echo "--- Parte 1: Instalando paquetes del sistema ---"

# 1. Actualizar el sistema
echo "Actualizando el sistema..."
sudo apt update
sudo apt upgrade -y

# 2. Instalar Apache2
echo "Instalando Apache2..."
sudo apt install apache2 -y

# 3. Instalar MySQL Server 8.0
echo "Instalando MySQL Server 8.0..."
echo "Configurando repositorio de MySQL..."
wget https://dev.mysql.com/get/mysql-apt-config_0.8.34-1_all.deb
sudo dpkg -i mysql-apt-config_0.8.34-1_all.deb
sudo apt update
sudo apt install mysql-server -y

# 4. Instalar PHP y módulos necesarios
echo "Instalando PHP y módulos necesarios..."
sudo apt install php libapache2-mod-php php-mysql php-mbstring php-gd php-curl php-json php-xml -y

# 5. Reiniciar Apache
echo "Reiniciando el servicio de Apache..."
sudo systemctl restart apache2

echo "--- Parte 1 completada. Componentes instalados. ---"

# --- Parte 2: Despliegue de la Aplicación (asume que el repositorio ya está clonado) ---
echo "--- Parte 2: Configurando la aplicación ---"

read -p "Introduce el nombre exacto de la carpeta del proyecto (ej: ProyectoIA-EEST): " project_folder

# 1. Copiar la carpeta del proyecto
echo "Copiando la carpeta del proyecto a /var/www/html/..."
sudo cp -r "$project_folder" "/var/www/html/$project_folder"

# 2. Asignar los permisos correctos
echo "Asignando permisos..."
sudo chown -R www-data:www-data "/var/www/html/$project_folder"
sudo chmod -R 755 "/var/www/html/$project_folder"

# 3. Reiniciar Apache para aplicar los permisos
echo "Reiniciando Apache para aplicar los permisos..."
sudo systemctl restart apache2

echo "--- Script de inicialización completado. ---"
echo "Ahora, puedes configurar la base de datos y la aplicación manualmente."
echo "Sigue los pasos de la Parte 2 y 4 de tu instructivo."
