# Guía de Integración para App iOS (Swift)

Esta guía detalla cómo conectar tu aplicación iOS (Swift) con la API de "Bóveda de Documentos".

## 1. Configuración Inicial

**Base URL:**
Si usas Railway, tu URL base será algo como:
`https://neozama-production.up.railway.app/api`

**Librería Recomendada:**
Se recomienda usar **Alamofire** para manejar las peticiones HTTP, ya que facilita el manejo de cabeceras y Multipart (subida de archivos).

---

## 2. Autenticación (Login)

Para acceder a los datos, primero debes obtener un **Token**.

*   **Endpoint:** `POST /login`
*   **Body (JSON):**
    ```json
    {
        "email": "usuario@ejemplo.com",
        "password": "password123",
        "device_name": "iPhone 13"
    }
    ```
*   **Respuesta Exitosa (200 OK):**
    ```json
    {
        "token": "1|laravel_sanctum_token_string..."
    }
    ```
*   **Acción en Swift:** Guarda este `token` en `UserDefaults` o `Keychain`. Lo necesitarás para todas las siguientes peticiones.

---

## 3. Peticiones Autenticadas

Para todas las siguientes peticiones, debes enviar el token en la cabecera **Authorization**.

**Header:**
`Authorization: Bearer <TU_TOKEN>`

### A. Listar Documentos
*   **Endpoint:** `GET /documents`
*   **Parámetros Opcionales:**
    *   `search`: Buscar por título (ej: `?search=contrato`)
    *   `category_id`: Filtrar por categoría (ej: `?category_id=1`)
*   **Respuesta (Array JSON):**
    ```json
    [
        {
            "id": 1,
            "title": "Contrato",
            "file_path": "documents/xyz.pdf",
            "category": { "id": 1, "name": "Legal" }
        },
        ...
    ]
    ```

### B. Subir Documento (Multipart)
Esta es la parte más compleja. Debes enviar el archivo como `multipart/form-data`.

*   **Endpoint:** `POST /documents`
*   **Headers:**
    *   `Authorization: Bearer <TOKEN>`
    *   `Content-Type: multipart/form-data`
*   **Campos (Form Data):**
    *   `file`: (El archivo binario, ej: PDF o Imagen)
    *   `title`: "Mi Documento" (String)
    *   `description`: "Descripción opcional" (String)
    *   `category_id`: 1 (Int)
    *   `is_public`: 0 (Int/Bool, 0 = Privado)

**Ejemplo con Alamofire (Swift):**
```swift
let headers: HTTPHeaders = [
    "Authorization": "Bearer \(token)",
    "Accept": "application/json"
]

AF.upload(multipartFormData: { multipartFormData in
    multipartFormData.append(fileData, withName: "file", fileName: "doc.pdf", mimeType: "application/pdf")
    multipartFormData.append("Mi Título".data(using: .utf8)!, withName: "title")
    multipartFormData.append("\(categoryId)".data(using: .utf8)!, withName: "category_id")
}, to: "https://.../api/documents", headers: headers)
.responseJSON { response in
    debugPrint(response)
}
```

### C. Descargar Documento
*   **Endpoint:** `GET /documents/{id}/download`
*   **Acción:** Esta URL devuelve el flujo de bytes del archivo. Puedes usarla para mostrar un PDF en un `PDFView` o guardarlo en el dispositivo.

---

## 4. Manejo de Errores

La API devuelve códigos de estado estándar:
*   **200/201:** Éxito.
*   **401 Unauthorized:** El token es inválido o expiró. -> **Redirigir a Login**.
*   **422 Unprocessable Entity:** Error de validación (ej: falta el título o el archivo es muy pesado). Revisa el cuerpo de la respuesta para ver los mensajes de error.
*   **500 Server Error:** Error interno del servidor.
