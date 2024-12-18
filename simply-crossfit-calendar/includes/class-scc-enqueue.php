<?php

class SCC_Enqueue {
  public static function enqueue_scripts() {
      // Encolar todos los scripts de chunks
      $chunks = glob(SCC_PLUGIN_DIR . 'next-assets/static/chunks/*.js');
      foreach ($chunks as $chunk) {
          wp_enqueue_script(
              'next-js-chunk-' . basename($chunk, '.js'), // Handle
              plugin_dir_url(__FILE__) . 'next-assets/static/chunks/' . basename($chunk), // Ruta al archivo
              array('jquery'), // Dependencias
              null, // Versión
              true // Cargar en el pie de página
          );
      }

      // Encolar todos los archivos CSS
      $cssFiles = glob(SCC_PLUGIN_DIR . 'next-assets/static/css/*.css');
      foreach ($cssFiles as $cssFile) {
          wp_enqueue_style(
              'next-js-style-' . basename($cssFile, '.css'), // Handle
              plugin_dir_url(__FILE__) . 'next-assets/static/css/' . basename($cssFile), // Ruta al archivo
              array(), // Dependencias
              null // Versión
          );
      }

      // Localizar el script para pasar datos de PHP a JavaScript
      $whatsapp_number = get_option('scc_whatsapp_number');
      wp_localize_script('next-js-chunk-main', 'sccData', array( // Asegúrate de que el handle coincida
          'apiUrl' => esc_url(rest_url('scc/v1/entrenamientos')),
          'whatsappNumber' => esc_attr($whatsapp_number), // Pasar el número de WhatsApp
      ));
  }
}