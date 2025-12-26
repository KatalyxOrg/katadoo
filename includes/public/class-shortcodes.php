<?php
/**
 * Classe de gestion des shortcodes.
 *
 * @package    Katadoo
 * @subpackage Katadoo/includes/public
 * @since      1.0.0
 */

// Sécurité : empêcher l'accès direct
if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

/**
 * Classe Katadoo_Shortcodes.
 *
 * Enregistre et gère les shortcodes du plugin.
 *
 * @since 1.0.0
 */
class Katadoo_Shortcodes {

    /**
     * Client Odoo.
     *
     * @since  1.0.0
     * @access private
     * @var    Katadoo_Odoo_Client
     */
    private $odoo_client;

    /**
     * Configuration.
     *
     * @since  1.0.0
     * @access private
     * @var    Katadoo_Config
     */
    private $config;

    /**
     * Constructeur.
     *
     * @since 1.0.0
     * @param Katadoo_Odoo_Client $odoo_client Client Odoo.
     * @param Katadoo_Config      $config      Configuration.
     */
    public function __construct( $odoo_client, $config ) {
        $this->odoo_client = $odoo_client;
        $this->config      = $config;
    }

    /**
     * Enregistre les shortcodes.
     *
     * @since 1.0.0
     */
    public function register_shortcodes() {
        if ( $this->config->is_module_enabled( 'newsletter' ) ) {
            add_shortcode( 'katadoo_newsletter', array( $this, 'render_newsletter_form' ) );
        }

        if ( $this->config->is_module_enabled( 'helpdesk' ) ) {
            add_shortcode( 'katadoo_helpdesk', array( $this, 'render_helpdesk_form' ) );
        }
    }

    /**
     * Génère le formulaire Newsletter.
     *
     * @since  1.0.0
     * @param  array $atts Attributs du shortcode.
     * @return string HTML du formulaire.
     */
    public function render_newsletter_form( $atts ) {
        $settings = $this->config->get_newsletter_settings();

        $atts = shortcode_atts( array(
            'list_id'     => $settings['default_list_id'],
            'show_name'   => in_array( 'name', $settings['form_fields'], true ) ? 'true' : 'false',
            'show_phone'  => in_array( 'phone', $settings['form_fields'], true ) ? 'true' : 'false',
            'show_company' => in_array( 'company', $settings['form_fields'], true ) ? 'true' : 'false',
            'button_text' => $settings['button_text'],
            'class'       => '',
        ), $atts, 'katadoo_newsletter' );

        $show_name    = filter_var( $atts['show_name'], FILTER_VALIDATE_BOOLEAN );
        $show_phone   = filter_var( $atts['show_phone'], FILTER_VALIDATE_BOOLEAN );
        $show_company = filter_var( $atts['show_company'], FILTER_VALIDATE_BOOLEAN );

        ob_start();
        ?>
        <div class="katadoo-form katadoo-newsletter-form <?php echo esc_attr( $atts['class'] ); ?>">
            <form id="katadoo-newsletter-form-<?php echo esc_attr( uniqid() ); ?>" class="katadoo-form-inner" data-type="newsletter" method="post">
                <?php wp_nonce_field( 'katadoo_public_nonce', 'katadoo_nonce' ); ?>
                <input type="hidden" name="action" value="katadoo_newsletter_subscribe" />
                <input type="hidden" name="list_id" value="<?php echo esc_attr( $atts['list_id'] ); ?>" />

                <?php if ( $show_name ) : ?>
                <div class="katadoo-field">
                    <label for="katadoo-name"><?php esc_html_e( 'Nom', 'katadoo' ); ?></label>
                    <input type="text" 
                           id="katadoo-name" 
                           name="name" 
                           class="katadoo-input"
                           placeholder="<?php esc_attr_e( 'Votre nom', 'katadoo' ); ?>" />
                </div>
                <?php endif; ?>

                <div class="katadoo-field">
                    <label for="katadoo-email"><?php esc_html_e( 'Email', 'katadoo' ); ?> <span class="required">*</span></label>
                    <input type="email" 
                           id="katadoo-email" 
                           name="email" 
                           class="katadoo-input"
                           placeholder="<?php esc_attr_e( 'Votre email', 'katadoo' ); ?>"
                           required />
                </div>

                <?php if ( $show_phone ) : ?>
                <div class="katadoo-field">
                    <label for="katadoo-phone"><?php esc_html_e( 'Téléphone', 'katadoo' ); ?></label>
                    <input type="tel" 
                           id="katadoo-phone" 
                           name="phone" 
                           class="katadoo-input"
                           placeholder="<?php esc_attr_e( 'Votre téléphone', 'katadoo' ); ?>" />
                </div>
                <?php endif; ?>

                <?php if ( $show_company ) : ?>
                <div class="katadoo-field">
                    <label for="katadoo-company"><?php esc_html_e( 'Entreprise', 'katadoo' ); ?></label>
                    <input type="text" 
                           id="katadoo-company" 
                           name="company" 
                           class="katadoo-input"
                           placeholder="<?php esc_attr_e( 'Votre entreprise', 'katadoo' ); ?>" />
                </div>
                <?php endif; ?>

                <div class="katadoo-field katadoo-submit">
                    <button type="submit" class="katadoo-button">
                        <?php echo esc_html( $atts['button_text'] ); ?>
                    </button>
                </div>

                <div class="katadoo-message" style="display: none;"></div>

                <?php $recaptcha_settings = $this->config->get_recaptcha_settings(); ?>
                <?php if ( $recaptcha_settings['enabled'] ) : ?>
                    <p class="katadoo-recaptcha-notice">
                        <?php 
                        printf(
                            /* translators: 1: Privacy Policy URL, 2: Terms of Service URL */
                            esc_html__( 'Ce site est protégé par reCAPTCHA et la %1$s et les %2$s de Google s\'appliquent.', 'katadoo' ),
                            '<a href="https://policies.google.com/privacy" target="_blank">' . esc_html__( 'Politique de confidentialité', 'katadoo' ) . '</a>',
                            '<a href="https://policies.google.com/terms" target="_blank">' . esc_html__( 'Conditions d\'utilisation', 'katadoo' ) . '</a>'
                        );
                        ?>
                    </p>
                <?php endif; ?>
            </form>
        </div>
        <?php
        return ob_get_clean();
    }

