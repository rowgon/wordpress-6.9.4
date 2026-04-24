# Tecnoterrazas WordPress Kit

Esta carpeta es una base nueva y separada para reconstruir `Home`, `Proyectos` y `Single Proyecto` sin tocar la carpeta original `tecnoterrazas-templates`.

## Estructura

```text
tecnoterrazas-wordpress-kit/
├── plugin/
│   └── tecnoterrazas-core/
│       ├── assets/
│       │   ├── css/frontend.css
│       │   └── js/frontend.js
│       ├── includes/class-tt-core.php
│       └── tecnoterrazas-core.php
└── templates/
    └── build-notes.md
```

## Que incluye esta version

- Plugin propio para `CPT proyecto`
- Taxonomia `tipo_proyecto`
- Campos ACF locales
- Shortcode `[tt_projects_grid per_page="9"]`
- Shortcode `[tt_presupuesto proyecto="..."]`
- AJAX ya desacoplado de `Code Snippets`
- Validacion backend de privacidad y un honeypot antispam basico
- CSS frontend base para grid y formulario

## Instalacion recomendada

1. Copia `plugin/tecnoterrazas-core` dentro de `wp-content/plugins/`.
2. Activa `Tecnoterrazas Core`.
3. Si usas ACF Pro, el grupo de campos aparecera automaticamente en `proyecto`.
4. Guarda los enlaces permanentes una vez activado.
5. Crea o ajusta las paginas con Elementor usando las notas de `templates/build-notes.md`.

## Objetivo de esta carpeta

Esta carpeta no intenta conservar el flujo viejo de snippets manuales. La idea es dejar una base mas limpia para reconstruir las paginas a partir de las referencias visuales:

- `Home`
- `Nuestros Proyectos`
- `Ficha individual de proyecto`

## Siguiente iteracion recomendada

1. Crear `Header` y `Footer` globales en Elementor Theme Builder.
2. Montar `Single Proyecto` con Dynamic Tags de ACF.
3. Construir `Home` fiel a la captura, reutilizando los shortcodes y el estilo base donde convenga.
4. Convertir el CTA y el bloque de contacto en secciones reutilizables.
