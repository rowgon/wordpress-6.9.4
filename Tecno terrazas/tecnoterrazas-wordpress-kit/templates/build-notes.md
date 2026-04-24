# Build Notes

Estas notas sirven como guia de recreacion en Elementor sin depender de la carpeta original.

## 1. Home

### Secciones recomendadas

1. `Header` con logo, menu y CTA `Presupuesto`.
2. `Hero split` a dos columnas:
   - izquierda con imagen grande
   - derecha con fondo azul, titular grande y CTA
3. `Trayectoria`:
   - imagen vertical a la izquierda
   - bloque editorial a la derecha
4. `Servicios destacados`:
   - grid visual de 5 tarjetas
5. `Soluciones`:
   - imagen tecnica a la izquierda
   - listado numerado de categorias a la derecha
6. `Proceso / Como trabajamos`:
   - fondo con overlay azul
   - 3 columnas con pasos
7. `Testimonios`
8. `Formulario + contacto`
9. `Footer`

## 2. Proyectos

### Piezas clave

1. Hero con imagen de fondo y titulo centrado.
2. Filtros por taxonomia `tipo_proyecto`.
3. Grid dinamico usando:

```text
[tt_projects_grid per_page="9"]
```

4. CTA final con fondo fotografico.

## 3. Single Proyecto

### Modelo dinamico

1. Galeria superior:
   - usar ACF `galeria_proyecto`
2. Contenido principal:
   - `Post Title`
   - `Post Content`
3. Caja lateral:
   - `cliente_nombre`
   - `ubicacion_proyecto`
   - `ano_proyecto`
4. Fila de especificaciones:
   - `servicio_tipo`
   - `tiempo_ejecucion`
   - `metros_cuadrados`
   - `ubicacion_tipo`
   - `tipo_instalacion`
5. Formulario:

```text
[tt_presupuesto]
```

6. Bloque de ubicacion/contacto.

## Recomendacion tecnica

- Usar este kit nuevo como base funcional.
- No mezclar esta implementacion con los snippets viejos.
- Mantener `header`, `footer` y `single proyecto` en Theme Builder para que todo sea reutilizable.
