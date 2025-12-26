<?php
/**
 * Template de la page ReCaptcha.
 *
 * @package    Katadoo
 * @subpackage Katadoo/includes/admin/partials
 * @since      1.0.3
 */

// Sécurité : empêcher l'accès direct
if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

$defaults = array(
    'enabled'    => false,
    'site_key'   => '',
    'secret_key' => '',
    'threshold'  => 0.5,
);

$saved_settings = get_option( 'katadoo_recaptcha_settings', array() );
$settings = wp_parse_args( $saved_settings, $defaults );
?>

<div class="wrap katadoo-admin">
    <h1>
        <span class="dashicons dashicons-shield"></span>
        <?php esc_html_e( 'Katadoo - Google ReCaptcha v3', 'katadoo' ); ?>
    </h1>

    <div class="katadoo-admin-content">
        <div class="katadoo-card">
            <div class="katadoo-card-header">
                <h2><?php esc_html_e( 'Configuration ReCaptcha', 'katadoo' ); ?></h2>
                <p class="description">
                    <?php esc_html_e( 'Protégez vos formulaires contre le spam avec Google ReCaptcha v3.', 'katadoo' ); ?>
                </p>
            </div>

            <form method="post" action="options.php">
                <?php settings_fields( 'katadoo_recaptcha' ); ?>

                <table class="form-table">
                    <tr>
                        <th scope="row">
                            <?php esc_html_e( 'Activer ReCaptcha', 'katadoo' ); ?>
                        </th>
                        <td>
                            <label class="katadoo-switch">
                                <input type="checkbox"
                                       name="katadoo_recaptcha_settings[enabled]"
                                       value="1"
                                       <?php checked( $settings['enabled'], 1 ); ?> />
                                <span class="katadoo-slider round"></span>
                            </label>
                            <p class="description">
                                <?php esc_html_e( 'Active la protection ReCaptcha v3 sur tous les formulaires Katadoo.', 'katadoo' ); ?>
                            </p>
                        </td>
                    </tr>

                    <tr>
                        <th scope="row">
                            <label for="recaptcha_site_key">
                                <?php esc_html_e( 'Clé du site', 'katadoo' ); ?>
                            </label>
                        </th>
                        <td>
                            <input type="text"
                                   id="recaptcha_site_key"
                                   name="katadoo_recaptcha_settings[site_key]"
                                   value="<?php echo esc_attr( $settings['site_key'] ); ?>"
                                   class="regular-text"
                                   placeholder="6Lc..." />
                            <p class="description">
                                <?php esc_html_e( 'Votre clé de site ReCaptcha v3.', 'katadoo' ); ?>
                            </p>
                        </td>
                    </tr>

                    <tr>
                        <th scope="row">
                            <label for="recaptcha_secret_key">
                                <?php esc_html_e( 'Clé secrète', 'katadoo' ); ?>
                            </label>
                        </th>
                        <td>
                            <input type="password"
                                   id="recaptcha_secret_key"
                                   name="katadoo_recaptcha_settings[secret_key]"
                                   value="<?php echo esc_attr( $settings['secret_key'] ); ?>"
                                   class="regular-text"
                                   placeholder="6Lc..." />
                            <p class="description">
                                <?php esc_html_e( 'Votre clé secrète ReCaptcha v3.', 'katadoo' ); ?>
                            </p>
                        </td>
                    </tr>

                    <tr>
                        <th scope="row">
                            <label for="recaptcha_threshold">
                                <?php esc_html_e( 'Seuil de score', 'katadoo' ); ?>
                            </label>
                        </th>
                        <td>
                            <input type="number"
                                   id="recaptcha_threshold"
                                   name="katadoo_recaptcha_settings[threshold]"
                                   value="<?php echo esc_attr( $settings['threshold'] ); ?>"
                                   step="0.1"
                                   min="0"
                                   max="1"
                                   class="small-text" />
                            <p class="description">
                                <?php esc_html_e( 'Score minimum (entre 0.0 et 1.0). Typiquement 0.5.', 'katadoo' ); ?>
                            </p>
                        </td>
                    </tr>
                </table>

                <div class="katadoo-actions">
                    <?php submit_button( __( 'Enregistrer', 'katadoo' ), 'primary', 'submit', false ); ?>
                </div>
            </form>
        </div>

        <div class="katadoo-sidebar">
            <div class="katadoo-card">
                <div class="katadoo-card-header">
                    <h3><?php esc_html_e( 'Aide', 'katadoo' ); ?></h3>
                </div>
                <div class="katadoo-card-body">
                    <p>
                        <?php
                        /* translators: %s: URL console google recaptcha */
                        printf(
                            esc_html__( 'Pour obtenir vos clés, rendez-vous sur la %s.', 'katadoo' ),
                            '<a href="https://www.google.com/recaptcha/admin/" target="_blank">Google ReCaptcha Admin Console</a>'
                        );
                        ?>
                    </p>
                    <p>
                        <strong><?php esc_html_e( 'Note :', 'katadoo' ); ?></strong>
                        <?php esc_html_e( 'Assurez-vous d\'utiliser une clé de type "V3 Score based".', 'katadoo' ); ?>
                    </p>
                </div>
            </div>
        </div>
    </div>
</div>
