# 📁 Bóveda de Documentos - API REST

[![Laravel](https://img.shields.io/badge/Laravel-12.x-red.svg)](https://laravel.com)
[![PHP](https://img.shields.io/badge/PHP-8.2+-blue.svg)](https://php.net)
[![Swagger](https://img.shields.io/badge/Swagger-OpenAPI-green.svg)](https://swagger.io)

Sistema profesional de gestión de documentos desarrollado con Laravel que permite almacenar, organizar, compartir y descargar archivos de forma segura mediante una API RESTful completamente documentada con Swagger.

## 🚀 Características Principales

- ✅ **API RESTful completa** con 18 endpoints
- ✅ **Autenticación segura** con Laravel Sanctum (API Tokens)
- ✅ **CRUD completo** de documentos y categorías
- ✅ **Subida y descarga de archivos** con validación
- ✅ **Sistema de categorías** para organizar documentos
- ✅ **Compartir documentos** con control de permisos (view, edit, download)
- ✅ **Documentos públicos/privados**
- ✅ **Soft deletes** (papelera de reciclaje)
- ✅ **Búsqueda y filtros** avanzados
- ✅ **Documentación automática con Swagger/OpenAPI** 📚
- ✅ **Validaciones robustas** con Form Requests
- ✅ **Colección Postman** incluida

## 📋 Requisitos

- PHP >= 8.2
- Composer
- MySQL/PostgreSQL
- Laravel 12.x

## ⚙️ Instalación Rápida

### Opción 1: Script automático (Windows)
```bash
.\install.bat
```

### Opción 2: Script automático (Linux/Mac)
```bash
chmod +x install.sh
./install.sh
```

### Opción 3: Instalación manual

1. **Instalar dependencias:**
```bash
composer install
```

2. **Configurar entorno:**
```bash
cp .env.example .env
php artisan key:generate
```

3. **Configurar base de datos en `.env`:**
```env
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=boveda_documentos
DB_USERNAME=root
DB_PASSWORD=tu_password
```

4. **Ejecutar migraciones:**
```bash
php artisan migrate
```

5. **(Opcional) Cargar datos de prueba:**
```bash
php artisan db:seed --class=DemoSeeder
```

6. **Generar documentación de Swagger:**
```bash
php artisan l5-swagger:generate
```

7. **Iniciar servidor:**
```bash
php artisan serve
```

## 📚 Documentación Interactiva con Swagger

Una vez iniciado el servidor, accede a la documentación completa de la API en:

### 🌐 http://localhost:8000/api/documentation

Desde Swagger podrás:
- ✅ Ver todos los endpoints disponibles
- ✅ Probar las peticiones directamente desde el navegador
- ✅ Ver ejemplos de request/response
- ✅ Autenticarte con tokens
- ✅ Explorar los esquemas de datos

## 🔐 Autenticación

Todas las rutas (excepto registro y login) requieren autenticación mediante Bearer Token.

### 1. Registrar usuario
```http
POST /api/register
Content-Type: application/json

{
  "name": "Juan Pérez",
  "email": "juan@example.com",
  "password": "password123",
  "password_confirmation": "password123"
}
```

### 2. Iniciar sesión
```http
POST /api/login
Content-Type: application/json

{
  "email": "juan@example.com",
  "password": "password123"
}
```

**Respuesta:**
```json
{
  "token": "1|xxxxxxxxxxxxxxxxxxxxx"
}
```

### 3. Usar el token en las peticiones
```http
Authorization: Bearer {token}
```

## 📝 Endpoints Principales

### Autenticación
- `POST /api/register` - Registrar usuario
- `POST /api/login` - Iniciar sesión
- `POST /api/logout` - Cerrar sesión
- `GET /api/user` - Obtener usuario actual

### Categorías
- `GET /api/categories` - Listar categorías
- `POST /api/categories` - Crear categoría
- `GET /api/categories/{id}` - Ver categoría
- `PUT /api/categories/{id}` - Actualizar categoría
- `DELETE /api/categories/{id}` - Eliminar categoría

### Documentos
- `GET /api/documents` - Listar documentos
- `POST /api/documents` - Subir documento
- `GET /api/documents/{id}` - Ver documento
- `GET /api/documents/{id}/download` - Descargar documento
- `PUT /api/documents/{id}` - Actualizar documento
- `DELETE /api/documents/{id}` - Eliminar documento
- `POST /api/documents/{id}/share` - Compartir documento
- `GET /api/documents/shared` - Documentos compartidos conmigo

## 📤 Ejemplo: Subir un documento

```http
POST /api/documents
Authorization: Bearer {token}
Content-Type: multipart/form-data

title: Contrato 2024
description: Contrato de servicios profesionales
file: [seleccionar archivo]
category_id: 1
is_public: false
```

## 🔍 Ejemplo: Buscar documentos

```http
GET /api/documents?search=contrato&category_id=1
Authorization: Bearer {token}
```

## 🤝 Ejemplo: Compartir documento

```http
POST /api/documents/5/share
Authorization: Bearer {token}
Content-Type: application/json

{
  "user_id": 2,
  "permission": "view"
}
```

Permisos: `view`, `edit`, `download`

## 🗂️ Estructura del Proyecto

```
app/
├── Http/
│   ├── Controllers/Api/
│   │   ├── AuthController.php (con anotaciones Swagger)
│   │   ├── CategoryController.php (con anotaciones Swagger)
│   │   ├── DocumentController.php (con anotaciones Swagger)
│   │   └── SwaggerController.php (esquemas OpenAPI)
│   └── Requests/
│       ├── StoreCategoryRequest.php
│       ├── StoreDocumentRequest.php
│       └── UpdateDocumentRequest.php
├── Models/
│   ├── User.php
│   ├── Category.php
│   └── Document.php
database/
├── migrations/
│   ├── 2024_11_13_000001_create_categories_table.php
│   ├── 2024_11_13_000002_create_documents_table.php
│   └── 2024_11_13_000003_create_document_shares_table.php
└── seeders/
    └── DemoSeeder.php
routes/
└── api.php
```

## 📦 Tecnologías Utilizadas

- **Laravel 12** - Framework PHP moderno
- **Laravel Sanctum** - Autenticación API con tokens
- **L5-Swagger (darkaonline/l5-swagger)** - Documentación OpenAPI/Swagger
- **MySQL** - Base de datos relacional
- **Eloquent ORM** - Mapeo objeto-relacional
