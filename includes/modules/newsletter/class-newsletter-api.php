<?php
/**
 * API Newsletter pour Odoo.
 *
 * @package    Katadoo
 * @subpackage Katadoo/includes/modules/newsletter
 * @since      1.0.0
 */

// Sécurité : empêcher l'accès direct
if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

/**
 * Classe Katadoo_Newsletter_Api.
 *
 * Gère les opérations API liées à la newsletter Odoo.
 *
 * @since 1.0.0
 */
class Katadoo_Newsletter_Api {

    /**
     * Client Odoo.
     *
     * @since  1.0.0
     * @access private
     * @var    Katadoo_Odoo_Client
     */
    private $odoo_client;

    /**
     * Modèle Odoo pour les listes.
     *
     * @since  1.0.0
     * @access private
     * @var    string
     */
    private $list_model = 'mailing.list';

    /**
     * Modèle Odoo pour les contacts.
     *
     * @since  1.0.0
     * @access private
     * @var    string
     */
    private $contact_model = 'mailing.contact';

    /**
     * Constructeur.
     *
     * @since 1.0.0
     * @param Katadoo_Odoo_Client $odoo_client Client Odoo.
     */
    public function __construct( Katadoo_Odoo_Client $odoo_client ) {
        $this->odoo_client = $odoo_client;
    }

    /**
     * Récupère les listes de diffusion actives.
     *
     * @since  1.0.0
     * @return array|false Liste des listes de diffusion ou false en cas d'erreur.
     */
    public function get_mailing_lists() {
        return $this->odoo_client->search_read(
            $this->list_model,
            array( array( 'active', '=', true ) ),
            array( 'id', 'name', 'contact_count' )
        );
    }

    /**
     * Récupère une liste de diffusion par son ID.
     *
     * @since  1.0.0
     * @param  int $list_id ID de la liste.
     * @return array|false Données de la liste ou false en cas d'erreur.
     */
    public function get_mailing_list( $list_id ) {
        $lists = $this->odoo_client->read(
            $this->list_model,
            array( $list_id ),
            array( 'id', 'name', 'contact_count', 'active' )
        );

        if ( $lists === false || empty( $lists ) ) {
            return false;
        }

        return $lists[0];
    }

    /**
     * Inscrit un contact à une liste de diffusion.
     *
     * @since  1.0.0
     * @param  string $email   Email du contact.
     * @param  string $name    Nom du contact.
     * @param  int    $list_id ID de la liste.
     * @param  array  $extra   Données supplémentaires (phone, company).
     * @return bool True si l'inscription a réussi.
     */
    public function subscribe( $email, $name, $list_id, $extra = array() ) {
        if ( empty( $email ) || $list_id <= 0 ) {
            return false;
        }

        // Vérifier si le contact existe déjà
        $existing = $this->odoo_client->search_read(
            $this->contact_model,
            array( array( 'email', '=', $email ) ),
            array( 'id', 'list_ids', 'subscription_list_ids' )
        );

        if ( $existing === false ) {
            return false;
        }

        if ( ! empty( $existing ) ) {
            // Mettre à jour le contact existant
            return $this->add_to_list( $existing[0]['id'], $list_id );
        }

        // Créer un nouveau contact
        return $this->create_contact( $email, $name, $list_id, $extra );
    }

    /**
     * Désinscrit un contact d'une liste de diffusion.
     *
     * @since  1.0.0
     * @param  string $email   Email du contact.
     * @param  int    $list_id ID de la liste.
     * @return bool True si la désinscription a réussi.
     */
    public function unsubscribe( $email, $list_id ) {
        if ( empty( $email ) || $list_id <= 0 ) {
            return false;
        }

        // Trouver le contact
        $contacts = $this->odoo_client->search_read(
            $this->contact_model,
            array( array( 'email', '=', $email ) ),
            array( 'id', 'list_ids' )
        );

        if ( $contacts === false || empty( $contacts ) ) {
            return false;
        }

        $contact = $contacts[0];

        // Retirer de la liste
        return $this->odoo_client->write(
            $this->contact_model,
            array( $contact['id'] ),
            array( 'list_ids' => array( array( 3, $list_id ) ) )
        );
    }

    /**
     * Crée un nouveau contact et l'inscrit à une liste.
     *
     * @since  1.0.0
     * @access private
     * @param  string $email   Email du contact.
     * @param  string $name    Nom du contact.
     * @param  int    $list_id ID de la liste.
     * @param  array  $extra   Données supplémentaires.
     * @return bool True si la création a réussi.
     */
    private function create_contact( $email, $name, $list_id, $extra = array() ) {
        $contact_data = array(
            'email'    => $email,
            'name'     => ! empty( $name ) ? $name : $email,
            'list_ids' => array( array( 4, $list_id ) ),
        );

        // Ajouter les données supplémentaires
        if ( ! empty( $extra['phone'] ) ) {
            $contact_data['phone'] = $extra['phone'];
        }

        if ( ! empty( $extra['company'] ) ) {
            $contact_data['company_name'] = $extra['company'];
        }

        $result = $this->odoo_client->create( $this->contact_model, $contact_data );

        return $result !== false;
    }

    /**
     * Ajoute un contact existant à une liste.
     *
     * @since  1.0.0
     * @access private
     * @param  int $contact_id ID du contact.
     * @param  int $list_id    ID de la liste.
     * @return bool True si l'ajout a réussi.
     */
    private function add_to_list( $contact_id, $list_id ) {
        // Utiliser la commande (4, id) pour ajouter à un Many2many
        return $this->odoo_client->write(
            $this->contact_model,
            array( $contact_id ),
            array( 'list_ids' => array( array( 4, $list_id ) ) )
        );
    }

    /**
     * Vérifie si un contact est inscrit à une liste.
     *
     * @since  1.0.0
     * @param  string $email   Email du contact.
     * @param  int    $list_id ID de la liste.
     * @return bool True si le contact est inscrit.
     */
    public function is_subscribed( $email, $list_id ) {
        $contacts = $this->odoo_client->search_read(
            $this->contact_model,
            array(
                array( 'email', '=', $email ),
                array( 'list_ids', 'in', array( $list_id ) ),
            ),
            array( 'id' )
        );

        return ! empty( $contacts );
    }

    /**
     * Compte le nombre de contacts dans une liste.
     *
     * @since  1.0.0
     * @param  int $list_id ID de la liste.
     * @return int|false Nombre de contacts ou false en cas d'erreur.
     */
    public function count_subscribers( $list_id ) {
        return $this->odoo_client->search_count(
            $this->contact_model,
            array( array( 'list_ids', 'in', array( $list_id ) ) )
        );
    }
}
