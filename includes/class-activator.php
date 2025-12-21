<?php
/**
 * Classe exécutée lors de l'activation du plugin.
 *
 * @package    Katadoo
 * @subpackage Katadoo/includes
 * @since      1.0.0
 */

// Sécurité : empêcher l'accès direct
if (!defined('ABSPATH')) {
    exit;
}

/**
 * Classe Katadoo_Activator.
 *
 * Cette classe définit le code nécessaire pour l'activation du plugin.
 *
 * @since 1.0.0
 */
class Katadoo_Activator
{

    /**
     * Actions exécutées lors de l'activation du plugin.
     *
     * Initialise les options par défaut et crée les tables nécessaires.
     *
     * @since 1.0.0
     */
    public static function activate()
    {
        // Vérifier la version PHP
        if (version_compare(PHP_VERSION, '8.0', '<')) {
            deactivate_plugins(plugin_basename(__FILE__));
            wp_die(
                esc_html__('Ce plugin nécessite PHP 8.0 ou supérieur.', 'katadoo'),
                'Plugin Activation Error',
                array('back_link' => true)
            );
        }

        // Vérifier la version WordPress
        if (version_compare(get_bloginfo('version'), '6.0', '<')) {
            deactivate_plugins(plugin_basename(__FILE__));
            wp_die(
                esc_html__('Ce plugin nécessite WordPress 6.0 ou supérieur.', 'katadoo'),
                'Plugin Activation Error',
                array('back_link' => true)
            );
        }

        // Initialiser les options par défaut
        self::init_default_options();

        // Flush les règles de réécriture
        flush_rewrite_rules();
    }

    /**
     * Initialise les options par défaut du plugin.
     *
     * @since 1.0.0
     */
    private static function init_default_options()
    {
        $default_options = array(
            'katadoo_odoo_url' => '',
            'katadoo_odoo_database' => '',
            'katadoo_odoo_username' => '',
            'katadoo_odoo_api_key' => '',
            'katadoo_modules' => array(
                'newsletter' => true,
                'helpdesk' => true,
            ),
            'katadoo_newsletter_settings' => array(
                'default_list_id' => 0,
                'form_fields' => array('email', 'name'),
            ),
            'katadoo_helpdesk_settings' => array(
                'default_team_id' => 0,
                'form_fields' => array('name', 'email', 'subject', 'description'),
            ),
        );

        foreach ($default_options as $option_name => $option_value) {
            if (get_option($option_name) === false) {
                add_option($option_name, $option_value);
            }
        }
    }
}
