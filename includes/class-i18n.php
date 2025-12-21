<?php
/**
 * Classe d'internationalisation du plugin.
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
 * Classe Katadoo_I18n.
 *
 * Définit les fonctionnalités d'internationalisation du plugin.
 *
 * @since 1.0.0
 */
class Katadoo_I18n
{

    /**
     * Charge le domaine de texte du plugin pour la traduction.
     *
     * @since 1.0.0
     */
    public function load_plugin_textdomain()
    {
        load_plugin_textdomain(
            'katadoo',
            false,
            dirname(dirname(plugin_basename(__FILE__))) . '/languages/'
        );
    }
}