    /**
     * Génère le formulaire Helpdesk.
     *
     * @since  1.0.0
     * @param  array $atts Attributs du shortcode.
     * @return string HTML du formulaire.
     */
    public function render_helpdesk_form( $atts ) {
        $settings = $this->config->get_helpdesk_settings();

        $atts = shortcode_atts( array(
            'team_id'       => $settings['default_team_id'],
            'show_name'     => in_array( 'name', $settings['form_fields'], true ) ? 'true' : 'false',
            'show_phone'    => in_array( 'phone', $settings['form_fields'], true ) ? 'true' : 'false',
            'show_priority' => in_array( 'priority', $settings['form_fields'], true ) ? 'true' : 'false',
            'button_text'   => $settings['button_text'],
            'class'         => '',
        ), $atts, 'katadoo_helpdesk' );

        $show_name     = filter_var( $atts['show_name'], FILTER_VALIDATE_BOOLEAN );
        $show_phone    = filter_var( $atts['show_phone'], FILTER_VALIDATE_BOOLEAN );
        $show_priority = filter_var( $atts['show_priority'], FILTER_VALIDATE_BOOLEAN );

        ob_start();
        ?>
        <div class="katadoo-form katadoo-helpdesk-form <?php echo esc_attr( $atts['class'] ); ?>">
            <form id="katadoo-helpdesk-form-<?php echo esc_attr( uniqid() ); ?>" class="katadoo-form-inner" data-type="helpdesk" method="post">
                <?php wp_nonce_field( 'katadoo_public_nonce', 'katadoo_nonce' ); ?>
                <input type="hidden" name="action" value="katadoo_helpdesk_submit" />
                <input type="hidden" name="team_id" value="<?php echo esc_attr( $atts['team_id'] ); ?>" />

                <?php if ( $show_name ) : ?>
                <div class="katadoo-field">
                    <label for="katadoo-name"><?php esc_html_e( 'Nom', 'katadoo' ); ?></label>
                    <input type="text" 
                           id="katadoo-name" 
                           name="name" 
                           class="katadoo-input"
                           placeholder="<?php esc_attr_e( 'Votre nom', 'katadoo' ); ?>" />
                </div>
                <?php endif; ?>

                <div class="katadoo-field">
                    <label for="katadoo-email"><?php esc_html_e( 'Email', 'katadoo' ); ?> <span class="required">*</span></label>
                    <input type="email" 
                           id="katadoo-email" 
                           name="email" 
                           class="katadoo-input"
                           placeholder="<?php esc_attr_e( 'Votre email', 'katadoo' ); ?>"
                           required />
                </div>

                <?php if ( $show_phone ) : ?>
                <div class="katadoo-field">
                    <label for="katadoo-phone"><?php esc_html_e( 'Téléphone', 'katadoo' ); ?></label>
                    <input type="tel" 
                           id="katadoo-phone" 
                           name="phone" 
                           class="katadoo-input"
                           placeholder="<?php esc_attr_e( 'Votre téléphone', 'katadoo' ); ?>" />
                </div>
                <?php endif; ?>

                <div class="katadoo-field">
                    <label for="katadoo-subject"><?php esc_html_e( 'Sujet', 'katadoo' ); ?> <span class="required">*</span></label>
                    <input type="text" 
                           id="katadoo-subject" 
                           name="subject" 
                           class="katadoo-input"
                           placeholder="<?php esc_attr_e( 'Sujet de votre demande', 'katadoo' ); ?>"
                           required />
                </div>

                <?php if ( $show_priority ) : ?>
                <div class="katadoo-field">
                    <label for="katadoo-priority"><?php esc_html_e( 'Priorité', 'katadoo' ); ?></label>
                    <select id="katadoo-priority" name="priority" class="katadoo-input">
                        <option value="1"><?php esc_html_e( 'Basse', 'katadoo' ); ?></option>
                        <option value="2" selected><?php esc_html_e( 'Normale', 'katadoo' ); ?></option>
                        <option value="3"><?php esc_html_e( 'Haute', 'katadoo' ); ?></option>
                        <option value="4"><?php esc_html_e( 'Urgente', 'katadoo' ); ?></option>
                    </select>
                </div>
                <?php endif; ?>

                <div class="katadoo-field">
                    <label for="katadoo-description"><?php esc_html_e( 'Description', 'katadoo' ); ?> <span class="required">*</span></label>
                    <textarea id="katadoo-description" 
                              name="description" 
                              class="katadoo-input katadoo-textarea"
                              placeholder="<?php esc_attr_e( 'Décrivez votre demande...', 'katadoo' ); ?>"
                              rows="5"
                              required></textarea>
                </div>

                <div class="katadoo-field katadoo-submit">
                    <button type="submit" class="katadoo-button">
                        <?php echo esc_html( $atts['button_text'] ); ?>
                    </button>
                </div>

                <div class="katadoo-message" style="display: none;"></div>

                <?php $recaptcha_settings = $this->config->get_recaptcha_settings(); ?>
                <?php if ( $recaptcha_settings['enabled'] ) : ?>
                    <p class="katadoo-recaptcha-notice">
                        <?php 
                        printf(
                            /* translators: 1: Privacy Policy URL, 2: Terms of Service URL */
                            esc_html__( 'Ce site est protégé par reCAPTCHA et la %1$s et les %2$s de Google s\'appliquent.', 'katadoo' ),
                            '<a href="https://policies.google.com/privacy" target="_blank">' . esc_html__( 'Politique de confidentialité', 'katadoo' ) . '</a>',
                            '<a href="https://policies.google.com/terms" target="_blank">' . esc_html__( 'Conditions d\'utilisation', 'katadoo' ) . '</a>'
                        );
                        ?>
                    </p>
                <?php endif; ?>
            </form>
        </div>
        <?php
        return ob_get_clean();
    }

