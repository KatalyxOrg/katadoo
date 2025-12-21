<?php
/**
 * Module Newsletter.
 *
 * @package    Katadoo
 * @subpackage Katadoo/includes/modules/newsletter
 * @since      1.0.0
 */

// Sécurité : empêcher l'accès direct
if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

/**
 * Classe Katadoo_Newsletter.
 *
 * Gère le module Newsletter.
 *
 * @since 1.0.0
 */
class Katadoo_Newsletter implements Katadoo_Module_Interface {

    /**
     * Client Odoo.
     *
     * @since  1.0.0
     * @access private
     * @var    Katadoo_Odoo_Client
     */
    private $odoo_client;

    /**
     * Configuration.
     *
     * @since  1.0.0
     * @access private
     * @var    Katadoo_Config
     */
    private $config;

    /**
     * API Newsletter.
     *
     * @since  1.0.0
     * @access private
     * @var    Katadoo_Newsletter_Api
     */
    private $api;

    /**
     * Constructeur.
     *
     * @since 1.0.0
     * @param Katadoo_Odoo_Client $odoo_client Client Odoo.
     * @param Katadoo_Config      $config      Configuration.
     */
    public function __construct( Katadoo_Odoo_Client $odoo_client, Katadoo_Config $config ) {
        $this->odoo_client = $odoo_client;
        $this->config      = $config;
        $this->api         = new Katadoo_Newsletter_Api( $odoo_client );
    }

    /**
     * Retourne l'identifiant du module.
     *
     * @since  1.0.0
     * @return string
     */
    public function get_id() {
        return 'newsletter';
    }

    /**
     * Retourne le nom du module.
     *
     * @since  1.0.0
     * @return string
     */
    public function get_name() {
        return __( 'Newsletter', 'katadoo' );
    }

    /**
     * Retourne la description du module.
     *
     * @since  1.0.0
     * @return string
     */
    public function get_description() {
        return __( 'Permet aux visiteurs de s\'inscrire à vos listes de diffusion Odoo.', 'katadoo' );
    }

    /**
     * Vérifie si le module est activé.
     *
     * @since  1.0.0
     * @return bool
     */
    public function is_enabled() {
        return $this->config->is_module_enabled( 'newsletter' );
    }

    /**
     * Initialise le module.
     *
     * @since 1.0.0
     */
    public function init() {
        // Le module est initialisé via le shortcode
    }

    /**
     * Retourne le shortcode du module.
     *
     * @since  1.0.0
     * @return string
     */
    public function get_shortcode() {
        return 'katadoo_newsletter';
    }

    /**
     * Retourne les champs de formulaire disponibles.
     *
     * @since  1.0.0
     * @return array
     */
    public function get_available_fields() {
        return array(
            'email' => array(
                'label'       => __( 'Email', 'katadoo' ),
                'type'        => 'email',
                'required'    => true,
                'placeholder' => __( 'Votre email', 'katadoo' ),
            ),
            'name' => array(
                'label'       => __( 'Nom', 'katadoo' ),
                'type'        => 'text',
                'required'    => false,
                'placeholder' => __( 'Votre nom', 'katadoo' ),
            ),
            'phone' => array(
                'label'       => __( 'Téléphone', 'katadoo' ),
                'type'        => 'tel',
                'required'    => false,
                'placeholder' => __( 'Votre téléphone', 'katadoo' ),
            ),
            'company' => array(
                'label'       => __( 'Entreprise', 'katadoo' ),
                'type'        => 'text',
                'required'    => false,
                'placeholder' => __( 'Votre entreprise', 'katadoo' ),
            ),
        );
    }

    /**
     * Génère le HTML du formulaire.
     *
     * @since  1.0.0
     * @param  array $atts Attributs.
     * @return string
     */
    public function render_form( $atts = array() ) {
        $settings = $this->config->get_newsletter_settings();

        $defaults = array(
            'list_id'     => $settings['default_list_id'],
            'fields'      => $settings['form_fields'],
            'button_text' => $settings['button_text'],
            'class'       => '',
        );

        $atts = wp_parse_args( $atts, $defaults );

        ob_start();
        include KATADOO_PLUGIN_DIR . 'includes/modules/newsletter/templates/form.php';
        return ob_get_clean();
    }

    /**
     * Traite la soumission du formulaire.
     *
     * @since  1.0.0
     * @param  array $data Données du formulaire.
     * @return array
     */
    public function process_form( $data ) {
        $email   = isset( $data['email'] ) ? sanitize_email( $data['email'] ) : '';
        $name    = isset( $data['name'] ) ? sanitize_text_field( $data['name'] ) : '';
        $phone   = isset( $data['phone'] ) ? sanitize_text_field( $data['phone'] ) : '';
        $company = isset( $data['company'] ) ? sanitize_text_field( $data['company'] ) : '';
        $list_id = isset( $data['list_id'] ) ? absint( $data['list_id'] ) : 0;

        $settings = $this->config->get_newsletter_settings();

        if ( empty( $email ) || ! is_email( $email ) ) {
            return array(
                'success' => false,
                'message' => __( 'Veuillez entrer une adresse email valide.', 'katadoo' ),
            );
        }

        if ( $list_id === 0 ) {
            $list_id = $settings['default_list_id'];
        }

        $result = $this->api->subscribe( $email, $name, $list_id, array(
            'phone'   => $phone,
            'company' => $company,
        ) );

        if ( $result ) {
            return array(
                'success' => true,
                'message' => $settings['success_message'],
            );
        }

        return array(
            'success' => false,
            'message' => $settings['error_message'],
        );
    }

    /**
     * Retourne l'API du module.
     *
     * @since  1.0.0
     * @return Katadoo_Newsletter_Api
     */
    public function get_api() {
        return $this->api;
    }
}
