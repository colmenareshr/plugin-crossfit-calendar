<?php

class SCC_REST_API {
    public static function init() {
        add_action('rest_api_init', function () {
            register_rest_route('scc/v1', '/entrenamientos', array(
                'methods' => 'GET',
                'callback' => [__CLASS__, 'get_entrenamientos'],
                'permission_callback' => '__return_true', // Permitir acceso público (ajustar según sea necesario)
            ));

            register_rest_route('scc/v1', '/entrenamientos', array(
                'methods' => 'POST',
                'callback' => [__CLASS__, 'create_entrenamiento'],
                'permission_callback' => '__return_true', // Permitir acceso público (ajustar según sea necesario)
            ));
        });
    }

    public static function get_entrenamientos(WP_REST_Request $request) {
        global $wpdb;
        $centro = $request->get_param('centro');
        $table_name = $wpdb->prefix . 'entrenamientos';

        // Obtener entrenamientos del centro especificado
        $entrenamientos = $wpdb->get_results($wpdb->prepare("SELECT * FROM $table_name WHERE centro = %s", $centro));

        return new WP_REST_Response($entrenamientos, 200);
    }

    public static function create_entrenamiento(WP_REST_Request $request) {
        global $wpdb;
        $table_name = $wpdb->prefix . 'entrenamientos';

        // Obtener datos del cuerpo de la solicitud
        $data = array(
            'centro' => sanitize_text_field($request->get_param('centro')),
            'titulo' => sanitize_text_field($request->get_param('titulo')),
            'inicio' => sanitize_text_field($request->get_param('inicio')),
            'fin' => sanitize_text_field($request->get_param('fin')),
            'instructor' => sanitize_text_field($request->get_param('instructor')),
            'descripcion' => sanitize_textarea_field($request->get_param('descripcion')),
        );

        // Insertar el nuevo entrenamiento en la base de datos
        $wpdb->insert($table_name, $data);

        return new WP_REST_Response('Entrenamiento creado', 201);
    }
}