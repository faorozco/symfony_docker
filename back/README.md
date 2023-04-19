Guía de instalación gDocument

Requerimientos Mínimos Actuales. 
(Actualmente se está alojando en DigitalOcean.com)

Servidor con 2 Gigas de RAM
25 Gigas de Disco Duro SSD
1 vCPU


Software a instalar en el servidor
Sistema operativo  Ubuntu Server 18.04 LTS o superior
Servidor Web Apache (Apache/2.4.29) o superior
Base de datos MySQL (Ver 14.14 Distrib 5.7.24, for Linux (x86_64)) o superior
Lenguaje de programación PHP 7.4 o superior
Certificado SSL Let’s Encrypt
https://www.digitalocean.com/community/tutorials/how-to-secure-apache-with-let-s-encrypt-on-ubuntu-18-04


Pasos de instalación

Configurar servidor virtual en Digital Ocean.

Ingresar a Digital Ocean y crear un nuevo droplet.


Configurar subdominio con nueva instalación.


Crear cuenta en Google Drive para el nuevo cliente.
Ejemplo. 
Usuario: gdocument.demostracion@gmail.com
Password: %gdocument$


Ingresar a https://console.cloud.google.com/. Crear un proyecto para acceder al almacenamiento de la cuenta creada (gdocument.demostracion@gmail.com)


Se le da el nombre de "gDocument Storage" y se le agrega la API de Google Drive


Luego se le crean credenciales de acceso
Da clic en "credenciales"  luego en "+ Crear Credenciales" y se escoge "ID de clientes OAuth" Si solicita crear pantalla de autorización diligenciamos  los datos.


Luego se crea la credencial de tipo oAuth2 de tipo "Aplicación de Escritorio" y se descarga la llave de autenticación con Google Drive (llave en formato JSON) al computador local.


Se procede a clonar el repositorio del backend de la aplicación.
git clone https://github.com/ingcarlosperez/backgdocument.git back


Se ajustan permisos del backend del proyecto
Siendo admglobal el usuario administrador
sudo chown -R admglobal:www-data back
Sobra? sudo chown -R webmaster:www-data back

Instalar composer
Sudo apt install composer      //con éste comando se instala versión desactualizada

Según la página del proyecto https://getcomposer.org/download/
se debe ejecutar lo siguiente para instalar la última versión:

php -r "copy('https://getcomposer.org/installer', 'composer-setup.php');"

sudo php composer-setup.php --install-dir=/usr/bin/ --filename=composer


Se realiza la instalación de dependencias del proyecto.
Dentro de back
composer install

sudo apt install acl

https://symfony.com/doc/current/setup/file_permissions.html

HTTPDUSER=$(ps axo user,comm | grep -E '[a]pache|[h]ttpd|[_]www|[w]ww-data|[n]ginx' | grep -v root | head -1 | cut -d\  -f1)

sudo setfacl -dR -m u:"$HTTPDUSER":rwX -m u:$(whoami):rwX var

sudo setfacl -R -m u:"$HTTPDUSER":rwX -m u:$(whoami):rwX var


Para revisar si hay dependencias desactualizadas
composer outdate


Actualizar dependencias
composer update


Se prueba la instalación ejecutando el comando: bin/console about
Debe arrojar algo como: 
-------------------- ----------------------------------------------
  Symfony
 -------------------- ----------------------------------------------
  Version          	5.1.2
  Long-Term Support	No
  End of maintenance   01/2021
  End of life      	01/2021
 -------------------- ----------------------------------------------
  Kernel
 -------------------- ----------------------------------------------
  Type             	App\Kernel
  Environment      	dev
  Debug            	true
  Charset          	UTF-8
  Cache directory  	./var/cache/dev (43.8 MiB)
  Log directory    	./var/log (187.8 MiB)
 -------------------- ----------------------------------------------
  PHP
 -------------------- ----------------------------------------------
  Version          	7.4.8
  Architecture     	64 bits
  Intl locale      	n/a
  Timezone         	America/New_York (2020-07-25T08:17:12-04:00)
  OPcache          	true
  APCu             	false
  Xdebug           	false
 -------------------- ----------------------------------------------

Se crea el archivo client_secret.json ejecutando el comando:
nano config/gdrivecredentials/client_secret.json 
Abrir el archivo descargado de Google Drive y se copia el contenido y se pega en el archivo client_secret.json


