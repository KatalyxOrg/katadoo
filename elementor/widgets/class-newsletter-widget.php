<?php
/**
 * Widget Elementor Newsletter.
 *
 * @package    Katadoo
 * @subpackage Katadoo/elementor/widgets
 * @since      1.0.0
 */

// Sécurité : empêcher l'accès direct
if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

use Elementor\Widget_Base;
use Elementor\Controls_Manager;

/**
 * Classe Katadoo_Newsletter_Widget.
 *
 * Widget Elementor pour le formulaire Newsletter.
 *
 * @since 1.0.0
 */
class Katadoo_Newsletter_Widget extends Widget_Base {

    /**
     * Retourne le nom du widget.
     *
     * @since  1.0.0
     * @return string
     */
    public function get_name() {
        return 'katadoo_newsletter';
    }

    /**
     * Retourne le titre du widget.
     *
     * @since  1.0.0
     * @return string
     */
    public function get_title() {
        return __( 'Katadoo Newsletter', 'katadoo' );
    }

    /**
     * Retourne l'icône du widget.
     *
     * @since  1.0.0
     * @return string
     */
    public function get_icon() {
        return 'eicon-mail';
    }

    /**
     * Retourne les catégories du widget.
     *
     * @since  1.0.0
     * @return array
     */
    public function get_categories() {
        return array( 'katadoo', 'general' );
    }

    /**
     * Retourne les mots-clés du widget.
     *
     * @since  1.0.0
     * @return array
     */
    public function get_keywords() {
        return array( 'newsletter', 'email', 'subscribe', 'odoo', 'katadoo', 'mailing' );
    }

