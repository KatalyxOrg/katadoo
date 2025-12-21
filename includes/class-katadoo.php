<?php
/**
 * Classe principale du plugin Katadoo.
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
 * Classe Katadoo.
 *
 * Cette classe orchestre le chargement du plugin et de ses dépendances.
 *
 * @since 1.0.0
 */
class Katadoo
{

    /**
     * Instance du loader pour gérer les hooks.
     *
     * @since  1.0.0
     * @access protected
     * @var    Katadoo_Loader $loader Maintient et enregistre tous les hooks.
     */
    protected $loader;

    /**
     * Instance du gestionnaire de configuration.
     *
     * @since  1.0.0
     * @access protected
     * @var    Katadoo_Config $config Gestionnaire de configuration.
     */
    protected $config;

    /**
     * Instance du client Odoo.
     *
     * @since  1.0.0
     * @access protected
     * @var    Katadoo_Odoo_Client $odoo_client Client API Odoo.
     */
    protected $odoo_client;

    /**
     * Modules chargés.
     *
     * @since  1.0.0
     * @access protected
     * @var    array $modules Liste des modules actifs.
     */
    protected $modules = array();

    /**
     * Version du plugin.
     *
     * @since  1.0.0
     * @access protected
     * @var    string $version Version actuelle du plugin.
     */
    protected $version;

    /**
     * Nom unique du plugin.
     *
     * @since  1.0.0
     * @access protected
     * @var    string $plugin_name Identifiant unique du plugin.
     */
    protected $plugin_name;

    /**
     * Constructeur.
     *
     * Initialise les propriétés de base et charge les dépendances.
     *
     * @since 1.0.0
     */
    public function __construct()
    {
        $this->version = KATADOO_VERSION;
        $this->plugin_name = 'katadoo';

        $this->load_dependencies();
        $this->set_locale();
        $this->init_core();
        $this->define_admin_hooks();
        $this->define_public_hooks();
        $this->load_modules();
        $this->init_elementor();
    }

    /**
     * Charge les fichiers de dépendances nécessaires.
     *
     * @since  1.0.0
     * @access private
     */
    private function load_dependencies()
    {
        // Le loader pour gérer les hooks
        require_once KATADOO_PLUGIN_DIR . 'includes/class-loader.php';
        $this->loader = new Katadoo_Loader();

        // L'internationalisation
        require_once KATADOO_PLUGIN_DIR . 'includes/class-i18n.php';

        // Le gestionnaire de configuration
        require_once KATADOO_PLUGIN_DIR . 'includes/core/class-config.php';

        // Le client Odoo
        require_once KATADOO_PLUGIN_DIR . 'includes/core/class-odoo-client.php';

        // L'interface des modules
        require_once KATADOO_PLUGIN_DIR . 'includes/core/interface-module.php';

        // L'administration
        require_once KATADOO_PLUGIN_DIR . 'includes/admin/class-admin.php';

        // Le frontend public
        require_once KATADOO_PLUGIN_DIR . 'includes/public/class-public.php';
        require_once KATADOO_PLUGIN_DIR . 'includes/public/class-shortcodes.php';
    }

    /**
     * Configure l'internationalisation du plugin.
     *
     * @since  1.0.0
     * @access private
     */
    private function set_locale()
    {
        $plugin_i18n = new Katadoo_I18n();
        $this->loader->add_action('plugins_loaded', $plugin_i18n, 'load_plugin_textdomain');
    }

    /**
     * Initialise les composants core.
     *
     * @since  1.0.0
     * @access private
     */
    private function init_core()
    {
        $this->config = new Katadoo_Config();
        $this->odoo_client = new Katadoo_Odoo_Client($this->config);
    }

    /**
     * Enregistre les hooks pour l'administration.
     *
     * @since  1.0.0
     * @access private
     */
    private function define_admin_hooks()
    {
        $plugin_admin = new Katadoo_Admin(
            $this->get_plugin_name(),
            $this->get_version(),
            $this->config,
            $this->odoo_client
        );

        $this->loader->add_action('admin_enqueue_scripts', $plugin_admin, 'enqueue_styles');
        $this->loader->add_action('admin_enqueue_scripts', $plugin_admin, 'enqueue_scripts');
        $this->loader->add_action('admin_menu', $plugin_admin, 'add_admin_menu');
        $this->loader->add_action('admin_init', $plugin_admin, 'register_settings');
        $this->loader->add_action('wp_ajax_katadoo_test_connection', $plugin_admin, 'ajax_test_connection');
        $this->loader->add_action('wp_ajax_katadoo_get_mailing_lists', $plugin_admin, 'ajax_get_mailing_lists');
        $this->loader->add_action('wp_ajax_katadoo_get_helpdesk_teams', $plugin_admin, 'ajax_get_helpdesk_teams');
    }

