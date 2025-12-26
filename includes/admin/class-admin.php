<?php
/**
 * Classe d'administration du plugin.
 *
 * @package    Katadoo
 * @subpackage Katadoo/includes/admin
 * @since      1.0.0
 */

// Sécurité : empêcher l'accès direct
if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

/**
 * Classe Katadoo_Admin.
 *
 * Gère l'interface d'administration du plugin.
 *
 * @since 1.0.0
 */
class Katadoo_Admin {

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
     * @since  1.0.0
     * @access private
     * @var    Katadoo_Config
     */
    private $config;

    /**
     * Instance du client Odoo.
     *
     * @since  1.0.0
     * @access private
     * @var    Katadoo_Odoo_Client
     */
    private $odoo_client;

    /**
     * Constructeur.
     *
     * @since 1.0.0
     * @param string              $plugin_name  Nom du plugin.
     * @param string              $version      Version du plugin.
     * @param Katadoo_Config      $config       Instance de configuration.
     * @param Katadoo_Odoo_Client $odoo_client  Instance du client Odoo.
     */
    public function __construct( $plugin_name, $version, $config, $odoo_client ) {
        $this->plugin_name = $plugin_name;
        $this->version     = $version;
        $this->config      = $config;
        $this->odoo_client = $odoo_client;
    }

    /**
     * Enregistre les styles CSS de l'administration.
     *
     * @since 1.0.0
     * @param string $hook Page courante.
     */
    public function enqueue_styles( $hook ) {
        if ( strpos( $hook, 'katadoo' ) === false ) {
            return;
        }

        wp_enqueue_style(
            $this->plugin_name . '-admin',
            KATADOO_PLUGIN_URL . 'assets/css/admin.css',
            array(),
            $this->version,
            'all'
        );
    }

    /**
     * Enregistre les scripts JavaScript de l'administration.
     *
     * @since 1.0.0
     * @param string $hook Page courante.
     */
    public function enqueue_scripts( $hook ) {
        if ( strpos( $hook, 'katadoo' ) === false ) {
            return;
        }

        wp_enqueue_script(
            $this->plugin_name . '-admin',
            KATADOO_PLUGIN_URL . 'assets/js/admin.js',
            array( 'jquery' ),
            $this->version,
            true
        );

        wp_localize_script(
            $this->plugin_name . '-admin',
            'katadooAdmin',
            array(
                'ajaxUrl' => admin_url( 'admin-ajax.php' ),
                'nonce'   => wp_create_nonce( 'katadoo_admin_nonce' ),
                'strings' => array(
                    'testing'        => __( 'Test en cours...', 'katadoo' ),
                    'loading'        => __( 'Chargement...', 'katadoo' ),
                    'error'          => __( 'Une erreur est survenue.', 'katadoo' ),
                    'saved'          => __( 'Paramètres enregistrés.', 'katadoo' ),
                    'confirmReset'   => __( 'Êtes-vous sûr de vouloir réinitialiser les paramètres ?', 'katadoo' ),
                    'selectList'     => __( 'Sélectionnez une liste', 'katadoo' ),
                    'selectTeam'     => __( 'Sélectionnez une équipe', 'katadoo' ),
                ),
            )
        );
    }

    /**
     * Ajoute le menu d'administration.
     *
     * @since 1.0.0
     */
    public function add_admin_menu() {
        // Menu principal
        add_menu_page(
            __( 'Katadoo', 'katadoo' ),
            __( 'Katadoo', 'katadoo' ),
            'manage_options',
            'katadoo',
            array( $this, 'display_settings_page' ),
            'dashicons-share-alt',
            80
        );

        // Sous-menu Connexion
        add_submenu_page(
            'katadoo',
            __( 'Connexion Odoo', 'katadoo' ),
            __( 'Connexion', 'katadoo' ),
            'manage_options',
            'katadoo',
            array( $this, 'display_settings_page' )
        );

        // Sous-menu Modules
        add_submenu_page(
            'katadoo',
            __( 'Modules', 'katadoo' ),
            __( 'Modules', 'katadoo' ),
            'manage_options',
            'katadoo-modules',
            array( $this, 'display_modules_page' )
        );

        // Sous-menu Newsletter
        add_submenu_page(
            'katadoo',
            __( 'Newsletter', 'katadoo' ),
            __( 'Newsletter', 'katadoo' ),
            'manage_options',
            'katadoo-newsletter',
            array( $this, 'display_newsletter_page' )
        );

        // Sous-menu Helpdesk
        add_submenu_page(
            'katadoo',
            __( 'Helpdesk', 'katadoo' ),
            __( 'Helpdesk', 'katadoo' ),
            'manage_options',
            'katadoo-helpdesk',
            array( $this, 'display_helpdesk_page' )
        );

        // Sous-menu ReCaptcha
        add_submenu_page(
            'katadoo',
            __( 'Google ReCaptcha', 'katadoo' ),
            __( 'ReCaptcha', 'katadoo' ),
            'manage_options',
            'katadoo-recaptcha',
            array( $this, 'display_recaptcha_page' )
        );
    }

