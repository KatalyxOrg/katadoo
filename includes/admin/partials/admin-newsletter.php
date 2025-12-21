<?php
/**
 * Template de la page Newsletter.
 *
 * @package    Katadoo
 * @subpackage Katadoo/includes/admin/partials
 * @since      1.0.0
 */

// Sécurité : empêcher l'accès direct
if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

$defaults = array(
    'default_list_id' => 0,
    'form_fields'     => array( 'email', 'name' ),
    'success_message' => __( 'Merci pour votre inscription !', 'katadoo' ),
    'error_message'   => __( 'Une erreur est survenue. Veuillez réessayer.', 'katadoo' ),
    'button_text'     => __( 'S\'inscrire', 'katadoo' ),
);

$saved_settings = get_option( 'katadoo_newsletter_settings', array() );
$settings = wp_parse_args( $saved_settings, $defaults );

$available_fields = array(
    'email' => array(
        'label'    => __( 'Email', 'katadoo' ),
        'required' => true,
    ),
    'name' => array(
        'label'    => __( 'Nom', 'katadoo' ),
        'required' => false,
    ),
    'phone' => array(
        'label'    => __( 'Téléphone', 'katadoo' ),
        'required' => false,
    ),
    'company' => array(
        'label'    => __( 'Entreprise', 'katadoo' ),
        'required' => false,
    ),
);
?>

<div class="wrap katadoo-admin">
    <h1>
        <span class="dashicons dashicons-email-alt"></span>
        <?php esc_html_e( 'Katadoo - Newsletter', 'katadoo' ); ?>
    </h1>

    <div class="katadoo-admin-content">
        <div class="katadoo-card">
            <div class="katadoo-card-header">
                <h2><?php esc_html_e( 'Configuration du module Newsletter', 'katadoo' ); ?></h2>
                <p class="description">
                    <?php esc_html_e( 'Configurez le formulaire d\'inscription à la newsletter.', 'katadoo' ); ?>
                </p>
            </div>

            <form method="post" action="options.php">
                <?php settings_fields( 'katadoo_newsletter' ); ?>

                <table class="form-table">
                    <tr>
                        <th scope="row">
                            <label for="newsletter_default_list">
                                <?php esc_html_e( 'Liste de diffusion par défaut', 'katadoo' ); ?>
                            </label>
                        </th>
                        <td>
                            <select id="newsletter_default_list"
                                    name="katadoo_newsletter_settings[default_list_id]"
                                    class="regular-text">
                                <option value="0"><?php esc_html_e( '-- Chargement des listes...', 'katadoo' ); ?></option>
                            </select>
                            <button type="button" id="katadoo-refresh-lists" class="button button-secondary">
                                <span class="dashicons dashicons-update"></span>
                                <?php esc_html_e( 'Rafraîchir', 'katadoo' ); ?>
                            </button>
                            <p class="description">
                                <?php esc_html_e( 'Sélectionnez la liste de diffusion Odoo pour les inscriptions.', 'katadoo' ); ?>
                            </p>
                            <input type="hidden" id="newsletter_default_list_value" value="<?php echo esc_attr( $settings['default_list_id'] ); ?>" />
                        </td>
                    </tr>

                    <tr>
                        <th scope="row">
                            <?php esc_html_e( 'Champs du formulaire', 'katadoo' ); ?>
                        </th>
                        <td>
                            <fieldset>
                                <?php foreach ( $available_fields as $field_id => $field ) : ?>
                                    <label>
                                        <input type="checkbox"
                                               name="katadoo_newsletter_settings[form_fields][]"
                                               value="<?php echo esc_attr( $field_id ); ?>"
                                               <?php checked( in_array( $field_id, $settings['form_fields'], true ) ); ?>
                                               <?php disabled( $field['required'] ); ?> />
                                        <?php echo esc_html( $field['label'] ); ?>
                                        <?php if ( $field['required'] ) : ?>
                                            <span class="required">*</span>
                                        <?php endif; ?>
                                    </label>
                                    <br />
                                <?php endforeach; ?>
                            </fieldset>
                            <p class="description">
                                <?php esc_html_e( 'Sélectionnez les champs à afficher dans le formulaire.', 'katadoo' ); ?>
                            </p>
                        </td>
                    </tr>

                    <tr>
                        <th scope="row">
                            <label for="newsletter_success_message">
                                <?php esc_html_e( 'Message de succès', 'katadoo' ); ?>
                            </label>
                        </th>
                        <td>
                            <textarea id="newsletter_success_message"
                                      name="katadoo_newsletter_settings[success_message]"
                                      class="large-text"
                                      rows="2"><?php echo esc_textarea( $settings['success_message'] ); ?></textarea>
                        </td>
                    </tr>

                    <tr>
                        <th scope="row">
                            <label for="newsletter_error_message">
                                <?php esc_html_e( 'Message d\'erreur', 'katadoo' ); ?>
                            </label>
                        </th>
                        <td>
                            <textarea id="newsletter_error_message"
                                      name="katadoo_newsletter_settings[error_message]"
                                      class="large-text"
                                      rows="2"><?php echo esc_textarea( $settings['error_message'] ); ?></textarea>
                        </td>
                    </tr>

                    <tr>
                        <th scope="row">
                            <label for="newsletter_button_text">
                                <?php esc_html_e( 'Texte du bouton', 'katadoo' ); ?>
                            </label>
                        </th>
                        <td>
                            <input type="text"
                                   id="newsletter_button_text"
                                   name="katadoo_newsletter_settings[button_text]"
                                   value="<?php echo esc_attr( $settings['button_text'] ); ?>"
                                   class="regular-text" />
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
                    <h3><?php esc_html_e( 'Utilisation', 'katadoo' ); ?></h3>
                </div>
                <div class="katadoo-card-body">
                    <p><?php esc_html_e( 'Utilisez le shortcode suivant pour afficher le formulaire :', 'katadoo' ); ?></p>
                    <code>[katadoo_newsletter]</code>

                    <h4><?php esc_html_e( 'Paramètres du shortcode', 'katadoo' ); ?></h4>
                    <ul>
                        <li><code>list_id</code> - <?php esc_html_e( 'ID de la liste (optionnel)', 'katadoo' ); ?></li>
                        <li><code>show_name</code> - <?php esc_html_e( 'Afficher le champ nom (true/false)', 'katadoo' ); ?></li>
                        <li><code>button_text</code> - <?php esc_html_e( 'Texte personnalisé du bouton', 'katadoo' ); ?></li>
                    </ul>

                    <h4><?php esc_html_e( 'Exemple', 'katadoo' ); ?></h4>
                    <code>[katadoo_newsletter list_id="1" show_name="true"]</code>
                </div>
            </div>
        </div>
    </div>
</div>
