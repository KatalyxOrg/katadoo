=== Katadoo ===
Contributors: katalyx
Tags: odoo, newsletter, helpdesk, crm, integration, elementor
Requires at least: 6.0
Tested up to: 6.4
Requires PHP: 8.0
Stable tag: 1.0.1
License: GPLv2 or later
License URI: https://www.gnu.org/licenses/gpl-2.0.html

Connectez votre site WordPress à Odoo. Newsletter, Helpdesk et plus encore.

== Description ==

**Katadoo** est un plugin WordPress open source développé par [Katalyx](https://katalyx.fr) qui permet de connecter votre site WordPress à votre instance Odoo.

= Fonctionnalités =

* **Module Newsletter** - Ajoutez des formulaires d'inscription qui s'intègrent directement avec le module Email Marketing d'Odoo
* **Module Helpdesk** - Permettez à vos visiteurs de créer des tickets de support directement depuis votre site
* **Intégration Elementor** - Widgets Elementor avec contrôles de style complets
* **Architecture modulaire** - Facilement extensible pour ajouter de nouveaux modules

= Prérequis Odoo =

* Odoo 14+ (pour l'authentification par clé API)
* Module Email Marketing (pour la Newsletter)
* Module Helpdesk (pour l'Assistance) - Odoo Enterprise

= Shortcodes =

* `[katadoo_newsletter]` - Affiche le formulaire d'inscription à la newsletter
* `[katadoo_helpdesk]` - Affiche le formulaire de création de ticket

= Configuration =

1. Installez et activez le plugin
2. Allez dans **Katadoo > Connexion** dans l'administration WordPress
3. Entrez l'URL de votre instance Odoo, le nom de la base de données, votre nom d'utilisateur et votre clé API
4. Testez la connexion
5. Configurez les modules Newsletter et Helpdesk selon vos besoins

== Installation ==

1. Téléchargez le plugin depuis le dépôt WordPress ou GitHub
2. Uploadez le dossier `katadoo` dans le répertoire `/wp-content/plugins/`
3. Activez le plugin via le menu 'Extensions' de WordPress
4. Configurez le plugin dans **Katadoo > Connexion**

== Frequently Asked Questions ==

= Comment obtenir une clé API Odoo ? =

1. Connectez-vous à votre instance Odoo
2. Allez dans les préférences de votre utilisateur
3. Dans l'onglet "Sécurité du compte", créez une nouvelle clé API
4. Copiez la clé et collez-la dans la configuration Katadoo

= Le plugin fonctionne-t-il avec Odoo Community ? =

Le module Newsletter fonctionne avec Odoo Community (avec le module Email Marketing).
Le module Helpdesk nécessite Odoo Enterprise car le module Helpdesk n'est pas disponible en Community.

= Puis-je personnaliser le style des formulaires ? =

Oui ! Les formulaires utilisent des classes CSS que vous pouvez personnaliser.
Avec Elementor, vous avez accès à des contrôles de style directement dans l'éditeur.

== Screenshots ==

1. Page de configuration de la connexion Odoo
2. Gestion des modules
3. Configuration du module Newsletter
4. Configuration du module Helpdesk
5. Widget Elementor Newsletter
6. Widget Elementor Helpdesk

== Changelog ==

= 1.0.1 =
* Ajout d'options de personnalisation pour les widgets Elementor

= 1.0.0 =
* Version initiale
* Module Newsletter avec intégration mailing.list et mailing.contact Odoo
* Module Helpdesk avec intégration helpdesk.ticket Odoo
* Widgets Elementor pour Newsletter et Helpdesk
* Interface d'administration complète
* Support multilingue (FR/EN)

== Upgrade Notice ==

= 1.0.0 =
Version initiale du plugin.
