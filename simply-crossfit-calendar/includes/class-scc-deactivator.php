<?php

class SCC_Deactivator {
    public static function deactivate() {
        // Aquí puedes agregar cualquier lógica que necesites al desactivar el plugin.
        // Por ejemplo, eliminar la tabla de entrenamientos si es necesario.
        // self::drop_entrenamientos_table();
    }

    private static function drop_entrenamientos_table() {
        global $wpdb;

        // Nombre de la tabla
        $table_name = $wpdb->prefix . 'entrenamientos';

        // SQL para eliminar la tabla
        $sql = "DROP TABLE IF EXISTS $table_name;";

        // Ejecutar la consulta
        $wpdb->query($sql);
    }
}