    /**
     * Traite l'inscription à la newsletter via AJAX.
     *
     * @since 1.0.0
     */
    public function ajax_newsletter_subscribe() {
        check_ajax_referer( 'katadoo_public_nonce', 'katadoo_nonce' );

        // Vérification ReCaptcha
        $recaptcha_result = $this->verify_recaptcha();
        if ( is_wp_error( $recaptcha_result ) ) {
            wp_send_json_error( array(
                'message' => $recaptcha_result->get_error_message(),
            ) );
        }

        $email   = isset( $_POST['email'] ) ? sanitize_email( $_POST['email'] ) : '';
        $name    = isset( $_POST['name'] ) ? sanitize_text_field( $_POST['name'] ) : '';
        $phone   = isset( $_POST['phone'] ) ? sanitize_text_field( $_POST['phone'] ) : '';
        $company = isset( $_POST['company'] ) ? sanitize_text_field( $_POST['company'] ) : '';
        $list_id = isset( $_POST['list_id'] ) ? absint( $_POST['list_id'] ) : 0;

        if ( empty( $email ) || ! is_email( $email ) ) {
            wp_send_json_error( array(
                'message' => __( 'Veuillez entrer une adresse email valide.', 'katadoo' ),
            ) );
        }

        $settings = $this->config->get_newsletter_settings();

        if ( $list_id === 0 ) {
            $list_id = $settings['default_list_id'];
        }

        if ( $list_id === 0 ) {
            wp_send_json_error( array(
                'message' => __( 'Aucune liste de diffusion configurée.', 'katadoo' ),
            ) );
        }

        // Vérifier si le contact existe déjà
        $existing = $this->odoo_client->search_read(
            'mailing.contact',
            array( array( 'email', '=', $email ) ),
            array( 'id', 'list_ids' )
        );

        if ( $existing === false ) {
            wp_send_json_error( array(
                'message' => $settings['error_message'],
            ) );
        }

        if ( ! empty( $existing ) ) {
            // Mettre à jour le contact existant
            $contact_id = $existing[0]['id'];
            $current_lists = $existing[0]['list_ids'];

            if ( ! in_array( $list_id, $current_lists, true ) ) {
                $result = $this->odoo_client->write(
                    'mailing.contact',
                    array( $contact_id ),
                    array( 'list_ids' => array( array( 4, $list_id ) ) )
                );

                if ( ! $result ) {
                    wp_send_json_error( array(
                        'message' => $settings['error_message'],
                    ) );
                }
            }
        } else {
            // Créer un nouveau contact
            $contact_data = array(
                'email'    => $email,
                'name'     => $name ?: $email,
                'list_ids' => array( array( 4, $list_id ) ),
            );

            if ( ! empty( $phone ) ) {
                $contact_data['phone'] = $phone;
            }

            if ( ! empty( $company ) ) {
                $contact_data['company_name'] = $company;
            }

            $result = $this->odoo_client->create( 'mailing.contact', $contact_data );

            if ( $result === false ) {
                wp_send_json_error( array(
                    'message' => $settings['error_message'],
                ) );
            }
        }

        wp_send_json_success( array(
            'message' => $settings['success_message'],
        ) );
    }

