<?php
/**
 * TECNOTERRAZAS REDESIGN
 * Snippet 2: Grid, formulario y componentes dinamicos para Elementor.
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

function tt_redesign_asset_bootstrap() {
	static $done = false;

	if ( $done ) {
		return;
	}

	$done = true;

	wp_enqueue_style( 'dashicons' );
	wp_register_script( 'tt-redesign-inline', false, array(), '1.0.0', true );
	wp_enqueue_script( 'tt-redesign-inline' );
	wp_localize_script(
		'tt-redesign-inline',
		'ttRedesign',
		array(
			'ajaxUrl' => admin_url( 'admin-ajax.php' ),
			'nonce'   => wp_create_nonce( 'tt_redesign_nonce' ),
			'i18n'    => array(
				'loading'   => 'Cargando...',
				'noResults' => 'No se encontraron proyectos.',
				'error'     => 'Ha ocurrido un error. Intentalo de nuevo.',
			),
		)
	);

	$script = <<<'JS'
(function(){
  function postFormData(fd){
    return fetch(ttRedesign.ajaxUrl,{method:'POST',credentials:'same-origin',body:fd}).then(function(r){return r.json();});
  }

  function renderCard(project){
    var image = project.image ? '<div class="tt-rd-project-image" style="background-image:url(\'' + project.image.replace(/'/g,'%27') + '\')"></div>' : '<div class="tt-rd-project-image tt-rd-no-image"></div>';
    return '<article class="tt-rd-project-card"><a href="' + project.permalink + '" class="tt-rd-project-link">' + image + '<div class="tt-rd-project-overlay"></div><div class="tt-rd-project-info"><h3 class="tt-rd-project-title">' + project.title + '</h3><span class="tt-rd-project-more">VER PROYECTO</span></div></a></article>';
  }

  function initGrid(wrapper){
    var filters = wrapper.querySelector('[data-tt-rd-filters]');
    var grid = wrapper.querySelector('[data-tt-rd-grid]');
    var feedback = wrapper.querySelector('[data-tt-rd-feedback]');
    var loadMoreWrap = wrapper.querySelector('[data-tt-rd-loadmore-wrap]');
    var loadMoreButton = wrapper.querySelector('[data-tt-rd-loadmore]');
    var currentFilter = 'todos';
    var currentPage = 1;
    var maxPages = 1;
    var perPage = Number(wrapper.getAttribute('data-per-page') || 9);

    function setFeedback(message){
      feedback.textContent = message || '';
      feedback.hidden = !message;
    }

    function loadProjects(append){
      var fd = new FormData();
      fd.append('action','tt_redesign_filter_projects');
      fd.append('nonce',ttRedesign.nonce);
      fd.append('tipo_proyecto',currentFilter);
      fd.append('page',String(currentPage));
      fd.append('per_page',String(perPage));
      setFeedback(ttRedesign.i18n.loading);
      postFormData(fd).then(function(resp){
        if(!resp.success){ throw new Error(ttRedesign.i18n.error); }
        var projects = resp.data.projects || [];
        maxPages = Number(resp.data.max_pages || 1);
        if(append){ grid.insertAdjacentHTML('beforeend', projects.map(renderCard).join('')); }
        else { grid.innerHTML = projects.map(renderCard).join(''); }
        setFeedback(projects.length || currentPage > 1 ? '' : ttRedesign.i18n.noResults);
        loadMoreWrap.hidden = currentPage >= maxPages || !projects.length;
      }).catch(function(){
        setFeedback(ttRedesign.i18n.error);
        loadMoreWrap.hidden = true;
      });
    }

    if(filters){
      filters.addEventListener('click', function(event){
        var button = event.target.closest('[data-filter]');
        if(!button){ return; }
        filters.querySelectorAll('.tt-rd-filter-btn').forEach(function(btn){ btn.classList.remove('is-active'); });
        button.classList.add('is-active');
        currentFilter = button.getAttribute('data-filter') || 'todos';
        currentPage = 1;
        loadProjects(false);
      });
    }

    if(loadMoreButton){
      loadMoreButton.addEventListener('click', function(){
        if(currentPage >= maxPages){ return; }
        currentPage += 1;
        loadProjects(true);
      });
    }

    loadProjects(false);
  }

  function initForm(form){
    var button = form.querySelector('[data-tt-rd-submit]');
    var message = form.querySelector('[data-tt-rd-message]');

    function setMessage(text, type){
      message.textContent = text || '';
      message.hidden = !text;
      message.className = 'tt-rd-form-message' + (type ? ' ' + type : '');
    }

    form.addEventListener('submit', function(event){
      event.preventDefault();
      var fd = new FormData(form);
      fd.append('action','tt_redesign_presupuesto');
      fd.append('nonce',ttRedesign.nonce);
      button.disabled = true;
      setMessage(ttRedesign.i18n.loading, '');
      postFormData(fd).then(function(resp){
        if(!resp.success){ throw new Error((resp.data && resp.data.message) || ttRedesign.i18n.error); }
        setMessage(resp.data.message, 'is-success');
        form.reset();
      }).catch(function(error){
        setMessage(error.message || ttRedesign.i18n.error, 'is-error');
      }).finally(function(){
        button.disabled = false;
      });
    });
  }

  function initGallery(gallery){
    var track = gallery.querySelector('[data-tt-rd-track]');
    var slides = gallery.querySelectorAll('.tt-rd-gallery-slide');
    var index = 0;
    function render(){
      track.style.transform = 'translateX(' + (index * -100) + '%)';
    }
    gallery.addEventListener('click', function(event){
      var button = event.target.closest('[data-dir]');
      if(!button || slides.length < 2){ return; }
      index += Number(button.getAttribute('data-dir'));
      if(index < 0){ index = slides.length - 1; }
      if(index >= slides.length){ index = 0; }
      render();
    });
  }

  document.addEventListener('DOMContentLoaded', function(){
    document.querySelectorAll('[data-tt-rd-grid-wrapper]').forEach(initGrid);
    document.querySelectorAll('[data-tt-rd-form]').forEach(initForm);
    document.querySelectorAll('[data-tt-rd-gallery]').forEach(initGallery);
  });
})();
JS;

	wp_add_inline_script( 'tt-redesign-inline', $script );
}

function tt_redesign_map_project_card( $post_id ) {
	$thumb   = get_the_post_thumbnail_url( $post_id, 'tt-project-card' );
	$gallery = function_exists( 'get_field' ) ? get_field( 'galeria_proyecto', $post_id ) : null;
	$image   = '';

	if ( ! empty( $gallery ) && is_array( $gallery ) ) {
		$first = $gallery[0];
		$image = isset( $first['sizes']['tt-project-card'] ) ? $first['sizes']['tt-project-card'] : $first['url'];
	} elseif ( $thumb ) {
		$image = $thumb;
	}

	return array(
		'id'        => $post_id,
		'title'     => get_the_title( $post_id ),
		'permalink' => get_permalink( $post_id ),
		'image'     => $image,
	);
}

function tt_redesign_filter_projects() {
	check_ajax_referer( 'tt_redesign_nonce', 'nonce' );

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
			$projects[] = tt_redesign_map_project_card( get_the_ID() );
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
add_action( 'wp_ajax_tt_redesign_filter_projects', 'tt_redesign_filter_projects' );
add_action( 'wp_ajax_nopriv_tt_redesign_filter_projects', 'tt_redesign_filter_projects' );

function tt_redesign_projects_grid_shortcode( $atts ) {
	tt_redesign_asset_bootstrap();

	$atts = shortcode_atts(
		array(
			'per_page' => 9,
		),
		$atts,
		'tt_projects_grid'
	);

	$terms = get_terms(
		array(
			'taxonomy'   => 'tipo_proyecto',
			'hide_empty' => false,
		)
	);

	ob_start();
	?>
	<div class="tt-rd-grid-wrapper" data-tt-rd-grid-wrapper data-per-page="<?php echo esc_attr( absint( $atts['per_page'] ) ); ?>">
		<div class="tt-rd-filters" data-tt-rd-filters>
			<button type="button" class="tt-rd-filter-btn is-active" data-filter="todos">Todos</button>
			<?php foreach ( $terms as $term ) : ?>
				<button type="button" class="tt-rd-filter-btn" data-filter="<?php echo esc_attr( $term->slug ); ?>"><?php echo esc_html( $term->name ); ?></button>
			<?php endforeach; ?>
		</div>
		<div class="tt-rd-grid" data-tt-rd-grid></div>
		<div class="tt-rd-feedback" data-tt-rd-feedback hidden></div>
		<div class="tt-rd-loadmore-wrap" data-tt-rd-loadmore-wrap hidden>
			<button type="button" class="tt-rd-loadmore" data-tt-rd-loadmore>Cargar mas proyectos</button>
		</div>
	</div>
	<?php
	return ob_get_clean();
}
add_shortcode( 'tt_projects_grid', 'tt_redesign_projects_grid_shortcode' );

function tt_redesign_submit_presupuesto() {
	check_ajax_referer( 'tt_redesign_nonce', 'nonce' );

	$nombre      = sanitize_text_field( wp_unslash( $_POST['nombre'] ?? '' ) );
	$movil       = sanitize_text_field( wp_unslash( $_POST['movil'] ?? '' ) );
	$email       = sanitize_email( wp_unslash( $_POST['email'] ?? '' ) );
	$descripcion = sanitize_textarea_field( wp_unslash( $_POST['descripcion'] ?? '' ) );
	$proyecto    = sanitize_text_field( wp_unslash( $_POST['proyecto_ref'] ?? '' ) );
	$privacy     = absint( $_POST['privacy_policy'] ?? 0 );
	$honeypot    = sanitize_text_field( wp_unslash( $_POST['empresa_web'] ?? '' ) );

	if ( empty( $proyecto ) && is_singular( 'proyecto' ) ) {
		$proyecto = get_the_title();
	}

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

	$subject = 'Nueva solicitud de presupuesto - Tecnoterrazas';
	if ( $proyecto ) {
		$subject .= ' | ' . $proyecto;
	}

	$body  = '<div style="font-family:Arial,sans-serif;max-width:620px;">';
	$body .= '<div style="background:#1c3564;color:#fff;padding:20px;text-align:center;"><h2 style="margin:0;">Nueva solicitud de presupuesto</h2></div>';
	$body .= '<div style="padding:20px;background:#f7f8fb;">';
	$body .= '<p><strong>Nombre:</strong> ' . esc_html( $nombre ) . '</p>';
	$body .= '<p><strong>Movil:</strong> ' . esc_html( $movil ) . '</p>';
	$body .= '<p><strong>Email:</strong> ' . esc_html( $email ) . '</p>';
	$body .= '<p><strong>Proyecto:</strong> ' . esc_html( $proyecto ) . '</p>';
	$body .= '<p><strong>Descripcion:</strong><br>' . nl2br( esc_html( $descripcion ) ) . '</p>';
	$body .= '</div></div>';

	$headers = array(
		'Content-Type: text/html; charset=UTF-8',
		'Reply-To: ' . $nombre . ' <' . $email . '>',
	);

	$sent = wp_mail( get_option( 'admin_email' ), $subject, $body, $headers );

	if ( $sent ) {
		wp_send_json_success( array( 'message' => 'Gracias. Hemos recibido tu solicitud.' ) );
	}

	wp_send_json_error( array( 'message' => 'No se pudo enviar el formulario.' ), 500 );
}
add_action( 'wp_ajax_tt_redesign_presupuesto', 'tt_redesign_submit_presupuesto' );
add_action( 'wp_ajax_nopriv_tt_redesign_presupuesto', 'tt_redesign_submit_presupuesto' );

function tt_redesign_presupuesto_shortcode( $atts ) {
	tt_redesign_asset_bootstrap();

	$atts = shortcode_atts(
		array(
			'proyecto' => '',
		),
		$atts,
		'tt_presupuesto'
	);

	if ( empty( $atts['proyecto'] ) && is_singular( 'proyecto' ) ) {
		$atts['proyecto'] = get_the_title();
	}

	ob_start();
	?>
	<div class="tt-rd-form-card">
		<h2 class="tt-rd-form-title">Solicita tu presupuesto</h2>
		<p class="tt-rd-form-subtitle">Sin compromiso. Te responderemos en menos de 24 horas laborables.</p>
		<form class="tt-rd-form" data-tt-rd-form>
			<input type="hidden" name="proyecto_ref" value="<?php echo esc_attr( $atts['proyecto'] ); ?>">
			<input type="text" name="empresa_web" class="tt-rd-honeypot" tabindex="-1" autocomplete="off">
			<div class="tt-rd-form-row">
				<div class="tt-rd-form-group">
					<label for="tt-rd-nombre">Nombre</label>
					<input type="text" id="tt-rd-nombre" name="nombre" placeholder="Juan Perez" required>
				</div>
				<div class="tt-rd-form-group">
					<label for="tt-rd-movil">Movil</label>
					<input type="tel" id="tt-rd-movil" name="movil" placeholder="+34 000 000 000">
				</div>
			</div>
			<div class="tt-rd-form-group">
				<label for="tt-rd-email">Email</label>
				<input type="email" id="tt-rd-email" name="email" placeholder="tu@email.com" required>
			</div>
			<div class="tt-rd-form-group">
				<label for="tt-rd-desc">Descripcion del proyecto</label>
				<textarea id="tt-rd-desc" name="descripcion" rows="6" placeholder="Medidas aproximadas, ubicacion, producto..."></textarea>
			</div>
			<div class="tt-rd-form-check">
				<label><input type="checkbox" name="privacy_policy" value="1" required> He leido y acepto la Politica de Privacidad y el aviso legal.</label>
			</div>
			<button type="submit" class="tt-rd-form-button" data-tt-rd-submit>Solicitar presupuesto gratuito</button>
			<div class="tt-rd-form-message" data-tt-rd-message hidden></div>
		</form>
	</div>
	<?php
	return ob_get_clean();
}
add_shortcode( 'tt_presupuesto', 'tt_redesign_presupuesto_shortcode' );

function tt_redesign_project_gallery_shortcode() {
	tt_redesign_asset_bootstrap();

	if ( ! is_singular( 'proyecto' ) ) {
		return '';
	}

	$gallery = function_exists( 'get_field' ) ? get_field( 'galeria_proyecto' ) : array();
	if ( empty( $gallery ) ) {
		$thumb = get_the_post_thumbnail_url( get_the_ID(), 'tt-project-slider' );
		if ( $thumb ) {
			$gallery = array(
				array(
					'url' => $thumb,
				),
			);
		}
	}

	if ( empty( $gallery ) ) {
		return '';
	}

	ob_start();
	?>
	<div class="tt-rd-gallery" data-tt-rd-gallery>
		<div class="tt-rd-gallery-track" data-tt-rd-track>
			<?php foreach ( $gallery as $image ) : ?>
				<div class="tt-rd-gallery-slide">
					<img src="<?php echo esc_url( $image['url'] ); ?>" alt="<?php echo esc_attr( get_the_title() ); ?>">
				</div>
			<?php endforeach; ?>
		</div>
		<?php if ( count( $gallery ) > 1 ) : ?>
			<button type="button" class="tt-rd-gallery-nav is-prev" data-dir="-1" aria-label="Anterior">&#8592;</button>
			<button type="button" class="tt-rd-gallery-nav is-next" data-dir="1" aria-label="Siguiente">&#8594;</button>
		<?php endif; ?>
	</div>
	<?php
	return ob_get_clean();
}
add_shortcode( 'tt_project_gallery', 'tt_redesign_project_gallery_shortcode' );

function tt_redesign_project_title_shortcode() {
	if ( ! is_singular( 'proyecto' ) ) {
		return '';
	}
	return '<h1 class="tt-rd-single-title">' . esc_html( get_the_title() ) . '</h1>';
}
add_shortcode( 'tt_project_title', 'tt_redesign_project_title_shortcode' );

function tt_redesign_project_content_shortcode() {
	if ( ! is_singular( 'proyecto' ) ) {
		return '';
	}
	return '<div class="tt-rd-single-content">' . apply_filters( 'the_content', get_the_content() ) . '</div>';
}
add_shortcode( 'tt_project_content', 'tt_redesign_project_content_shortcode' );

function tt_redesign_project_details_shortcode() {
	if ( ! is_singular( 'proyecto' ) ) {
		return '';
	}

	$cliente   = function_exists( 'get_field' ) ? get_field( 'cliente_nombre' ) : '';
	$ubicacion = function_exists( 'get_field' ) ? get_field( 'ubicacion_proyecto' ) : '';
	$ano       = function_exists( 'get_field' ) ? get_field( 'ano_proyecto' ) : '';

	ob_start();
	?>
	<div class="tt-rd-details-card">
		<h3>Detalles del proyecto</h3>
		<div class="tt-rd-detail-item">
			<div class="tt-rd-detail-label">Cliente</div>
			<div class="tt-rd-detail-value"><?php echo esc_html( $cliente ); ?></div>
		</div>
		<div class="tt-rd-detail-item">
			<div class="tt-rd-detail-label">Localizacion</div>
			<div class="tt-rd-detail-value"><?php echo esc_html( $ubicacion ); ?></div>
		</div>
		<div class="tt-rd-detail-item">
			<div class="tt-rd-detail-label">Ano</div>
			<div class="tt-rd-detail-value"><?php echo esc_html( $ano ); ?></div>
		</div>
	</div>
	<?php
	return ob_get_clean();
}
add_shortcode( 'tt_project_details', 'tt_redesign_project_details_shortcode' );

function tt_redesign_project_specs_shortcode() {
	if ( ! is_singular( 'proyecto' ) ) {
		return '';
	}

	wp_enqueue_style( 'dashicons' );

	$items = array(
		array(
			'icon'  => 'dashicons-admin-tools',
			'label' => 'Servicio',
			'value' => function_exists( 'get_field' ) ? get_field( 'servicio_tipo' ) : '',
		),
		array(
			'icon'  => 'dashicons-clock',
			'label' => 'Tiempo de ejecucion',
			'value' => function_exists( 'get_field' ) ? get_field( 'tiempo_ejecucion' ) : '',
		),
		array(
			'icon'  => 'dashicons-editor-expand',
			'label' => 'Metros cuadrados',
			'value' => function_exists( 'get_field' ) ? get_field( 'metros_cuadrados' ) : '',
		),
		array(
			'icon'  => 'dashicons-location',
			'label' => 'Ubicacion',
			'value' => function_exists( 'get_field' ) ? get_field( 'ubicacion_tipo' ) : '',
		),
		array(
			'icon'  => 'dashicons-admin-generic',
			'label' => 'Tipo',
			'value' => function_exists( 'get_field' ) ? get_field( 'tipo_instalacion' ) : '',
		),
	);

	ob_start();
	?>
	<div class="tt-rd-specs">
		<?php foreach ( $items as $item ) : ?>
			<div class="tt-rd-spec-item">
				<span class="dashicons <?php echo esc_attr( $item['icon'] ); ?>"></span>
				<div class="tt-rd-spec-label"><?php echo esc_html( $item['label'] ); ?></div>
				<div class="tt-rd-spec-value"><?php echo esc_html( $item['value'] ); ?></div>
			</div>
		<?php endforeach; ?>
	</div>
	<?php
	return ob_get_clean();
}
add_shortcode( 'tt_project_specs', 'tt_redesign_project_specs_shortcode' );

function tt_redesign_contact_card_shortcode() {
	tt_redesign_asset_bootstrap();

	ob_start();
	?>
	<div class="tt-rd-contact-card">
		<div class="tt-rd-contact-overlay"></div>
		<div class="tt-rd-contact-inner">
			<div class="tt-rd-contact-kicker">Ubicanos</div>
			<h3>Estamos en Valencia</h3>
			<ul>
				<li><span class="dashicons dashicons-location"></span> Calle Alicante, 10 B - 46910 Sedavi, Valencia</li>
				<li><span class="dashicons dashicons-email-alt"></span> administracion@tecnoterrazas.com</li>
				<li><span class="dashicons dashicons-phone"></span> 672 634 271</li>
			</ul>
		</div>
	</div>
	<?php
	return ob_get_clean();
}
add_shortcode( 'tt_project_contact_card', 'tt_redesign_contact_card_shortcode' );