Se ejecuta el comando: bin/console app:create-credentials-gdrive
Sale un mensaje como.
Open the following link in your browser:
https://accounts.google.com/o/oauth2/auth?response_type=code&access_type=offline&client_id=904013710322-p2ufav226cl8odvlisueuptq2mu8gsqf.apps.googleusercontent.com&redirect_uri=urn%3Aietf%3Awg%3Aoauth%3A2.0%3Aoob&state&scope=https%3A%2F%2Fwww.googleapis.com%2Fauth%2Fdrive&approval_prompt=auto


Abrir esa ruta en un navegador Web y se otorgan permisos a la cuenta de google sobre la aplicación
Al final arroja un código que debe pegarse en la línea de comandos.
Esto crea un archivo config/gdrivecredentials/credenciales/credentials.json que permite la comunicación entre gDocument y Google Drive


Se crea el archivo ".env.local" donde se van a parametrizar los datos básicos de la instalación. 


Se debe crear base de datos MySQL

mysql -u webmaster -p    //preguntará la contraseña
 
mysql>create database gdocument_test;  //crear la base de datos
mysql>quit;

Para crear el archivo se ejecuta el comando en la raiz del proyecto back: nano .env.local


Se agrega lo siguiente:

DATABASE_URL=mysql://usuario:clave@servidor:3306/base_datos
DATABASE_URL=mysql://webmaster:1234@localhost:3306/gdocument_test

usuario: Usuario Base de Datos.
clave: Clave asignada al usuario de la Base de Datos.
servidor: Dirección IP del servidor de Base de Datos
base_datos: Nombre de la Base de Datos


Se crean las llaves jwt para comunica el backend con el frontend
Se crea el siguiente directorio mkdir -p config/jwt
Se crea la llave privada
openssl genpkey -out config/jwt/private.pem -aes256 -algorithm rsa -pkeyopt rsa_keygen_bits:4096
Se le coloca un clave a la llave
Se crea la llave publica
openssl pkey -in config/jwt/private.pem -out config/jwt/public.pem -pubout
Se edita el archivo .env y se coloca la clave de la llave jwt en el parámetro JWT_PASSPHRASE


Luego agregamos la ruta del tipo de letra usada para crear los stickers impresos en  el archivo ".env.local"
GDFONTPATH=/home/webmaster/www/gdocument_demostracion/back/config/fonts/Roboto/Roboto-Regular.ttf

Creamos las rutas temporales usadas
mkdir tmp
mkdir public/tmp


Vamos a la cuenta de Google Drive creada para la instalación y creamos las siguientes carpetas:

archivos
imagenes
imagenes\empresa
imagenes\usuarios

Copiamos los identificadores que nos muestra Google Drive para estas carpetas.
archivos
imagenes\empresa
imagenes\usuarios


Seguimos editando el archivo .env.local
Agregamos los siguientes parámetros para montar la estructura documental de la empresa


##CANT. DIGITOS ESTRUCTURADOCUMENTAL
FONDO=0
SECCION=2
SUBSECCION=2
SERIE=2
SUBSERIE=2

Agregamos el logo de la empresa a la carpeta public\images con el nombre logoempresa.png

Se copia el identificador google de la imagen y se agrega en el archivo env.local donde corresponde y en la base de datos en la tabla empresa, campo imagen de forma manual


Generamos la estructura de la Base de Datos con los comandos
bin/console make:migration
bin/console doctrine:migrations:migrate
Escribimos (yes) y damos enter

Ejecutamos los datos básicos incluidos en el archivo SQLInstall.sql
Para vaciar el dump SQLInstall.sql se ejecuta el siguiente comando:

 mysql -u webmaster -p --init-command="SET SESSION FOREIGN_KEY_CHECKS=0;" isaludco_isalud < SQLInstall.sql


Creamos un host virtual en el servidor para darle acceso al nuevo subdominio Ejemplo: demostracion.gdocument.co y demostracionback.gdocument.co
Frontend
<VirtualHost *:80>
    ServerName demostracion.gdocument.co
    DocumentRoot /varwww/gdocument_demostracion/front/dist
<Directory /home/webmaster/www/gdocument_demostracion/front/dist>    
AllowOverride None
Order Allow,Deny
Allow from All
FallbackResource /index.html
</Directory>
ErrorLog /var/log/apache2/demostracion.gdocument.co_error.log
CustomLog /var/log/apache2/demostracion.gdocument.co_access.log combined
</VirtualHost>


