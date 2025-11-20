# 🔌 Configuración de Conexión a Railway MySQL

## Variables de Entorno para Railway

Copia estas variables en tu servicio de Railway:

```env
# Application
APP_NAME="Bóveda de Documentos"
APP_ENV=production
APP_KEY=base64:LwGnW0D5lA+bGqFCfHpQjtX8OZ/Ki5FYO5YROxCCiPI=
APP_DEBUG=false
APP_URL=https://tu-dominio.up.railway.app

# Database - Red Privada (Recomendado)
DB_CONNECTION=mysql
DB_HOST=mysql.railway.internal
DB_PORT=3306
DB_DATABASE=railway
DB_USERNAME=root
DB_PASSWORD=GMsdYupELuMERfdRWvkWixfZQNQzVKsc

# Logging
LOG_CHANNEL=stack
LOG_LEVEL=error

# Session
SESSION_DRIVER=database
SESSION_LIFETIME=120

# Cache
CACHE_STORE=database

# Queue
QUEUE_CONNECTION=database

# Filesystem
FILESYSTEM_DISK=local
```

## 📊 Información de tu Base de Datos

### Red Privada (Dentro de Railway)
```
Host: mysql.railway.internal
Port: 3306
Database: railway
Username: root
Password: GMsdYupELuMERfdRWvkWixfZQNQzVKsc
```

### Red Pública (Para acceso externo)
```
Host: trolley.proxy.rlwy.net
Port: 26310
Database: railway
Username: root
Password: GMsdYupELuMERfdRWvkWixfZQNQzVKsc
URL: mysql://root:GMsdYupELuMERfdRWvkWixfZQNQzVKsc@trolley.proxy.rlwy.net:26310/railway
```

## 🔄 Usando Variables de Railway

También puedes usar referencias a las variables de tu servicio MySQL:

```env
DB_HOST=${{MySQL.MYSQLHOST}}
DB_PORT=${{MySQL.MYSQLPORT}}
DB_DATABASE=${{MySQL.MYSQLDATABASE}}
DB_USERNAME=${{MySQL.MYSQLUSER}}
DB_PASSWORD=${{MySQL.MYSQL_ROOT_PASSWORD}}
```

## ✅ Tablas Ya Creadas en tu BD

Tu base de datos Railway ya tiene estas tablas:
- ✅ `users`
- ✅ `categories`
- ✅ `documents`
- ✅ `document_shares`
- ✅ `migrations`
- ✅ `sessions`
- ✅ `cache`, `cache_locks`
- ✅ `jobs`, `job_batches`, `failed_jobs`
- ✅ `password_reset_tokens`

**No necesitas ejecutar migraciones** a menos que hayas hecho cambios.

## 🧪 Probar Conexión Localmente

Para probar la conexión desde tu máquina local:

```env
# .env (local)
DB_CONNECTION=mysql
DB_HOST=trolley.proxy.rlwy.net
DB_PORT=26310
DB_DATABASE=railway
DB_USERNAME=root
DB_PASSWORD=GMsdYupELuMERfdRWvkWixfZQNQzVKsc
```

Luego:
```bash
php artisan migrate:status
```

## 🚀 Después del Despliegue

1. **Generar Swagger:**
```bash
railway run php artisan l5-swagger:generate
```

2. **Limpiar caché:**
```bash
railway run php artisan config:cache
railway run php artisan route:cache
```

3. **Verificar:**
```
https://tu-dominio.up.railway.app/api/documentation
```

## 🔐 Seguridad

⚠️ **IMPORTANTE:** En producción:
- No compartas estas credenciales públicamente
- Usa variables de entorno de Railway
- Mantén `APP_DEBUG=false`
- Usa HTTPS (Railway lo proporciona automáticamente)

## 💡 Tips

1. **Red Privada:** Más rápida y segura dentro de Railway
2. **Red Pública:** Solo para acceso externo o desarrollo
3. **Variables Railway:** Facilitan cambios sin redeployar
