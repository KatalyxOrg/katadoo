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
     * Constructeur.
     *
     * @since 1.0.0
     * @param string $plugin_name Nom du plugin.
     * @param string $version     Version du plugin.
     */
    public function __construct( $plugin_name, $version ) {
        $this->plugin_name = $plugin_name;
        $this->version     = $version;
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
        wp_enqueue_script(
            $this->plugin_name . '-public',
            KATADOO_PLUGIN_URL . 'assets/js/public.js',
            array( 'jquery' ),
            $this->version,
            true
        );

        wp_localize_script(
            $this->plugin_name . '-public',
            'katadooPublic',
            array(
                'ajaxUrl' => admin_url( 'admin-ajax.php' ),
                'nonce'   => wp_create_nonce( 'katadoo_public_nonce' ),
                'strings' => array(
                    'sending'      => __( 'Envoi en cours...', 'katadoo' ),
                    'error'        => __( 'Une erreur est survenue. Veuillez réessayer.', 'katadoo' ),
                    'fieldRequired' => __( 'Ce champ est requis.', 'katadoo' ),
                    'invalidEmail' => __( 'Veuillez entrer une adresse email valide.', 'katadoo' ),
                ),
            )
        );
    }
}
