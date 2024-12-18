<?php
/*
Plugin Name: Simply Crossfit Calendar
Description: Un plugin para gestionar horarios de entrenamientos en centros de CrossFit.
Version: 1.0
Author: ColmenaDev
Author URI: https://colmenadev.com
License: GPL2
*/

// Evitar el acceso directo al archivo
if (!defined('ABSPATH')) {
    exit; // Salir si se accede directamente
}

// Definir constantes
define('SCC_PLUGIN_DIR', plugin_dir_path(__FILE__));
define('SCC_PLUGIN_URL', plugin_dir_url(__FILE__));

// Incluir archivos necesarios
require_once SCC_PLUGIN_DIR . 'includes/class-scc-activator.php';
require_once SCC_PLUGIN_DIR . 'includes/class-scc-deactivator.php';
require_once SCC_PLUGIN_DIR . 'includes/class-scc-rest-api.php';
require_once SCC_PLUGIN_DIR . 'includes/class-scc-enqueue.php';

// Función para encolar scripts y estilos
function mi_plugin_enqueue_assets() {
  // Encolar todos los scripts de chunks
  $chunks = glob(plugin_dir_path(__FILE__) . 'next-assets/static/chunks/*.js');
  foreach ($chunks as $chunk) {
      wp_enqueue_script(
          'next-js-chunk-' . basename($chunk, '.js'), // Handle
          plugin_dir_url(__FILE__) . 'next-assets/static/chunks/' . basename($chunk), // Ruta al archivo
          array(), // Dependencias
          null, // Versión
          true // Cargar en el pie de página
      );
  }
}

  // Encolar todos los archivos CSS
  $cssFiles = glob(plugin_dir_path(__FILE__) . 'next-assets/static/css/*.css');
  foreach ($cssFiles as $cssFile) {
      wp_enqueue_style(
          'next-js-style-' . basename($cssFile, '.css'), // Handle
          plugin_dir_url(__FILE__) . 'next-assets/static/css/' . basename($cssFile), // Ruta al archivo
          array(), // Dependencias
          null // Versión
      );
  }

// Activar el plugin
function scc_activate() {
    SCC_Activator::activate();
    add_option('scc_whatsapp_number', '');
}
register_activation_hook(__FILE__, 'scc_activate');

// Desactivar el plugin
function scc_deactivate() {
    SCC_Deactivator::deactivate();
}
register_deactivation_hook(__FILE__, 'scc_deactivate');

// Inicializar el plugin
function scc_init() {
    // Aquí puedes inicializar cualquier funcionalidad del plugin
    SCC_REST_API::init();
    SCC_Enqueue::enqueue_scripts();
}
add_action('init', 'scc_init');

// Agregar un menú de configuración
add_action('admin_menu', 'scc_add_admin_menu');
add_action('admin_init', 'scc_settings_init');

function scc_add_admin_menu() {
    add_options_page('Configuración de Simply Crossfit Calendar', 'SCC Settings', 'manage_options', 'scc', 'scc_options_page');
}

function scc_settings_init() {
    register_setting('sccSettings', 'scc_whatsapp_number');

    add_settings_section(
        'scc_section_developers',
        __('Configuración de WhatsApp', 'wordpress'),
        null,
        'sccSettings'
    );

    add_settings_field(
        'scc_whatsapp_number',
        __('Número de WhatsApp', 'wordpress'),
        'scc_whatsapp_number_render',
        'sccSettings',
        'scc_section_developers'
    );
}

function scc_whatsapp_number_render() {
    $options = get_option('scc_whatsapp_number');
    ?>
    <input type='text' name='scc_whatsapp_number' value='<?php echo esc_attr($options); ?>' placeholder="Ej: +1234567890" />
    <?php
}

function scc_options_page() {
    ?>
    <form action='options.php' method='post'>
        <h2>Configuración de Simply Crossfit Calendar</h2>
        <?php
        settings_fields('sccSettings');
        do_settings_sections('sccSettings');
        submit_button();
        ?>
    </form>
    <?php
}

register_activation_hook(__FILE__, 'scc_activate');

// Registrar el Custom Post Type para Entrenamientos
add_action('init', 'scc_register_entrenamiento_cpt');

function scc_register_entrenamiento_cpt() {
    $labels = array(
        'name'                  => _x('Entrenamientos', 'Post type general name', 'wordpress'),
        'singular_name'         => _x('Entrenamiento', 'Post type singular name', 'wordpress'),
        'menu_name'             => _x('Entrenamientos', 'Admin Menu text', 'wordpress'),
        'name_admin_bar'        => _x('Entrenamiento', 'Add New on Toolbar', 'wordpress'),
        'add_new'               => __('Agregar Nuevo', 'wordpress'),
        'add_new_item'          => __('Agregar Nuevo Entrenamiento', 'wordpress'),
        'new_item'              => __('Nuevo Entrenamiento', 'wordpress'),
        'edit_item'             => __('Editar Entrenamiento', 'wordpress'),
        'view_item'             => __('Ver Entrenamiento', 'wordpress'),
        'all_items'             => __('Todos los Entrenamientos', 'wordpress'),
        'search_items'          => __('Buscar Entrenamientos', 'wordpress'),
        'not_found'             => __('No se encontraron entrenamientos.', 'wordpress'),
        'not_found_in_trash'    => __('No se encontraron entrenamientos en la papelera.', 'wordpress'),
    );

    $args = array(
        'labels'             => $labels,
        'public'             => true,
        'publicly_queryable' => true,
        'show_ui'            => true,
        'show_in_menu'       => true,
        'query_var'          => true,
        'rewrite'            => array('slug' => 'entrenamiento'),
        'capability_type'    => 'post',
        'has_archive'        => true,
        'hierarchical'       => false,
        'menu_position'      => null,
        'supports'           => array('title', 'editor', 'custom-fields'), // Puedes agregar más soportes si es necesario
    );

    register_post_type('entrenamiento', $args);
}

add_action('admin_menu', 'scc_add_calendar_page');

function scc_add_calendar_page() {
    add_menu_page(
        'Calendario de Entrenamientos',
        'Calendario',
        'manage_options',
        'scc-calendar',
        'scc_render_calendar ',
        'dashicons-calendar-alt',
        6
    );
}

function scc_render_calendar() {
    echo '<div id="scc-calendar"></div>'; // Aquí puedes renderizar tu calendario
}