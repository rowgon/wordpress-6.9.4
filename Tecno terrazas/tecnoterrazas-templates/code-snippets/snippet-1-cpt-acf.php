<?php
/**
 * ============================================================
 * TECNOTERRAZAS - Snippet 1: CPT + Taxonomía + ACF Fields
 * ============================================================
 * 
 * Instrucciones:
 * 1. Ir a Code Snippets Pro → Añadir Nuevo
 * 2. Título: "Tecnoterrazas - CPT y ACF"
 * 3. Pegar este código
 * 4. Tipo: PHP → Ejecutar en todas partes
 * 5. Activar
 * 
 * Después de activar, ir a Ajustes → Enlaces permanentes → Guardar
 * (esto actualiza las rewrite rules para el CPT)
 */

// ============================================
// 1. CUSTOM POST TYPE: Proyecto
// ============================================
function tecnoterrazas_register_cpt() {
    $labels = array(
        'name'               => 'Proyectos',
        'singular_name'      => 'Proyecto',
        'menu_name'          => 'Proyectos TT',
        'add_new'            => 'Añadir Nuevo',
        'add_new_item'       => 'Añadir Nuevo Proyecto',
        'edit_item'          => 'Editar Proyecto',
        'new_item'           => 'Nuevo Proyecto',
        'view_item'          => 'Ver Proyecto',
        'search_items'       => 'Buscar Proyectos',
        'not_found'          => 'No se encontraron proyectos',
        'not_found_in_trash' => 'No hay proyectos en la papelera',
        'all_items'          => 'Todos los Proyectos',
    );

    $args = array(
        'labels'             => $labels,
        'public'             => true,
        'publicly_queryable' => true,
        'show_ui'            => true,
        'show_in_menu'       => true,
        'query_var'          => true,
        'rewrite'            => array( 'slug' => 'proyectos', 'with_front' => false ),
        'capability_type'    => 'post',
        'has_archive'        => true,
        'hierarchical'       => false,
        'menu_position'      => 5,
        'menu_icon'          => 'dashicons-portfolio',
        'supports'           => array( 'title', 'editor', 'thumbnail', 'excerpt' ),
        'show_in_rest'       => true,
    );

    register_post_type( 'proyecto', $args );
}
add_action( 'init', 'tecnoterrazas_register_cpt' );

// ============================================
// 2. TAXONOMÍA: Tipo de Proyecto
// ============================================
function tecnoterrazas_register_taxonomy() {
    $labels = array(
        'name'              => 'Tipos de Proyecto',
        'singular_name'     => 'Tipo de Proyecto',
        'search_items'      => 'Buscar Tipos',
        'all_items'         => 'Todos los Tipos',
        'parent_item'       => 'Tipo Padre',
        'parent_item_colon' => 'Tipo Padre:',
        'edit_item'         => 'Editar Tipo',
        'update_item'       => 'Actualizar Tipo',
        'add_new_item'      => 'Añadir Nuevo Tipo',
        'new_item_name'     => 'Nuevo Tipo',
        'menu_name'         => 'Tipos de Proyecto',
    );

    $args = array(
        'hierarchical'      => true,
        'labels'            => $labels,
        'show_ui'           => true,
        'show_admin_column' => true,
        'query_var'         => true,
        'rewrite'           => array( 'slug' => 'tipo-proyecto' ),
        'show_in_rest'      => true,
    );

    register_taxonomy( 'tipo_proyecto', array( 'proyecto' ), $args );
}
add_action( 'init', 'tecnoterrazas_register_taxonomy' );

// ============================================
// 3. CREAR CATEGORÍAS POR DEFECTO
// ============================================
function tecnoterrazas_create_default_terms() {
    $terms = array(
        'Pérgolas',
        'Cerramientos',
        'Cortinas de Cristal',
        'Techos Móviles',
    );

    foreach ( $terms as $term ) {
        if ( ! term_exists( $term, 'tipo_proyecto' ) ) {
            wp_insert_term( $term, 'tipo_proyecto' );
        }
    }
}
add_action( 'init', 'tecnoterrazas_create_default_terms' );

