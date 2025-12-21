<?php
/**
 * Client API Odoo utilisant XML-RPC (implémentation pure PHP).
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
 * Classe Katadoo_Odoo_Client.
 *
 * Client pour communiquer avec l'API Odoo via XML-RPC.
 * Implémentation pure PHP compatible avec PHP 8+.
 *
 * @since 1.0.0
 */
class Katadoo_Odoo_Client
{

    /**
     * Configuration du plugin.
     *
     * @since  1.0.0
     * @access private
     * @var    Katadoo_Config
     */
    private $config;

    /**
     * URL de l'instance Odoo.
     *
     * @since  1.0.0
     * @access private
     * @var    string
     */
    private $url;

    /**
     * Nom de la base de données.
     *
     * @since  1.0.0
     * @access private
     * @var    string
     */
    private $database;

    /**
     * Nom d'utilisateur.
     *
     * @since  1.0.0
     * @access private
     * @var    string
     */
    private $username;

    /**
     * Clé API ou mot de passe.
     *
     * @since  1.0.0
     * @access private
     * @var    string
     */
    private $api_key;

    /**
     * UID de l'utilisateur authentifié.
     *
     * @since  1.0.0
     * @access private
     * @var    int|null
     */
    private $uid = null;

    /**
     * Dernière erreur rencontrée.
     *
     * @since  1.0.0
     * @access private
     * @var    string
     */
    private $last_error = '';

    /**
     * Constructeur.
     *
     * @since 1.0.0
     * @param Katadoo_Config $config Instance de la configuration.
     */
    public function __construct(Katadoo_Config $config)
    {
        $this->config = $config;
        $this->url = rtrim($config->get_odoo_url(), '/');
        $this->database = $config->get_odoo_database();
        $this->username = $config->get_odoo_username();
        $this->api_key = $config->get_odoo_api_key();
    }

    /**
     * Authentifie l'utilisateur auprès d'Odoo.
     *
     * @since  1.0.0
     * @return bool True si l'authentification a réussi.
     */
    public function authenticate()
    {
        if ($this->uid !== null) {
            return true;
        }

        if (empty($this->url) || empty($this->database) || empty($this->username) || empty($this->api_key)) {
            $this->last_error = __('Configuration Odoo incomplète.', 'katadoo');
            return false;
        }

        try {
            $common_endpoint = $this->url . '/xmlrpc/2/common';

            $request = $this->xmlrpc_encode_request(
                'authenticate',
                array($this->database, $this->username, $this->api_key, array())
            );

            $response = $this->make_request($common_endpoint, $request);

            if ($response === false) {
                return false;
            }

            $result = $this->xmlrpc_decode($response);

            if (is_array($result) && isset($result['faultCode'])) {
                $this->last_error = isset($result['faultString']) ? $result['faultString'] : __('Erreur d\'authentification.', 'katadoo');
                return false;
            }

            if (!is_int($result) || $result === 0) {
                $this->last_error = __('Identifiants invalides.', 'katadoo');
                return false;
            }

            $this->uid = $result;
            return true;

        } catch (Exception $e) {
            $this->last_error = $e->getMessage();
            return false;
        }
    }

    /**
     * Exécute une méthode sur un modèle Odoo.
     *
     * @since  1.0.0
     * @param  string $model  Nom du modèle Odoo (ex: 'res.partner').
     * @param  string $method Nom de la méthode (ex: 'search', 'create').
     * @param  array  $args   Arguments positionnels.
     * @param  array  $kwargs Arguments nommés.
     * @return mixed Résultat de l'appel ou false en cas d'erreur.
     */
    public function execute($model, $method, $args = array(), $kwargs = array())
    {
        if (!$this->authenticate()) {
            return false;
        }

        try {
            $object_endpoint = $this->url . '/xmlrpc/2/object';

            $params = array(
                $this->database,
                $this->uid,
                $this->api_key,
                $model,
                $method,
            );

            // Ajouter les arguments positionnels comme un seul élément (liste)
            // execute_kw attend: (db, uid, password, model, method, args, kwargs)
            // où args est une liste d'arguments positionnels
            $params[] = $args;

            // Ajouter les arguments nommés si présents
            if (!empty($kwargs)) {
                $params[] = $kwargs;
            }

            $request = $this->xmlrpc_encode_request('execute_kw', $params);

            $response = $this->make_request($object_endpoint, $request);

            if ($response === false) {
                return false;
            }

            $result = $this->xmlrpc_decode($response);

            if (is_array($result) && isset($result['faultCode'])) {
                $this->last_error = isset($result['faultString']) ? $result['faultString'] : __('Erreur lors de l\'exécution.', 'katadoo');
                return false;
            }

            return $result;

        } catch (Exception $e) {
            $this->last_error = $e->getMessage();
            return false;
        }
    }