Backend
	<VirtualHost *:80>
ServerName demostracionback.gdocument.co
DocumentRoot /home/webmaster/www/gdocument_demostracion/back/public
<Directory /home/webmaster/www/gdocument_demostracion/back/public>
AllowOverride None
Order Allow,Deny
Allow from All
FallbackResource /index.php
</Directory>
<Directory /home/webmaster/www/gdocument_demostracion/back/public/bundles>
FallbackResource disabled
</Directory>
ErrorLog /var/log/apache2/demostracionback.gdocument.co_error.log
CustomLog /var/log/apache2/demostracionback.gdocument.co_access.log combined
SetEnvIf Authorization "(.*)" HTTP_AUTHORIZATION=$1
Header set Access-Control-Allow-Origin "*"
</VirtualHost>



Se habilitan los dos dominios nuevos:
Sudo a2enmod headers
Systemctl restart apache2
Sudo a2ensite gdocument_vmdesarrollo_back.conf
sudo a2ensite  gdocument_demostracion.conf
sudo a2ensite  gdocument_demostracion_back.conf
sudo chmod -R 775 back
sudo chown -R admglobal:www-data back

Se ejecuta el comando para que el servidor de aplicaciones tome los dos nuevos subdominios:
sudo systemctl reload apache2 


Se activa el certificado SSL para los dos dominios nuevos. Esto se está firmando en este momento con una entidad certficadora llamada Let's Encrypt. Es gratuito.
Se ejecuta el comando sudo certbot --apache

Sale un listado de los subdominios que pueden ser asegurados. 
Ejemplo: demostracion.gdocument.co y demostracionback.gdocument.co
Se da el número del primero, se ejecuta el comando nuevamente y se da el segundo.

Which names would you like to activate HTTPS for?
- - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - -
1: demostracion.gdocument.co
2: demostracionback.gdocument.co
3: gdi.gdocument.co
4: gdiback.gdocument.co
5: globaldoc.gdocument.co
6: globaldocback.gdocument.co
- - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - -

Please choose whether or not to redirect HTTP traffic to HTTPS, removing HTTP access.
- - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - -
1: No redirect - Make no further changes to the webserver configuration.
2: Redirect - Make all requests redirect to secure HTTPS access. Choose this for
new sites, or if you're confident your site works on HTTPS. You can undo this
change by editing your web server's configuration.
- - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - -
Select the appropriate number [1-2] then [enter] (press 'c' to cancel): 2

Ubicado en la carpeta raíz del proyecto /home/webmaster/www/gdocument_demostracion se realiza un chequeo de permisos a todo el back ejecutando los comandos:
sudo chmod -R 775 back
sudo chown -R webmaster:www-data back


Se prueba que el backend esté funcionando correctamente
Se accede a la URL https://demostracionback.gdocument.co/api
Debe mostrar la página principal de las APIs usadas en el proyecto y debe mostrarla con el certificado activo(candado en la URL).

Ahora se configura el frontend.
Instalar
Sudo apt install npm
npm install @angular/cli

Para subirla a la nueva instalación se crea la carpeta "front" en el servidor Remoto  en la ruta:  /home/webmaster/www/gdocument_demostracion
Ejecutando el comando: mkdir front

Se copia en el servidor la carpeta dist del front. Esto se copia del servidor de desarrollo local.
git clone https://github.com/grupodesarrolloinnovacion/idoc.git front
Se configura el valor --max_old_space_size=2048 2 gigas de memoria RAM en el archivo package.json dentro de la carpeta front
Se ejecuta el comando:
npm run build dentro de front



Se cambia el parámetro urlbase en el archivo front\dist\assets\global_config.json  y se le coloca la url del backend a instalar:
Ejemplo:
"urlbase": "https://demostracionback.gdocument.co/api/"


Se realiza un chequeo de permisos a todo el front ejecutando los comandos
sudo chmod -R 775 front
sudo chown -R webmaster:www-data front


Se activan las tareas programadas para envío de notificaciones y compartir documentos
*/1 * * * * /usr/bin/php /home/webmaster/www/gdocument_demostracion/back/bin/console gdocument:send-mail >/dev/null 2>&1


*/1 * * * * /usr/bin/php /home/webmaster/www/gdocument_demostracion/back/bin/console gdocument:notify >/dev/null 2>&1
Agregar la imagen por defecto del perfil en la carpeta imagenes/usuarios de la cuenta de Google Drive.
Copia el identificador de la imagen profile.jpg en la variable de ambiente DEFAULT_AVATAR ubicada en el archivo .env.local


