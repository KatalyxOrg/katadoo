<?php
/**
 * Katadoo - Plugin WordPress pour Connecter Odoo
 *
 * @package           Katadoo
 * @author            Katalyx
 * @copyright         2024 Katalyx
 * @license           GPL-2.0-or-later
 *
 * @wordpress-plugin
 * Plugin Name:       Katadoo
 * Plugin URI:        https://github.com/katalyx/katadoo
 * Description:       Connectez votre site WordPress à Odoo. Newsletter, Helpdesk et plus encore.
 * Version:           1.0.0
 * Requires at least: 6.0
 * Requires PHP:      8.0
 * Author:            Katalyx
 * Author URI:        https://katalyx.fr
 * Text Domain:       katadoo
 * Domain Path:       /languages
 * License:           GPL v2 or later
 * License URI:       https://www.gnu.org/licenses/gpl-2.0.html
 */

// Sécurité : empêcher l'accès direct
if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

/**
 * Version actuelle du plugin.
 */
define( 'KATADOO_VERSION', '1.0.0' );

/**
 * Chemin absolu du répertoire du plugin.
 */
define( 'KATADOO_PLUGIN_DIR', plugin_dir_path( __FILE__ ) );

/**
 * URL du répertoire du plugin.
 */
define( 'KATADOO_PLUGIN_URL', plugin_dir_url( __FILE__ ) );

/**
 * Nom du fichier principal du plugin.
 */
define( 'KATADOO_PLUGIN_BASENAME', plugin_basename( __FILE__ ) );

/**
 * Préfixe pour les options de base de données.
 */
define( 'KATADOO_PREFIX', 'katadoo_' );

/**
 * Code exécuté lors de l'activation du plugin.
 *
 * @since 1.0.0
 */
function katadoo_activate() {
    require_once KATADOO_PLUGIN_DIR . 'includes/class-activator.php';
    Katadoo_Activator::activate();
}

/**
 * Code exécuté lors de la désactivation du plugin.
 *
 * @since 1.0.0
 */
function katadoo_deactivate() {
    require_once KATADOO_PLUGIN_DIR . 'includes/class-deactivator.php';
    Katadoo_Deactivator::deactivate();
}

register_activation_hook( __FILE__, 'katadoo_activate' );
register_deactivation_hook( __FILE__, 'katadoo_deactivate' );

/**
 * Autoloader pour les classes du plugin.
 *
 * @param string $class_name Le nom de la classe à charger.
 * @since 1.0.0
 */
function katadoo_autoloader( $class_name ) {
    // Vérifier si la classe appartient à notre namespace
    if ( strpos( $class_name, 'Katadoo' ) !== 0 ) {
        return;
    }

    // Convertir le nom de classe en chemin de fichier
    $class_file = 'class-' . strtolower( str_replace( '_', '-', str_replace( 'Katadoo_', '', $class_name ) ) ) . '.php';

    // Chemins possibles pour les fichiers de classe
    $paths = array(
        KATADOO_PLUGIN_DIR . 'includes/',
        KATADOO_PLUGIN_DIR . 'includes/core/',
        KATADOO_PLUGIN_DIR . 'includes/admin/',
        KATADOO_PLUGIN_DIR . 'includes/public/',
        KATADOO_PLUGIN_DIR . 'includes/modules/newsletter/',
        KATADOO_PLUGIN_DIR . 'includes/modules/helpdesk/',
        KATADOO_PLUGIN_DIR . 'elementor/',
        KATADOO_PLUGIN_DIR . 'elementor/widgets/',
    );

    foreach ( $paths as $path ) {
        $file = $path . $class_file;
        if ( file_exists( $file ) ) {
            require_once $file;
            return;
        }
    }

    // Chercher aussi les interfaces
    $interface_file = 'interface-' . strtolower( str_replace( '_', '-', str_replace( 'Katadoo_', '', $class_name ) ) ) . '.php';
    foreach ( $paths as $path ) {
        $file = $path . $interface_file;
        if ( file_exists( $file ) ) {
            require_once $file;
            return;
        }
    }
}

spl_autoload_register( 'katadoo_autoloader' );

/**
 * Démarrer le plugin.
 *
 * @since 1.0.0
 */
function katadoo_run() {
    $plugin = new Katadoo();
    $plugin->run();
}

katadoo_run();