    /**
     * Recherche des enregistrements.
     *
     * @since  1.0.0
     * @param  string $model   Nom du modèle.
     * @param  array  $domain  Domaine de recherche.
     * @param  array  $options Options (limit, offset, order).
     * @return array|false Liste des IDs ou false en cas d'erreur.
     */
    public function search($model, $domain = array(), $options = array())
    {
        return $this->execute($model, 'search', array($domain), $options);
    }

    /**
     * Recherche et lit des enregistrements.
     *
     * @since  1.0.0
     * @param  string $model   Nom du modèle.
     * @param  array  $domain  Domaine de recherche.
     * @param  array  $fields  Champs à retourner.
     * @param  array  $options Options (limit, offset, order).
     * @return array|false Liste des enregistrements ou false en cas d'erreur.
     */
    public function search_read($model, $domain = array(), $fields = array(), $options = array())
    {
        $kwargs = array_merge($options, array('fields' => $fields));
        return $this->execute($model, 'search_read', array($domain), $kwargs);
    }

    /**
     * Lit des enregistrements par leurs IDs.
     *
     * @since  1.0.0
     * @param  string $model  Nom du modèle.
     * @param  array  $ids    Liste des IDs.
     * @param  array  $fields Champs à retourner.
     * @return array|false Liste des enregistrements ou false en cas d'erreur.
     */
    public function read($model, $ids, $fields = array())
    {
        return $this->execute($model, 'read', array($ids), array('fields' => $fields));
    }

    /**
     * Crée un nouvel enregistrement.
     *
     * @since  1.0.0
     * @param  string $model  Nom du modèle.
     * @param  array  $values Valeurs des champs.
     * @return int|false ID du nouvel enregistrement ou false en cas d'erreur.
     */
    public function create($model, $values)
    {
        return $this->execute($model, 'create', array($values));
    }

    /**
     * Met à jour des enregistrements.
     *
     * @since  1.0.0
     * @param  string $model  Nom du modèle.
     * @param  array  $ids    Liste des IDs à mettre à jour.
     * @param  array  $values Nouvelles valeurs.
     * @return bool True si la mise à jour a réussi.
     */
    public function write($model, $ids, $values)
    {
        $result = $this->execute($model, 'write', array($ids, $values));
        return $result === true;
    }

    /**
     * Supprime des enregistrements.
     *
     * @since  1.0.0
     * @param  string $model Nom du modèle.
     * @param  array  $ids   Liste des IDs à supprimer.
     * @return bool True si la suppression a réussi.
     */
    public function unlink($model, $ids)
    {
        $result = $this->execute($model, 'unlink', array($ids));
        return $result === true;
    }

    /**
     * Compte le nombre d'enregistrements.
     *
     * @since  1.0.0
     * @param  string $model  Nom du modèle.
     * @param  array  $domain Domaine de recherche.
     * @return int|false Nombre d'enregistrements ou false en cas d'erreur.
     */
    public function search_count($model, $domain = array())
    {
        return $this->execute($model, 'search_count', array($domain));
    }

    /**
     * Retourne les informations sur les champs d'un modèle.
     *
     * @since  1.0.0
     * @param  string $model      Nom du modèle.
     * @param  array  $attributes Attributs à retourner.
     * @return array|false Informations sur les champs ou false en cas d'erreur.
     */
    public function fields_get($model, $attributes = array('string', 'type', 'required'))
    {
        return $this->execute($model, 'fields_get', array(), array('attributes' => $attributes));
    }

