<?php
/**
 * Classe de chargement des hooks WordPress.
 *
 * @package    Katadoo
 * @subpackage Katadoo/includes
 * @since      1.0.0
 */

// Sécurité : empêcher l'accès direct
if (!defined('ABSPATH')) {
    exit;
}

/**
 * Classe Katadoo_Loader.
 *
 * Maintient une liste de tous les hooks (actions et filtres) enregistrés
 * et les applique à WordPress lors de l'exécution du plugin.
 *
 * @since 1.0.0
 */
class Katadoo_Loader
{

    /**
     * Actions enregistrées.
     *
     * @since  1.0.0
     * @access protected
     * @var    array $actions Actions WordPress à enregistrer.
     */
    protected $actions = array();

    /**
     * Filtres enregistrés.
     *
     * @since  1.0.0
     * @access protected
     * @var    array $filters Filtres WordPress à enregistrer.
     */
    protected $filters = array();

    /**
     * Ajoute une action à la liste des hooks.
     *
     * @since 1.0.0
     * @param string $hook          Nom du hook WordPress.
     * @param object $component     Instance de l'objet contenant la méthode.
     * @param string $callback      Méthode à appeler.
     * @param int    $priority      Priorité du hook (défaut: 10).
     * @param int    $accepted_args Nombre d'arguments acceptés (défaut: 1).
     */
    public function add_action($hook, $component, $callback, $priority = 10, $accepted_args = 1)
    {
        $this->actions = $this->add($this->actions, $hook, $component, $callback, $priority, $accepted_args);
    }

    /**
     * Ajoute un filtre à la liste des hooks.
     *
     * @since 1.0.0
     * @param string $hook          Nom du hook WordPress.
     * @param object $component     Instance de l'objet contenant la méthode.
     * @param string $callback      Méthode à appeler.
     * @param int    $priority      Priorité du hook (défaut: 10).
     * @param int    $accepted_args Nombre d'arguments acceptés (défaut: 1).
     */
    public function add_filter($hook, $component, $callback, $priority = 10, $accepted_args = 1)
    {
        $this->filters = $this->add($this->filters, $hook, $component, $callback, $priority, $accepted_args);
    }

    /**
     * Utilitaire pour ajouter un hook à la collection.
     *
     * @since  1.0.0
     * @access private
     * @param  array  $hooks         Collection de hooks.
     * @param  string $hook          Nom du hook.
     * @param  object $component     Instance de l'objet.
     * @param  string $callback      Méthode callback.
     * @param  int    $priority      Priorité.
     * @param  int    $accepted_args Nombre d'arguments.
     * @return array  Collection mise à jour.
     */
    private function add($hooks, $hook, $component, $callback, $priority, $accepted_args)
    {
        $hooks[] = array(
            'hook' => $hook,
            'component' => $component,
            'callback' => $callback,
            'priority' => $priority,
            'accepted_args' => $accepted_args,
        );

        return $hooks;
    }

    /**
     * Enregistre tous les hooks avec WordPress.
     *
     * @since 1.0.0
     */
    public function run()
    {
        foreach ($this->filters as $hook) {
            add_filter(
                $hook['hook'],
                array($hook['component'], $hook['callback']),
                $hook['priority'],
                $hook['accepted_args']
            );
        }

        foreach ($this->actions as $hook) {
            add_action(
                $hook['hook'],
                array($hook['component'], $hook['callback']),
                $hook['priority'],
                $hook['accepted_args']
            );
        }
    }
}