    /**
     * Enregistre les contrôles du widget.
     *
     * @since 1.0.0
     */
    protected function register_controls() {
        // Section Contenu
        $this->start_controls_section(
            'section_content',
            array(
                'label' => __( 'Contenu', 'katadoo' ),
                'tab'   => Controls_Manager::TAB_CONTENT,
            )
        );

        $this->add_control(
            'list_id',
            array(
                'label'       => __( 'Liste de diffusion', 'katadoo' ),
                'type'        => Controls_Manager::NUMBER,
                'default'     => 0,
                'description' => __( 'ID de la liste Odoo. Laissez 0 pour utiliser la liste par défaut.', 'katadoo' ),
            )
        );

        $this->add_control(
            'show_name',
            array(
                'label'        => __( 'Afficher le champ Nom', 'katadoo' ),
                'type'         => Controls_Manager::SWITCHER,
                'label_on'     => __( 'Oui', 'katadoo' ),
                'label_off'    => __( 'Non', 'katadoo' ),
                'return_value' => 'yes',
                'default'      => 'yes',
            )
        );

        $this->add_control(
            'show_phone',
            array(
                'label'        => __( 'Afficher le champ Téléphone', 'katadoo' ),
                'type'         => Controls_Manager::SWITCHER,
                'label_on'     => __( 'Oui', 'katadoo' ),
                'label_off'    => __( 'Non', 'katadoo' ),
                'return_value' => 'yes',
                'default'      => '',
            )
        );

        $this->add_control(
            'show_company',
            array(
                'label'        => __( 'Afficher le champ Entreprise', 'katadoo' ),
                'type'         => Controls_Manager::SWITCHER,
                'label_on'     => __( 'Oui', 'katadoo' ),
                'label_off'    => __( 'Non', 'katadoo' ),
                'return_value' => 'yes',
                'default'      => '',
            )
        );

        $this->add_control(
            'button_text',
            array(
                'label'   => __( 'Texte du bouton', 'katadoo' ),
                'type'    => Controls_Manager::TEXT,
                'default' => __( 'S\'inscrire', 'katadoo' ),
            )
        );

        $this->end_controls_section();

        // Section Messages
        $this->start_controls_section(
            'section_messages',
            array(
                'label' => __( 'Messages', 'katadoo' ),
                'tab'   => Controls_Manager::TAB_CONTENT,
            )
        );

        $this->add_control(
            'success_message',
            array(
                'label'   => __( 'Message de succès', 'katadoo' ),
                'type'    => Controls_Manager::TEXTAREA,
                'default' => __( 'Merci pour votre inscription !', 'katadoo' ),
            )
        );

        $this->add_control(
            'error_message',
            array(
                'label'   => __( 'Message d\'erreur', 'katadoo' ),
                'type'    => Controls_Manager::TEXTAREA,
                'default' => __( 'Une erreur est survenue. Veuillez réessayer.', 'katadoo' ),
            )
        );

        $this->end_controls_section();

        // Section Style - Formulaire
        $this->start_controls_section(
            'section_style_form',
            array(
                'label' => __( 'Formulaire', 'katadoo' ),
                'tab'   => Controls_Manager::TAB_STYLE,
            )
        );

        $this->add_control(
            'form_background',
            array(
                'label'     => __( 'Couleur de fond', 'katadoo' ),
                'type'      => Controls_Manager::COLOR,
                'default'   => '#ffffff',
                'selectors' => array(
                    '{{WRAPPER}} .katadoo-form-inner' => 'background-color: {{VALUE}};',
                ),
            )
        );

        $this->add_control(
            'form_border_radius',
            array(
                'label'      => __( 'Bordure arrondie', 'katadoo' ),
                'type'       => Controls_Manager::SLIDER,
                'size_units' => array( 'px' ),
                'range'      => array(
                    'px' => array(
                        'min' => 0,
                        'max' => 30,
                    ),
                ),
                'default'    => array(
                    'unit' => 'px',
                    'size' => 8,
                ),
                'selectors'  => array(
                    '{{WRAPPER}} .katadoo-form-inner' => 'border-radius: {{SIZE}}{{UNIT}};',
                ),
            )
        );

        $this->add_control(
            'form_padding',
            array(
                'label'      => __( 'Padding', 'katadoo' ),
                'type'       => Controls_Manager::DIMENSIONS,
                'size_units' => array( 'px', 'em' ),
                'default'    => array(
                    'top'    => 24,
                    'right'  => 24,
                    'bottom' => 24,
                    'left'   => 24,
                    'unit'   => 'px',
                ),
                'selectors'  => array(
                    '{{WRAPPER}} .katadoo-form-inner' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
                ),
            )
        );

        $this->end_controls_section();

        // Section Style - Bouton
        $this->start_controls_section(
            'section_style_button',
            array(
                'label' => __( 'Bouton', 'katadoo' ),
                'tab'   => Controls_Manager::TAB_STYLE,
            )
        );

        $this->add_control(
            'button_background',
            array(
                'label'     => __( 'Couleur de fond', 'katadoo' ),
                'type'      => Controls_Manager::COLOR,
                'default'   => '#7c3aed',
                'selectors' => array(
                    '{{WRAPPER}} .katadoo-button' => 'background-color: {{VALUE}};',
                ),
            )
        );

        $this->add_control(
            'button_text_color',
            array(
                'label'     => __( 'Couleur du texte', 'katadoo' ),
                'type'      => Controls_Manager::COLOR,
                'default'   => '#ffffff',
                'selectors' => array(
                    '{{WRAPPER}} .katadoo-button' => 'color: {{VALUE}};',
                ),
            )
        );

        $this->add_control(
            'button_hover_background',
            array(
                'label'     => __( 'Couleur de fond (hover)', 'katadoo' ),
                'type'      => Controls_Manager::COLOR,
                'default'   => '#6d28d9',
                'selectors' => array(
                    '{{WRAPPER}} .katadoo-button:hover' => 'background-color: {{VALUE}};',
                ),
            )
        );

        $this->add_control(
            'button_border_radius',
            array(
                'label'      => __( 'Bordure arrondie', 'katadoo' ),
                'type'       => Controls_Manager::SLIDER,
                'size_units' => array( 'px' ),
                'range'      => array(
                    'px' => array(
                        'min' => 0,
                        'max' => 30,
                    ),
                ),
                'default'    => array(
                    'unit' => 'px',
                    'size' => 6,
                ),
                'selectors'  => array(
                    '{{WRAPPER}} .katadoo-button' => 'border-radius: {{SIZE}}{{UNIT}};',
                ),
            )
        );

        $this->add_control(
            'button_full_width',
            array(
                'label'        => __( 'Largeur complète', 'katadoo' ),
                'type'         => Controls_Manager::SWITCHER,
                'label_on'     => __( 'Oui', 'katadoo' ),
                'label_off'    => __( 'Non', 'katadoo' ),
                'return_value' => 'yes',
                'default'      => '',
                'selectors'    => array(
                    '{{WRAPPER}} .katadoo-button' => 'width: 100%;',
                ),
            )
        );

        $this->end_controls_section();
    }

    /**
     * Génère le rendu du widget.
     *
     * @since 1.0.0
     */
    protected function render() {
        $settings = $this->get_settings_for_display();

        $atts = array(
            'list_id'      => $settings['list_id'],
            'show_name'    => $settings['show_name'] === 'yes' ? 'true' : 'false',
            'show_phone'   => $settings['show_phone'] === 'yes' ? 'true' : 'false',
            'show_company' => $settings['show_company'] === 'yes' ? 'true' : 'false',
            'button_text'  => $settings['button_text'],
        );

        echo do_shortcode( '[katadoo_newsletter ' . $this->build_shortcode_atts( $atts ) . ']' );
    }

    /**
     * Construit les attributs du shortcode.
     *
     * @since  1.0.0
     * @param  array $atts Attributs.
     * @return string
     */
    private function build_shortcode_atts( $atts ) {
        $output = '';

        foreach ( $atts as $key => $value ) {
            $output .= sprintf( '%s="%s" ', $key, esc_attr( $value ) );
        }

        return trim( $output );
    }
}
