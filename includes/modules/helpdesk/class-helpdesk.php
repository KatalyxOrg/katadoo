<?php
/**
 * Module Helpdesk.
 *
 * @package    Katadoo
 * @subpackage Katadoo/includes/modules/helpdesk
 * @since      1.0.0
 */

// Sécurité : empêcher l'accès direct
if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

/**
 * Classe Katadoo_Helpdesk.
 *
 * Gère le module Helpdesk.
 *
 * @since 1.0.0
 */
class Katadoo_Helpdesk implements Katadoo_Module_Interface {

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
     * API Helpdesk.
     *
     * @since  1.0.0
     * @access private
     * @var    Katadoo_Helpdesk_Api
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
        $this->api         = new Katadoo_Helpdesk_Api( $odoo_client );
    }

    /**
     * Retourne l'identifiant du module.
     *
     * @since  1.0.0
     * @return string
     */
    public function get_id() {
        return 'helpdesk';
    }

    /**
     * Retourne le nom du module.
     *
     * @since  1.0.0
     * @return string
     */
    public function get_name() {
        return __( 'Helpdesk', 'katadoo' );
    }

    /**
     * Retourne la description du module.
     *
     * @since  1.0.0
     * @return string
     */
    public function get_description() {
        return __( 'Permet aux visiteurs de créer des tickets de support Odoo.', 'katadoo' );
    }

    /**
     * Vérifie si le module est activé.
     *
     * @since  1.0.0
     * @return bool
     */
    public function is_enabled() {
        return $this->config->is_module_enabled( 'helpdesk' );
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
        return 'katadoo_helpdesk';
    }

    /**
     * Retourne les champs de formulaire disponibles.
     *
     * @since  1.0.0
     * @return array
     */
    public function get_available_fields() {
        return array(
            'name' => array(
                'label'       => __( 'Nom', 'katadoo' ),
                'type'        => 'text',
                'required'    => false,
                'placeholder' => __( 'Votre nom', 'katadoo' ),
            ),
            'email' => array(
                'label'       => __( 'Email', 'katadoo' ),
                'type'        => 'email',
                'required'    => true,
                'placeholder' => __( 'Votre email', 'katadoo' ),
            ),
            'phone' => array(
                'label'       => __( 'Téléphone', 'katadoo' ),
                'type'        => 'tel',
                'required'    => false,
                'placeholder' => __( 'Votre téléphone', 'katadoo' ),
            ),
            'subject' => array(
                'label'       => __( 'Sujet', 'katadoo' ),
                'type'        => 'text',
                'required'    => true,
                'placeholder' => __( 'Sujet de votre demande', 'katadoo' ),
            ),
            'description' => array(
                'label'       => __( 'Description', 'katadoo' ),
                'type'        => 'textarea',
                'required'    => true,
                'placeholder' => __( 'Décrivez votre demande...', 'katadoo' ),
            ),
            'priority' => array(
                'label'    => __( 'Priorité', 'katadoo' ),
                'type'     => 'select',
                'required' => false,
                'options'  => array(
                    '1' => __( 'Basse', 'katadoo' ),
                    '2' => __( 'Normale', 'katadoo' ),
                    '3' => __( 'Haute', 'katadoo' ),
                    '4' => __( 'Urgente', 'katadoo' ),
                ),
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
        $settings = $this->config->get_helpdesk_settings();

        $defaults = array(
            'team_id'     => $settings['default_team_id'],
            'fields'      => $settings['form_fields'],
            'button_text' => $settings['button_text'],
            'class'       => '',
        );

        $atts = wp_parse_args( $atts, $defaults );

        ob_start();
        include KATADOO_PLUGIN_DIR . 'includes/modules/helpdesk/templates/form.php';
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
        $email       = isset( $data['email'] ) ? sanitize_email( $data['email'] ) : '';
        $name        = isset( $data['name'] ) ? sanitize_text_field( $data['name'] ) : '';
        $phone       = isset( $data['phone'] ) ? sanitize_text_field( $data['phone'] ) : '';
        $subject     = isset( $data['subject'] ) ? sanitize_text_field( $data['subject'] ) : '';
        $description = isset( $data['description'] ) ? sanitize_textarea_field( $data['description'] ) : '';
        $priority    = isset( $data['priority'] ) ? absint( $data['priority'] ) : 2;
        $team_id     = isset( $data['team_id'] ) ? absint( $data['team_id'] ) : 0;

        $settings = $this->config->get_helpdesk_settings();

        if ( empty( $email ) || ! is_email( $email ) ) {
            return array(
                'success' => false,
                'message' => __( 'Veuillez entrer une adresse email valide.', 'katadoo' ),
            );
        }

        if ( empty( $subject ) ) {
            return array(
                'success' => false,
                'message' => __( 'Le sujet est requis.', 'katadoo' ),
            );
        }

        if ( empty( $description ) ) {
            return array(
                'success' => false,
                'message' => __( 'La description est requise.', 'katadoo' ),
            );
        }

        if ( $team_id === 0 ) {
            $team_id = $settings['default_team_id'];
        }

        $ticket_id = $this->api->create_ticket( array(
            'email'       => $email,
            'name'        => $name,
            'phone'       => $phone,
            'subject'     => $subject,
            'description' => $description,
            'priority'    => $priority,
            'team_id'     => $team_id,
        ) );

        if ( $ticket_id ) {
            return array(
                'success'   => true,
                'message'   => $settings['success_message'],
                'ticket_id' => $ticket_id,
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
     * @return Katadoo_Helpdesk_Api
     */
    public function get_api() {
        return $this->api;
    }
}
