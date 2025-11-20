# 🚂 Guía de Despliegue en Railway

## 📋 Requisitos Previos

- ✅ Cuenta en Railway.app
- ✅ Base de datos MySQL en Railway (ya configurada)
- ✅ Git instalado
- ✅ Repositorio GitHub con el proyecto

---

## 🗄️ Paso 1: Verificar Base de Datos MySQL en Railway

### Credenciales de tu MySQL Railway:
```
Host (Privado):  mysql.railway.internal
Host (Público):  trolley.proxy.rlwy.net:26310
Puerto:          3306
Database:        railway
Usuario:         root
Password:        GMsdYupELuMERfdRWvkWixfZQNQzVKsc
```

✅ **Ya tienes las tablas creadas:**
- `categories`
- `document_shares`
- `documents`
- `users`
- `cache`, `cache_locks`
- `failed_jobs`, `job_batches`, `jobs`
- `migrations`
- `password_reset_tokens`
- `sessions`

---

## 🐳 Paso 2: Preparar Dockerfile (Ya Creado)

El `Dockerfile` ya está creado y configurado con:
- PHP 8.2 con Apache
- Todas las extensiones necesarias
- Composer
- Configuración optimizada para producción

---

## 📦 Paso 3: Subir Código a GitHub

### 3.1. Inicializar Git (si no está inicializado)
```bash
cd "d:\Cosas de la escuela\Tareas\Tareas Universidad\boveda-documentos"
git init
```

### 3.2. Agregar archivos al repositorio
```bash
git add .
git commit -m "feat: API Bóveda de Documentos con Swagger - Lista para Railway"
```

### 3.3. Conectar con tu repositorio GitHub
```bash
# Si ya tienes el repo creado (NeoZama_ApiRest)
git remote add origin https://github.com/xXValiSamaXx/NeoZama_ApiRest.git
git branch -M main
git push -u origin main
```

---

## 🚀 Paso 4: Desplegar en Railway

### 4.1. Crear Nuevo Servicio en Railway

1. Ve a **Railway.app** y accede a tu proyecto
2. Click en **"+ New"** → **"GitHub Repo"**
3. Selecciona tu repositorio: **`xXValiSamaXx/NeoZama_ApiRest`**
4. Railway detectará automáticamente el `Dockerfile`

### 4.2. Conectar Servicio con Base de Datos MySQL

**IMPORTANTE:** Primero debes conectar tu servicio Laravel con el servicio MySQL en Railway:

1. En tu **servicio Laravel**, ve a **Settings** → **Connect**
2. Selecciona tu servicio **MySQL** 
3. Railway creará automáticamente las variables de referencia

### 4.3. Configurar Variables de Entorno

En Railway, ve a **Variables** y agrega (usando referencias al servicio MySQL):

```env
APP_NAME=Bóveda de Documentos
APP_ENV=production
APP_KEY=base64:LwGnW0D5lA+bGqFCfHpQjtX8OZ/Ki5FYO5YROxCCiPI=
APP_DEBUG=false
APP_URL=https://tu-app.up.railway.app

# Base de datos (REFERENCIAS al servicio MySQL)
DB_CONNECTION=mysql
DB_HOST=${{MySQL.MYSQL_PRIVATE_URL}}
DB_PORT=${{MySQL.MYSQL_PORT}}
DB_DATABASE=${{MySQL.MYSQL_DATABASE}}
DB_USERNAME=${{MySQL.MYSQL_USER}}
DB_PASSWORD=${{MySQL.MYSQL_PASSWORD}}

# Logging
LOG_CHANNEL=stack
LOG_LEVEL=error

# Session y Cache
SESSION_DRIVER=database
CACHE_STORE=database

# Queue
QUEUE_CONNECTION=database

# Filesystem
FILESYSTEM_DISK=local
```

**💡 Beneficios de usar referencias:**
- ✅ Conexión automática entre servicios
- ✅ Si cambias la contraseña de MySQL, se actualiza automáticamente
- ✅ Usa la red privada de Railway (más rápido y seguro)
- ✅ No necesitas copiar/pegar credenciales manualmente

### 4.4. Configurar Networking

1. En tu servicio Laravel, ve a **Settings**
2. En **Networking**, click en **"Generate Domain"**
3. Obtendrás una URL como: `https://tu-proyecto.up.railway.app`
4. **Actualiza `APP_URL`** en variables de entorno con esta URL

### 4.5. Verificar Conexión

Railway conectará automáticamente tu servicio con MySQL usando:
- **Red privada** (más rápido y seguro)
- **Variables compartidas** (sincronización automática)
- El host será `mysql.railway.internal` automáticamente

