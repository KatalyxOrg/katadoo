<?php
/**
 * Gestionnaire de configuration du plugin.
 *
 * @package    Katadoo
 * @subpackage Katadoo/includes/core
 * @since      1.0.0
 */

// Sécurité : empêcher l'accès direct
if (!defined('ABSPATH')) {
    exit;
}

/**
 * Classe Katadoo_Config.
 *
 * Gère toutes les options de configuration du plugin.
 *
 * @since 1.0.0
 */
class Katadoo_Config
{

    /**
     * Préfixe des options.
     *
     * @since  1.0.0
     * @access private
     * @var    string
     */
    private $prefix = 'katadoo_';

    /**
     * Cache des options.
     *
     * @since  1.0.0
     * @access private
     * @var    array
     */
    private $cache = array();

    /**
     * Retourne l'URL Odoo.
     *
     * @since  1.0.0
     * @return string L'URL de l'instance Odoo.
     */
    public function get_odoo_url()
    {
        return $this->get_option('odoo_url', '');
    }

    /**
     * Retourne le nom de la base de données Odoo.
     *
     * @since  1.0.0
     * @return string Le nom de la base de données.
     */
    public function get_odoo_database()
    {
        return $this->get_option('odoo_database', '');
    }

    /**
     * Retourne le nom d'utilisateur Odoo.
     *
     * @since  1.0.0
     * @return string Le nom d'utilisateur.
     */
    public function get_odoo_username()
    {
        return $this->get_option('odoo_username', '');
    }

    /**
     * Retourne la clé API Odoo.
     *
     * @since  1.0.0
     * @return string La clé API.
     */
    public function get_odoo_api_key()
    {
        return $this->get_option('odoo_api_key', '');
    }

    /**
     * Vérifie si la configuration Odoo est complète.
     *
     * @since  1.0.0
     * @return bool True si la configuration est complète.
     */
    public function is_configured()
    {
        return !empty($this->get_odoo_url())
            && !empty($this->get_odoo_database())
            && !empty($this->get_odoo_username())
            && !empty($this->get_odoo_api_key());
    }

    /**
     * Retourne la liste des modules activés.
     *
     * @since  1.0.0
     * @return array Liste des identifiants des modules activés.
     */
    public function get_enabled_modules()
    {
        $modules = $this->get_option('modules', array());
        $enabled = array();

        foreach ($modules as $module_id => $is_enabled) {
            if ($is_enabled) {
                $enabled[] = $module_id;
            }
        }

        return $enabled;
    }

    /**
     * Vérifie si un module est activé.
     *
     * @since  1.0.0
     * @param  string $module_id Identifiant du module.
     * @return bool True si le module est activé.
     */
    public function is_module_enabled($module_id)
    {
        $modules = $this->get_option('modules', array());
        return isset($modules[$module_id]) && $modules[$module_id];
    }

    /**
     * Retourne les paramètres du module Newsletter.
     *
     * @since  1.0.0
     * @return array Paramètres du module Newsletter.
     */
    public function get_newsletter_settings()
    {
        return $this->get_option('newsletter_settings', array(
            'default_list_id' => 0,
            'form_fields' => array('email', 'name'),
            'success_message' => __('Merci pour votre inscription !', 'katadoo'),
            'error_message' => __('Une erreur est survenue. Veuillez réessayer.', 'katadoo'),
            'button_text' => __('S\'inscrire', 'katadoo'),
        ));
    }

    /**
     * Retourne les paramètres du module Helpdesk.
     *
     * @since  1.0.0
     * @return array Paramètres du module Helpdesk.
     */
    public function get_helpdesk_settings()
    {
        return $this->get_option('helpdesk_settings', array(
            'default_team_id' => 0,
            'form_fields' => array('name', 'email', 'subject', 'description'),
            'success_message' => __('Votre ticket a été créé avec succès !', 'katadoo'),
            'error_message' => __('Une erreur est survenue. Veuillez réessayer.', 'katadoo'),
            'button_text' => __('Envoyer', 'katadoo'),
        ));
    }

    /**
     * Retourne les paramètres Google ReCaptcha v3.
     *
     * @since  1.0.3
     * @return array Paramètres ReCaptcha.
     */
    public function get_recaptcha_settings()
    {
        return $this->get_option('recaptcha_settings', array(
            'enabled'    => false,
            'site_key'   => '',
            'secret_key' => '',
            'threshold'  => 0.5,
        ));
    }

    /**
     * Récupère une option avec mise en cache.
     *
     * @since  1.0.0
     * @param  string $key     Clé de l'option (sans préfixe).
     * @param  mixed  $default Valeur par défaut.
     * @return mixed Valeur de l'option.
     */
    public function get_option($key, $default = null)
    {
        $full_key = $this->prefix . $key;

        if (!isset($this->cache[$full_key])) {
            $this->cache[$full_key] = get_option($full_key, $default);
        }

        return $this->cache[$full_key];
    }

    /**
     * Met à jour une option.
     *
     * @since  1.0.0
     * @param  string $key   Clé de l'option (sans préfixe).
     * @param  mixed  $value Nouvelle valeur.
     * @return bool True si la mise à jour a réussi.
     */
    public function update_option($key, $value)
    {
        $full_key = $this->prefix . $key;

        // Vider le cache
        unset($this->cache[$full_key]);

        return update_option($full_key, $value);
    }

    /**
     * Supprime une option.
     *
     * @since  1.0.0
     * @param  string $key Clé de l'option (sans préfixe).
     * @return bool True si la suppression a réussi.
     */
    public function delete_option($key)
    {
        $full_key = $this->prefix . $key;

        // Vider le cache
        unset($this->cache[$full_key]);

        return delete_option($full_key);
    }

    /**
     * Retourne toutes les clés d'options disponibles.
     *
     * @since  1.0.0
     * @return array Liste des clés d'options.
     */
    public function get_all_option_keys()
    {
        return array(
            'odoo_url',
            'odoo_database',
            'odoo_username',
            'odoo_api_key',
            'modules',
            'newsletter_settings',
            'helpdesk_settings',
            'recaptcha_settings',
        );
    }

    /**
     * Efface le cache des options.
     *
     * @since 1.0.0
     */
    public function clear_cache()
    {
        $this->cache = array();
    }
}
