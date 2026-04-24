# Tecnoterrazas Elementor Redesign Kit

Kit nuevo y separado para recrear en WordPress las 3 paginas del rediseño sin tocar la carpeta anterior ni la web en produccion.

## Contenido

```text
tecnoterrazas-elementor-redesign-kit/
├── elementor-templates/
│   ├── tt-redesign-home.json
│   ├── tt-redesign-proyectos.json
│   └── tt-redesign-single-proyecto.json
├── code-snippets/
│   ├── snippet-1-cpt-acf.php
│   ├── snippet-2-dynamic-components.php
│   └── snippet-3-redesign-css.css
└── assets/images/
```

## Objetivo

- `Home` lo mas parecida posible a la captura `Home.png`
- `Proyectos` lo mas parecida posible a la captura de archivo
- `Single Proyecto` usando Elementor + ACF + Code Snippets sin tocar produccion

## Flujo recomendado

1. Importa `snippet-1-cpt-acf.php` en Code Snippets.
2. Importa `snippet-2-dynamic-components.php` en Code Snippets.
3. Importa `snippet-3-redesign-css.css` en Code Snippets como CSS frontend.
4. Guarda enlaces permanentes.
5. Importa las plantillas JSON en Elementor.
6. Asigna manualmente las imagenes de cada bloque despues de importar.

## Notas importantes

- El `single` esta pensado para funcionar con widgets de shortcode dentro de Elementor.
- No depende de Dynamic Tags complejas para que la importacion sea mas estable.
- Los shortcodes dinamicos principales son:
  - `[tt_projects_grid per_page="9"]`
  - `[tt_presupuesto]`
  - `[tt_project_gallery]`
  - `[tt_project_title]`
  - `[tt_project_content]`
  - `[tt_project_details]`
  - `[tt_project_specs]`
  - `[tt_project_contact_card]`

## Ajustes manuales despues de importar

- Reasignar imagenes de hero, servicios, trayectoria y CTA.
- Revisar enlaces del menu/botones.
- Ajustar tipografia si la web no carga `Montserrat`.
- En el `Single Proyecto`, crear la plantilla en Theme Builder y aplicar condicion a `proyecto`.
