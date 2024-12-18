<?php

class SCC_Activator {
    public static function activate() {
        self::create_entrenamientos_table();
    }

    private static function create_entrenamientos_table() {
        global $wpdb;

        // Nombre de la tabla
        $table_name = $wpdb->prefix . 'entrenamientos';
        $charset_collate = $wpdb->get_charset_collate();

        // SQL para crear la tabla
        $sql = "CREATE TABLE $table_name (
            id mediumint(9) NOT NULL AUTO_INCREMENT,
            centro varchar(255) NOT NULL,
            titulo varchar(255) NOT NULL,
            inicio datetime NOT NULL,
            fin datetime NOT NULL,
            instructor varchar(255) NOT NULL,
            descripcion text,
            PRIMARY KEY (id)
        ) $charset_collate;";

        // Incluir el archivo de actualización de la base de datos
        require_once(ABSPATH . 'wp-admin/includes/upgrade.php');
        dbDelta($sql);
    }
}