    /**
     * Teste la connexion à Odoo.
     *
     * @since  1.0.0
     * @return array Résultat du test avec 'success' et 'message'.
     */
    public function test_connection()
    {
        // Rafraîchir les paramètres de configuration
        $this->url = rtrim($this->config->get_odoo_url(), '/');
        $this->database = $this->config->get_odoo_database();
        $this->username = $this->config->get_odoo_username();
        $this->api_key = $this->config->get_odoo_api_key();
        $this->uid = null;

        if (!$this->config->is_configured()) {
            return array(
                'success' => false,
                'message' => __('Veuillez remplir tous les champs de configuration.', 'katadoo'),
            );
        }

        // Tester la version d'Odoo
        try {
            $common_endpoint = $this->url . '/xmlrpc/2/common';

            $request = $this->xmlrpc_encode_request('version', array());
            $response = $this->make_request($common_endpoint, $request);

            if ($response === false) {
                return array(
                    'success' => false,
                    'message' => $this->last_error,
                );
            }

            $version = $this->xmlrpc_decode($response);

            if (is_array($version) && isset($version['faultCode'])) {
                return array(
                    'success' => false,
                    'message' => __('Impossible de récupérer la version d\'Odoo.', 'katadoo'),
                );
            }

        } catch (Exception $e) {
            return array(
                'success' => false,
                'message' => sprintf(__('Erreur de connexion : %s', 'katadoo'), $e->getMessage()),
            );
        }

        // Tester l'authentification
        if (!$this->authenticate()) {
            return array(
                'success' => false,
                'message' => $this->last_error,
            );
        }

        $version_string = isset($version['server_version']) ? $version['server_version'] : __('Inconnue', 'katadoo');

        return array(
            'success' => true,
            'message' => sprintf(
                /* translators: %s: Odoo version number */
                __('Connexion réussie ! Version Odoo : %s', 'katadoo'),
                $version_string
            ),
            'version' => $version,
            'uid' => $this->uid,
        );
    }

    /**
     * Effectue une requête HTTP vers Odoo.
     *
     * @since  1.0.0
     * @access private
     * @param  string $endpoint URL de l'endpoint.
     * @param  string $request  Corps de la requête XML-RPC.
     * @return string|false Corps de la réponse ou false en cas d'erreur.
     */
    private function make_request($endpoint, $request)
    {
        $response = wp_remote_post(
            $endpoint,
            array(
                'headers' => array(
                    'Content-Type' => 'text/xml',
                ),
                'body' => $request,
                'timeout' => 30,
            )
        );

        if (is_wp_error($response)) {
            $this->last_error = $response->get_error_message();
            return false;
        }

        $status_code = wp_remote_retrieve_response_code($response);

        if ($status_code !== 200) {
            $this->last_error = sprintf(
                /* translators: %d: HTTP status code */
                __('Erreur HTTP : %d', 'katadoo'),
                $status_code
            );
            return false;
        }

        return wp_remote_retrieve_body($response);
    }

    /**
     * Encode une requête XML-RPC (implémentation pure PHP).
     *
     * @since  1.0.0
     * @access private
     * @param  string $method Nom de la méthode.
     * @param  array  $params Paramètres.
     * @return string XML encodé.
     */
    private function xmlrpc_encode_request($method, $params)
    {
        $xml = '<?xml version="1.0" encoding="UTF-8"?>';
        $xml .= '<methodCall>';
        $xml .= '<methodName>' . htmlspecialchars($method, ENT_XML1, 'UTF-8') . '</methodName>';
        $xml .= '<params>';

        foreach ($params as $param) {
            $xml .= '<param><value>' . $this->xmlrpc_encode_value($param) . '</value></param>';
        }

        $xml .= '</params>';
        $xml .= '</methodCall>';

        return $xml;
    }

