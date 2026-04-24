<?php
/**
 * TECNOTERRAZAS REDESIGN
 * Snippet 1: CPT + Taxonomia + ACF
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

function tt_redesign_register_cpt() {
	$labels = array(
		'name'               => 'Proyectos',
		'singular_name'      => 'Proyecto',
		'menu_name'          => 'Proyectos TT',
		'add_new'            => 'Anadir nuevo',
		'add_new_item'       => 'Anadir nuevo proyecto',
		'edit_item'          => 'Editar proyecto',
		'new_item'           => 'Nuevo proyecto',
		'view_item'          => 'Ver proyecto',
		'search_items'       => 'Buscar proyectos',
		'not_found'          => 'No se encontraron proyectos',
		'not_found_in_trash' => 'No hay proyectos en la papelera',
		'all_items'          => 'Todos los proyectos',
	);

	$args = array(
		'labels'             => $labels,
		'public'             => true,
		'publicly_queryable' => true,
		'show_ui'            => true,
		'show_in_menu'       => true,
		'query_var'          => true,
		'rewrite'            => array(
			'slug'       => 'proyectos',
			'with_front' => false,
		),
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
add_action( 'init', 'tt_redesign_register_cpt' );

function tt_redesign_register_taxonomy() {
	$labels = array(
		'name'              => 'Tipos de Proyecto',
		'singular_name'     => 'Tipo de Proyecto',
		'search_items'      => 'Buscar tipos',
		'all_items'         => 'Todos los tipos',
		'edit_item'         => 'Editar tipo',
		'update_item'       => 'Actualizar tipo',
		'add_new_item'      => 'Anadir nuevo tipo',
		'new_item_name'     => 'Nuevo tipo',
		'menu_name'         => 'Tipos de Proyecto',
	);

	register_taxonomy(
		'tipo_proyecto',
		array( 'proyecto' ),
		array(
			'hierarchical'      => true,
			'labels'            => $labels,
			'show_ui'           => true,
			'show_admin_column' => true,
			'query_var'         => true,
			'rewrite'           => array( 'slug' => 'tipo-proyecto' ),
			'show_in_rest'      => true,
		)
	);
}
add_action( 'init', 'tt_redesign_register_taxonomy' );

function tt_redesign_create_default_terms() {
	$terms = array(
		'Pergolas',
		'Cerramientos',
		'Cortinas de Cristal',
		'Techos Moviles',
	);

	foreach ( $terms as $term ) {
		if ( ! term_exists( $term, 'tipo_proyecto' ) ) {
			wp_insert_term( $term, 'tipo_proyecto' );
		}
	}
}
add_action( 'init', 'tt_redesign_create_default_terms' );

function tt_redesign_register_acf_fields() {
	if ( ! function_exists( 'acf_add_local_field_group' ) ) {
		return;
	}

	acf_add_local_field_group(
		array(
			'key'    => 'group_tt_redesign_proyecto',
			'title'  => 'Detalles del Proyecto - Redesign',
			'fields' => array(
				array(
					'key'       => 'field_tt_tab_galeria',
					'label'     => 'Galeria',
					'name'      => '',
					'type'      => 'tab',
					'placement' => 'top',
				),
				array(
					'key'           => 'field_tt_galeria',
					'label'         => 'Galeria del Proyecto',
					'name'          => 'galeria_proyecto',
					'type'          => 'gallery',
					'return_format' => 'array',
					'preview_size'  => 'medium',
					'library'       => 'all',
					'min'           => 0,
					'max'           => 20,
				),
				array(
					'key'       => 'field_tt_tab_info',
					'label'     => 'Informacion',
					'name'      => '',
					'type'      => 'tab',
					'placement' => 'top',
				),
				array(
					'key'   => 'field_tt_cliente',
					'label' => 'Cliente',
					'name'  => 'cliente_nombre',
					'type'  => 'text',
				),
				array(
					'key'   => 'field_tt_ubicacion',
					'label' => 'Ubicacion',
					'name'  => 'ubicacion_proyecto',
					'type'  => 'text',
				),
				array(
					'key'           => 'field_tt_ano',
					'label'         => 'Ano',
					'name'          => 'ano_proyecto',
					'type'          => 'number',
					'default_value' => gmdate( 'Y' ),
				),
				array(
					'key'       => 'field_tt_tab_specs',
					'label'     => 'Especificaciones',
					'name'      => '',
					'type'      => 'tab',
					'placement' => 'top',
				),
				array(
					'key'           => 'field_tt_servicio',
					'label'         => 'Servicio',
					'name'          => 'servicio_tipo',
					'type'          => 'select',
					'choices'       => array(
						'Instalacion'   => 'Instalacion',
						'Diseno'        => 'Diseno',
						'Remodelacion'  => 'Remodelacion',
						'Mantenimiento' => 'Mantenimiento',
					),
					'default_value' => 'Instalacion',
				),
				array(
					'key'   => 'field_tt_tiempo',
					'label' => 'Tiempo de ejecucion',
					'name'  => 'tiempo_ejecucion',
					'type'  => 'text',
				),
				array(
					'key'   => 'field_tt_metros',
					'label' => 'Metros cuadrados',
					'name'  => 'metros_cuadrados',
					'type'  => 'text',
				),
				array(
					'key'           => 'field_tt_espacio',
					'label'         => 'Tipo de espacio',
					'name'          => 'ubicacion_tipo',
					'type'          => 'select',
					'choices'       => array(
						'Jardin'    => 'Jardin',
						'Terraza'   => 'Terraza',
						'Piscina'   => 'Piscina',
						'Patio'     => 'Patio',
						'Atico'     => 'Atico',
						'Comercial' => 'Comercial',
					),
					'default_value' => 'Terraza',
				),
				array(
					'key'           => 'field_tt_tipo',
					'label'         => 'Tipo',
					'name'          => 'tipo_instalacion',
					'type'          => 'select',
					'choices'       => array(
						'Cortina'     => 'Cortina',
						'Pergola'     => 'Pergola',
						'Techo'       => 'Techo',
						'Cerramiento' => 'Cerramiento',
						'Valla'       => 'Valla',
					),
					'default_value' => 'Pergola',
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
			'active'   => true,
		)
	);
}
add_action( 'acf/init', 'tt_redesign_register_acf_fields' );

function tt_redesign_image_sizes() {
	add_image_size( 'tt-project-card', 900, 640, true );
	add_image_size( 'tt-project-slider', 1600, 900, true );
}
add_action( 'after_setup_theme', 'tt_redesign_image_sizes' );
