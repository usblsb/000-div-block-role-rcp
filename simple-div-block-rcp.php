<?php
/**
 * Plugin Name: 000A Simple Div Block Role y Capabilities
 * Description: Un plugin sencillo que añade un bloque 'div' en Gutenberg que se muestra en el frontend, solo a los usuarios con un determinado Role, Capabilities y estado de membresía activa en Restric Content Pro.
 * Version: 03-01-2024
 * Author: Juan Luis Martel
 * Author URI: https://www.webyblog.es
 */


// Prevenir acceso directo al archivo del plugin
if ( ! defined( 'ABSPATH' ) ) exit;



// Función para añadir enlace de Ayuda en el plugin que muestra el fichero ayuda.html
function jlmr_agregar_enlace_ayuda_register_block_rcp( $links_array, $plugin_file_name, $plugin_data, $status ) {
    if ( strpos( $plugin_file_name, basename(__FILE__) ) ) {
        // Construye la URL del archivo de ayuda
        $ayuda_url = plugins_url( 'ayuda.html', __FILE__ );

        // Añade el enlace de 'Ayuda' al final de la lista de enlaces
        $links_array[] = '<a rel="noopener noreferrer nofollow"  href="' . esc_url( $ayuda_url ) . '" target="_blank">Ayuda</a>';
    }

    return $links_array;
}

add_filter( 'plugin_row_meta', 'jlmr_agregar_enlace_ayuda_register_block_rcp', 10, 4 );





function jlmr_container_div_block_register_block_rcp() {
    wp_register_script(
        'jlmr-container-div-block-editor-script-rcp',
        plugins_url( 'simple-div-block-rcp.js', __FILE__ ),
        array( 'wp-blocks', 'wp-element', 'wp-editor', 'wp-components', 'wp-data' ),
        filemtime( plugin_dir_path( __FILE__ ) . 'simple-div-block-rcp.js' )
    );

    register_block_type( 'jlmr/container-div', array(
        'editor_script' => 'jlmr-container-div-block-editor-script-rcp',
        'render_callback' => 'jlmr_container_div_block_render_callback_rcp'
    ) );
}

add_action( 'init', 'jlmr_container_div_block_register_block_rcp' );

function jlmr_container_div_block_render_callback_rcp( $block_attributes, $content ) {
    // Comprobar si Restrict Content Pro está activo.
    if ( ! function_exists( 'rcp_user_has_active_membership' ) || ! function_exists( 'rcp_is_pending_verification' ) ) {
        return '<div class="jlmr-message"><h3>Sistema de restricción apagado.</h3></div>';
    }

    // Verificar si el usuario está logueado.
    if ( ! is_user_logged_in() ) {
        return '<div class="jlmr-message"><h3>Por favor, inicia sesión para ver este contenido.</h3></div>';
    }

    // Verificar permisos, roles, y membresía activa.
    if ( jlmr_current_user_can( $block_attributes['permission'] ) && 
         jlmr_user_has_role( $block_attributes['role'] ) && 
         jlmr_user_has_active_membership() ) {

        // Comprobar si el usuario está pendiente de verificación.
        if ( jlmr_is_user_pending_verification() ) {
            return '<div class="jlmr-message"><h3>Por favor, verifica tu dirección de correo electrónico para ver este contenido.</h3></div>';
        }

        // Si todas las comprobaciones son correctas, renderizar el contenido.
        return $content;
    }

    return ''; // No renderizar nada si el usuario no cumple con los requisitos.
}

function jlmr_user_has_role( $role ) {
    $user = wp_get_current_user();
    if ( in_array( $role, (array) $user->roles ) ) {
        return true;
    }
    return false;
}

function jlmr_current_user_can( $permission ) {
    return current_user_can( $permission );
}

function jlmr_user_has_active_membership() {
    if ( function_exists( 'rcp_user_has_active_membership' ) ) {
        return rcp_user_has_active_membership();
    }
    return false;
}

function jlmr_is_user_pending_verification() {
    if ( function_exists( 'rcp_is_pending_verification' ) ) {
        return rcp_is_pending_verification();
    }
    return false;
}
