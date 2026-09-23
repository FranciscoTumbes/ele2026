# Sistema Electoral CPIS ET (Elecciones 2026)

Sistema de totalización de actas electorales — PHP nativo + MySQL/MariaDB (XAMPP).

## 🚀 Instalación Rápida (XAMPP)

1. Copia la carpeta a `C:\xampp\htdocs\sistema-electoral`
2. Inicia **Apache** y **MySQL** en el panel de XAMPP
3. Importa el archivo **cpiset.sql** (única base oficial; crea las tablas,
   borra y recrea todo automáticamente, e inserta los datos reales):
   ```bash
   C:\xampp\mysql\bin\mysql.exe -u root cpiset < cpiset.sql
   ```
   o desde phpMyAdmin → selecciona la base `cpiset` → Importar → `cpiset.sql`
4. Verifica credenciales en `config/database.php` (por defecto: `root` sin contraseña)
5. Comprueba la conexión ejecutando el diagnóstico:
   ```bash
   php verificar_base.php
   ```
   (o ábrelo en el navegador: `http://localhost/sistema-electoral/verificar_base.php`)
   Debe mostrar `[OK]` en todas las líneas.
6. Accede a: `http://localhost/sistema-electoral/public`

> ⚠️ Los antiguos scripts `BaseGeneral.sql` y `seed_datos_prueba.sql` fueron
> **eliminados del repositorio** por contener errores de claves foráneas y
> truncamientos. Usa únicamente `cpiset.sql`.

## 🔑 Usuarios de prueba

| Usuario     | Rol        |
|-------------|------------|
| `admin1`    | ADMIN      |
| `Prueba`    | ADMIN      |
| `Francisco` | ADMIN      |

(Contraseñas bcrypt existentes en la base; no se modifican al reimportar.)

## 🗂️ Estructura

```
config/        database.php, cors.php
controllers/   AuthController, ActaController, CatalogoController, UbicacionController
core/          Router, SecureSession, Response
middleware/    AuthMiddleware, AdminMiddleware, JuradoMiddleware
models/        Usuario, Eleccion, Acta, Candidato, Ubicacion/{Region,Provincia,Distrito}
views/         login, dashboard, plantillas
public/        index.php (front controller), .htaccess
cpiset.sql     Base de datos oficial (estructura + datos)
verificar_base.php  Diagnóstico de conexión a la BD
```

## ✅ Solución de problemas frecuentes

- **"No se puede conectar / Access denied"** → El servicio MySQL está detenido
  o `config/database.php` tiene otra contraseña. Alinea ambos.
- **Error 404 en rutas** → Habilita `mod_rewrite` en Apache
  (`httpd.conf`: quita el `#` de `LoadModule rewrite_module`) y reinicia.
- **Tablas vacías tras importar** → Vuelve a importar `cpiset.sql` sobre la
  base `cpiset` existente (el script incluye `DROP TABLE`).
