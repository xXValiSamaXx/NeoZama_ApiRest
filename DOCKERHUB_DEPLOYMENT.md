# 🐳 Despliegue via DockerHub → Railway

## 📋 Requisitos

- ✅ Docker Desktop instalado y corriendo
- ✅ Cuenta en DockerHub (https://hub.docker.com)
- ✅ Cuenta en Railway con MySQL configurado

---

## 🚀 Paso 1: Subir Imagen a DockerHub

### Opción A: Usar el script automatizado

Ejecuta el script que automatiza todo:

```bash
.\deploy-dockerhub.bat
```

El script te pedirá:
1. Tu usuario de DockerHub
2. Tu contraseña de DockerHub (al hacer `docker login`)
3. Y subirá la imagen automáticamente

### Opción B: Comandos manuales

```bash
# 1. Construir la imagen
docker build -t TU_USUARIO/boveda-documentos-api:latest .

# 2. Login en DockerHub
docker login

# 3. Subir la imagen
docker push TU_USUARIO/boveda-documentos-api:latest
```

**Reemplaza `TU_USUARIO` con tu usuario de DockerHub**

---

## 🚂 Paso 2: Configurar Railway

### 2.1. Crear Servicio desde DockerHub

1. Ve a tu proyecto en **Railway Dashboard**
2. Click en **"+ New"**
3. Selecciona **"Docker Image"**
4. Ingresa tu imagen:
   ```
   TU_USUARIO/boveda-documentos-api:latest
   ```

### 2.2. Configurar Variables de Entorno

En el servicio recién creado, ve a **Variables** y agrega:

```env
# Aplicación
APP_NAME=Bóveda de Documentos
APP_ENV=production
APP_KEY=base64:LwGnW0D5lA+bGqFCfHpQjtX8OZ/Ki5FYO5YROxCCiPI=
APP_DEBUG=false

# Base de datos (REFERENCIAS al servicio MySQL)
DB_CONNECTION=mysql
DB_HOST=${{MySQL.MYSQLHOST}}
DB_DATABASE=${{MySQL.MYSQLDATABASE}}
DB_USERNAME=${{MySQL.MYSQLUSER}}
DB_PASSWORD=${{MySQL.MYSQLPASSWORD}}
DB_PORT=${{MySQL.MYSQLPORT}}

# Laravel
LOG_CHANNEL=stack
LOG_LEVEL=error
SESSION_DRIVER=database
CACHE_STORE=database
QUEUE_CONNECTION=database
FILESYSTEM_DISK=local
```

### 2.3. Conectar con MySQL

1. En tu servicio nuevo, ve a **Settings** → **Connect**
2. Selecciona el servicio **MySQL**
3. Railway creará las referencias automáticamente

### 2.4. Generar Dominio

1. Ve a **Settings** → **Networking**
2. Click en **"Generate Domain"**
3. Copia el dominio generado
4. Actualiza la variable `APP_URL`:
   ```env
   APP_URL=https://tu-dominio.up.railway.app
   ```

### 2.5. Desplegar

Railway desplegará automáticamente la imagen de DockerHub. 🚀

---

## ✅ Verificación

### Ver Logs

En Railway Dashboard → Tu servicio → **Logs**

Deberías ver:
```
🚀 Iniciando aplicación...
🗄️  Ejecutando migraciones...
   INFO  Migration successful.
📝 Generando cache de configuración...
📚 Generando documentación Swagger...
✅ Aplicación lista!
🌐 Swagger UI disponible en: /api/documentation
```

### Probar API

```bash
curl https://tu-dominio.up.railway.app/api/documentation
```

---

## 🔄 Actualizar la Aplicación

Cuando hagas cambios en el código:

```bash
# 1. Reconstruir imagen
docker build -t TU_USUARIO/boveda-documentos-api:latest .

# 2. Subir a DockerHub
docker push TU_USUARIO/boveda-documentos-api:latest

# 3. En Railway Dashboard:
#    - Ve a tu servicio
#    - Click en "Redeploy"
#    - Railway descargará la nueva imagen automáticamente
```

---

## 💡 Ventajas de este método

✅ **Control total** sobre la imagen Docker  
✅ **Pruebas locales** antes de desplegar  
✅ **Versionado** de imágenes en DockerHub  
✅ **Rollback fácil** cambiando el tag  
✅ **Sin dependencia** del código fuente en Railway  
✅ **Builds más rápidos** en Railway (solo descarga imagen)  

---

## 🎯 Resumen del Flujo

```
┌─────────────┐
│   Código    │
│   Laravel   │
└──────┬──────┘
       │
       ▼
┌─────────────┐
│  Dockerfile │ ← docker build
└──────┬──────┘
       │
       ▼
┌─────────────┐
│  DockerHub  │ ← docker push
└──────┬──────┘
       │
       ▼
┌─────────────┐
│   Railway   │ ← Despliega imagen
└──────┬──────┘
       │
       ▼
┌─────────────┐
│  Producción │
└─────────────┘
```

---

**¡La imagen está lista para desplegarse! 🚀**
