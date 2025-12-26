<?php
/**
 * Classe pour les fonctionnalités publiques.
 *
 * @package    Katadoo
 * @subpackage Katadoo/includes/public
 * @since      1.0.0
 */

// Sécurité : empêcher l'accès direct
if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

/**
 * Classe Katadoo_Public.
 *
 * Gère les fonctionnalités côté frontend.
 *
 * @since 1.0.0
 */
class Katadoo_Public {

    /**
     * Nom du plugin.
     *
     * @since  1.0.0
     * @access private
     * @var    string
     */
    private $plugin_name;

    /**
     * Version du plugin.
     *
     * @since  1.0.0
     * @access private
     * @var    string
     */
    private $version;

    /**
     * Instance de configuration.
     *
     * @since  1.0.3
     * @access private
     * @var    Katadoo_Config
     */
    private $config;

    /**
     * Constructeur.
     *
     * @since 1.0.0
     * @param string         $plugin_name Nom du plugin.
     * @param string         $version     Version du plugin.
     * @param Katadoo_Config $config      Instance de configuration.
     */
    public function __construct( $plugin_name, $version, $config ) {
        $this->plugin_name = $plugin_name;
        $this->version     = $version;
        $this->config      = $config;
    }

    /**
     * Enregistre les styles CSS du frontend.
     *
     * @since 1.0.0
     */
    public function enqueue_styles() {
        wp_enqueue_style(
            $this->plugin_name . '-public',
            KATADOO_PLUGIN_URL . 'assets/css/public.css',
            array(),
            $this->version,
            'all'
        );
    }

    /**
     * Enregistre les scripts JavaScript du frontend.
     *
     * @since 1.0.0
     */
    public function enqueue_scripts() {
        $recaptcha_settings = $this->config->get_recaptcha_settings();

        if ( $recaptcha_settings['enabled'] && ! empty( $recaptcha_settings['site_key'] ) ) {
            wp_enqueue_script(
                'google-recaptcha',
                'https://www.google.com/recaptcha/api.js?render=' . $recaptcha_settings['site_key'],
                array(),
                null,
                true
            );
        }

        $deps = array( 'jquery' );
        if ( $recaptcha_settings['enabled'] && ! empty( $recaptcha_settings['site_key'] ) ) {
            $deps[] = 'google-recaptcha';
        }

        wp_enqueue_script(
            $this->plugin_name . '-public',
            KATADOO_PLUGIN_URL . 'assets/js/public.js',
            $deps,
            $this->version,
            true
        );

        wp_localize_script(
            $this->plugin_name . '-public',
            'katadooPublic',
            array(
                'ajaxUrl'   => admin_url( 'admin-ajax.php' ),
                'nonce'     => wp_create_nonce( 'katadoo_public_nonce' ),
                'recaptcha' => array(
                    'enabled' => $recaptcha_settings['enabled'],
                    'siteKey' => $recaptcha_settings['site_key'],
                ),
                'strings'   => array(
                    'sending'       => __( 'Envoi en cours...', 'katadoo' ),
                    'error'         => __( 'Une erreur est survenue. Veuillez réessayer.', 'katadoo' ),
                    'fieldRequired' => __( 'Ce champ est requis.', 'katadoo' ),
                    'invalidEmail'  => __( 'Veuillez entrer une adresse email valide.', 'katadoo' ),
                ),
            )
        );
    }
}