    /**
     * Traite la création de ticket via AJAX.
     *
     * @since 1.0.0
     */
    public function ajax_helpdesk_submit() {
        check_ajax_referer( 'katadoo_public_nonce', 'katadoo_nonce' );

        // Vérification ReCaptcha
        $recaptcha_result = $this->verify_recaptcha();
        if ( is_wp_error( $recaptcha_result ) ) {
            wp_send_json_error( array(
                'message' => $recaptcha_result->get_error_message(),
            ) );
        }

        $email       = isset( $_POST['email'] ) ? sanitize_email( $_POST['email'] ) : '';
        $name        = isset( $_POST['name'] ) ? sanitize_text_field( $_POST['name'] ) : '';
        $phone       = isset( $_POST['phone'] ) ? sanitize_text_field( $_POST['phone'] ) : '';
        $subject     = isset( $_POST['subject'] ) ? sanitize_text_field( $_POST['subject'] ) : '';
        $description = isset( $_POST['description'] ) ? sanitize_textarea_field( $_POST['description'] ) : '';
        $priority    = isset( $_POST['priority'] ) ? absint( $_POST['priority'] ) : 2;
        $team_id     = isset( $_POST['team_id'] ) ? absint( $_POST['team_id'] ) : 0;

        $settings = $this->config->get_helpdesk_settings();

        if ( empty( $email ) || ! is_email( $email ) ) {
            wp_send_json_error( array(
                'message' => __( 'Veuillez entrer une adresse email valide.', 'katadoo' ),
            ) );
        }

        if ( empty( $subject ) ) {
            wp_send_json_error( array(
                'message' => __( 'Le sujet est requis.', 'katadoo' ),
            ) );
        }

        if ( empty( $description ) ) {
            wp_send_json_error( array(
                'message' => __( 'La description est requise.', 'katadoo' ),
            ) );
        }

        if ( $team_id === 0 ) {
            $team_id = $settings['default_team_id'];
        }

        // Chercher ou créer un partenaire
        $partner_id = $this->get_or_create_partner( $email, $name, $phone );

        if ( $partner_id === false ) {
            wp_send_json_error( array(
                'message' => $settings['error_message'],
            ) );
        }

        // Créer le ticket
        $ticket_data = array(
            'name'        => $subject,
            'description' => $description,
            'partner_id'  => $partner_id,
            'priority'    => (string) $priority,
        );

        if ( $team_id > 0 ) {
            $ticket_data['team_id'] = $team_id;
        }

        $ticket_id = $this->odoo_client->create( 'helpdesk.ticket', $ticket_data );

        if ( $ticket_id === false ) {
            wp_send_json_error( array(
                'message' => $settings['error_message'],
            ) );
        }

        wp_send_json_success( array(
            'message'   => $settings['success_message'],
            'ticket_id' => $ticket_id,
        ) );
    }

