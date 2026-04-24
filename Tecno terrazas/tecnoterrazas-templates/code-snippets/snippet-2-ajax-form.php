<?php
/**
 * ============================================================
 * TECNOTERRAZAS - Snippet 2: AJAX Filtros + Formulario
 * ============================================================
 * 
 * Instrucciones:
 * 1. Code Snippets Pro → Añadir Nuevo
 * 2. Título: "Tecnoterrazas - AJAX y Formulario"
 * 3. Pegar este código → Tipo PHP → Ejecutar en todas partes
 * 4. Activar
 */

// ============================================
// 1. ENQUEUE + LOCALIZE AJAX URL
// ============================================
function tecnoterrazas_enqueue_ajax() {
    wp_localize_script( 'jquery', 'tt_ajax', array(
        'url'   => admin_url( 'admin-ajax.php' ),
        'nonce' => wp_create_nonce( 'tt_nonce' ),
    ) );
}
add_action( 'wp_enqueue_scripts', 'tecnoterrazas_enqueue_ajax' );

// ============================================
// 2. AJAX: Filtrar Proyectos
// ============================================
function tecnoterrazas_filter_projects() {
    check_ajax_referer( 'tt_nonce', 'nonce' );

    $tipo     = sanitize_text_field( $_POST['tipo_proyecto'] ?? '' );
    $page     = max( 1, intval( $_POST['page'] ?? 1 ) );
    $per_page = 9;

    $args = array(
        'post_type'      => 'proyecto',
        'posts_per_page' => $per_page,
        'paged'          => $page,
        'post_status'    => 'publish',
        'orderby'        => 'date',
        'order'          => 'DESC',
    );

    if ( ! empty( $tipo ) && $tipo !== 'todos' ) {
        $args['tax_query'] = array( array(
            'taxonomy' => 'tipo_proyecto',
            'field'    => 'slug',
            'terms'    => $tipo,
        ) );
    }

    $query    = new WP_Query( $args );
    $projects = array();

    if ( $query->have_posts() ) {
        while ( $query->have_posts() ) {
            $query->the_post();
            $thumb   = get_the_post_thumbnail_url( get_the_ID(), 'tt-project-card' );
            $gallery = function_exists('get_field') ? get_field('galeria_proyecto') : null;
            $image   = '';
            if ( $gallery && ! empty( $gallery ) ) {
                $image = $gallery[0]['sizes']['tt-project-card'] ?? $gallery[0]['url'];
            } elseif ( $thumb ) {
                $image = $thumb;
            }
            $projects[] = array(
                'id'        => get_the_ID(),
                'title'     => get_the_title(),
                'permalink' => get_permalink(),
                'image'     => $image,
                'tipos'     => wp_get_post_terms( get_the_ID(), 'tipo_proyecto', array('fields'=>'names') ),
            );
        }
    }
    wp_reset_postdata();

    wp_send_json_success( array(
        'projects'     => $projects,
        'max_pages'    => $query->max_num_pages,
        'current_page' => $page,
        'total'        => $query->found_posts,
    ) );
}
add_action( 'wp_ajax_tt_filter_projects', 'tecnoterrazas_filter_projects' );
add_action( 'wp_ajax_nopriv_tt_filter_projects', 'tecnoterrazas_filter_projects' );

