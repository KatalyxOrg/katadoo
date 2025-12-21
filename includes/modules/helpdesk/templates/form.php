<?php
/**
 * Template du formulaire Helpdesk.
 *
 * @package    Katadoo
 * @subpackage Katadoo/includes/modules/helpdesk/templates
 * @since      1.0.0
 *
 * @var array $atts Attributs du formulaire.
 */

// Sécurité : empêcher l'accès direct
if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

$form_id     = 'katadoo-helpdesk-' . uniqid();
$fields      = isset( $atts['fields'] ) ? $atts['fields'] : array( 'email', 'subject', 'description' );
$team_id     = isset( $atts['team_id'] ) ? absint( $atts['team_id'] ) : 0;
$button_text = isset( $atts['button_text'] ) ? $atts['button_text'] : __( 'Envoyer', 'katadoo' );
$class       = isset( $atts['class'] ) ? $atts['class'] : '';
?>

<div class="katadoo-form katadoo-helpdesk-form <?php echo esc_attr( $class ); ?>">
    <form id="<?php echo esc_attr( $form_id ); ?>" class="katadoo-form-inner" data-type="helpdesk">
        <?php wp_nonce_field( 'katadoo_public_nonce', 'katadoo_nonce' ); ?>
        <input type="hidden" name="action" value="katadoo_helpdesk_submit" />
        <input type="hidden" name="team_id" value="<?php echo esc_attr( $team_id ); ?>" />

        <?php if ( in_array( 'name', $fields, true ) ) : ?>
        <div class="katadoo-field">
            <label for="<?php echo esc_attr( $form_id ); ?>-name">
                <?php esc_html_e( 'Nom', 'katadoo' ); ?>
            </label>
            <input type="text"
                   id="<?php echo esc_attr( $form_id ); ?>-name"
                   name="name"
                   class="katadoo-input"
                   placeholder="<?php esc_attr_e( 'Votre nom', 'katadoo' ); ?>" />
        </div>
        <?php endif; ?>

        <div class="katadoo-field">
            <label for="<?php echo esc_attr( $form_id ); ?>-email">
                <?php esc_html_e( 'Email', 'katadoo' ); ?> <span class="required">*</span>
            </label>
            <input type="email"
                   id="<?php echo esc_attr( $form_id ); ?>-email"
                   name="email"
                   class="katadoo-input"
                   placeholder="<?php esc_attr_e( 'Votre email', 'katadoo' ); ?>"
                   required />
        </div>

        <?php if ( in_array( 'phone', $fields, true ) ) : ?>
        <div class="katadoo-field">
            <label for="<?php echo esc_attr( $form_id ); ?>-phone">
                <?php esc_html_e( 'Téléphone', 'katadoo' ); ?>
            </label>
            <input type="tel"
                   id="<?php echo esc_attr( $form_id ); ?>-phone"
                   name="phone"
                   class="katadoo-input"
                   placeholder="<?php esc_attr_e( 'Votre téléphone', 'katadoo' ); ?>" />
        </div>
        <?php endif; ?>

        <div class="katadoo-field">
            <label for="<?php echo esc_attr( $form_id ); ?>-subject">
                <?php esc_html_e( 'Sujet', 'katadoo' ); ?> <span class="required">*</span>
            </label>
            <input type="text"
                   id="<?php echo esc_attr( $form_id ); ?>-subject"
                   name="subject"
                   class="katadoo-input"
                   placeholder="<?php esc_attr_e( 'Sujet de votre demande', 'katadoo' ); ?>"
                   required />
        </div>

        <?php if ( in_array( 'priority', $fields, true ) ) : ?>
        <div class="katadoo-field">
            <label for="<?php echo esc_attr( $form_id ); ?>-priority">
                <?php esc_html_e( 'Priorité', 'katadoo' ); ?>
            </label>
            <select id="<?php echo esc_attr( $form_id ); ?>-priority"
                    name="priority"
                    class="katadoo-input">
                <option value="1"><?php esc_html_e( 'Basse', 'katadoo' ); ?></option>
                <option value="2" selected><?php esc_html_e( 'Normale', 'katadoo' ); ?></option>
                <option value="3"><?php esc_html_e( 'Haute', 'katadoo' ); ?></option>
                <option value="4"><?php esc_html_e( 'Urgente', 'katadoo' ); ?></option>
            </select>
        </div>
        <?php endif; ?>

        <div class="katadoo-field">
            <label for="<?php echo esc_attr( $form_id ); ?>-description">
                <?php esc_html_e( 'Description', 'katadoo' ); ?> <span class="required">*</span>
            </label>
            <textarea id="<?php echo esc_attr( $form_id ); ?>-description"
                      name="description"
                      class="katadoo-input katadoo-textarea"
                      placeholder="<?php esc_attr_e( 'Décrivez votre demande...', 'katadoo' ); ?>"
                      rows="5"
                      required></textarea>
        </div>

        <div class="katadoo-field katadoo-submit">
            <button type="submit" class="katadoo-button">
                <?php echo esc_html( $button_text ); ?>
            </button>
        </div>

        <div class="katadoo-message" style="display: none;"></div>
    </form>
</div>
