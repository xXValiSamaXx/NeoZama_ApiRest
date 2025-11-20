@echo off
echo ========================================
echo   DESPLIEGUE EN RAILWAY - PASO A PASO
echo ========================================
echo.

REM Verificar si Git está inicializado
if not exist .git (
    echo [1/5] Inicializando Git...
    git init
    echo ✓ Git inicializado
) else (
    echo ✓ Git ya está inicializado
)
echo.

REM Agregar todos los archivos
echo [2/5] Agregando archivos al repositorio...
git add .
echo ✓ Archivos agregados
echo.

REM Hacer commit
echo [3/5] Creando commit...
git commit -m "feat: API Boveda de Documentos con Swagger - Lista para Railway"
echo ✓ Commit creado
echo.

REM Configurar remote (si no existe)
git remote | findstr origin >nul
if errorlevel 1 (
    echo [4/5] Configurando repositorio remoto...
    set /p REPO="Ingresa la URL de tu repositorio GitHub: "
    git remote add origin %REPO%
    echo ✓ Repositorio remoto configurado
) else (
    echo ✓ Repositorio remoto ya configurado
)
echo.

REM Hacer push
echo [5/5] Subiendo código a GitHub...
git branch -M main
git push -u origin main
echo ✓ Código subido a GitHub
echo.

echo ========================================
echo   ✓ CÓDIGO SUBIDO EXITOSAMENTE
echo ========================================
echo.
echo SIGUIENTES PASOS:
echo.
echo 1. Ve a Railway.app
echo 2. Click en "+ New" y selecciona "GitHub Repo"
echo 3. Selecciona tu repositorio
echo 4. Configura las variables de entorno (ver RAILWAY_DEPLOYMENT.md)
echo 5. Railway desplegará automáticamente
echo.
echo Consulta RAILWAY_DEPLOYMENT.md para más detalles.
echo.
pause