    /**
     * Encode une valeur en XML-RPC.
     *
     * @since  1.0.0
     * @access private
     * @param  mixed $value Valeur à encoder.
     * @return string XML encodé.
     */
    private function xmlrpc_encode_value($value)
    {
        if (is_null($value)) {
            return '<nil/>';
        }

        if (is_bool($value)) {
            return '<boolean>' . ($value ? '1' : '0') . '</boolean>';
        }

        if (is_int($value)) {
            return '<int>' . $value . '</int>';
        }

        if (is_float($value)) {
            return '<double>' . $value . '</double>';
        }

        if (is_string($value)) {
            return '<string>' . htmlspecialchars($value, ENT_XML1, 'UTF-8') . '</string>';
        }

        if (is_array($value)) {
            // Vérifier si c'est un tableau associatif (struct)
            if ($this->is_assoc_array($value)) {
                $xml = '<struct>';
                foreach ($value as $key => $val) {
                    $xml .= '<member>';
                    $xml .= '<name>' . htmlspecialchars((string)$key, ENT_XML1, 'UTF-8') . '</name>';
                    $xml .= '<value>' . $this->xmlrpc_encode_value($val) . '</value>';
                    $xml .= '</member>';
                }
                $xml .= '</struct>';
                return $xml;
            } else {
                // Tableau indexé (array)
                $xml = '<array><data>';
                foreach ($value as $val) {
                    $xml .= '<value>' . $this->xmlrpc_encode_value($val) . '</value>';
                }
                $xml .= '</data></array>';
                return $xml;
            }
        }

        // Par défaut, convertir en string
        return '<string>' . htmlspecialchars((string)$value, ENT_XML1, 'UTF-8') . '</string>';
    }

    /**
     * Vérifie si un tableau est associatif.
     *
     * @since  1.0.0
     * @access private
     * @param  array $array Tableau à vérifier.
     * @return bool True si associatif.
     */
    private function is_assoc_array($array)
    {
        if (empty($array)) {
            return false;
        }
        return array_keys($array) !== range(0, count($array) - 1);
    }

    /**
     * Décode une réponse XML-RPC.
     *
     * @since  1.0.0
     * @access private
     * @param  string $xml XML à décoder.
     * @return mixed Valeur décodée.
     */
    private function xmlrpc_decode($xml)
    {
        libxml_use_internal_errors(true);
        $doc = simplexml_load_string($xml);

        if ($doc === false) {
            $this->last_error = __('Erreur de parsing XML.', 'katadoo');
            return false;
        }

        // Vérifier s'il y a une erreur (fault)
        if (isset($doc->fault)) {
            $fault = $this->xmlrpc_decode_value($doc->fault->value);
            return $fault;
        }

        // Récupérer la valeur de retour
        if (isset($doc->params->param->value)) {
            return $this->xmlrpc_decode_value($doc->params->param->value);
        }

        return null;
    }

    /**
     * Décode une valeur XML-RPC.
     *
     * @since  1.0.0
     * @access private
     * @param  SimpleXMLElement $value Élément XML.
     * @return mixed Valeur décodée.
     */
    private function xmlrpc_decode_value($value)
    {
        // Si la valeur contient directement du texte sans type, c'est une string
        $children = $value->children();
        if (count($children) === 0) {
            return (string) $value;
        }

        $child = $children[0];
        $name = $child->getName();

        switch ($name) {
            case 'nil':
                return null;

            case 'boolean':
                return ((string) $child === '1' || strtolower((string) $child) === 'true');

            case 'int':
            case 'i4':
            case 'i8':
                return (int) $child;

            case 'double':
                return (float) $child;

            case 'string':
                return (string) $child;

            case 'base64':
                return base64_decode((string) $child);

            case 'dateTime.iso8601':
                return (string) $child;

            case 'array':
                $result = array();
                if (isset($child->data->value)) {
                    foreach ($child->data->value as $val) {
                        $result[] = $this->xmlrpc_decode_value($val);
                    }
                }
                return $result;

            case 'struct':
                $result = array();
                if (isset($child->member)) {
                    foreach ($child->member as $member) {
                        $key = (string) $member->name;
                        $result[$key] = $this->xmlrpc_decode_value($member->value);
                    }
                }
                return $result;

            default:
                return (string) $child;
        }
    }

    /**
     * Retourne la dernière erreur.
     *
     * @since  1.0.0
     * @return string Message d'erreur.
     */
    public function get_last_error()
    {
        return $this->last_error;
    }

    /**
     * Retourne l'UID de l'utilisateur authentifié.
     *
     * @since  1.0.0
     * @return int|null UID ou null si non authentifié.
     */
    public function get_uid()
    {
        return $this->uid;
    }

    /**
     * Réinitialise la connexion.
     *
     * @since 1.0.0
     */
    public function reset()
    {
        $this->uid = null;
        $this->last_error = '';
    }
}
