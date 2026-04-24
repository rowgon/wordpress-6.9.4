<?php

namespace TT;

use WP_Query;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class Core {
	public static function boot() {
		add_action( 'init', array( __CLASS__, 'register_post_type' ) );
		add_action( 'init', array( __CLASS__, 'register_taxonomy' ) );
		add_action( 'init', array( __CLASS__, 'create_default_terms' ) );
		add_action( 'acf/init', array( __CLASS__, 'register_acf_fields' ) );
		add_action( 'after_setup_theme', array( __CLASS__, 'register_image_sizes' ) );
		add_action( 'wp_enqueue_scripts', array( __CLASS__, 'register_assets' ) );

		add_shortcode( 'tt_projects_grid', array( __CLASS__, 'projects_grid_shortcode' ) );
		add_shortcode( 'tt_presupuesto', array( __CLASS__, 'presupuesto_shortcode' ) );

		add_action( 'wp_ajax_tt_filter_projects', array( __CLASS__, 'filter_projects' ) );
		add_action( 'wp_ajax_nopriv_tt_filter_projects', array( __CLASS__, 'filter_projects' ) );
		add_action( 'wp_ajax_tt_presupuesto', array( __CLASS__, 'submit_presupuesto' ) );
		add_action( 'wp_ajax_nopriv_tt_presupuesto', array( __CLASS__, 'submit_presupuesto' ) );
	}

	public static function register_post_type() {
		$labels = array(
			'name'               => 'Proyectos',
			'singular_name'      => 'Proyecto',
			'menu_name'          => 'Proyectos TT',
			'add_new'            => 'Anadir Nuevo',
			'add_new_item'       => 'Anadir Nuevo Proyecto',
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

	public static function register_taxonomy() {
		$labels = array(
			'name'              => 'Tipos de Proyecto',
			'singular_name'     => 'Tipo de Proyecto',
			'search_items'      => 'Buscar Tipos',
			'all_items'         => 'Todos los Tipos',
			'parent_item'       => 'Tipo Padre',
			'parent_item_colon' => 'Tipo Padre:',
			'edit_item'         => 'Editar Tipo',
			'update_item'       => 'Actualizar Tipo',
			'add_new_item'      => 'Anadir Nuevo Tipo',
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

	public static function create_default_terms() {
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

	public static function register_acf_fields() {
		if ( ! function_exists( 'acf_add_local_field_group' ) ) {
			return;
		}

		acf_add_local_field_group(
			array(
				'key'    => 'group_tt_proyecto_detalles',
				'title'  => 'Detalles del Proyecto - Tecnoterrazas',
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
						'instructions'  => 'Sube las imagenes del proyecto. La primera puede usarse en listados.',
						'return_format' => 'array',
						'preview_size'  => 'medium',
						'library'       => 'all',
						'min'           => 0,
						'max'           => 20,
					),
					array(
						'key'       => 'field_tt_tab_cliente',
						'label'     => 'Informacion',
						'name'      => '',
						'type'      => 'tab',
						'placement' => 'top',
					),
					array(
						'key'         => 'field_tt_cliente',
						'label'       => 'Nombre del Cliente',
						'name'        => 'cliente_nombre',
						'type'        => 'text',
						'placeholder' => 'Nombre del cliente',
					),
					array(
						'key'         => 'field_tt_ubicacion',
						'label'       => 'Ubicacion del Proyecto',
						'name'        => 'ubicacion_proyecto',
						'type'        => 'text',
						'placeholder' => 'Ciudad o zona',
					),
					array(
						'key'           => 'field_tt_ano',
						'label'         => 'Ano de Ejecucion',
						'name'          => 'ano_proyecto',
						'type'          => 'number',
						'default_value' => gmdate( 'Y' ),
						'min'           => 2000,
						'max'           => 2035,
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
						'label'         => 'Tipo de Servicio',
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
						'key'         => 'field_tt_tiempo',
						'label'       => 'Tiempo de Ejecucion',
						'name'        => 'tiempo_ejecucion',
						'type'        => 'text',
						'placeholder' => 'Ej: 4 semanas',
					),
					array(
						'key'         => 'field_tt_metros',
						'label'       => 'Metros Cuadrados',
						'name'        => 'metros_cuadrados',
						'type'        => 'text',
						'placeholder' => 'Ej: 36 m2',
					),
					array(
						'key'           => 'field_tt_ubicacion_tipo',
						'label'         => 'Tipo de Espacio',
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
						'key'           => 'field_tt_tipo_instalacion',
						'label'         => 'Tipo de Instalacion',
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
				'position' => 'normal',
				'style'    => 'default',
				'active'   => true,
			)
		);
	}

	public static function register_image_sizes() {
		add_image_size( 'tt-project-card', 900, 640, true );
		add_image_size( 'tt-project-slider', 1600, 900, true );
	}

	public static function register_assets() {
		wp_register_style(
			'tt-core-frontend',
			TT_CORE_URL . 'assets/css/frontend.css',
			array(),
			TT_CORE_VERSION
		);

		wp_register_script(
			'tt-core-frontend',
			TT_CORE_URL . 'assets/js/frontend.js',
			array(),
			TT_CORE_VERSION,
			true
		);

		wp_localize_script(
			'tt-core-frontend',
			'ttCore',
			array(
				'ajaxUrl' => admin_url( 'admin-ajax.php' ),
				'nonce'   => wp_create_nonce( 'tt_nonce' ),
				'i18n'    => array(
					'loading'   => 'Cargando...',
					'noResults' => 'No se encontraron proyectos para este filtro.',
					'error'     => 'Ha ocurrido un error. Intentalo de nuevo.',
				),
			)
		);
	}

	protected static function enqueue_frontend_assets() {
		wp_enqueue_style( 'tt-core-frontend' );
		wp_enqueue_script( 'tt-core-frontend' );
	}

	public static function projects_grid_shortcode( $atts ) {
		self::enqueue_frontend_assets();

		$atts = shortcode_atts(
			array(
				'per_page' => 9,
			),
			$atts,
			'tt_projects_grid'
		);

		$per_page = max( 1, absint( $atts['per_page'] ) );
		$terms    = get_terms(
			array(
				'taxonomy'   => 'tipo_proyecto',
				'hide_empty' => false,
			)
		);

		ob_start();
		?>
		<div class="tt-projects-wrapper" data-per-page="<?php echo esc_attr( $per_page ); ?>">
			<div class="tt-filters" data-tt-filters>
				<button class="tt-filter-btn active" type="button" data-filter="todos">Todos</button>
				<?php foreach ( $terms as $term ) : ?>
					<button class="tt-filter-btn" type="button" data-filter="<?php echo esc_attr( $term->slug ); ?>">
						<?php echo esc_html( $term->name ); ?>
					</button>
				<?php endforeach; ?>
			</div>
			<div class="tt-projects-grid" data-tt-grid></div>
			<div class="tt-grid-feedback" data-tt-feedback aria-live="polite"></div>
			<div class="tt-load-more-wrap" data-tt-load-more-wrap hidden>
				<button class="tt-btn-load-more" type="button" data-tt-load-more>Cargar mas proyectos</button>
			</div>
		</div>
		<?php
		return ob_get_clean();
	}

	public static function presupuesto_shortcode( $atts ) {
		self::enqueue_frontend_assets();

		$atts = shortcode_atts(
			array(
				'proyecto' => '',
			),
			$atts,
			'tt_presupuesto'
		);

		ob_start();
		?>
		<div class="tt-form-presupuesto" id="tt-form-presupuesto">
			<h2 class="tt-form-title">Solicita tu presupuesto</h2>
			<p class="tt-form-subtitle">Sin compromiso. Te responderemos en menos de 24 horas laborables.</p>
			<form class="tt-form" data-tt-form>
				<input type="hidden" name="proyecto_ref" value="<?php echo esc_attr( $atts['proyecto'] ); ?>">
				<input type="text" name="empresa_web" class="tt-honeypot" tabindex="-1" autocomplete="off">
				<div class="tt-form-row">
					<div class="tt-form-group">
						<label for="tt-nombre">Nombre</label>
						<input type="text" id="tt-nombre" name="nombre" placeholder="Juan Perez" required>
					</div>
					<div class="tt-form-group">
						<label for="tt-movil">Movil</label>
						<input type="tel" id="tt-movil" name="movil" placeholder="+34 000 000 000">
					</div>
				</div>
				<div class="tt-form-group">
					<label for="tt-email">Email</label>
					<input type="email" id="tt-email" name="email" placeholder="tu@email.com" required>
				</div>
				<div class="tt-form-group">
					<label for="tt-descripcion">Descripcion del proyecto</label>
					<textarea id="tt-descripcion" name="descripcion" rows="5" placeholder="Medidas aproximadas, ubicacion, producto..."></textarea>
				</div>
				<div class="tt-form-group tt-form-check">
					<label>
						<input type="checkbox" name="privacy_policy" value="1" required>
						He leido y acepto la Politica de Privacidad y el aviso legal.
					</label>
				</div>
				<button type="submit" class="tt-btn-submit" data-tt-submit>Solicitar presupuesto gratuito</button>
				<div class="tt-form-message" data-tt-message hidden></div>
			</form>
		</div>
		<?php
		return ob_get_clean();
	}

	public static function filter_projects() {
		check_ajax_referer( 'tt_nonce', 'nonce' );

		$tipo     = sanitize_text_field( wp_unslash( $_POST['tipo_proyecto'] ?? '' ) );
		$page     = max( 1, absint( $_POST['page'] ?? 1 ) );
		$per_page = max( 1, absint( $_POST['per_page'] ?? 9 ) );

		$args = array(
			'post_type'      => 'proyecto',
			'posts_per_page' => $per_page,
			'paged'          => $page,
			'post_status'    => 'publish',
			'orderby'        => 'date',
			'order'          => 'DESC',
		);

		if ( ! empty( $tipo ) && 'todos' !== $tipo ) {
			$args['tax_query'] = array(
				array(
					'taxonomy' => 'tipo_proyecto',
					'field'    => 'slug',
					'terms'    => $tipo,
				),
			);
		}

		$query    = new WP_Query( $args );
		$projects = array();

		if ( $query->have_posts() ) {
			while ( $query->have_posts() ) {
				$query->the_post();
				$projects[] = self::map_project_card( get_the_ID() );
			}
		}
		wp_reset_postdata();

		wp_send_json_success(
			array(
				'projects'     => $projects,
				'max_pages'    => (int) $query->max_num_pages,
				'current_page' => $page,
				'total'        => (int) $query->found_posts,
			)
		);
	}

	protected static function map_project_card( $post_id ) {
		$thumb   = get_the_post_thumbnail_url( $post_id, 'tt-project-card' );
		$gallery = function_exists( 'get_field' ) ? get_field( 'galeria_proyecto', $post_id ) : null;
		$image   = '';

		if ( ! empty( $gallery ) && is_array( $gallery ) ) {
			$first = $gallery[0];
			$image = $first['sizes']['tt-project-card'] ?? $first['url'] ?? '';
		} elseif ( $thumb ) {
			$image = $thumb;
		}

		return array(
			'id'        => $post_id,
			'title'     => get_the_title( $post_id ),
			'permalink' => get_permalink( $post_id ),
			'image'     => $image,
			'tipos'     => wp_get_post_terms( $post_id, 'tipo_proyecto', array( 'fields' => 'names' ) ),
		);
	}

	public static function submit_presupuesto() {
		check_ajax_referer( 'tt_nonce', 'nonce' );

		$nombre      = sanitize_text_field( wp_unslash( $_POST['nombre'] ?? '' ) );
		$movil       = sanitize_text_field( wp_unslash( $_POST['movil'] ?? '' ) );
		$email       = sanitize_email( wp_unslash( $_POST['email'] ?? '' ) );
		$descripcion = sanitize_textarea_field( wp_unslash( $_POST['descripcion'] ?? '' ) );
		$proyecto    = sanitize_text_field( wp_unslash( $_POST['proyecto_ref'] ?? '' ) );
		$privacy     = absint( $_POST['privacy_policy'] ?? 0 );
		$honeypot    = sanitize_text_field( wp_unslash( $_POST['empresa_web'] ?? '' ) );

		if ( ! empty( $honeypot ) ) {
			wp_send_json_error( array( 'message' => 'No se pudo procesar la solicitud.' ), 400 );
		}

		if ( empty( $nombre ) || empty( $email ) ) {
			wp_send_json_error( array( 'message' => 'Completa nombre y email.' ), 400 );
		}

		if ( ! is_email( $email ) ) {
			wp_send_json_error( array( 'message' => 'Email no valido.' ), 400 );
		}

		if ( ! $privacy ) {
			wp_send_json_error( array( 'message' => 'Debes aceptar la politica de privacidad.' ), 400 );
		}

		$to      = get_option( 'admin_email' );
		$subject = 'Nueva solicitud de presupuesto - Tecnoterrazas';
		if ( $proyecto ) {
			$subject .= ' | ' . $proyecto;
		}

		$body  = '<div style="font-family:Arial,sans-serif;max-width:640px;">';
		$body .= '<div style="background:#1d3766;color:#fff;padding:24px;text-align:center;"><h2 style="margin:0;">Nueva solicitud de presupuesto</h2></div>';
		$body .= '<div style="padding:24px;background:#f7f8fb;">';
		$body .= '<p><strong>Nombre:</strong> ' . esc_html( $nombre ) . '</p>';
		$body .= '<p><strong>Telefono:</strong> ' . esc_html( $movil ) . '</p>';
		$body .= '<p><strong>Email:</strong> ' . esc_html( $email ) . '</p>';
		$body .= '<p><strong>Proyecto:</strong> ' . esc_html( $proyecto ) . '</p>';
		$body .= '<p><strong>Acepta privacidad:</strong> Si</p>';
		$body .= '<p><strong>Descripcion:</strong><br>' . nl2br( esc_html( $descripcion ) ) . '</p>';
		$body .= '</div></div>';

		$headers = array(
			'Content-Type: text/html; charset=UTF-8',
			'Reply-To: ' . $nombre . ' <' . $email . '>',
		);

		if ( wp_mail( $to, $subject, $body, $headers ) ) {
			wp_send_json_success( array( 'message' => 'Gracias. Hemos recibido tu solicitud.' ) );
		}

		wp_send_json_error( array( 'message' => 'No se pudo enviar el formulario. Revisa la configuracion de correo.' ), 500 );
	}
}
