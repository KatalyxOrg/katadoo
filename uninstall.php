<?php
/**
 * Script de désinstallation du plugin.
 *
 * Ce fichier est exécuté uniquement lors de la suppression du plugin
 * via l'interface WordPress (pas lors de la désactivation).
 *
 * @package Katadoo
 * @since   1.0.0
 */

// Sécurité : vérifier que WordPress est bien en train de désinstaller
if ( ! defined( 'WP_UNINSTALL_PLUGIN' ) ) {
    exit;
}

/**
 * Supprime toutes les options du plugin.
 *
 * @since 1.0.0
 */
function katadoo_uninstall() {
    // Liste des options à supprimer
    $options = array(
        'katadoo_odoo_url',
        'katadoo_odoo_database',
        'katadoo_odoo_username',
        'katadoo_odoo_api_key',
        'katadoo_modules',
        'katadoo_newsletter_settings',
        'katadoo_helpdesk_settings',
    );

    // Supprimer chaque option
    foreach ( $options as $option ) {
        delete_option( $option );
    }

    // Si multisite, supprimer aussi les options de tous les sites
    if ( is_multisite() ) {
        $sites = get_sites();

        foreach ( $sites as $site ) {
            switch_to_blog( $site->blog_id );

            foreach ( $options as $option ) {
                delete_option( $option );
            }

            restore_current_blog();
        }
    }

    // Nettoyer les transients éventuels
    delete_transient( 'katadoo_odoo_connection_status' );
    delete_transient( 'katadoo_mailing_lists' );
    delete_transient( 'katadoo_helpdesk_teams' );
}

// Exécuter la désinstallation
katadoo_uninstall();
