<?php
/**
 * Interface pour les modules Katadoo.
 *
 * @package    Katadoo
 * @subpackage Katadoo/includes/core
 * @since      1.0.0
 */

// Sécurité : empêcher l'accès direct
if (!defined('ABSPATH')) {
    exit;
}

/**
 * Interface Katadoo_Module_Interface.
 *
 * Tous les modules doivent implémenter cette interface.
 *
 * @since 1.0.0
 */
interface Katadoo_Module_Interface
{

    /**
     * Retourne l'identifiant unique du module.
     *
     * @since  1.0.0
     * @return string Identifiant du module.
     */
    public function get_id();

    /**
     * Retourne le nom affichable du module.
     *
     * @since  1.0.0
     * @return string Nom du module.
     */
    public function get_name();

    /**
     * Retourne la description du module.
     *
     * @since  1.0.0
     * @return string Description du module.
     */
    public function get_description();

    /**
     * Vérifie si le module est activé.
     *
     * @since  1.0.0
     * @return bool True si le module est activé.
     */
    public function is_enabled();

    /**
     * Initialise le module.
     *
     * @since 1.0.0
     */
    public function init();

    /**
     * Retourne le shortcode du module.
     *
     * @since  1.0.0
     * @return string Tag du shortcode.
     */
    public function get_shortcode();

    /**
     * Génère le HTML du formulaire.
     *
     * @since  1.0.0
     * @param  array $atts Attributs du shortcode/widget.
     * @return string HTML du formulaire.
     */
    public function render_form($atts = array());

    /**
     * Retourne les champs de formulaire configurables.
     *
     * @since  1.0.0
     * @return array Liste des champs disponibles.
     */
    public function get_available_fields();

    /**
     * Traite la soumission du formulaire.
     *
     * @since  1.0.0
     * @param  array $data Données du formulaire.
     * @return array Résultat du traitement.
     */
    public function process_form($data);
}
