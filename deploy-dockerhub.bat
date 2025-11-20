@echo off
echo ====================================
echo  Despliegue a DockerHub y Railway
echo ====================================
echo.

REM Solicitar usuario de DockerHub
set /p DOCKERHUB_USER="Ingresa tu usuario de DockerHub: "

REM Definir nombre de la imagen
set IMAGE_NAME=boveda-documentos-api
set IMAGE_TAG=latest
set FULL_IMAGE=%DOCKERHUB_USER%/%IMAGE_NAME%:%IMAGE_TAG%

echo.
echo [1/5] Construyendo imagen Docker...
docker build -t %FULL_IMAGE% .

if %errorlevel% neq 0 (
    echo ERROR: Fallo al construir la imagen
    pause
    exit /b 1
)

echo.
echo [2/5] Iniciando sesion en DockerHub...
docker login

if %errorlevel% neq 0 (
    echo ERROR: Fallo al iniciar sesion en DockerHub
    pause
    exit /b 1
)

echo.
echo [3/5] Subiendo imagen a DockerHub...
docker push %FULL_IMAGE%

if %errorlevel% neq 0 (
    echo ERROR: Fallo al subir la imagen
    pause
    exit /b 1
)

echo.
echo [4/5] Verificando imagen en DockerHub...
echo Imagen subida exitosamente: %FULL_IMAGE%
echo.
echo Puedes verificarla en: https://hub.docker.com/r/%DOCKERHUB_USER%/%IMAGE_NAME%

echo.
echo [5/5] Configuracion para Railway:
echo.
echo ====================================
echo  SIGUIENTE: Configurar en Railway
echo ====================================
echo.
echo 1. Ve a tu proyecto en Railway Dashboard
echo 2. Crea un NUEVO servicio:
echo    - Click en "+ New"
echo    - Selecciona "Docker Image"
echo.
echo 3. Ingresa la imagen:
echo    %FULL_IMAGE%
echo.
echo 4. Configura las variables de entorno:
echo    DB_CONNECTION=mysql
echo    DB_HOST=${{MySQL.MYSQLHOST}}
echo    DB_DATABASE=${{MySQL.MYSQLDATABASE}}
echo    DB_USERNAME=${{MySQL.MYSQLUSER}}
echo    DB_PASSWORD=${{MySQL.MYSQLPASSWORD}}
echo    DB_PORT=${{MySQL.MYSQLPORT}}
echo    APP_NAME=Boveda de Documentos
echo    APP_ENV=production
echo    APP_KEY=base64:LwGnW0D5lA+bGqFCfHpQjtX8OZ/Ki5FYO5YROxCCiPI=
echo    APP_DEBUG=false
echo    LOG_LEVEL=error
echo    SESSION_DRIVER=database
echo    CACHE_STORE=database
echo    QUEUE_CONNECTION=database
echo    FILESYSTEM_DISK=local
echo.
echo 5. En Settings - Networking:
echo    - Genera un dominio publico
echo    - Actualiza APP_URL con ese dominio
echo.
echo 6. Conecta el servicio con MySQL:
echo    - Settings - Connect
echo    - Selecciona el servicio MySQL
echo.
echo 7. Despliega el servicio
echo.
echo ====================================
echo  IMAGEN LISTA EN DOCKERHUB
echo ====================================
echo.
pause
