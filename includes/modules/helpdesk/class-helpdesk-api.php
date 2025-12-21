<?php
/**
 * API Helpdesk pour Odoo.
 *
 * @package    Katadoo
 * @subpackage Katadoo/includes/modules/helpdesk
 * @since      1.0.0
 */

// Sécurité : empêcher l'accès direct
if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

/**
 * Classe Katadoo_Helpdesk_Api.
 *
 * Gère les opérations API liées au helpdesk Odoo.
 *
 * @since 1.0.0
 */
class Katadoo_Helpdesk_Api {

    /**
     * Client Odoo.
     *
     * @since  1.0.0
     * @access private
     * @var    Katadoo_Odoo_Client
     */
    private $odoo_client;

    /**
     * Modèle Odoo pour les tickets.
     *
     * @since  1.0.0
     * @access private
     * @var    string
     */
    private $ticket_model = 'helpdesk.ticket';

    /**
     * Modèle Odoo pour les équipes.
     *
     * @since  1.0.0
     * @access private
     * @var    string
     */
    private $team_model = 'helpdesk.team';

    /**
     * Modèle Odoo pour les partenaires.
     *
     * @since  1.0.0
     * @access private
     * @var    string
     */
    private $partner_model = 'res.partner';

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
     * Récupère les équipes Helpdesk.
     *
     * @since  1.0.0
     * @return array|false Liste des équipes ou false en cas d'erreur.
     */
    public function get_teams() {
        return $this->odoo_client->search_read(
            $this->team_model,
            array(),
            array( 'id', 'name', 'use_website_helpdesk_form' )
        );
    }

    /**
     * Récupère une équipe par son ID.
     *
     * @since  1.0.0
     * @param  int $team_id ID de l'équipe.
     * @return array|false Données de l'équipe ou false en cas d'erreur.
     */
    public function get_team( $team_id ) {
        $teams = $this->odoo_client->read(
            $this->team_model,
            array( $team_id ),
            array( 'id', 'name' )
        );

        if ( $teams === false || empty( $teams ) ) {
            return false;
        }

        return $teams[0];
    }

    /**
     * Crée un ticket Helpdesk.
     *
     * @since  1.0.0
     * @param  array $data Données du ticket.
     * @return int|false ID du ticket créé ou false en cas d'erreur.
     */
    public function create_ticket( $data ) {
        $email       = isset( $data['email'] ) ? $data['email'] : '';
        $name        = isset( $data['name'] ) ? $data['name'] : '';
        $phone       = isset( $data['phone'] ) ? $data['phone'] : '';
        $subject     = isset( $data['subject'] ) ? $data['subject'] : '';
        $description = isset( $data['description'] ) ? $data['description'] : '';
        $priority    = isset( $data['priority'] ) ? $data['priority'] : 2;
        $team_id     = isset( $data['team_id'] ) ? $data['team_id'] : 0;

        if ( empty( $email ) || empty( $subject ) ) {
            return false;
        }

        // Récupérer ou créer le partenaire
        $partner_id = $this->get_or_create_partner( $email, $name, $phone );

        if ( $partner_id === false ) {
            return false;
        }

        // Préparer les données du ticket
        $ticket_data = array(
            'name'        => $subject,
            'description' => $description,
            'partner_id'  => $partner_id,
            'priority'    => (string) $priority,
        );

        if ( $team_id > 0 ) {
            $ticket_data['team_id'] = $team_id;
        }

        // Créer le ticket
        return $this->odoo_client->create( $this->ticket_model, $ticket_data );
    }

    /**
     * Récupère un ticket par son ID.
     *
     * @since  1.0.0
     * @param  int $ticket_id ID du ticket.
     * @return array|false Données du ticket ou false en cas d'erreur.
     */
    public function get_ticket( $ticket_id ) {
        $tickets = $this->odoo_client->read(
            $this->ticket_model,
            array( $ticket_id ),
            array( 'id', 'name', 'description', 'partner_id', 'team_id', 'stage_id', 'priority', 'create_date' )
        );

        if ( $tickets === false || empty( $tickets ) ) {
            return false;
        }

        return $tickets[0];
    }

    /**
     * Récupère ou crée un partenaire.
     *
     * @since  1.0.0
     * @access private
     * @param  string $email Email du partenaire.
     * @param  string $name  Nom du partenaire.
     * @param  string $phone Téléphone du partenaire.
     * @return int|false ID du partenaire ou false en cas d'erreur.
     */
    private function get_or_create_partner( $email, $name = '', $phone = '' ) {
        // Chercher un partenaire existant
        $existing = $this->odoo_client->search(
            $this->partner_model,
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
            'name'  => ! empty( $name ) ? $name : $email,
        );

        if ( ! empty( $phone ) ) {
            $partner_data['phone'] = $phone;
        }

        return $this->odoo_client->create( $this->partner_model, $partner_data );
    }

    /**
     * Récupère les tickets d'un partenaire.
     *
     * @since  1.0.0
     * @param  string $email Email du partenaire.
     * @param  int    $limit Nombre maximum de tickets.
     * @return array|false Liste des tickets ou false en cas d'erreur.
     */
    public function get_tickets_by_email( $email, $limit = 10 ) {
        // Trouver le partenaire
        $partners = $this->odoo_client->search(
            $this->partner_model,
            array( array( 'email', '=', $email ) ),
            array( 'limit' => 1 )
        );

        if ( $partners === false || empty( $partners ) ) {
            return array();
        }

        $partner_id = $partners[0];

        // Récupérer les tickets du partenaire
        return $this->odoo_client->search_read(
            $this->ticket_model,
            array( array( 'partner_id', '=', $partner_id ) ),
            array( 'id', 'name', 'stage_id', 'priority', 'create_date' ),
            array( 'limit' => $limit, 'order' => 'create_date desc' )
        );
    }

    /**
     * Compte le nombre de tickets par statut pour une équipe.
     *
     * @since  1.0.0
     * @param  int $team_id ID de l'équipe.
     * @return array|false Comptage par statut ou false en cas d'erreur.
     */
    public function count_tickets_by_status( $team_id = 0 ) {
        $domain = array();

        if ( $team_id > 0 ) {
            $domain[] = array( 'team_id', '=', $team_id );
        }

        // Récupérer tous les tickets avec leur stage
        $tickets = $this->odoo_client->search_read(
            $this->ticket_model,
            $domain,
            array( 'stage_id' )
        );

        if ( $tickets === false ) {
            return false;
        }

        $counts = array();

        foreach ( $tickets as $ticket ) {
            $stage_name = $ticket['stage_id'][1] ?? 'Unknown';

            if ( ! isset( $counts[ $stage_name ] ) ) {
                $counts[ $stage_name ] = 0;
            }

            $counts[ $stage_name ]++;
        }

        return $counts;
    }
}
