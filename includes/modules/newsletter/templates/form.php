<?php
/**
 * Template du formulaire Newsletter.
 *
 * @package    Katadoo
 * @subpackage Katadoo/includes/modules/newsletter/templates
 * @since      1.0.0
 *
 * @var array $atts Attributs du formulaire.
 */

// Sécurité : empêcher l'accès direct
if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

$form_id     = 'katadoo-newsletter-' . uniqid();
$fields      = isset( $atts['fields'] ) ? $atts['fields'] : array( 'email' );
$list_id     = isset( $atts['list_id'] ) ? absint( $atts['list_id'] ) : 0;
$button_text = isset( $atts['button_text'] ) ? $atts['button_text'] : __( 'S\'inscrire', 'katadoo' );
$class       = isset( $atts['class'] ) ? $atts['class'] : '';
?>

<div class="katadoo-form katadoo-newsletter-form <?php echo esc_attr( $class ); ?>">
    <form id="<?php echo esc_attr( $form_id ); ?>" class="katadoo-form-inner" data-type="newsletter">
        <?php wp_nonce_field( 'katadoo_public_nonce', 'katadoo_nonce' ); ?>
        <input type="hidden" name="action" value="katadoo_newsletter_subscribe" />
        <input type="hidden" name="list_id" value="<?php echo esc_attr( $list_id ); ?>" />

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

        <?php if ( in_array( 'company', $fields, true ) ) : ?>
        <div class="katadoo-field">
            <label for="<?php echo esc_attr( $form_id ); ?>-company">
                <?php esc_html_e( 'Entreprise', 'katadoo' ); ?>
            </label>
            <input type="text"
                   id="<?php echo esc_attr( $form_id ); ?>-company"
                   name="company"
                   class="katadoo-input"
                   placeholder="<?php esc_attr_e( 'Votre entreprise', 'katadoo' ); ?>" />
        </div>
        <?php endif; ?>

        <div class="katadoo-field katadoo-submit">
            <button type="submit" class="katadoo-button">
                <?php echo esc_html( $button_text ); ?>
            </button>
        </div>

        <div class="katadoo-message" style="display: none;"></div>
    </form>
</div>
