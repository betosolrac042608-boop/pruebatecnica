# 🌾 Sistema de Gestión Agrícola

[![Laravel](https://img.shields.io/badge/Laravel-10-red)](https://laravel.com)
[![Filament](https://img.shields.io/badge/Filament-3.2-orange)](https://filamentphp.com)
[![PHP](https://img.shields.io/badge/PHP-8.1+-blue)](https://php.net)
[![License](https://img.shields.io/badge/license-MIT-green)](LICENSE)

Sistema completo de gestión agrícola con panel administrativo desarrollado con **Laravel 10** y **Filament 3**. Incluye gestión de animales, cultivos, herramientas, actividades y planificación de tareas.

## ✨ Características Principales

### 📊 Dashboard Profesional
- Estadísticas en tiempo real con gráficos
- Widgets informativos (animales activos, cultivos, herramientas)
- Actividades recientes y próximas acciones
- Diseño moderno y responsivo

### 🎯 Módulos del Sistema

#### Gestión de Activos
- 🏠 **Animales**: Control de ganado con seguimiento sanitario
- ✨ **Cultivos**: Gestión de siembra, cosecha, fertilización y riego
- 🔧 **Herramientas**: Inventario de maquinaria con programación de mantenimiento

#### Gestión Operativa
- ✅ **Actividades**: Registro completo de tareas realizadas
- 📅 **Acciones Programadas**: Calendario y planificación de tareas futuras

#### Reportes
- 📊 **Reportes de Actividad**: Generación de reportes con filtros avanzados y exportación a CSV

#### Administración
- 👥 **Usuarios**: Control de acceso con roles
- 🛡️ **Roles**: Sistema de permisos personalizable
- 📋 **Catálogos**: Estados y tipos de acciones

### 🚀 Funcionalidades

- ✅ CRUD completo para todos los módulos
- ✅ Validaciones exhaustivas en formularios
- ✅ Búsqueda y filtros avanzados
- ✅ Acciones masivas (bulk actions)
- ✅ Notificaciones del sistema
- ✅ Soft deletes con restauración
- ✅ **Reportes con exportación a CSV**
- ✅ **Filtros por rango de fechas**
- ✅ **Filtros por tipo de acción y estado**
- ✅ API REST completa
- ✅ Diseño responsive (móvil, tablet, desktop)
- ✅ Interfaz en español
- ✅ Login seguro con verificación de email

## 📋 Requisitos

- PHP 8.1 o superior
- Composer
- MySQL 5.7+ o PostgreSQL
- Node.js y NPM
- Extensiones PHP: PDO, mbstring, openssl, json, bcmath

## 🚀 Instalación

### Configuración Inicial

**1. Habilitar extensión ZIP en PHP:**
- Abre `C:\xampp\php\php.ini`
- Busca `;extension=zip` y quita el `;`
- Reinicia Apache en XAMPP

**2. Instalar dependencias:**
```cmd
composer install
npm install
```

**3. Configurar entorno:**
```cmd
copy .env.example .env
php artisan key:generate
```

Edita `.env`:
```env
DB_DATABASE=pruebatecnica
DB_USERNAME=root
DB_PASSWORD=
```

**4. Crear base de datos:**
- Abre phpMyAdmin: http://localhost/phpmyadmin
- Crea base de datos: `pruebatecnica`

**5. Migrar y poblar:**
```cmd
php artisan migrate --seed
```

**6. Compilar assets:**
```cmd
npm run build
```

**7. Iniciar servidor:**
```cmd
php artisan serve
```

**Acceder:** http://127.0.0.1:8000/admin

## 🔑 Credenciales

**Email:** admin@sistema.com  
**Password:** password

## ⚠️ Solución de Errores

**CSS no se muestra:**
```cmd
npm run build
php artisan optimize:clear

## Plan de trabajo diario con IA

Este proyecto ahora genera un plan diario por predio enviando las tareas por zona al modelo GPT (OpenAI). Para activar la integración debes configurar la clave en el archivo `.env`:

```dotenv
OPENAI_API_KEY=sk-xxxx…
OPENAI_MODEL=gpt-3.5-turbo
OPENAI_VERIFY_SSL=false
```

La clave se utiliza en `ChatGptService`, y el modelo por defecto es `gpt-3.5-turbo` (puedes cambiarlo a `gpt-4-turbo` si tienes acceso). Si la clave no está disponible el sistema vuelve a un plan básico local.

**Nota:** El sistema usa `gpt-3.5-turbo` por defecto y no incluye parámetros de temperatura ni max_tokens para permitir que el modelo use sus valores predeterminados optimizados.

Cuando el administrador crea un `PlanTrabajoDiario`, el job `EnviarPlanTrabajoAGptJob` envía automáticamente las tareas por zona a ChatGPT y guarda el JSON resultante en la base. Si deseas crear el plan por API:

```bash
curl -X POST /api/plan-trabajo/generar \
  -H "Authorization: Bearer $TOKEN" \
  -d "predio_id=1&fecha=2025-11-25&rol_encargado=Eduardo&turno_inicio=07:30&turno_fin=18:00&comida_inicio=14:00&comida_fin=15:30"
```

Los roles `supervisor` y `operario` ven los planes, pero solo el admin puede crearlos. Luego puedes subir fotos (antes y después) con `/api/plan-trabajo/foto` y registrar evaluaciones de IA con `/api/plan-trabajo/evaluacion`.
```

**Error de base de datos:**
```cmd
php artisan migrate:fresh --seed
php artisan optimize:clear
```

## 🔌 API REST

**Base URL:** `http://127.0.0.1:8000/api/v1`

**Endpoints:** animales, cultivos, herramientas, actividades, acciones-programadas  
**Métodos:** GET, POST, PUT, DELETE

## 📊 Módulo de Reportes

**Ruta:** /admin/reportes-actividad

**Características:**
- Filtros: rango de fechas, tipo de acción, estado, responsable
- Exportación completa a CSV
- Exportación de registros seleccionados
- Estadísticas en tiempo real

## 📦 Módulos del Sistema

**Gestión de Activos:**
- Animales (control sanitario, peso, ubicación)
- Cultivos (siembra, cosecha, riego, fertilización)
- Herramientas (inventario, mantenimiento, responsables)

**Gestión Operativa:**
- Actividades (registro de tareas realizadas)
- Acciones Programadas (calendario de tareas futuras)

**Reportes:**
- Reportes de Actividad (filtros avanzados + exportación CSV)

**Administración:**
- Usuarios (roles y permisos)
- Catálogos (roles, estados, tipos de acción)

## 🛠️ Stack

Laravel 10 • Filament 3.2 • MySQL • Tailwind CSS • Livewire

## 🔧 Comandos

```cmd
php artisan optimize:clear          # Limpiar caché
php artisan migrate:fresh --seed    # Reiniciar BD
npm run build                       # Compilar CSS
```

---

**Desarrollado con Laravel y Filament** • Prueba Técnica Sistema Agrícola

