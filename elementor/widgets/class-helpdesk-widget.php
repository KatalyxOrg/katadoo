<?php
/**
 * Widget Elementor Helpdesk.
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
use Elementor\Group_Control_Typography;
use Elementor\Group_Control_Border;
use Elementor\Group_Control_Box_Shadow;
use Elementor\Group_Control_Background;

/**
 * Classe Katadoo_Helpdesk_Widget.
 *
 * Widget Elementor pour le formulaire Helpdesk.
 *
 * @since 1.0.0
 */
class Katadoo_Helpdesk_Widget extends Widget_Base {

    /**
     * Retourne le nom du widget.
     *
     * @since  1.0.0
     * @return string
     */
    public function get_name() {
        return 'katadoo_helpdesk';
    }

    /**
     * Retourne le titre du widget.
     *
     * @since  1.0.0
     * @return string
     */
    public function get_title() {
        return __( 'Katadoo Helpdesk', 'katadoo' );
    }

    /**
     * Retourne l'icône du widget.
     *
     * @since  1.0.0
     * @return string
     */
    public function get_icon() {
        return 'eicon-headphones';
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
        return array( 'helpdesk', 'ticket', 'support', 'odoo', 'katadoo', 'contact' );
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
            'team_id',
            array(
                'label'       => __( 'Équipe Helpdesk', 'katadoo' ),
                'type'        => Controls_Manager::NUMBER,
                'default'     => 0,
                'description' => __( 'ID de l\'équipe Odoo. Laissez 0 pour utiliser l\'équipe par défaut.', 'katadoo' ),
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
            'show_priority',
            array(
                'label'        => __( 'Afficher le champ Priorité', 'katadoo' ),
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
                'default' => __( 'Envoyer', 'katadoo' ),
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
                'default' => __( 'Votre ticket a été créé avec succès !', 'katadoo' ),
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

        // Section Style - Form Container
        $this->start_controls_section(
            'section_style_form',
            array(
                'label' => __( 'Conteneur Formulaire', 'katadoo' ),
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

        $this->add_group_control(
            Group_Control_Box_Shadow::get_type(),
            array(
                'name'     => 'form_box_shadow',
                'selector' => '{{WRAPPER}} .katadoo-form-inner',
            )
        );

        $this->add_control(
            'form_border_radius',
            array(
                'label'      => __( 'Bordure arrondie', 'katadoo' ),
                'type'       => Controls_Manager::DIMENSIONS,
                'size_units' => array( 'px', '%' ),
                'selectors'  => array(
                    '{{WRAPPER}} .katadoo-form-inner' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
                ),
            )
        );

        $this->add_control(
            'form_padding',
            array(
                'label'      => __( 'Padding', 'katadoo' ),
                'type'       => Controls_Manager::DIMENSIONS,
                'size_units' => array( 'px', 'em', '%' ),
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

        $this->add_control(
            'field_gap',
            array(
                'label'      => __( 'Espacement entre champs', 'katadoo' ),
                'type'       => Controls_Manager::SLIDER,
                'size_units' => array( 'px' ),
                'range'      => array(
                    'px' => array(
                        'min' => 0,
                        'max' => 60,
                    ),
                ),
                'default'    => array(
                    'unit' => 'px',
                    'size' => 15,
                ),
                'selectors'  => array(
                    '{{WRAPPER}} .katadoo-field' => 'margin-bottom: {{SIZE}}{{UNIT}};',
                ),
            )
        );

        $this->end_controls_section();

        // Section Style - Labels
        $this->start_controls_section(
            'section_style_labels',
            array(
                'label' => __( 'Labels', 'katadoo' ),
                'tab'   => Controls_Manager::TAB_STYLE,
            )
        );

        $this->add_group_control(
            Group_Control_Typography::get_type(),
            array(
                'name'     => 'label_typography',
                'selector' => '{{WRAPPER}} .katadoo-field label',
            )
        );

        $this->add_control(
            'label_color',
            array(
                'label'     => __( 'Couleur du texte', 'katadoo' ),
                'type'      => Controls_Manager::COLOR,
                'selectors' => array(
                    '{{WRAPPER}} .katadoo-field label' => 'color: {{VALUE}};',
                ),
            )
        );

        $this->add_control(
            'label_spacing',
            array(
                'label'      => __( 'Espacement inférieur', 'katadoo' ),
                'type'       => Controls_Manager::SLIDER,
                'size_units' => array( 'px' ),
                'range'      => array(
                    'px' => array(
                        'min' => 0,
                        'max' => 50,
                    ),
                ),
                'default'    => array(
                    'unit' => 'px',
                    'size' => 5,
                ),
                'selectors'  => array(
                    '{{WRAPPER}} .katadoo-field label' => 'margin-bottom: {{SIZE}}{{UNIT}}; display: inline-block;',
                ),
            )
        );

        $this->end_controls_section();

        // Section Style - Champs (Fields)
        $this->start_controls_section(
            'section_style_inputs',
            array(
                'label' => __( 'Champs', 'katadoo' ),
                'tab'   => Controls_Manager::TAB_STYLE,
            )
        );

        $this->add_group_control(
            Group_Control_Typography::get_type(),
            array(
                'name'     => 'input_typography',
                'selector' => '{{WRAPPER}} .katadoo-input',
            )
        );

        $this->add_control(
            'input_text_color',
            array(
                'label'     => __( 'Couleur du texte', 'katadoo' ),
                'type'      => Controls_Manager::COLOR,
                'selectors' => array(
                    '{{WRAPPER}} .katadoo-input' => 'color: {{VALUE}};',
                ),
            )
        );

        $this->add_control(
            'input_placeholder_color',
            array(
                'label'     => __( 'Couleur du placeholder', 'katadoo' ),
                'type'      => Controls_Manager::COLOR,
                'selectors' => array(
                    '{{WRAPPER}} .katadoo-input::placeholder' => 'color: {{VALUE}};',
                    '{{WRAPPER}} .katadoo-input::-webkit-input-placeholder' => 'color: {{VALUE}};',
                    '{{WRAPPER}} .katadoo-input::-moz-placeholder' => 'color: {{VALUE}};',
                    '{{WRAPPER}} .katadoo-input:-ms-input-placeholder' => 'color: {{VALUE}};',
                ),
            )
        );

        $this->add_control(
            'input_background_color',
            array(
                'label'     => __( 'Couleur de fond', 'katadoo' ),
                'type'      => Controls_Manager::COLOR,
                'default'   => '#ffffff',
                'selectors' => array(
                    '{{WRAPPER}} .katadoo-input' => 'background-color: {{VALUE}};',
                ),
            )
        );

        $this->add_group_control(
            Group_Control_Border::get_type(),
            array(
                'name'     => 'input_border',
                'selector' => '{{WRAPPER}} .katadoo-input',
            )
        );

        $this->add_control(
            'input_border_radius',
            array(
                'label'      => __( 'Bordure arrondie', 'katadoo' ),
                'type'       => Controls_Manager::DIMENSIONS,
                'size_units' => array( 'px', '%' ),
                'selectors'  => array(
                    '{{WRAPPER}} .katadoo-input' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
                ),
            )
        );

        $this->add_control(
            'input_padding',
            array(
                'label'      => __( 'Padding', 'katadoo' ),
                'type'       => Controls_Manager::DIMENSIONS,
                'size_units' => array( 'px', 'em' ),
                'selectors'  => array(
                    '{{WRAPPER}} .katadoo-input' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
                ),
            )
        );

        $this->add_group_control(
            Group_Control_Box_Shadow::get_type(),
            array(
                'name'     => 'input_box_shadow',
                'selector' => '{{WRAPPER}} .katadoo-input',
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

        $this->add_group_control(
            Group_Control_Typography::get_type(),
            array(
                'name'     => 'button_typography',
                'selector' => '{{WRAPPER}} .katadoo-button',
            )
        );

        $this->start_controls_tabs( 'tabs_button_style' );

        // Onglet Normal
        $this->start_controls_tab(
            'tab_button_normal',
            array(
                'label' => __( 'Normal', 'katadoo' ),
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

        $this->add_group_control(
            Group_Control_Box_Shadow::get_type(),
            array(
                'name'     => 'button_box_shadow',
                'selector' => '{{WRAPPER}} .katadoo-button',
            )
        );

        $this->end_controls_tab();

        // Onglet Hover
        $this->start_controls_tab(
            'tab_button_hover',
            array(
                'label' => __( 'Au survol', 'katadoo' ),
            )
        );

        $this->add_control(
            'button_text_color_hover',
            array(
                'label'     => __( 'Couleur du texte', 'katadoo' ),
                'type'      => Controls_Manager::COLOR,
                'selectors' => array(
                    '{{WRAPPER}} .katadoo-button:hover' => 'color: {{VALUE}};',
                ),
            )
        );

        $this->add_control(
            'button_hover_background',
            array(
                'label'     => __( 'Couleur de fond', 'katadoo' ),
                'type'      => Controls_Manager::COLOR,
                'default'   => '#6d28d9',
                'selectors' => array(
                    '{{WRAPPER}} .katadoo-button:hover' => 'background-color: {{VALUE}};',
                ),
            )
        );

        $this->add_group_control(
            Group_Control_Box_Shadow::get_type(),
            array(
                'name'     => 'button_box_shadow_hover',
                'selector' => '{{WRAPPER}} .katadoo-button:hover',
            )
        );

        $this->end_controls_tab();

        $this->end_controls_tabs();

        $this->add_group_control(
            Group_Control_Border::get_type(),
            array(
                'name'     => 'button_border',
                'selector' => '{{WRAPPER}} .katadoo-button',
                'separator' => 'before',
            )
        );

        $this->add_control(
            'button_border_radius',
            array(
                'label'      => __( 'Bordure arrondie', 'katadoo' ),
                'type'       => Controls_Manager::DIMENSIONS,
                'size_units' => array( 'px', '%' ),
                'selectors'  => array(
                    '{{WRAPPER}} .katadoo-button' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
                ),
            )
        );

        $this->add_control(
            'button_padding',
            array(
                'label'      => __( 'Padding', 'katadoo' ),
                'type'       => Controls_Manager::DIMENSIONS,
                'size_units' => array( 'px', 'em' ),
                'selectors'  => array(
                    '{{WRAPPER}} .katadoo-button' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
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

        // Section Style - Messages
        $this->start_controls_section(
            'section_style_messages',
            array(
                'label' => __( 'Messages', 'katadoo' ),
                'tab'   => Controls_Manager::TAB_STYLE,
            )
        );

        $this->add_group_control(
            Group_Control_Typography::get_type(),
            array(
                'name'     => 'message_typography',
                'selector' => '{{WRAPPER}} .katadoo-message',
            )
        );

        $this->add_control(
            'message_success_color',
            array(
                'label'     => __( 'Succès - Couleur Texte', 'katadoo' ),
                'type'      => Controls_Manager::COLOR,
                'default'   => '#155724',
                'selectors' => array(
                    '{{WRAPPER}} .katadoo-message.katadoo-success' => 'color: {{VALUE}};',
                ),
            )
        );

        $this->add_control(
            'message_success_bg',
            array(
                'label'     => __( 'Succès - Couleur Fond', 'katadoo' ),
                'type'      => Controls_Manager::COLOR,
                'default'   => '#d4edda',
                'selectors' => array(
                    '{{WRAPPER}} .katadoo-message.katadoo-success' => 'background-color: {{VALUE}}; border-color: {{VALUE}};',
                ),
            )
        );

        $this->add_control(
            'message_error_color',
            array(
                'label'     => __( 'Erreur - Couleur Texte', 'katadoo' ),
                'type'      => Controls_Manager::COLOR,
                'default'   => '#721c24',
                'selectors' => array(
                    '{{WRAPPER}} .katadoo-message.katadoo-error' => 'color: {{VALUE}};',
                ),
            )
        );

        $this->add_control(
            'message_error_bg',
            array(
                'label'     => __( 'Erreur - Couleur Fond', 'katadoo' ),
                'type'      => Controls_Manager::COLOR,
                'default'   => '#f8d7da',
                'selectors' => array(
                    '{{WRAPPER}} .katadoo-message.katadoo-error' => 'background-color: {{VALUE}}; border-color: {{VALUE}};',
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
            'team_id'       => $settings['team_id'],
            'show_name'     => $settings['show_name'] === 'yes' ? 'true' : 'false',
            'show_phone'    => $settings['show_phone'] === 'yes' ? 'true' : 'false',
            'show_priority' => $settings['show_priority'] === 'yes' ? 'true' : 'false',
            'button_text'   => $settings['button_text'],
        );

        echo do_shortcode( '[katadoo_helpdesk ' . $this->build_shortcode_atts( $atts ) . ']' );
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