// ============================================
// 3. AJAX: Formulario de Presupuesto
// ============================================
function tecnoterrazas_submit_presupuesto() {
    check_ajax_referer( 'tt_nonce', 'nonce' );

    $nombre      = sanitize_text_field( $_POST['nombre'] ?? '' );
    $movil       = sanitize_text_field( $_POST['movil'] ?? '' );
    $email       = sanitize_email( $_POST['email'] ?? '' );
    $descripcion = sanitize_textarea_field( $_POST['descripcion'] ?? '' );
    $proyecto    = sanitize_text_field( $_POST['proyecto_ref'] ?? '' );

    if ( empty( $nombre ) || empty( $email ) ) {
        wp_send_json_error( array( 'message' => 'Completa nombre y email.' ) );
        return;
    }
    if ( ! is_email( $email ) ) {
        wp_send_json_error( array( 'message' => 'Email no válido.' ) );
        return;
    }

    $to      = get_option( 'admin_email' );
    $subject = 'Nueva Solicitud Presupuesto - Tecnoterrazas';
    if ( $proyecto ) $subject .= ' | ' . $proyecto;

    $body = "<div style='font-family:Arial;max-width:600px;'>
        <div style='background:#0D1B2A;color:#fff;padding:20px;text-align:center;'><h2>Nueva Solicitud de Presupuesto</h2></div>
        <div style='padding:20px;background:#f9f9f9;'>
        <p><strong>Nombre:</strong> " . esc_html($nombre) . "</p>
        <p><strong>Teléfono:</strong> " . esc_html($movil) . "</p>
        <p><strong>Email:</strong> " . esc_html($email) . "</p>
        <p><strong>Proyecto:</strong> " . esc_html($proyecto) . "</p>
        <p><strong>Descripción:</strong><br>" . nl2br(esc_html($descripcion)) . "</p>
        </div></div>";

    $headers = array(
        'Content-Type: text/html; charset=UTF-8',
        'Reply-To: ' . $nombre . ' <' . $email . '>',
    );

    $sent = wp_mail( $to, $subject, $body, $headers );
    if ( $sent ) {
        wp_send_json_success( array( 'message' => '¡Gracias! Te responderemos en 24-48h.' ) );
    } else {
        wp_send_json_error( array( 'message' => 'Error al enviar. Llámanos: 672 634 271.' ) );
    }
}
add_action( 'wp_ajax_tt_presupuesto', 'tecnoterrazas_submit_presupuesto' );
add_action( 'wp_ajax_nopriv_tt_presupuesto', 'tecnoterrazas_submit_presupuesto' );

// ============================================
// 4. SHORTCODE: Formulario de Presupuesto
// ============================================
function tecnoterrazas_form_shortcode( $atts ) {
    $atts = shortcode_atts( array( 'proyecto' => '' ), $atts );
    ob_start();
    ?>
    <div class="tt-form-presupuesto" id="tt-form-presupuesto">
        <h2 class="tt-form-title">Solicita tu presupuesto</h2>
        <p class="tt-form-subtitle">Sin compromiso. Te responderemos en un máximo de 24-48 horas laborables.</p>
        <form id="tt-presupuesto-form" class="tt-form">
            <input type="hidden" name="proyecto_ref" value="<?php echo esc_attr($atts['proyecto']); ?>">
            <div class="tt-form-row">
                <div class="tt-form-group">
                    <label for="tt-nombre">NOMBRE</label>
                    <input type="text" id="tt-nombre" name="nombre" placeholder="Juan Pérez" required>
                </div>
                <div class="tt-form-group">
                    <label for="tt-movil">MÓVIL</label>
                    <input type="tel" id="tt-movil" name="movil" placeholder="+34 000 000 000">
                </div>
            </div>
            <div class="tt-form-group">
                <label for="tt-email">EMAIL</label>
                <input type="email" id="tt-email" name="email" placeholder="tu@email.com" required>
            </div>
            <div class="tt-form-group">
                <label for="tt-descripcion">DESCRIPCIÓN DEL PROYECTO</label>
                <textarea id="tt-descripcion" name="descripcion" rows="4" placeholder="Medidas aproximadas, ubicación, producto..."></textarea>
            </div>
            <div class="tt-form-group tt-form-check">
                <label><input type="checkbox" required> He leído y acepto la <a href="/politica-de-privacidad/" target="_blank">Política de Privacidad</a> y el aviso legal.</label>
            </div>
            <button type="submit" class="tt-btn-submit" id="tt-btn-submit">SOLICITAR PRESUPUESTO GRATUITO</button>
            <div class="tt-form-message" id="tt-form-message" style="display:none;"></div>
        </form>
    </div>
    <script>
    (function(){
        var form=document.getElementById('tt-presupuesto-form');
        if(!form)return;
        form.addEventListener('submit',function(e){
            e.preventDefault();
            var btn=document.getElementById('tt-btn-submit'),msg=document.getElementById('tt-form-message'),orig=btn.textContent;
            btn.textContent='Enviando...';btn.disabled=true;msg.style.display='none';
            var fd=new FormData(form);fd.append('action','tt_presupuesto');fd.append('nonce',tt_ajax.nonce);
            fetch(tt_ajax.url,{method:'POST',body:fd}).then(function(r){return r.json();}).then(function(d){
                msg.style.display='block';
                if(d.success){msg.className='tt-form-message tt-success';msg.textContent=d.data.message;form.reset();}
                else{msg.className='tt-form-message tt-error';msg.textContent=d.data.message;}
            }).catch(function(){msg.style.display='block';msg.className='tt-form-message tt-error';msg.textContent='Error de conexión.';})
            .finally(function(){btn.textContent=orig;btn.disabled=false;});
        });
    })();
    </script>
    <?php
    return ob_get_clean();
}
add_shortcode( 'tt_presupuesto', 'tecnoterrazas_form_shortcode' );

