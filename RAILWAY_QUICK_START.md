# ⚡ Guía Rápida de 5 Minutos - Railway

## 🎯 Objetivo
Desplegar tu API de Bóveda de Documentos en Railway en 5 pasos.

---

## 📋 Pre-requisitos
- ✅ Cuenta en Railway.app
- ✅ MySQL ya configurado en Railway
- ✅ Repositorio GitHub: `xXValiSamaXx/NeoZama_ApiRest`

---

## 🚀 5 PASOS RÁPIDOS

### **Paso 1: Subir Código a GitHub** (2 min)

```bash
cd "d:\Cosas de la escuela\Tareas\Tareas Universidad\boveda-documentos"

# Ejecutar el script automático
.\deploy-to-railway.bat
```

O manualmente:
```bash
git add .
git commit -m "feat: API lista para Railway"
git push origin main
```

---

### **Paso 2: Crear Servicio en Railway** (1 min)

1. Ve a **Railway.app**
2. Abre tu proyecto (donde está MySQL)
3. Click **"+ New"** → **"GitHub Repo"**
4. Selecciona: **`xXValiSamaXx/NeoZama_ApiRest`**
5. Railway detectará el `Dockerfile` automáticamente ✅

---

### **Paso 3: Configurar Variables** (1 min)

En tu nuevo servicio, ve a **"Variables"** y pega esto:

```env
APP_NAME=Bóveda de Documentos
APP_ENV=production
APP_KEY=base64:LwGnW0D5lA+bGqFCfHpQjtX8OZ/Ki5FYO5YROxCCiPI=
APP_DEBUG=false

DB_CONNECTION=mysql
DB_HOST=mysql.railway.internal
DB_PORT=3306
DB_DATABASE=railway
DB_USERNAME=root
DB_PASSWORD=GMsdYupELuMERfdRWvkWixfZQNQzVKsc

LOG_CHANNEL=stack
LOG_LEVEL=error
SESSION_DRIVER=database
CACHE_STORE=database
QUEUE_CONNECTION=database
FILESYSTEM_DISK=local
```

💡 **Importante:** Después de que se genere el dominio, actualiza:
```env
APP_URL=https://tu-dominio-generado.up.railway.app
```

---

### **Paso 4: Generar Dominio** (30 seg)

1. En tu servicio, ve a **"Settings"**
2. Sección **"Networking"**
3. Click **"Generate Domain"**
4. Copia el dominio generado (ej: `boveda-docs.up.railway.app`)
5. Actualiza la variable `APP_URL` con ese dominio

---

### **Paso 5: Esperar Despliegue** (1-2 min)

Railway construirá y desplegará automáticamente:
- 🔨 Construyendo imagen Docker...
- 📦 Instalando dependencias...
- ✅ Desplegando...

**Cuando veas "Success"**, tu API estará lista! 🎉

---

## ✅ Verificar que Funciona

### 1. Abrir Swagger UI
```
https://tu-dominio.up.railway.app/api/documentation
```

### 2. Probar Registro
```bash
curl -X POST https://tu-dominio.up.railway.app/api/register \
  -H "Content-Type: application/json" \
  -d '{
    "name": "Test User",
    "email": "test@example.com",
    "password": "password123",
    "password_confirmation": "password123"
  }'
```

### 3. Ver Logs
En Railway, click en tu servicio → **"Logs"**

---

## 🔧 Comandos Post-Despliegue (Opcional)

Si necesitas ejecutar comandos:

```bash
# Instalar Railway CLI
npm i -g @railway/cli

# Login
railway login

# Vincular proyecto
railway link

# Ejecutar comandos
railway run php artisan l5-swagger:generate
railway run php artisan config:cache
```

---

## 📊 Lo que Tendrás

| Recurso | URL |
|---------|-----|
| **API Base** | `https://tu-dominio.up.railway.app/api` |
| **Swagger UI** | `https://tu-dominio.up.railway.app/api/documentation` |
| **Health Check** | `https://tu-dominio.up.railway.app` |

---

## ❓ Problemas Comunes

### "Build Failed"
- Verifica que el `Dockerfile` esté en la raíz
- Revisa los logs de build

### "Application Error"
- Verifica las variables de entorno
- Asegúrate que `APP_KEY` esté configurada
- Revisa los logs de tu servicio

### "Database Connection Failed"
- Verifica que uses `mysql.railway.internal`
- Confirma que el password sea correcto
- Asegúrate que ambos servicios estén en el mismo proyecto

---

## 🎯 Checklist Final

- [ ] Código en GitHub
- [ ] Servicio creado en Railway
- [ ] Variables configuradas
- [ ] Dominio generado y configurado en APP_URL
- [ ] Despliegue exitoso
- [ ] Swagger UI accesible
- [ ] Endpoints funcionando

---

## 📱 Compartir tu API

Ahora puedes compartir:
```
📚 Documentación: https://tu-dominio.up.railway.app/api/documentation
🔗 API Base: https://tu-dominio.up.railway.app/api
```

---

## 🎉 ¡LISTO!

Tu API de Bóveda de Documentos está en producción con:
- ✅ HTTPS automático
- ✅ Despliegue continuo (auto-deploy en cada push)
- ✅ Swagger UI funcionando
- ✅ Base de datos MySQL conectada
- ✅ Logs en tiempo real

---

**Total de tiempo: 5 minutos** ⚡

Para más detalles, consulta: `RAILWAY_DEPLOYMENT.md`
