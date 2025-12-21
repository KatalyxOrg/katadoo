<?php
/**
 * Intégration Elementor.
 *
 * @package    Katadoo
 * @subpackage Katadoo/elementor
 * @since      1.0.0
 */

// Sécurité : empêcher l'accès direct
if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

/**
 * Classe Katadoo_Elementor.
 *
 * Gère l'intégration avec Elementor.
 *
 * @since 1.0.0
 */
class Katadoo_Elementor {

    /**
     * Instance statique pour accès global.
     *
     * @since  1.0.0
     * @access private
     * @var    Katadoo_Elementor
     */
    private static $instance = null;

    /**
     * Modules Katadoo.
     *
     * @since  1.0.0
     * @access private
     * @var    array
     */
    private $modules;

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
     * Version minimale d'Elementor requise.
     *
     * @since  1.0.0
     * @access private
     * @var    string
     */
    private $minimum_elementor_version = '3.0.0';

    /**
     * Constructeur.
     *
     * @since 1.0.0
     * @param array               $modules     Modules Katadoo.
     * @param Katadoo_Odoo_Client $odoo_client Client Odoo.
     * @param Katadoo_Config      $config      Configuration.
     */
    public function __construct( $modules, $odoo_client, $config ) {
        $this->modules     = $modules;
        $this->odoo_client = $odoo_client;
        $this->config      = $config;

        // Stocker l'instance pour accès statique
        self::$instance = $this;

        $this->init();
    }

    /**
     * Retourne l'instance courante.
     *
     * @since  1.0.0
     * @return Katadoo_Elementor|null
     */
    public static function get_instance() {
        return self::$instance;
    }

    /**
     * Retourne le client Odoo.
     *
     * @since  1.0.0
     * @return Katadoo_Odoo_Client
     */
    public function get_odoo_client() {
        return $this->odoo_client;
    }

    /**
     * Retourne la configuration.
     *
     * @since  1.0.0
     * @return Katadoo_Config
     */
    public function get_config() {
        return $this->config;
    }

    /**
     * Initialise l'intégration Elementor.
     *
     * @since 1.0.0
     */
    public function init() {
        // Vérifier la version d'Elementor
        if ( ! $this->is_compatible() ) {
            add_action( 'admin_notices', array( $this, 'admin_notice_minimum_elementor_version' ) );
            return;
        }

        // Enregistrer les widgets
        add_action( 'elementor/widgets/register', array( $this, 'register_widgets' ) );

        // Enregistrer la catégorie de widgets
        add_action( 'elementor/elements/categories_registered', array( $this, 'register_categories' ) );

        // Enregistrer les styles pour l'éditeur
        add_action( 'elementor/editor/after_enqueue_styles', array( $this, 'editor_styles' ) );
    }

    /**
     * Vérifie la compatibilité avec Elementor.
     *
     * @since  1.0.0
     * @return bool
     */
    public function is_compatible() {
        if ( ! defined( 'ELEMENTOR_VERSION' ) ) {
            return false;
        }

        return version_compare( ELEMENTOR_VERSION, $this->minimum_elementor_version, '>=' );
    }

    /**
     * Notice d'administration pour version Elementor insuffisante.
     *
     * @since 1.0.0
     */
    public function admin_notice_minimum_elementor_version() {
        if ( isset( $_GET['activate'] ) ) {
            unset( $_GET['activate'] );
        }

        $message = sprintf(
            /* translators: 1: Plugin name 2: Elementor 3: Required Elementor version */
            esc_html__( '"%1$s" nécessite "%2$s" version %3$s ou supérieure.', 'katadoo' ),
            '<strong>Katadoo</strong>',
            '<strong>Elementor</strong>',
            $this->minimum_elementor_version
        );

        printf( '<div class="notice notice-warning is-dismissible"><p>%1$s</p></div>', $message );
    }

    /**
     * Enregistre la catégorie de widgets Katadoo.
     *
     * @since 1.0.0
     * @param \Elementor\Elements_Manager $elements_manager Gestionnaire d'éléments.
     */
    public function register_categories( $elements_manager ) {
        $elements_manager->add_category(
            'katadoo',
            array(
                'title' => __( 'Katadoo', 'katadoo' ),
                'icon'  => 'eicon-integration',
            )
        );
    }

    /**
     * Enregistre les widgets Elementor.
     *
     * @since 1.0.0
     * @param \Elementor\Widgets_Manager $widgets_manager Gestionnaire de widgets.
     */
    public function register_widgets( $widgets_manager ) {
        // Charger les fichiers des widgets
        require_once KATADOO_PLUGIN_DIR . 'elementor/widgets/class-newsletter-widget.php';
        require_once KATADOO_PLUGIN_DIR . 'elementor/widgets/class-helpdesk-widget.php';

        // Note: Les widgets Elementor doivent être instanciés sans paramètres personnalisés
        // Ils récupèrent les dépendances via Katadoo_Elementor::get_instance()

        // Enregistrer les widgets
        if ( $this->config->is_module_enabled( 'newsletter' ) ) {
            $widgets_manager->register( new Katadoo_Newsletter_Widget() );
        }

        if ( $this->config->is_module_enabled( 'helpdesk' ) ) {
            $widgets_manager->register( new Katadoo_Helpdesk_Widget() );
        }
    }

    /**
     * Styles pour l'éditeur Elementor.
     *
     * @since 1.0.0
     */
    public function editor_styles() {
        wp_enqueue_style(
            'katadoo-elementor-editor',
            KATADOO_PLUGIN_URL . 'assets/css/public.css',
            array(),
            KATADOO_VERSION
        );
    }
}