    /**
     * Enregistre les paramètres.
     *
     * @since 1.0.0
     */
    public function register_settings() {
        // Section Connexion Odoo
        register_setting( 'katadoo_connection', 'katadoo_odoo_url', array(
            'type'              => 'string',
            'sanitize_callback' => 'esc_url_raw',
        ) );
        register_setting( 'katadoo_connection', 'katadoo_odoo_database', array(
            'type'              => 'string',
            'sanitize_callback' => 'sanitize_text_field',
        ) );
        register_setting( 'katadoo_connection', 'katadoo_odoo_username', array(
            'type'              => 'string',
            'sanitize_callback' => 'sanitize_text_field',
        ) );
        register_setting( 'katadoo_connection', 'katadoo_odoo_api_key', array(
            'type'              => 'string',
            'sanitize_callback' => 'sanitize_text_field',
        ) );

        // Section Modules
        register_setting( 'katadoo_modules', 'katadoo_modules', array(
            'type'              => 'array',
            'sanitize_callback' => array( $this, 'sanitize_modules' ),
        ) );

        // Section Newsletter
        register_setting( 'katadoo_newsletter', 'katadoo_newsletter_settings', array(
            'type'              => 'array',
            'sanitize_callback' => array( $this, 'sanitize_newsletter_settings' ),
        ) );

        // Section Helpdesk
        register_setting( 'katadoo_helpdesk', 'katadoo_helpdesk_settings', array(
            'type'              => 'array',
            'sanitize_callback' => array( $this, 'sanitize_helpdesk_settings' ),
        ) );

        // Section ReCaptcha
        register_setting( 'katadoo_recaptcha', 'katadoo_recaptcha_settings', array(
            'type'              => 'array',
            'sanitize_callback' => array( $this, 'sanitize_recaptcha_settings' ),
        ) );
    }

    /**
     * Sanitize les paramètres des modules.
     *
     * @since  1.0.0
     * @param  array $input Valeurs à sanitizer.
     * @return array Valeurs sanitizées.
     */
    public function sanitize_modules( $input ) {
        $sanitized = array();
        $available_modules = array( 'newsletter', 'helpdesk' );

        foreach ( $available_modules as $module ) {
            $sanitized[ $module ] = isset( $input[ $module ] ) && $input[ $module ] ? true : false;
        }

        return $sanitized;
    }

    /**
     * Sanitize les paramètres Newsletter.
     *
     * @since  1.0.0
     * @param  array $input Valeurs à sanitizer.
     * @return array Valeurs sanitizées.
     */
    public function sanitize_newsletter_settings( $input ) {
        return array(
            'default_list_id' => isset( $input['default_list_id'] ) ? absint( $input['default_list_id'] ) : 0,
            'form_fields'     => isset( $input['form_fields'] ) ? array_map( 'sanitize_text_field', $input['form_fields'] ) : array( 'email' ),
            'success_message' => isset( $input['success_message'] ) ? sanitize_textarea_field( $input['success_message'] ) : '',
            'error_message'   => isset( $input['error_message'] ) ? sanitize_textarea_field( $input['error_message'] ) : '',
            'button_text'     => isset( $input['button_text'] ) ? sanitize_text_field( $input['button_text'] ) : '',
        );
    }

    /**
     * Sanitize les paramètres Helpdesk.
     *
     * @since  1.0.0
     * @param  array $input Valeurs à sanitizer.
     * @return array Valeurs sanitizées.
     */
    public function sanitize_helpdesk_settings( $input ) {
        return array(
            'default_team_id' => isset( $input['default_team_id'] ) ? absint( $input['default_team_id'] ) : 0,
            'form_fields'     => isset( $input['form_fields'] ) ? array_map( 'sanitize_text_field', $input['form_fields'] ) : array( 'email', 'subject', 'description' ),
            'success_message' => isset( $input['success_message'] ) ? sanitize_textarea_field( $input['success_message'] ) : '',
            'error_message'   => isset( $input['error_message'] ) ? sanitize_textarea_field( $input['error_message'] ) : '',
            'button_text'     => isset( $input['button_text'] ) ? sanitize_text_field( $input['button_text'] ) : '',
        );
    }

