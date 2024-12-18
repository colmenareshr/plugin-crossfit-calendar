<?php
/**
 * Plugin Name: CrossFit Calendar
 * Description: Un plugin para gestionar entrenamientos de CrossFit con un calendario.
 * Version: 1.1
 * Author: Colemadev
 */

// Evitar acceso directo
if (!defined('ABSPATH')) {
    exit;
}

// Registrar el shortcode para el calendario
function crossfit_calendar_shortcode() {
    // Div donde React montará la app
    return '<div id="react-calendar-root"></div>';
}
add_shortcode('crossfit_calendar', 'crossfit_calendar_shortcode');

// Cargar React, CSS y pasar datos
function crossfit_calendar_enqueue_scripts() {
    // Ajusta las rutas según dónde subas tu app React
    wp_enqueue_script(
        'react-calendar-app',
        plugins_url('crossfit-calendar-app/dist/assets/index-Dqj0bjcm.js', __FILE__),
        ['wp-element'], // Esto asegura que React y ReactDOM estén disponibles
        '1.1.0',
        true
    );

    // Estilos opcionales (habilitar si usas CSS desde React)
    // wp_enqueue_style(
    //     'react-calendar-style',
    //     plugins_url('crossfit-calendar-app/dist/assets/index-DVH67hMT.css', __FILE__),
    //     array(),
    //     '1.0.0'
    // );

    // Pasar la configuración de WhatsApp y horarios a JavaScript
    wp_localize_script('react-calendar-app', 'crossfitCalendarConfig', array(
        'whatsappNumber' => get_option('crossfit_calendar_whatsapp_number', ''),
        'schedules' => get_option('crossfit_calendar_schedules', []) // Horarios predefinidos
    ));
}
add_action('wp_enqueue_scripts', 'crossfit_calendar_enqueue_scripts');

// Crear menú para configuración
function crossfit_calendar_settings_menu() {
    add_options_page(
        'Configuración del Calendario CrossFit',
        'Calendario CrossFit',
        'manage_options',
        'crossfit-calendar-settings',
        'crossfit_calendar_settings_page'
    );
}
add_action('admin_menu', 'crossfit_calendar_settings_menu');

// Página de configuración del plugin
function crossfit_calendar_settings_page() {
    ?>
    <div class="wrap">
        <h1>Configuración del Calendario CrossFit</h1>
        <form method="post" action="options.php">
            <?php
            settings_fields('crossfit_calendar_settings');
            do_settings_sections('crossfit-calendar-settings');
            submit_button();
            ?>
        </form>
    </div>
    <?php
}

// Registrar campos de configuración
function crossfit_calendar_register_settings() {
    // Número de WhatsApp
    register_setting('crossfit_calendar_settings', 'crossfit_calendar_whatsapp_number');
    // Horarios predefinidos
    register_setting('crossfit_calendar_settings', 'crossfit_calendar_schedules');

    add_settings_section(
        'crossfit_calendar_main_settings',
        'Ajustes Principales',
        null,
        'crossfit-calendar-settings'
    );

    add_settings_field(
        'crossfit_calendar_whatsapp_number',
        'Número de WhatsApp',
        'crossfit_calendar_whatsapp_field_callback',
        'crossfit-calendar-settings',
        'crossfit_calendar_main_settings'
    );

    add_settings_field(
        'crossfit_calendar_schedules',
        'Horarios Predefinidos',
        'crossfit_calendar_schedules_field_callback',
        'crossfit-calendar-settings',
        'crossfit_calendar_main_settings'
    );
}
add_action('admin_init', 'crossfit_calendar_register_settings');

// Campo para ingresar el número de WhatsApp
function crossfit_calendar_whatsapp_field_callback() {
    $whatsapp_number = get_option('crossfit_calendar_whatsapp_number', '');
    echo '<input type="text" name="crossfit_calendar_whatsapp_number" value="' . esc_attr($whatsapp_number) . '" class="regular-text" />';
}

// Campo para ingresar horarios predefinidos
function crossfit_calendar_schedules_field_callback() {
    $schedules = get_option('crossfit_calendar_schedules', []);
    echo '<textarea name="crossfit_calendar_schedules" rows="10" cols="50" class="large-text code">' . esc_textarea(json_encode($schedules)) . '</textarea>';
    echo '<p class="description">Introduce los horarios en formato JSON. Ejemplo: [{"time": "10:00 AM", "class": "Yoga"}, {"time": "12:00 PM", "class": "CrossFit"}]</p>';
}


function register_centro_post_type() {
  register_post_type('centro', [
      'labels' => [
          'name' => 'Centros',
          'singular_name' => 'Centro',
      ],
      'public' => true,
      'has_archive' => true,
      'supports' => ['title', 'editor', 'thumbnail'],
      'show_in_rest' => true,
  ]);
}
add_action('init', 'register_centro_post_type');

function add_centro_meta_boxes() {
  add_meta_box(
      'centro_details',
      'Detalles del Centro',
      'render_centro_meta_box',
      'centro',
      'normal',
      'high'
  );
}
add_action('add_meta_boxes', 'add_centro_meta_boxes');

function render_centro_meta_box($post) {
  $whatsapp = get_post_meta($post->ID, '_centro_whatsapp', true);
  $horarios = get_post_meta($post->ID, '_centro_horarios', true);
  ?>
  <label for="centro_whatsapp">Número de WhatsApp:</label>
  <input type="text" name="centro_whatsapp" id="centro_whatsapp" value="<?php echo esc_attr($whatsapp); ?>" style="width: 100%; margin-bottom: 10px;">

  <label for="centro_horarios">Horarios:</label>
  <textarea name="centro_horarios" id="centro_horarios" rows="5" style="width: 100%;"><?php echo esc_textarea($horarios); ?></textarea>
  <p>Formato: título|hora_inicio|hora_fin (uno por línea).</p>
  <?php
}

function save_centro_meta_box($post_id) {
  if (array_key_exists('centro_whatsapp', $_POST)) {
      update_post_meta($post_id, '_centro_whatsapp', sanitize_text_field($_POST['centro_whatsapp']));
  }
  if (array_key_exists('centro_horarios', $_POST)) {
      update_post_meta($post_id, '_centro_horarios', sanitize_textarea_field($_POST['centro_horarios']));
  }
}
add_action('save_post', 'save_centro_meta_box');

function render_centro_horarios($atts) {
  $atts = shortcode_atts(['id' => null], $atts, 'centro_horarios');

  if (!$atts['id']) {
      return 'ID del centro no especificado.';
  }

  $whatsapp = get_post_meta($atts['id'], '_centro_whatsapp', true);
  $horarios_raw = get_post_meta($atts['id'], '_centro_horarios', true);
  $horarios = explode("\n", $horarios_raw);

  ob_start();
  ?>
  <div class="centro-horarios">
      <h3>Horarios</h3>
      <ul>
          <?php foreach ($horarios as $horario): ?>
              <?php 
              $parts = explode('|', $horario);
              if (count($parts) !== 3) continue;
              list($title, $start, $end) = $parts;
              ?>
              <li>
                  <strong><?php echo esc_html($title); ?></strong>: 
                  <?php echo esc_html($start); ?> - <?php echo esc_html($end); ?>
              </li>
          <?php endforeach; ?>
      </ul>
      <?php if ($whatsapp): ?>
          <p>
              <a href="https://wa.me/<?php echo esc_attr($whatsapp); ?>" target="_blank">
                  Reservar por WhatsApp
              </a>
          </p>
      <?php endif; ?>
  </div>
  <?php
  return ob_get_clean();
}
add_shortcode('centro_horarios', 'render_centro_horarios');
