# 🎬 PeliAnime — Catálogo de películas y animes (Laravel 12)

Proyecto académico en **Laravel 12** para administrar un catálogo de **películas** y **animes**: registro e inicio de sesión
personalizados, recuperación de contraseña, CRUD con imágenes, listados con DataTables, gráficas con Chart.js,
reportes PDF, API REST protegida con tokens (Sanctum) y consumo de servicios web (API propia + Jikan/MyAnimeList).

**Equipo:** Integrante 1 · Integrante 2 · Integrante 3
**Sitio publicado:** `https://TU-PROYECTO.up.railway.app` · **Repositorio:** `https://github.com/USUARIO/pelianime`

---

## 1. Requisitos

| Herramienta | Versión |
|---|---|
| PHP | 8.2 o superior (extensiones: pdo_mysql, mbstring, gd, fileinfo, dom, zip) |
| Composer | 2.x |
| MySQL / MariaDB | 8.x / 10.x (sirve XAMPP, Laragon o MySQL Workbench) |
| Git | cualquiera |

No se necesita Node/npm: Bootstrap, DataTables y Chart.js se cargan por CDN.

## 2. Instalación y ejecución en local

```bash
# 1. Clonar (o descomprimir el .zip) y entrar a la carpeta
git clone https://github.com/USUARIO/pelianime.git
cd pelianime

# 2. Instalar dependencias de PHP
composer install

# 3. Crear el archivo de configuración y la llave de la app
cp .env.example .env          # en Windows (cmd): copy .env.example .env
php artisan key:generate

# 4. Crear la base de datos vacía "pelianime" en MySQL (phpMyAdmin o consola):
#    CREATE DATABASE pelianime CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
#    y revisar DB_USERNAME / DB_PASSWORD en el .env

# 5. Crear las tablas (migraciones) y cargar datos de ejemplo (seeders)
php artisan migrate --seed

# 6. Enlazar la carpeta pública de imágenes subidas
php artisan storage:link

# 7. Levantar el servidor
php artisan serve --no-reload
```

Abrir **http://127.0.0.1:8000**

**Usuario de prueba:** `admin@pelianime.com` / `Admin12345` (o crea uno nuevo en *Crear cuenta*).

> **¿Por qué `--no-reload`?** El módulo *Cliente API REST* hace que Laravel se llame a sí mismo por HTTP.
> El servidor de desarrollo atiende una sola petición a la vez, así que se quedaría esperándose a sí mismo.
> Con `PHP_CLI_SERVER_WORKERS=4` (ya está en `.env.example`) + `--no-reload` se levantan 4 procesos y funciona.
> En el hosting no hace falta.

Para reiniciar la base de datos desde cero: `php artisan migrate:fresh --seed`

### Probar "¿Olvidaste tu contraseña?" en local
Con `MAIL_MAILER=log` el correo no se envía: se escribe al final de `storage/logs/laravel.log`.
Abre ese archivo, copia el enlace `http://127.0.0.1:8000/restablecer-contrasena/...` y pégalo en el navegador.
Para enviar correos reales configura Gmail (ver comentarios en `.env.example`; requiere *contraseña de aplicación*).

---

## 3. Cómo cumple cada requisito

| Requisito | Dónde está |
|---|---|
| Vistas heredadas de maestras | `resources/views/layouts/base.blade.php` (maestra raíz) → `app.blade.php` (panel), `auth.blade.php` (login/registro), `pdf.blade.php` (reportes). Todas las vistas usan `@extends` |
| Base de datos MySQL | `.env` (`DB_CONNECTION=mysql`) |
| Landing page | `resources/views/welcome.blade.php` (ruta `/`) |
| Registro y login personalizados | `app/Http/Controllers/Auth/AuthController.php` + `resources/views/auth/*` (sin Breeze ni Jetstream) |
| Rutas protegidas | `routes/web.php`: grupos `guest` y `auth`; `routes/api.php`: `auth:sanctum`; `throttle` en login. Configuración en `bootstrap/app.php` |
| BD con migraciones | `database/migrations/` (users + campos de perfil, generos, peliculas, animes, tokens) y `database/seeders/` |
| Modelos y controladores coherentes | `app/Models` (User, Genero, Pelicula, Anime con relaciones `belongsTo`/`hasMany`), controladores por recurso, validación en `app/Http/Requests` |
| Restaurar contraseña | `Auth/PasswordResetController.php`, correo en `app/Notifications/RestablecerPassword.php` |
| Dashboard con usuario activo y menú | `resources/views/dashboard/index.blade.php` y el menú en `layouts/app.blade.php` |
| Logout | Menú → *Cerrar sesión* (POST `/logout`, invalida sesión y regenera token CSRF) |
| Listar 2 tablas con DataTables | Menú → *Listar películas* / *Listar animes* (`partials/datatables.blade.php`) |
| Editar perfil del usuario activo | Menú → *Editar perfil* (`ProfileController`): datos, foto y contraseña |
| Chart | Dashboard: barras (títulos por género) y dona (animes por estado) con Chart.js |
| CRUD en 2 tablas | `PeliculaController` y `AnimeController` (`Route::resource`) |
| Ruta API para apps externas | `routes/api.php` — ver sección 4 |
| Informe PDF | Menú → *PDF películas* / *PDF animes* (`ReporteController`, paquete barryvdh/laravel-dompdf) |
| Subir imágenes | Póster de películas, portada de animes y foto de perfil (`storage/app/public`) |
| Consumir RESTful GET/POST/PUT/DELETE | Menú → *Cliente API REST* (`ClienteApiController`, usa `Http::` de Laravel) |
| Tokens, cabeceras y autorización | Tokens Bearer de Sanctum, cabeceras `Authorization`, `Accept`, `X-Cliente`; revocar/regenerar token. Además consume un servicio externo: *Importar de Jikan* (`JikanController`) |

