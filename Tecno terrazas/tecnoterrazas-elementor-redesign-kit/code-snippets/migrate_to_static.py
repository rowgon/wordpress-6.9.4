import json
import os

def replace_widget(element):
    """Recursively walk elements and replace specific static widgets with HTML widgets."""
    if isinstance(element, list):
        for i, el in enumerate(element):
            element[i] = replace_widget(el)
    elif isinstance(element, dict):
        # Find specs (icon-list with class tt-rd-specs)
        if element.get('elType') == 'widget' and element.get('widgetType') == 'icon-list':
            if element.get('settings', {}).get('css_classes') == 'tt-rd-specs':
                return {
                    "id": element['id'],
                    "elType": "widget",
                    "widgetType": "html",
                    "settings": {
                        "html": """<div class=\"tt-rd-specs\">
    <div class=\"tt-rd-spec-item\">
        <span class=\"dashicons dashicons-admin-tools\"></span>
        <div class=\"tt-rd-spec-label\">Servicio</div>
        <div class=\"tt-rd-spec-value\">Instalación</div>
    </div>
    <div class=\"tt-rd-spec-item\">
        <span class=\"dashicons dashicons-clock\"></span>
        <div class=\"tt-rd-spec-label\">Tiempo de ejecución</div>
        <div class=\"tt-rd-spec-value\">4 Semanas</div>
    </div>
    <div class=\"tt-rd-spec-item\">
        <span class=\"dashicons dashicons-editor-expand\"></span>
        <div class=\"tt-rd-spec-label\">Metros cuadrados</div>
        <div class=\"tt-rd-spec-value\">6MT2</div>
    </div>
    <div class=\"tt-rd-spec-item\">
        <span class=\"dashicons dashicons-location\"></span>
        <div class=\"tt-rd-spec-label\">Ubicación</div>
        <div class=\"tt-rd-spec-value\">Jardín</div>
    </div>
    <div class=\"tt-rd-spec-item\">
        <span class=\"dashicons dashicons-admin-generic\"></span>
        <div class=\"tt-rd-spec-label\">Tipo</div>
        <div class=\"tt-rd-spec-value\">Cortina</div>
    </div>
</div>"""
                    },
                    "elements": []
                }
        
        # Find form (Elementor Form widget)
        if element.get('elType') == 'widget' and element.get('widgetType') == 'form':
            return {
                "id": element['id'],
                "elType": "widget",
                "widgetType": "html",
                "settings": {
                    "html": """<div class=\"tt-rd-form-card\">
    <h2 class=\"tt-rd-form-title\">Solicita tu presupuesto</h2>
    <p class=\"tt-rd-form-subtitle\">Sin compromiso. Te responderemos en menos de 24 horas laborables.</p>
    <form class=\"tt-rd-form\" action=\"/\" method=\"POST\">
        <div class=\"tt-rd-form-row\">
            <div class=\"tt-rd-form-group\">
                <label for=\"tt-rd-nombre\">Nombre</label>
                <input type=\"text\" id=\"tt-rd-nombre\" name=\"nombre\" placeholder=\"Juan Pérez\" required>
            </div>
            <div class=\"tt-rd-form-group\">
                <label for=\"tt-rd-movil\">Móvil</label>
                <input type=\"tel\" id=\"tt-rd-movil\" name=\"movil\" placeholder=\"+34 000 000 000\">
            </div>
        </div>
        <div class=\"tt-rd-form-group\">
            <label for=\"tt-rd-email\">Email</label>
            <input type=\"email\" id=\"tt-rd-email\" name=\"email\" placeholder=\"tu@email.com\" required>
        </div>
        <div class=\"tt-rd-form-group\">
            <label for=\"tt-rd-desc\">Descripción del proyecto</label>
            <textarea id=\"tt-rd-desc\" name=\"descripcion\" rows=\"6\" placeholder=\"Medidas aproximadas, ubicación, producto...\"></textarea>
        </div>
        <div class=\"tt-rd-form-check\">
            <label><input type=\"checkbox\" name=\"privacy_policy\" value=\"1\" required> He leído y acepto la Política de Privacidad y el aviso legal.</label>
        </div>
        <button type=\"button\" class=\"tt-rd-form-button\" onclick=\"alert('Formulario estático. Debes procesar el envío con tu backend.');\">Solicitar presupuesto gratuito</button>
    </form>
</div>"""
                },
                "elements": []
            }
        
        # Find static grid (Inner Section with class tt-rd-static-grid)
        if element.get('elType') == 'section' and element.get('settings', {}).get('css_classes') == 'tt-rd-static-grid':
            return {
                "id": element['id'],
                "elType": "widget",
                "widgetType": "html",
                "settings": {
                    "html": """<div class=\"tt-rd-grid-wrapper\" data-tt-rd-grid-wrapper data-per-page=\"9\">
    <div class=\"tt-rd-filters\" data-tt-rd-filters>
        <button type=\"button\" class=\"tt-rd-filter-btn is-active\" data-filter=\"todos\">Todos</button>
        <button type=\"button\" class=\"tt-rd-filter-btn\" data-filter=\"pergolas\">Pérgolas</button>
        <button type=\"button\" class=\"tt-rd-filter-btn\" data-filter=\"cerramientos\">Cerramientos</button>
        <button type=\"button\" class=\"tt-rd-filter-btn\" data-filter=\"cortinas\">Cortinas de Cristal</button>
        <button type=\"button\" class=\"tt-rd-filter-btn\" data-filter=\"techos\">Techos Móviles</button>
    </div>
    <div class=\"tt-rd-grid\" data-tt-rd-grid>
        <!-- Tarjeta de Proyecto 1 -->
        <article class=\"tt-rd-project-card\">
            <a href=\"#\" class=\"tt-rd-project-link\">
                <div class=\"tt-rd-project-image\" style=\"background-image:url('https://via.placeholder.com/600x400')\"></div>
                <div class=\"tt-rd-project-overlay\"></div>
                <div class=\"tt-rd-project-info\">
                    <h3 class=\"tt-rd-project-title\">Proyecto unifamiliar Montserrat</h3>
                    <span class=\"tt-rd-project-more\">VER PROYECTO →</span>
                </div>
            </a>
        </article>
        <!-- Tarjeta de Proyecto 2 -->
        <article class=\"tt-rd-project-card\">
            <a href=\"#\" class=\"tt-rd-project-link\">
                <div class=\"tt-rd-project-image\" style=\"background-image:url('https://via.placeholder.com/600x400')\"></div>
                <div class=\"tt-rd-project-overlay\"></div>
                <div class=\"tt-rd-project-info\">
                    <h3 class=\"tt-rd-project-title\">Pérgola & Vista</h3>
                    <span class=\"tt-rd-project-more\">VER PROYECTO →</span>
                </div>
            </a>
        </article>
        <!-- Tarjeta de Proyecto 3 -->
        <article class=\"tt-rd-project-card\">
            <a href=\"#\" class=\"tt-rd-project-link\">
                <div class=\"tt-rd-project-image\" style=\"background-image:url('https://via.placeholder.com/600x400')\"></div>
                <div class=\"tt-rd-project-overlay\"></div>
                <div class=\"tt-rd-project-info\">
                    <h3 class=\"tt-rd-project-title\">Pérgola Bioclimática con Iluminación LED</h3>
                    <span class=\"tt-rd-project-more\">VER PROYECTO →</span>
                </div>
            </a>
        </article>
        <!-- Tarjeta de Proyecto 4 -->
        <article class=\"tt-rd-project-card\">
            <a href=\"#\" class=\"tt-rd-project-link\">
                <div class=\"tt-rd-project-image\" style=\"background-image:url('https://via.placeholder.com/600x400')\"></div>
                <div class=\"tt-rd-project-overlay\"></div>
                <div class=\"tt-rd-project-info\">
                    <h3 class=\"tt-rd-project-title\">Patio y Luz en Vallterna</h3>
                    <span class=\"tt-rd-project-more\">VER PROYECTO →</span>
                </div>
            </a>
        </article>
        <!-- Tarjeta de Proyecto 5 -->
        <article class=\"tt-rd-project-card\">
            <a href=\"#\" class=\"tt-rd-project-link\">
                <div class=\"tt-rd-project-image\" style=\"background-image:url('https://via.placeholder.com/600x400')\"></div>
                <div class=\"tt-rd-project-overlay\"></div>
                <div class=\"tt-rd-project-info\">
                    <h3 class=\"tt-rd-project-title\">Techo fijo con panel autoportante</h3>
                    <span class=\"tt-rd-project-more\">VER PROYECTO →</span>
                </div>
            </a>
        </article>
        <!-- Tarjeta de Proyecto 6 -->
        <article class=\"tt-rd-project-card\">
            <a href=\"#\" class=\"tt-rd-project-link\">
                <div class=\"tt-rd-project-image\" style=\"background-image:url('https://via.placeholder.com/600x400')\"></div>
                <div class=\"tt-rd-project-overlay\"></div>
                <div class=\"tt-rd-project-info\">
                    <h3 class=\"tt-rd-project-title\">Tecnología y Confort - Techo Móvil en Los Monasterios</h3>
                    <span class=\"tt-rd-project-more\">VER PROYECTO →</span>
                </div>
            </a>
        </article>
    </div>
    <div class=\"tt-rd-loadmore-wrap\" data-tt-rd-loadmore-wrap>
        <button type=\"button\" class=\"tt-rd-loadmore\" onclick=\"alert('Debes enlazar esto a una página de archivo.');\">Cargar más proyectos</button>
    </div>
</div>"""
                },
                "elements": []
            }
        
        if 'elements' in element:
            element['elements'] = replace_widget(element['elements'])
            
    return element

def process_file(filepath):
    print(f"Procesando {filepath}...")
    with open(filepath, 'r', encoding='utf-8') as f:
        data = json.load(f)
    
    data['content'] = replace_widget(data['content'])
    
    with open(filepath, 'w', encoding='utf-8') as f:
        json.dump(data, f, ensure_ascii=False, indent=2)
    print(f"{filepath} actualizado correctamente.")

base_path = '/var/www/html/wordpress/Tecno terrazas/tecnoterrazas-elementor-redesign-kit/elementor-templates'
process_file(os.path.join(base_path, 'tt-redesign-single-proyecto.json'))
process_file(os.path.join(base_path, 'tt-redesign-proyectos.json'))
process_file(os.path.join(base_path, 'tt-redesign-home.json'))
