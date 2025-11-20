# 🔗 Guía de Conexión: Laravel + MySQL en Railway

## 🎯 Paso a Paso para Conectar Servicios

### ✅ Paso 1: Crear Servicio Laravel desde GitHub

1. En Railway, ve a tu proyecto
2. Click **"+ New"** → **"GitHub Repo"**
3. Selecciona: `xXValiSamaXx/NeoZama_ApiRest`
4. Railway detectará el `Dockerfile` automáticamente
5. Espera a que termine el primer build

---

### 🔗 Paso 2: CONECTAR Laravel con MySQL (MUY IMPORTANTE)

Esto es lo que faltaba hacer:

1. Click en tu **servicio Laravel** (el que acabas de crear)
2. Ve a **Settings** (⚙️)
3. Busca la sección **"Connect"** o **"Service Connections"**
4. Click en **"+ Connect"** o **"Link Service"**
5. Selecciona tu servicio **MySQL** de la lista
6. Railway creará automáticamente las variables compartidas

**🎉 Ahora están conectados!**

---

### 📝 Paso 3: Configurar Variables de Entorno con Referencias

Ahora en tu servicio Laravel, ve a **Variables** y agrega:

#### Variables Básicas:
```env
APP_NAME=Bóveda de Documentos
APP_ENV=production
APP_KEY=base64:LwGnW0D5lA+bGqFCfHpQjtX8OZ/Ki5FYO5YROxCCiPI=
APP_DEBUG=false
```

#### Variables de Base de Datos (REFERENCIAS):
```env
DB_CONNECTION=mysql
DB_HOST=${{MySQL.MYSQL_PRIVATE_URL}}
DB_PORT=${{MySQL.MYSQL_PORT}}
DB_DATABASE=${{MySQL.MYSQL_DATABASE}}
DB_USERNAME=${{MySQL.MYSQL_USER}}
DB_PASSWORD=${{MySQL.MYSQL_PASSWORD}}
```

**Nota:** Reemplaza `MySQL` por el **nombre real** de tu servicio MySQL en Railway si es diferente.

#### Variables de Laravel:
```env
LOG_CHANNEL=stack
LOG_LEVEL=error
SESSION_DRIVER=database
CACHE_STORE=database
QUEUE_CONNECTION=database
FILESYSTEM_DISK=local
```

---

### 🌐 Paso 4: Generar Dominio

1. En tu servicio Laravel, ve a **Settings** → **Networking**
2. Click **"Generate Domain"**
3. Copia la URL generada (ejemplo: `https://neozama-apirest.up.railway.app`)
4. Ve a **Variables** y agrega/actualiza:
```env
APP_URL=https://neozama-apirest.up.railway.app
```

---

### 🔄 Paso 5: Redesplegar

1. Ve a tu servicio Laravel
2. Click en **"Redeploy"** para aplicar las variables
3. Railway ejecutará automáticamente:
   - `php artisan migrate --force`
   - `php artisan l5-swagger:generate`
   - Todos los caches

---

## ✅ Verificación de Conexión

### Opción A: Ver Logs
1. Ve a tu servicio Laravel → **Logs**
2. Busca:
```
🚀 Iniciando aplicación...
🗄️  Ejecutando migraciones...
   INFO  Migration successful.
📝 Generando cache de configuración...
📚 Generando documentación Swagger...
✅ Aplicación lista!
```

### Opción B: Probar API
```bash
curl https://tu-dominio.up.railway.app/api/documentation
```

Deberías ver la interfaz de Swagger UI.

---

## 🔍 Cómo Verificar la Conexión

### En Railway Dashboard:

1. **Ver variables compartidas:**
   - Ve a tu servicio Laravel → Variables
   - Las variables con `${{MySQL.*}}` se mostrarán con sus valores reales

2. **Ver servicios conectados:**
   - En Settings → Connect
   - Deberías ver el servicio MySQL listado

3. **Red Privada:**
   - Railway usará `mysql.railway.internal` automáticamente
   - Esto es más rápido y seguro que usar IP pública

---

## 🎯 Diagrama de Conexión

```
┌─────────────────────────────────────┐
│  Railway Project                    │
│                                     │
│  ┌──────────────┐   🔗 Conectado   │
│  │   Laravel    │◄──────────────┐  │
│  │   Service    │               │  │
│  │ (Port 8080)  │               │  │
│  └──────────────┘               │  │
│         │                       │  │
│         │ Variables compartidas │  │
│         ▼                       │  │
│  ┌──────────────┐               │  │
│  │    MySQL     │───────────────┘  │
│  │   Service    │                  │
│  │ (Port 3306)  │                  │
│  └──────────────┘                  │
│                                     │
│  Red Privada: mysql.railway.internal│
└─────────────────────────────────────┘
```

---

## 🚨 Solución de Problemas

### ❌ Error: "SQLSTATE[HY000] [2002] Connection refused"
**Causa:** El servicio Laravel NO está conectado con MySQL

**Solución:**
1. Ve a Laravel Service → Settings → Connect
2. Conecta con el servicio MySQL
3. Redespliega

### ❌ Error: Variables ${{MySQL.*}} no se resuelven
**Causa:** El nombre del servicio MySQL es diferente

**Solución:**
1. Ve a tu servicio MySQL y copia su **nombre exacto**
2. Reemplaza `MySQL` con el nombre correcto en las variables
3. Ejemplo: Si se llama `mysql-prod`, usa `${{mysql-prod.MYSQL_PASSWORD}}`

### ❌ Error: "Access denied for user"
**Causa:** Estás usando credenciales hardcodeadas en lugar de referencias

**Solución:**
1. Elimina las variables con valores hardcodeados
2. Usa las referencias `${{MySQL.*}}`
3. Redespliega

---

## 💡 Ventajas de Usar Referencias

| Con Referencias | Sin Referencias |
|----------------|-----------------|
| ✅ Sincronización automática | ❌ Copiar/pegar manual |
| ✅ Red privada (rápido) | ❌ IP pública (lento) |
| ✅ Seguro | ⚠️ Credenciales expuestas |
| ✅ Se actualiza solo | ❌ Actualización manual |
| ✅ Variables compartidas | ❌ Duplicación de datos |

---

## 📋 Checklist de Conexión

- [ ] Servicio Laravel creado desde GitHub
- [ ] Servicio Laravel **CONECTADO** con MySQL (Settings → Connect)
- [ ] Variables configuradas con **referencias** `${{MySQL.*}}`
- [ ] Dominio generado
- [ ] `APP_URL` actualizada con el dominio
- [ ] Redeploy ejecutado
- [ ] Logs muestran "Migration successful"
- [ ] Swagger UI accesible en `/api/documentation`

---

## 🎉 Resultado Final

Una vez conectado correctamente, tendrás:

- ✅ Laravel y MySQL comunicándose por red privada
- ✅ Variables sincronizadas automáticamente
- ✅ Migraciones ejecutadas exitosamente
- ✅ API funcionando en producción
- ✅ Swagger UI disponible públicamente

**URL Final:** `https://tu-dominio.up.railway.app/api/documentation`

---

**¡Ahora sí está todo conectado correctamente! 🚀**