### Modelo de datos

```
users (id, name, email, password, avatar, telefono, bio, ...)
  │ 1
  │──── N peliculas (id, titulo, director, genero_id, anio, duracion_min, calificacion, sinopsis, poster, user_id)
  │──── N animes    (id, titulo, estudio, genero_id, anio, episodios, estado, calificacion, sinopsis, imagen, user_id)
generos (id, nombre) 1 ──── N peliculas / animes
personal_access_tokens  → tokens de la API (Sanctum)
password_reset_tokens   → tokens de "olvidé mi contraseña"
```

---

## 4. API REST

Base: `http://127.0.0.1:8000/api` (en producción: `https://TU-DOMINIO/api`).
Enviar siempre la cabecera `Accept: application/json`. En la carpeta `docs/` hay una **colección de Postman** lista para importar.

| Método | Ruta | Protegida | Descripción |
|---|---|---|---|
| POST | `/api/login` | No | Devuelve `access_token` (body: `email`, `password`) |
| GET | `/api/generos` | No | Lista de géneros |
| GET | `/api/me` | Sí | Usuario dueño del token |
| POST | `/api/logout` | Sí | Revoca el token actual |
| GET | `/api/peliculas` | Sí | Lista (filtros `?buscar=`, `?genero_id=`) |
| GET | `/api/peliculas/{id}` | Sí | Detalle |
| POST | `/api/peliculas` | Sí | Crear |
| PUT | `/api/peliculas/{id}` | Sí | Actualizar (campos parciales) |
| DELETE | `/api/peliculas/{id}` | Sí | Eliminar |
| GET/POST/PUT/DELETE | `/api/animes...` | Sí | Igual que películas |

Ejemplo con curl:

```bash
# 1) Obtener token
curl -X POST http://127.0.0.1:8000/api/login -H "Accept: application/json" \
     -d "email=admin@pelianime.com" -d "password=Admin12345"

# 2) Usar el token
curl http://127.0.0.1:8000/api/animes -H "Accept: application/json" \
     -H "Authorization: Bearer 1|xxxxxxxxxxxxxxxx"

# 3) Crear
curl -X POST http://127.0.0.1:8000/api/peliculas -H "Accept: application/json" \
     -H "Authorization: Bearer 1|xxxxxxxx" -H "Content-Type: application/json" \
     -d '{"titulo":"Dune","director":"Denis Villeneuve","genero_id":3,"anio":2021,"calificacion":8}'
```

Respuestas: `200/201` éxito, `401` sin token o token inválido, `404` no existe, `422` errores de validación.
Para enviar imagen por API usar `POST` con `multipart/form-data` (en `PUT` PHP no lee archivos; usar `POST` + `_method=PUT`).

---

## 5. Publicar en internet (Railway + MySQL)

1. Subir el proyecto a GitHub (sección 6).
2. Entrar a <https://railway.app> con la cuenta de GitHub → **New Project → Deploy from GitHub repo** → elegir `pelianime`.
3. En el mismo proyecto: **New → Database → MySQL**.
4. En el servicio de la app → **Variables**, agregar:
   ```
   APP_NAME=PeliAnime
   APP_ENV=production
   APP_DEBUG=false
   APP_KEY=base64:...        # el que tienes en tu .env local (php artisan key:generate --show)
   APP_URL=https://<tu-dominio>.up.railway.app
   APP_LOCALE=es
   DB_CONNECTION=mysql
   DB_HOST=${{MySQL.MYSQLHOST}}
   DB_PORT=${{MySQL.MYSQLPORT}}
   DB_DATABASE=${{MySQL.MYSQLDATABASE}}
   DB_USERNAME=${{MySQL.MYSQLUSER}}
   DB_PASSWORD=${{MySQL.MYSQLPASSWORD}}
   SESSION_DRIVER=database
   CACHE_STORE=database
   MAIL_MAILER=log           # o la configuración SMTP de Gmail
   ```
5. **Settings → Deploy → Pre-deploy command:** `php artisan migrate --force --seed && php artisan storage:link`
6. **Settings → Networking → Generate Domain** para obtener la URL pública y ponerla en `APP_URL`.

Notas: Railway da créditos de prueba; revisen el plan vigente antes de la sustentación. Los archivos subidos se
guardan en el disco del contenedor y se pierden al redesplegar, salvo que agreguen un **Volume** montado en
`/app/storage/app/public`. Otras opciones: Render (con Docker), Laravel Cloud o un hosting compartido con PHP 8.2+.

## 6. Subir a GitHub

```bash
git init
git add .
git commit -m "Proyecto PeliAnime - Laravel 12"
git branch -M main
git remote add origin https://github.com/USUARIO/pelianime.git
git push -u origin main
```
El `.gitignore` ya excluye `vendor/` y `.env` (nunca subir el `.env` con contraseñas).
Agreguen a los otros integrantes en **Settings → Collaborators** para que cada uno haga commits.

---

## 7. Guion sugerido para la sustentación (15 min)

| Min | Quién | Qué mostrar |
|---|---|---|
| 0-3 | Integrante 1 | Landing, registro, login, olvidé mi contraseña (mostrar el correo), rutas protegidas (entrar a `/dashboard` sin sesión) |
| 3-8 | Integrante 2 | Dashboard (usuario activo, gráficas), CRUD de películas y animes con imágenes, DataTables (buscar/ordenar), PDF, editar perfil |
| 8-13 | Integrante 3 | API en Postman (login → token → GET/POST/PUT/DELETE, 401 sin token), *Cliente API REST* (cabeceras y respuesta), importar desde Jikan |
| 13-15 | Todos | Migraciones/modelos/relaciones en el código, GitHub y preguntas |