    /**
     * Sanitize les paramètres ReCaptcha.
     *
     * @since  1.0.3
     * @param  array $input Valeurs à sanitizer.
     * @return array Valeurs sanitizées.
     */
    public function sanitize_recaptcha_settings( $input ) {
        return array(
            'enabled'    => isset( $input['enabled'] ) ? (bool) $input['enabled'] : false,
            'site_key'   => isset( $input['site_key'] ) ? sanitize_text_field( $input['site_key'] ) : '',
            'secret_key' => isset( $input['secret_key'] ) ? sanitize_text_field( $input['secret_key'] ) : '',
            'threshold'  => isset( $input['threshold'] ) ? floatval( $input['threshold'] ) : 0.5,
        );
    }

    /**
     * Affiche la page de paramètres de connexion.
     *
     * @since 1.0.0
     */
    public function display_settings_page() {
        require_once KATADOO_PLUGIN_DIR . 'includes/admin/partials/admin-connection.php';
    }

    /**
     * Affiche la page des modules.
     *
     * @since 1.0.0
     */
    public function display_modules_page() {
        require_once KATADOO_PLUGIN_DIR . 'includes/admin/partials/admin-modules.php';
    }

    /**
     * Affiche la page Newsletter.
     *
     * @since 1.0.0
     */
    public function display_newsletter_page() {
        require_once KATADOO_PLUGIN_DIR . 'includes/admin/partials/admin-newsletter.php';
    }

    /**
     * Affiche la page Helpdesk.
     *
     * @since 1.0.0
     */
    public function display_helpdesk_page() {
        require_once KATADOO_PLUGIN_DIR . 'includes/admin/partials/admin-helpdesk.php';
    }

    /**
     * Affiche la page ReCaptcha.
     *
     * @since 1.0.3
     */
    public function display_recaptcha_page() {
        require_once KATADOO_PLUGIN_DIR . 'includes/admin/partials/admin-recaptcha.php';
    }

    /**
     * Test de connexion AJAX.
     *
     * @since 1.0.0
     */
    public function ajax_test_connection() {
        check_ajax_referer( 'katadoo_admin_nonce', 'nonce' );

        if ( ! current_user_can( 'manage_options' ) ) {
            wp_send_json_error( array( 'message' => __( 'Permission refusée.', 'katadoo' ) ) );
        }

        // Rafraîchir le cache de configuration
        $this->config->clear_cache();

        $result = $this->odoo_client->test_connection();

        if ( $result['success'] ) {
            wp_send_json_success( $result );
        } else {
            wp_send_json_error( $result );
        }
    }

    /**
     * Récupère les mailing lists AJAX.
     *
     * @since 1.0.0
     */
    public function ajax_get_mailing_lists() {
        check_ajax_referer( 'katadoo_admin_nonce', 'nonce' );

        if ( ! current_user_can( 'manage_options' ) ) {
            wp_send_json_error( array( 'message' => __( 'Permission refusée.', 'katadoo' ) ) );
        }

        $lists = $this->odoo_client->search_read(
            'mailing.list',
            array(),
            array( 'id', 'name', 'contact_count' )
        );

        if ( $lists === false ) {
            wp_send_json_error( array(
                'message' => $this->odoo_client->get_last_error(),
            ) );
        }

        wp_send_json_success( array( 'lists' => $lists ) );
    }

    /**
     * Récupère les équipes Helpdesk AJAX.
     *
     * @since 1.0.0
     */
    public function ajax_get_helpdesk_teams() {
        check_ajax_referer( 'katadoo_admin_nonce', 'nonce' );

        if ( ! current_user_can( 'manage_options' ) ) {
            wp_send_json_error( array( 'message' => __( 'Permission refusée.', 'katadoo' ) ) );
        }

        $teams = $this->odoo_client->search_read(
            'helpdesk.team',
            array(),
            array( 'id', 'name' )
        );

        if ( $teams === false ) {
            wp_send_json_error( array(
                'message' => $this->odoo_client->get_last_error(),
            ) );
        }

        wp_send_json_success( array( 'teams' => $teams ) );
    }
}