---

## 🔧 Paso 5: Ejecutar Migraciones

### Opción A: Desde Railway CLI

1. Instala Railway CLI:
```bash
npm i -g @railway/cli
```

2. Autentícate:
```bash
railway login
```

3. Vincula tu proyecto:
```bash
railway link
```

4. Ejecuta migraciones:
```bash
railway run php artisan migrate --force
railway run php artisan l5-swagger:generate
```

### Opción B: Desde el Dashboard de Railway

1. Ve a tu servicio en Railway
2. Click en **"Console"** o **"Shell"**
3. Ejecuta:
```bash
php artisan migrate --force
php artisan l5-swagger:generate
php artisan config:cache
```

---

## ✅ Paso 6: Verificar Despliegue

### 6.1. Acceder a tu API
```
https://tu-proyecto.up.railway.app/api/documentation
```

### 6.2. Probar endpoints
```bash
# Registro
curl -X POST https://tu-proyecto.up.railway.app/api/register \
  -H "Content-Type: application/json" \
  -d '{
    "name": "Test User",
    "email": "test@example.com",
    "password": "password123",
    "password_confirmation": "password123"
  }'
```

---

## 🔍 Troubleshooting

### Error: "could not find driver"
**Solución:** Verifica que el Dockerfile instale `pdo_mysql`:
```dockerfile
RUN docker-php-ext-install pdo_mysql
```

### Error: "Connection refused"
**Solución:** 
- Verifica que uses `mysql.railway.internal` (red privada)
- O el host público si estás conectando desde fuera

### Error: "Table not found"
**Solución:** Ejecuta las migraciones:
```bash
railway run php artisan migrate --force
```

### Error: "Permission denied"
**Solución:** Los permisos se configuran en el Dockerfile:
```dockerfile
RUN chmod -R 755 /var/www/html/storage
```

---

## 📊 Monitoreo

### Ver Logs en Tiempo Real
```bash
railway logs
```

### Desde el Dashboard
1. Ve a tu servicio
2. Click en **"Logs"**
3. Filtra por errores o warnings

---

## 🔐 Seguridad en Producción

### 1. Generar nueva APP_KEY
```bash
php artisan key:generate --show
```
Copia el resultado y actualiza `APP_KEY` en Railway.

### 2. Desactivar Debug
```env
APP_DEBUG=false
LOG_LEVEL=error
```

### 3. HTTPS Automático
Railway proporciona HTTPS automáticamente. ✅

### 4. Actualizar APP_URL
```env
APP_URL=https://tu-dominio.up.railway.app
```

---

## 🚀 Despliegue Continuo

Railway desplegará automáticamente cada vez que hagas push a `main`:

```bash
git add .
git commit -m "feat: nueva funcionalidad"
git push origin main
```

Railway detectará el cambio y redespllegará automáticamente. 🎉

---

## 📝 Comandos Útiles

```bash
# Ver servicios
railway status

# Ejecutar comandos
railway run php artisan migrate
railway run php artisan db:seed
railway run php artisan cache:clear

# Ver variables
railway variables

# Abrir en navegador
railway open
```

---

## 🎯 Checklist Final

- [ ] Código subido a GitHub
- [ ] Servicio creado en Railway
- [ ] Variables de entorno configuradas
- [ ] Dominio generado
- [ ] Migraciones ejecutadas
- [ ] Swagger generado
- [ ] API funcionando en `/api/documentation`
- [ ] Endpoints probados

---

## 📱 URLs Importantes

Después del despliegue, tendrás:

- **API Base:** `https://tu-proyecto.up.railway.app/api`
- **Swagger UI:** `https://tu-proyecto.up.railway.app/api/documentation`
- **Health Check:** `https://tu-proyecto.up.railway.app/`

---

## 💡 Tips Extra

### 1. Usar Dominio Personalizado
En Railway Settings → Networking → Custom Domain

### 2. Configurar CORS
Ya está configurado en Laravel, pero puedes ajustarlo en `config/cors.php`

### 3. Optimizar para Producción
Ya incluido en el Dockerfile:
```bash
composer install --no-dev --optimize-autoloader
php artisan config:cache
php artisan route:cache
php artisan view:cache
```

---

## 🆘 Soporte

Si tienes problemas:
1. Revisa los logs: `railway logs`
2. Verifica las variables de entorno
3. Comprueba la conexión con MySQL
4. Verifica que las migraciones se ejecutaron

---

**¡Tu API estará en producción en minutos! 🎉**