    /**
     * Récupère ou crée un partenaire Odoo.
     *
     * @since  1.0.0
     * @param  string $email Email du partenaire.
     * @param  string $name  Nom du partenaire.
     * @param  string $phone Téléphone du partenaire.
     * @return int|false ID du partenaire ou false en cas d'erreur.
     */
    private function get_or_create_partner( $email, $name = '', $phone = '' ) {
        // Chercher un partenaire existant
        $existing = $this->odoo_client->search(
            'res.partner',
            array( array( 'email', '=', $email ) ),
            array( 'limit' => 1 )
        );

        if ( $existing === false ) {
            return false;
        }

        if ( ! empty( $existing ) ) {
            return $existing[0];
        }

        // Créer un nouveau partenaire
        $partner_data = array(
            'email' => $email,
            'name'  => $name ?: $email,
        );

        if ( ! empty( $phone ) ) {
            $partner_data['phone'] = $phone;
        }

        return $this->odoo_client->create( 'res.partner', $partner_data );
    }

    /**
     * Vérifie le jeton ReCaptcha.
     *
     * @since  1.0.3
     * @return bool|WP_Error True si valide, WP_Error sinon.
     */
    private function verify_recaptcha() {
        $settings = $this->config->get_recaptcha_settings();

        if ( ! $settings['enabled'] ) {
            return true;
        }

        $token = isset( $_POST['recaptcha_token'] ) ? sanitize_text_field( $_POST['recaptcha_token'] ) : '';

        if ( empty( $token ) ) {
            return new WP_Error( 'recaptcha_missing', __( 'La vérification ReCaptcha a échoué. Veuillez réessayer.', 'katadoo' ) );
        }

        $response = wp_remote_post( 'https://www.google.com/recaptcha/api/siteverify', array(
            'body' => array(
                'secret'   => $settings['secret_key'],
                'response' => $token,
                'remoteip' => $_SERVER['REMOTE_ADDR'],
            ),
        ) );

        if ( is_wp_error( $response ) ) {
            return $response;
        }

        $body = json_decode( wp_remote_retrieve_body( $response ), true );

        if ( ! isset( $body['success'] ) || ! $body['success'] ) {
            $error_codes = isset( $body['error-codes'] ) ? implode( ', ', $body['error-codes'] ) : 'unknown';
            return new WP_Error( 'recaptcha_failed', __( 'Échec de la vérification anti-spam (' . $error_codes . '). Veuillez réessayer.', 'katadoo' ) );
        }

        if ( isset( $body['score'] ) && $body['score'] < $settings['threshold'] ) {
            return new WP_Error( 'recaptcha_low_score', __( 'Désolé, votre soumission a été identifiée comme potentiellement indésirable.', 'katadoo' ) );
        }

        return true;
    }
}
