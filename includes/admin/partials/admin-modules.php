<?php
/**
 * Template de la page des modules.
 *
 * @package    Katadoo
 * @subpackage Katadoo/includes/admin/partials
 * @since      1.0.0
 */

// Sécurité : empêcher l'accès direct
if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

$modules_option = get_option( 'katadoo_modules', array(
    'newsletter' => true,
    'helpdesk'   => true,
) );

$available_modules = array(
    'newsletter' => array(
        'name'        => __( 'Newsletter', 'katadoo' ),
        'description' => __( 'Ajoutez des formulaires d\'inscription à la newsletter Odoo sur votre site WordPress.', 'katadoo' ),
        'icon'        => 'dashicons-email-alt',
        'shortcode'   => '[katadoo_newsletter]',
    ),
    'helpdesk' => array(
        'name'        => __( 'Helpdesk', 'katadoo' ),
        'description' => __( 'Permettez à vos visiteurs de créer des tickets de support directement depuis WordPress.', 'katadoo' ),
        'icon'        => 'dashicons-tickets-alt',
        'shortcode'   => '[katadoo_helpdesk]',
    ),
);
?>

<div class="wrap katadoo-admin">
    <h1>
        <span class="dashicons dashicons-share-alt"></span>
        <?php esc_html_e( 'Katadoo - Modules', 'katadoo' ); ?>
    </h1>

    <div class="katadoo-admin-content">
        <div class="katadoo-card katadoo-card-full">
            <div class="katadoo-card-header">
                <h2><?php esc_html_e( 'Gestion des modules', 'katadoo' ); ?></h2>
                <p class="description">
                    <?php esc_html_e( 'Activez ou désactivez les modules Katadoo selon vos besoins.', 'katadoo' ); ?>
                </p>
            </div>

            <form method="post" action="options.php">
                <?php settings_fields( 'katadoo_modules' ); ?>

                <div class="katadoo-modules-grid">
                    <?php foreach ( $available_modules as $module_id => $module ) : ?>
                        <div class="katadoo-module-card <?php echo ! empty( $modules_option[ $module_id ] ) ? 'active' : ''; ?>">
                            <div class="katadoo-module-header">
                                <span class="dashicons <?php echo esc_attr( $module['icon'] ); ?>"></span>
                                <h3><?php echo esc_html( $module['name'] ); ?></h3>
                            </div>
                            <div class="katadoo-module-body">
                                <p><?php echo esc_html( $module['description'] ); ?></p>
                                <p class="katadoo-shortcode">
                                    <code><?php echo esc_html( $module['shortcode'] ); ?></code>
                                </p>
                            </div>
                            <div class="katadoo-module-footer">
                                <label class="katadoo-toggle">
                                    <input type="checkbox"
                                           name="katadoo_modules[<?php echo esc_attr( $module_id ); ?>]"
                                           value="1"
                                           <?php checked( ! empty( $modules_option[ $module_id ] ) ); ?> />
                                    <span class="katadoo-toggle-slider"></span>
                                    <span class="katadoo-toggle-label">
                                        <?php esc_html_e( 'Activé', 'katadoo' ); ?>
                                    </span>
                                </label>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>

                <div class="katadoo-actions">
                    <?php submit_button( __( 'Enregistrer les modifications', 'katadoo' ), 'primary' ); ?>
                </div>
            </form>
        </div>
    </div>
</div>