Ajustar timezone en versión de PHP usada con Apache. Debe ser 
date.timezone = America/Bogota. 
Este ajuste se hace en el archivo /etc/php/x.x/apache2/php.ini

x.x hace referencia a la versión de PHP usada.
Ejemplo: x.x = 7.4 /etc/php/7.4/apache2/php.ini



EJEMPLO PARÁMETROS DE CONFIGURACIÓN ALOJADOS EN .env.local

### Ambiente de la aplicación en aplicación en producción en valor debe ser prod
APP_ENV=prod

### Conexión a la base de datos
DATABASE_URL=mysql://gdocument:%gdocument$@127.0.0.1:3306/gdocument_gdi

### Llave API plataforma envío de correo
SENDGRID_API_KEY=SG.EhPeRvUMSLW5lpczTaKNgA.R7t-QtesTPRhOER1YxWJQUzgBZ29mJ1w1ZVw_oMnr58

### Tiempos de vigencia para tokens de autenticación. Valor en segundos
JWT_PASSPHRASE=[Valor colocado el generar las llaves]

### 24 Horas
JWT_TOKEN_TTL=86400 

### JWT_REFRESH_TOKEN_TTL SIEMPRE DEBE SER MAYOR JWT_TOKEN_TTL
### 48 Horas
JWT_REFRESH_TOKEN_TTL=172800

### Fuente usada para crear los stickers impresos
GDFONTPATH=/home/webmaster/www/gdocument_gdi/back/config/fonts/Roboto/Roboto-Regular.ttf

### Ubicación archivos temporales
TMP_LOCATION=/home/webmaster/www/gdocument_gdi/back/tmp/

### Ubicación archivos temporales
PUBLIC_TMP_LOCATION=/home/webmaster/www/gdocument_gdi/back/public/tmp/

### File Location Identificador de Google Drive
FILE_LOCATION=1Dq2DtwJYR7K7FF2TOjYYpskctCNOC6ZG

## Image Location Identificador de Google Drive
IMAGE_LOCATION=1gaznHoEUX51HATg32UxztuEMDajk1wha

## Default image to user Identificador de Google Drive
DEFAULT_AVATAR=1NntLZGJct-ybykvfxn6Odp-4DDINZTGi

### Ejecuta NotifyCommand a SendMailCommand manualmente. 
### Despacho inmediato de notificaciones y correos
COMMAND_FROM_CONTROLLER=true

### Maximun number of mails sended per SendMailCommand request
MAX_SEND_MAILS=20

### ANCHO HOJA PIXELES
PAPER_WIDTH=536

### ALTO HOJA PIXELES
PAPER_HEIGHT=674

### ANCHO STICKER IMPRESION HOJA CARTA=PAPER_GRID_IMAGE_WIDTH
PAPER_STICKER_IMAGE_WIDTH=178

### ANCHO GRILLA IMPRESION HOJA CARTA=PAPER_WIDTH/3
PAPER_GRID_IMAGE_WIDTH=178

### ALTO GRILLA IMPRESION HOJA CARTA=PAPER_HEIGHT/3
PAPER_GRID_IMAGE_HEIGHT=224

### Escoger tipo de impresora
RADICADORA=true

### ANCHO STICKER RADICADORA EN PIXELES
RADICADORA_STICKER_WIDTH=170

### ALTO STICKER RADICADORA EN PIXELES
RADICADORA_STICKER_HEIGHT=91

### ALTURA IMAGEN STICKER RADICADORA
RADICADORA_STICKER_IMAGE_HEIGHT=115

## URL BASE DEL PROYECTO
BASE_URL="http://back.gdocument.local"

##CANT. DIGITOS ESTRUCTURADOCUMENTAL
FONDO=0
SECCION=2
SUBSECCION=2
SERIE=2
SUBSERIE=2


##ICONOS ESTRUCTURADOCUMENTAL
FONDO_ICON=business
SECCION_ICON=account_tree
SUBSECCION_ICON=device_hub
SERIE_ICON=storage
SUBSERIE_ICON=dns
DEFAULT_ICON=folder
TIPO_DOCUMENTAL_ICON=description

A tener en cuenta…

gDocument puede ser instalado en cualquier plataforma Cloud de Internet, desde que se cumpla con las características mínimas de instalación.
