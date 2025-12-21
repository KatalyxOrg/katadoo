<?php
/**
 * Template de la page de connexion Odoo.
 *
 * @package    Katadoo
 * @subpackage Katadoo/includes/admin/partials
 * @since      1.0.0
 */

// Sécurité : empêcher l'accès direct
if ( ! defined( 'ABSPATH' ) ) {
    exit;
}
?>

<div class="wrap katadoo-admin">
    <h1>
        <span class="dashicons dashicons-share-alt"></span>
        <?php esc_html_e( 'Katadoo - Connexion Odoo', 'katadoo' ); ?>
    </h1>

    <div class="katadoo-admin-content">
        <div class="katadoo-card">
            <div class="katadoo-card-header">
                <h2><?php esc_html_e( 'Configuration de la connexion', 'katadoo' ); ?></h2>
                <p class="description">
                    <?php esc_html_e( 'Configurez les paramètres de connexion à votre instance Odoo.', 'katadoo' ); ?>
                </p>
            </div>

            <form method="post" action="options.php" id="katadoo-connection-form">
                <?php settings_fields( 'katadoo_connection' ); ?>

                <table class="form-table">
                    <tr>
                        <th scope="row">
                            <label for="katadoo_odoo_url">
                                <?php esc_html_e( 'URL Odoo', 'katadoo' ); ?>
                            </label>
                        </th>
                        <td>
                            <input type="url"
                                   id="katadoo_odoo_url"
                                   name="katadoo_odoo_url"
                                   value="<?php echo esc_attr( get_option( 'katadoo_odoo_url', '' ) ); ?>"
                                   class="regular-text"
                                   placeholder="https://votre-instance.odoo.com"
                                   required />
                            <p class="description">
                                <?php esc_html_e( 'L\'URL de votre instance Odoo (sans slash final).', 'katadoo' ); ?>
                            </p>
                        </td>
                    </tr>

                    <tr>
                        <th scope="row">
                            <label for="katadoo_odoo_database">
                                <?php esc_html_e( 'Base de données', 'katadoo' ); ?>
                            </label>
                        </th>
                        <td>
                            <input type="text"
                                   id="katadoo_odoo_database"
                                   name="katadoo_odoo_database"
                                   value="<?php echo esc_attr( get_option( 'katadoo_odoo_database', '' ) ); ?>"
                                   class="regular-text"
                                   required />
                            <p class="description">
                                <?php esc_html_e( 'Le nom de la base de données Odoo.', 'katadoo' ); ?>
                            </p>
                        </td>
                    </tr>

                    <tr>
                        <th scope="row">
                            <label for="katadoo_odoo_username">
                                <?php esc_html_e( 'Nom d\'utilisateur', 'katadoo' ); ?>
                            </label>
                        </th>
                        <td>
                            <input type="text"
                                   id="katadoo_odoo_username"
                                   name="katadoo_odoo_username"
                                   value="<?php echo esc_attr( get_option( 'katadoo_odoo_username', '' ) ); ?>"
                                   class="regular-text"
                                   placeholder="admin@example.com"
                                   required />
                            <p class="description">
                                <?php esc_html_e( 'L\'email ou le login de l\'utilisateur Odoo.', 'katadoo' ); ?>
                            </p>
                        </td>
                    </tr>

                    <tr>
                        <th scope="row">
                            <label for="katadoo_odoo_api_key">
                                <?php esc_html_e( 'Clé API', 'katadoo' ); ?>
                            </label>
                        </th>
                        <td>
                            <input type="password"
                                   id="katadoo_odoo_api_key"
                                   name="katadoo_odoo_api_key"
                                   value="<?php echo esc_attr( get_option( 'katadoo_odoo_api_key', '' ) ); ?>"
                                   class="regular-text"
                                   required />
                            <p class="description">
                                <?php esc_html_e( 'La clé API de l\'utilisateur Odoo (disponible depuis Odoo 14+).', 'katadoo' ); ?>
                                <a href="https://www.odoo.com/documentation/17.0/developer/reference/external_api.html#api-keys" target="_blank">
                                    <?php esc_html_e( 'Comment générer une clé API ?', 'katadoo' ); ?>
                                </a>
                            </p>
                        </td>
                    </tr>
                </table>

                <div class="katadoo-actions">
                    <?php submit_button( __( 'Enregistrer', 'katadoo' ), 'primary', 'submit', false ); ?>

                    <button type="button" id="katadoo-test-connection" class="button button-secondary">
                        <span class="dashicons dashicons-update"></span>
                        <?php esc_html_e( 'Tester la connexion', 'katadoo' ); ?>
                    </button>
                </div>

                <div id="katadoo-connection-result" class="katadoo-notice" style="display: none;"></div>
            </form>
        </div>

        <div class="katadoo-sidebar">
            <div class="katadoo-card">
                <div class="katadoo-card-header">
                    <h3><?php esc_html_e( 'À propos', 'katadoo' ); ?></h3>
                </div>
                <div class="katadoo-card-body">
                    <p>
                        <strong>Katadoo</strong> <?php echo esc_html( KATADOO_VERSION ); ?>
                    </p>
                    <p>
                        <?php esc_html_e( 'Un plugin open source par Katalyx pour connecter Odoo à WordPress.', 'katadoo' ); ?>
                    </p>
                    <ul>
                        <li>
                            <a href="https://github.com/katalyxorg/katadoo" target="_blank">
                                <span class="dashicons dashicons-external"></span>
                                <?php esc_html_e( 'GitHub', 'katadoo' ); ?>
                            </a>
                        </li>
                        <li>
                            <a href="https://katalyx.fr" target="_blank">
                                <span class="dashicons dashicons-external"></span>
                                <?php esc_html_e( 'Site web Katalyx', 'katadoo' ); ?>
                            </a>
                        </li>
                    </ul>
                </div>
            </div>

            <div class="katadoo-card">
                <div class="katadoo-card-header">
                    <h3><?php esc_html_e( 'Prérequis Odoo', 'katadoo' ); ?></h3>
                </div>
                <div class="katadoo-card-body">
                    <ul>
                        <li><?php esc_html_e( 'Odoo 14+ (pour les clés API)', 'katadoo' ); ?></li>
                        <li><?php esc_html_e( 'Module Email Marketing (Newsletter)', 'katadoo' ); ?></li>
                        <li><?php esc_html_e( 'Module Helpdesk (Assistance)', 'katadoo' ); ?></li>
                    </ul>
                </div>
            </div>
        </div>
    </div>
</div>