// ============================================
// 5. SHORTCODE: Grid de Proyectos con Filtros
// ============================================
function tecnoterrazas_grid_shortcode( $atts ) {
    $atts = shortcode_atts( array( 'per_page' => 9 ), $atts );
    $terms = get_terms( array( 'taxonomy' => 'tipo_proyecto', 'hide_empty' => false ) );
    ob_start();
    ?>
    <div class="tt-projects-wrapper">
        <div class="tt-filters" id="tt-filters">
            <button class="tt-filter-btn active" data-filter="todos">TODOS</button>
            <?php foreach($terms as $t): ?>
            <button class="tt-filter-btn" data-filter="<?php echo esc_attr($t->slug); ?>"><?php echo esc_html(strtoupper($t->name)); ?></button>
            <?php endforeach; ?>
        </div>
        <div class="tt-projects-grid" id="tt-projects-grid"></div>
        <div class="tt-load-more-wrap" id="tt-load-more-wrap" style="display:none;">
            <button class="tt-btn-load-more" id="tt-btn-load-more">CARGAR MÁS PROYECTOS</button>
        </div>
    </div>
    <script>
    (function(){
        var cf='todos',cp=1,mp=1,grid=document.getElementById('tt-projects-grid'),
            lmw=document.getElementById('tt-load-more-wrap'),lmb=document.getElementById('tt-btn-load-more');
        function load(f,p,append){
            var fd=new FormData();fd.append('action','tt_filter_projects');fd.append('nonce',tt_ajax.nonce);
            fd.append('tipo_proyecto',f);fd.append('page',p);
            fetch(tt_ajax.url,{method:'POST',body:fd}).then(function(r){return r.json();}).then(function(d){
                if(!d.success)return;var pj=d.data.projects;mp=d.data.max_pages;cp=d.data.current_page;
                var h='';pj.forEach(function(p){
                    h+='<div class="tt-project-card"><a href="'+p.permalink+'" class="tt-project-link">';
                    h+=p.image?'<div class="tt-project-image" style="background-image:url('+p.image+')"></div>':'<div class="tt-project-image tt-no-image"></div>';
                    h+='<div class="tt-project-info"><h3 class="tt-project-title">'+p.title+'</h3><span class="tt-project-ver">VER PROYECTO →</span></div></a></div>';
                });
                if(append)grid.insertAdjacentHTML('beforeend',h);else grid.innerHTML=h;
                lmw.style.display=(cp<mp)?'flex':'none';
            });
        }
        document.getElementById('tt-filters').addEventListener('click',function(e){
            if(e.target.classList.contains('tt-filter-btn')){
                document.querySelectorAll('.tt-filter-btn').forEach(function(b){b.classList.remove('active');});
                e.target.classList.add('active');cf=e.target.dataset.filter;cp=1;load(cf,1,false);
            }
        });
        lmb.addEventListener('click',function(){cp++;load(cf,cp,true);});
        load('todos',1,false);
    })();
    </script>
    <?php
    return ob_get_clean();
}
add_shortcode( 'tt_projects_grid', 'tecnoterrazas_grid_shortcode' );
