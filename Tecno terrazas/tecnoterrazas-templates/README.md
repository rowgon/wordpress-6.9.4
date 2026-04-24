# 📦 Tecnoterrazas - Guía de Instalación

## Pre-requisitos
- WordPress instalado y funcionando
- **Elementor Pro** activo
- **ACF Pro** activo
- **Code Snippets Pro** activo

---

## Paso 1: Instalar Code Snippets

### Snippet 1 - CPT y ACF Fields
1. Ir a **Code Snippets → Añadir Nuevo**
2. Título: `Tecnoterrazas - CPT y ACF`
3. Copiar el contenido de `code-snippets/snippet-1-cpt-acf.php`
4. Tipo: **PHP** → Ejecutar en: **Todas partes**
5. **Activar** el snippet
6. Ir a **Ajustes → Enlaces permanentes → Guardar cambios** (esto actualiza las URLs del CPT)

✅ Verificación: Debería aparecer "Proyectos TT" en el menú lateral del admin con las categorías: Pérgolas, Cerramientos, Cortinas de Cristal, Techos Móviles.

### Snippet 2 - AJAX y Formulario
1. **Code Snippets → Añadir Nuevo**
2. Título: `Tecnoterrazas - AJAX y Formulario`
3. Copiar el contenido de `code-snippets/snippet-2-ajax-form.php`
4. Tipo: **PHP** → Ejecutar en: **Todas partes**
5. **Activar**

✅ Verificación: Los shortcodes `[tt_projects_grid]` y `[tt_presupuesto]` estarán disponibles.

### Snippet 3 - CSS Custom
1. **Code Snippets → Añadir Nuevo**
2. Título: `Tecnoterrazas - CSS Custom`
3. Copiar el contenido de `code-snippets/snippet-3-custom-css.css`
4. Tipo: **CSS** (o HTML con tags `<style>`)
5. Ubicación: **Frontend**
6. **Activar**

> **Nota**: Si Code Snippets no soporta CSS directo, envuelve el contenido en:
> ```html
> <style>
> /* pegar aquí todo el CSS */
> </style>
> ```
> Y selecciona tipo **HTML** → Ubicación: **Header del sitio**

---

## Paso 2: Importar Templates de Elementor

### Template Home
1. Ir a **Elementor → Templates → Saved Templates**
2. Click en **Importar Templates**
3. Subir `elementor-templates/tecnoterrazas-home.json`
4. Crear nueva página: "Home Tecnoterrazas" (o el nombre que prefieras)
5. Editar con Elementor → Click en carpeta (📁) → **Mis Templates** → Insertar "Tecnoterrazas - Home"
6. **Ajustar**: 
   - Subir imagen de fondo del Hero (usar la imagen generada o una propia)
   - Subir imágenes en las cards de servicios
   - Ajustar textos según necesidad
   - Configurar link del botón CTA

### Template Proyectos (Listado)
1. Importar `elementor-templates/tecnoterrazas-proyectos.json` igual que arriba
2. Crear nueva página: "Nuestros Proyectos"
3. Insertar el template
4. **Ajustar**:
   - Subir imagen de fondo del Hero banner
   - El grid se carga automáticamente via el shortcode `[tt_projects_grid]`

### Template Single Proyecto
Para el detalle de cada proyecto, tienes dos opciones:

#### Opción A: Usar como página modelo (recomendado para empezar)
1. Importar `elementor-templates/tecnoterrazas-single-proyecto.json`
2. Usar como referencia visual al crear cada proyecto individualmente

#### Opción B: Theme Builder (uso avanzado con Dynamic Tags)
1. Ir a **Elementor → Theme Builder → Single**
2. Añadir nuevo → Nombre: "Single Proyecto"
3. Construir el layout usando los widgets y Dynamic Tags de ACF:
   - **Image Carousel** → Dynamic Tag: ACF Gallery → `galeria_proyecto`
   - **Heading** → Dynamic Tag: Post Title
   - **Text Editor** → Dynamic Tag: Post Content
   - **Sidebar detalles**: Text widgets con Dynamic Tags:
     - ACF Text → `cliente_nombre`
     - ACF Text → `ubicacion_proyecto`
     - ACF Number → `ano_proyecto`
   - **Specs row**: Icon Boxes con Dynamic Tags:
     - ACF Select → `servicio_tipo`
     - ACF Text → `tiempo_ejecucion`
     - ACF Text → `metros_cuadrados`
     - ACF Select → `ubicacion_tipo`
     - ACF Select → `tipo_instalacion`
   - **Formulario**: Shortcode widget → `[tt_presupuesto]`
4. **Condiciones de display**: Post Type → proyecto
5. Publicar

---

## Paso 3: Crear Proyectos de Prueba

1. Ir a **Proyectos TT → Añadir Nuevo**
2. Título: "Cortina de Cristal Corredera con Pérgola Bioclimática"
3. Contenido (editor): Descripción detallada del proyecto
4. **Imagen destacada**: Subir foto principal
5. **Campos ACF** (aparecen debajo del editor):
   - Galería: Subir 3-5 imágenes del proyecto
   - Cliente: "Juan L."
   - Ubicación: "Valencia"
   - Año: 2024
   - Servicio: Instalación
   - Tiempo: "4 Semanas"
   - Metros: "6MT²"
   - Espacio: Jardín
   - Tipo: Cortina
6. **Tipo de Proyecto** (sidebar derecho): Seleccionar "Cortinas de Cristal"
7. Publicar

Repetir para crear más proyectos de demo.

---

## Paso 4: Configurar Menú

1. Ir a **Apariencia → Menús**
2. Agregar las nuevas páginas al menú:
   - Home Tecnoterrazas
   - Nuestros Proyectos
3. Guardar menú

---

## Estructura de Archivos

```
tecnoterrazas-templates/
├── code-snippets/
│   ├── snippet-1-cpt-acf.php          # CPT + taxonomía + ACF fields
│   ├── snippet-2-ajax-form.php        # AJAX + formulario + shortcodes
│   └── snippet-3-custom-css.css       # Design system CSS completo
├── elementor-templates/
│   ├── tecnoterrazas-home.json        # Home page template
│   ├── tecnoterrazas-proyectos.json   # Projects archive template
│   └── tecnoterrazas-single-proyecto.json  # Single project template
├── assets/
│   └── images/                        # Imágenes generadas con IA
└── README.md                          # Esta guía
```

---

## Shortcodes Disponibles

| Shortcode | Uso | Parámetros |
|-----------|-----|------------|
| `[tt_projects_grid]` | Grid de proyectos con filtros AJAX | `per_page="9"` |
| `[tt_presupuesto]` | Formulario de presupuesto | `proyecto="nombre"` |

---

## Rollback (Deshacer)

Si algo falla, simplemente:
1. **Desactivar** los 3 snippets en Code Snippets
2. **Eliminar** las páginas creadas
3. **Eliminar** los templates importados en Elementor

**No se modificó nada del sitio existente.**

---

## Notas Importantes

- Las imágenes en `assets/images/` son generadas con IA para demo. Reemplázalas con fotos reales.
- El formulario envía email al admin de WordPress. Para cambiar el destinatario, edita el Snippet 2.
- Los colores se pueden ajustar cambiando las variables CSS en el Snippet 3 (sección `:root`).
- La fuente es Montserrat de Google Fonts. Se carga automáticamente si Elementor la usa. Si no, añade en el Snippet 3 (HTML): `<link href="https://fonts.googleapis.com/css2?family=Montserrat:wght@300;400;500;600;700&display=swap" rel="stylesheet">`
