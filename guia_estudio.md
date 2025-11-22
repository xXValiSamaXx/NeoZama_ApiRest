# Guía de Estudio - Bóveda de Documentos

Esta guía explica cómo el proyecto cumple con cada uno de los requisitos solicitados por el profesor, indicando qué archivos y componentes son responsables de cada función.

---

## 1. Base de Datos y Migraciones
**Requisito:** "Creación de tablas desde el Framework (con migraciones)... al menos 3 tablas con CRUD."

*   **Concepto:** Laravel usa "Migraciones" como control de versiones para la base de datos. Definimos la estructura en PHP y Laravel crea las tablas SQL.
*   **Dónde está en el código:**
    *   `database/migrations/2024_11_13_000001_create_categories_table.php`: Tabla de Categorías.
    *   `database/migrations/2024_11_13_000002_create_documents_table.php`: Tabla de Documentos (Relación Maestro-Detalle).
    *   `database/migrations/2024_11_13_000003_create_document_shares_table.php`: Tabla pivote para compartir documentos.
    *   `database/migrations/0001_01_01_000000_create_users_table.php`: Tabla de Usuarios (creada por defecto y modificada para añadir `is_admin`).

## 2. Modelos y ORM (Eloquent)
**Requisito:** "Creación de modelos para interactuar con la bd... mediante un ORM como eloquent."

*   **Concepto:** Cada tabla tiene un "Modelo" en PHP. En lugar de escribir SQL (`SELECT * FROM documents`), usamos objetos (`Document::all()`).
*   **Dónde está en el código:**
    *   `app/Models/Document.php`: Define la relación `belongsTo(Category::class)` y `belongsTo(User::class)`.
    *   `app/Models/Category.php`: Define la relación `hasMany(Document::class)`.
    *   `app/Models/User.php`: Gestiona la autenticación y relaciones.

## 3. Controladores y Lógica de Negocio
**Requisito:** "Uso de controladores para los requerimientos o reglas del negocio."

*   **Concepto:** Los controladores reciben la petición del usuario, procesan la lógica (validar, guardar, borrar) y devuelven una respuesta.
*   **Dónde está en el código:**
    *   `app/Http/Controllers/Api/DocumentController.php`: Contiene toda la lógica CRUD (Index, Store, Show, Update, Destroy) para la API.
    *   `app/Http/Controllers/Api/AuthController.php`: Maneja el registro y login de la API.
    *   `app/Http/Controllers/Web/WebController.php`: Lógica para el Panel de Control Web (Dashboard).

## 4. Rutas y API REST
**Requisito:** "Uso de rutas... creación de una API para una tabla que incluya seguridad básica."

*   **Concepto:** Las rutas definen las URLs de tu aplicación (`/api/documents`, `/login`).
*   **Dónde está en el código:**
    *   `routes/api.php`: Define los "endpoints" de la API. Usamos `apiResource` que crea automáticamente las rutas estándar (GET, POST, PUT, DELETE).
    *   `routes/web.php`: Define las rutas para el panel web (login, dashboard).

## 5. Seguridad (Sanctum)
**Requisito:** "Sistema de seguridad básico... uso de un complemento como sanctum."

*   **Concepto:** Protegemos las rutas para que solo usuarios registrados puedan acceder. Usamos "Tokens" para la API.
*   **Dónde está en el código:**
    *   `routes/api.php`: El grupo `middleware('auth:sanctum')` protege las rutas.
    *   `app/Http/Controllers/Api/AuthController.php`: Genera el token con `$user->createToken()`.

## 6. Vistas y Panel de Control
**Requisito:** "Creación de vistas... un panel de control... diseño básico (AdminLTE/Tailwind)."

*   **Concepto:** La interfaz gráfica para el usuario. Usamos Blade (motor de plantillas de Laravel) y Tailwind CSS.
*   **Dónde está en el código:**
    *   `resources/views/layouts/app.blade.php`: La estructura base (menú, pie de página).
    *   `resources/views/dashboard.blade.php`: Pantalla principal con estadísticas.
    *   `resources/views/documents/index.blade.php`: Tabla para gestionar documentos.

## 7. Pruebas Automatizadas (Testing)
**Requisito:** "Realización de pruebas automatizadas... Usar seeder y factories."

*   **Concepto:** Código que prueba tu código. Verifica que el login funcione, que se puedan crear documentos, etc.
*   **Dónde está en el código:**
    *   `tests/Feature/AuthTest.php`: Prueba el registro y login.
    *   `tests/Feature/DocumentTest.php`: Prueba subir y borrar documentos.
    *   `database/factories/DocumentFactory.php`: Genera documentos falsos para pruebas.
    *   `database/seeders/DemoSeeder.php`: Llena la base de datos con datos de prueba iniciales.

---

### Preguntas Frecuentes para el Examen

**P: ¿Dónde está la relación Maestro-Detalle?**
R: En la base de datos entre `categories` (Maestro) y `documents` (Detalle). En el código, en `app/Models/Category.php` (`hasMany`) y `app/Models/Document.php` (`belongsTo`).

**P: ¿Cómo aseguras que un usuario no borre los documentos de otro?**
R: En el `DocumentController`, método `destroy`, verificamos: `if ($document->user_id !== $request->user()->id)`. Además, usamos "Policy" implícita o verificaciones manuales.

**P: ¿Qué hace el comando `php artisan migrate`?**
R: Ejecuta los archivos de `database/migrations` para crear o actualizar las tablas en la base de datos real.