// ============================================
// 4. ACF PRO FIELD GROUP: Detalles del Proyecto
// ============================================
function tecnoterrazas_register_acf_fields() {
    if ( ! function_exists( 'acf_add_local_field_group' ) ) {
        return;
    }

    acf_add_local_field_group( array(
        'key'      => 'group_tt_proyecto_detalles',
        'title'    => 'Detalles del Proyecto - Tecnoterrazas',
        'fields'   => array(
            // --- TAB: Galería ---
            array(
                'key'   => 'field_tt_tab_galeria',
                'label' => 'Galería',
                'name'  => '',
                'type'  => 'tab',
                'placement' => 'top',
            ),
            array(
                'key'           => 'field_tt_galeria',
                'label'         => 'Galería del Proyecto',
                'name'          => 'galeria_proyecto',
                'type'          => 'gallery',
                'instructions'  => 'Sube las imágenes del proyecto. La primera será la principal del slider.',
                'required'      => 0,
                'return_format' => 'array',
                'preview_size'  => 'medium',
                'library'       => 'all',
                'min'           => 0,
                'max'           => 20,
            ),
            // --- TAB: Información del Cliente ---
            array(
                'key'   => 'field_tt_tab_cliente',
                'label' => 'Información',
                'name'  => '',
                'type'  => 'tab',
                'placement' => 'top',
            ),
            array(
                'key'         => 'field_tt_cliente',
                'label'       => 'Nombre del Cliente',
                'name'        => 'cliente_nombre',
                'type'        => 'text',
                'instructions'=> 'Ej: Juan L.',
                'placeholder' => 'Nombre del cliente',
            ),
            array(
                'key'         => 'field_tt_ubicacion',
                'label'       => 'Ubicación del Proyecto',
                'name'        => 'ubicacion_proyecto',
                'type'        => 'text',
                'instructions'=> 'Ej: Valencia',
                'placeholder' => 'Ciudad o zona',
            ),
            array(
                'key'           => 'field_tt_ano',
                'label'         => 'Año de Ejecución',
                'name'          => 'ano_proyecto',
                'type'          => 'number',
                'instructions'  => 'Ej: 2024',
                'default_value' => date('Y'),
                'min'           => 2000,
                'max'           => 2030,
            ),
            // --- TAB: Especificaciones Técnicas ---
            array(
                'key'   => 'field_tt_tab_specs',
                'label' => 'Especificaciones',
                'name'  => '',
                'type'  => 'tab',
                'placement' => 'top',
            ),
            array(
                'key'     => 'field_tt_servicio',
                'label'   => 'Tipo de Servicio',
                'name'    => 'servicio_tipo',
                'type'    => 'select',
                'choices' => array(
                    'Instalación'    => 'Instalación',
                    'Diseño'         => 'Diseño',
                    'Remodelación'   => 'Remodelación',
                    'Mantenimiento'  => 'Mantenimiento',
                ),
                'default_value' => 'Instalación',
            ),
            array(
                'key'         => 'field_tt_tiempo',
                'label'       => 'Tiempo de Ejecución',
                'name'        => 'tiempo_ejecucion',
                'type'        => 'text',
                'placeholder' => 'Ej: 4 Semanas',
            ),
            array(
                'key'         => 'field_tt_metros',
                'label'       => 'Metros Cuadrados',
                'name'        => 'metros_cuadrados',
                'type'        => 'text',
                'placeholder' => 'Ej: 6MT²',
            ),
            array(
                'key'     => 'field_tt_ubicacion_tipo',
                'label'   => 'Tipo de Espacio',
                'name'    => 'ubicacion_tipo',
                'type'    => 'select',
                'choices' => array(
                    'Jardín'    => 'Jardín',
                    'Terraza'   => 'Terraza',
                    'Piscina'   => 'Piscina',
                    'Patio'     => 'Patio',
                    'Ático'     => 'Ático',
                    'Comercial' => 'Comercial',
                ),
                'default_value' => 'Terraza',
            ),
            array(
                'key'     => 'field_tt_tipo_instalacion',
                'label'   => 'Tipo de Instalación',
                'name'    => 'tipo_instalacion',
                'type'    => 'select',
                'choices' => array(
                    'Cortina'      => 'Cortina',
                    'Pérgola'      => 'Pérgola',
                    'Techo'        => 'Techo',
                    'Cerramiento'  => 'Cerramiento',
                    'Valla'        => 'Valla',
                ),
                'default_value' => 'Pérgola',
            ),
        ),
        'location' => array(
            array(
                array(
                    'param'    => 'post_type',
                    'operator' => '==',
                    'value'    => 'proyecto',
                ),
            ),
        ),
        'menu_order'            => 0,
        'position'              => 'normal',
        'style'                 => 'default',
        'label_placement'       => 'top',
        'instruction_placement' => 'label',
        'active'                => true,
    ) );
}
add_action( 'acf/init', 'tecnoterrazas_register_acf_fields' );

// ============================================
// 5. TAMAÑOS DE IMAGEN PERSONALIZADOS
// ============================================
function tecnoterrazas_image_sizes() {
    add_image_size( 'tt-project-card', 600, 400, true );
    add_image_size( 'tt-project-slider', 1200, 700, true );
    add_image_size( 'tt-hero', 1920, 900, true );
}
add_action( 'after_setup_theme', 'tecnoterrazas_image_sizes' );