    /**
     * Enregistre les hooks pour le frontend.
     *
     * @since  1.0.0
     * @access private
     */
    private function define_public_hooks()
    {
        $plugin_public = new Katadoo_Public(
            $this->get_plugin_name(),
            $this->get_version()
        );

        $this->loader->add_action('wp_enqueue_scripts', $plugin_public, 'enqueue_styles');
        $this->loader->add_action('wp_enqueue_scripts', $plugin_public, 'enqueue_scripts');

        // Initialiser les shortcodes
        $shortcodes = new Katadoo_Shortcodes($this->odoo_client, $this->config);
        $this->loader->add_action('init', $shortcodes, 'register_shortcodes');

        // Endpoints AJAX pour les soumissions de formulaires
        $this->loader->add_action('wp_ajax_katadoo_newsletter_subscribe', $shortcodes, 'ajax_newsletter_subscribe');
        $this->loader->add_action('wp_ajax_nopriv_katadoo_newsletter_subscribe', $shortcodes, 'ajax_newsletter_subscribe');
        $this->loader->add_action('wp_ajax_katadoo_helpdesk_submit', $shortcodes, 'ajax_helpdesk_submit');
        $this->loader->add_action('wp_ajax_nopriv_katadoo_helpdesk_submit', $shortcodes, 'ajax_helpdesk_submit');
    }

    /**
     * Charge les modules activés.
     *
     * @since  1.0.0
     * @access private
     */
    private function load_modules()
    {
        $enabled_modules = $this->config->get_enabled_modules();

        // Module Newsletter
        if (in_array('newsletter', $enabled_modules, true)) {
            require_once KATADOO_PLUGIN_DIR . 'includes/modules/newsletter/class-newsletter.php';
            require_once KATADOO_PLUGIN_DIR . 'includes/modules/newsletter/class-newsletter-api.php';
            $this->modules['newsletter'] = new Katadoo_Newsletter($this->odoo_client, $this->config);
        }

        // Module Helpdesk
        if (in_array('helpdesk', $enabled_modules, true)) {
            require_once KATADOO_PLUGIN_DIR . 'includes/modules/helpdesk/class-helpdesk.php';
            require_once KATADOO_PLUGIN_DIR . 'includes/modules/helpdesk/class-helpdesk-api.php';
            $this->modules['helpdesk'] = new Katadoo_Helpdesk($this->odoo_client, $this->config);
        }

        // Initialiser chaque module
        foreach ($this->modules as $module) {
            $module->init();
        }
    }

    /**
     * Initialise l'intégration Elementor si disponible.
     *
     * @since  1.0.0
     * @access private
     */
    private function init_elementor()
    {
        // Vérifier si Elementor est actif
        $this->loader->add_action('plugins_loaded', $this, 'load_elementor_integration');
    }

    /**
     * Charge l'intégration Elementor.
     *
     * @since 1.0.0
     */
    public function load_elementor_integration()
    {
        if (did_action('elementor/loaded')) {
            require_once KATADOO_PLUGIN_DIR . 'elementor/class-elementor.php';
            new Katadoo_Elementor($this->modules, $this->odoo_client, $this->config);
        }
    }

    /**
     * Exécute le plugin.
     *
     * @since 1.0.0
     */
    public function run()
    {
        $this->loader->run();
    }

    /**
     * Retourne le nom du plugin.
     *
     * @since  1.0.0
     * @return string Le nom du plugin.
     */
    public function get_plugin_name()
    {
        return $this->plugin_name;
    }

    /**
     * Retourne le loader des hooks.
     *
     * @since  1.0.0
     * @return Katadoo_Loader Le loader.
     */
    public function get_loader()
    {
        return $this->loader;
    }

    /**
     * Retourne la version du plugin.
     *
     * @since  1.0.0
     * @return string La version du plugin.
     */
    public function get_version()
    {
        return $this->version;
    }

    /**
     * Retourne les modules chargés.
     *
     * @since  1.0.0
     * @return array Les modules.
     */
    public function get_modules()
    {
        return $this->modules;
    }

    /**
     * Retourne le client Odoo.
     *
     * @since  1.0.0
     * @return Katadoo_Odoo_Client Le client Odoo.
     */
    public function get_odoo_client()
    {
        return $this->odoo_client;
    }

    /**
     * Retourne la configuration.
     *
     * @since  1.0.0
     * @return Katadoo_Config La configuration.
     */
    public function get_config()
    {
        return $this->config;
    }
}
