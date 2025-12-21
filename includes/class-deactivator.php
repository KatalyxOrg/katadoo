<?php
/**
 * Classe exécutée lors de la désactivation du plugin.
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
 * Classe Katadoo_Deactivator.
 *
 * Cette classe définit le code nécessaire pour la désactivation du plugin.
 *
 * @since 1.0.0
 */
class Katadoo_Deactivator
{

    /**
     * Actions exécutées lors de la désactivation du plugin.
     *
     * Note: Cette méthode ne supprime pas les données.
     * Les données sont conservées pour une éventuelle réactivation.
     *
     * @since 1.0.0
     */
    public static function deactivate()
    {
        // Flush les règles de réécriture
        flush_rewrite_rules();

        // Note: On ne supprime pas les options ici.
        // Elles seront supprimées uniquement lors de la désinstallation.
    }
}
