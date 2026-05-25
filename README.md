# Inventario de Residuos IECF

Aplicación web en HTML5, CSS, JavaScript, PHP y MySQL para registrar mediciones de reciclaje por zona, salón y jornada.

## Instalación rápida

1. Copia esta carpeta dentro de `htdocs` si usas XAMPP.
2. Crea la base importando `database.sql` desde phpMyAdmin o MySQL.
3. Revisa la conexión en `includes/config.php`.
4. Abre `http://localhost/ReciclajedeAulas/`.

## Usuarios de ejemplo

- `software` / `software123`: aprendiz de software, solo registra mediciones.
- `monitoreo` / `monitoreo123`: aprendiz de monitoreo, solo registra mediciones.
- `jefe` / `jefe123`: jefe de proyecto, accede al panel, usuarios, edición y eliminación.

## Módulos

- Inicio de sesión por roles.
- Registro de mediciones de residuos.
- Clasificación automática del residuo.
- Panel con indicadores, gráficos y mapa por zonas.
- CRUD de mediciones para el jefe de proyecto.
- Administración de usuarios para el jefe de proyecto.
