<div align="center">

# Katadoo

**Connectez votre site WordPress à Odoo**

[![WordPress Plugin Version](https://img.shields.io/badge/version-1.0.0-blue.svg)](https://github.com/katalyxorg/katadoo)
[![WordPress Tested](https://img.shields.io/badge/WordPress-6.0%2B-0073aa.svg)](https://wordpress.org/)
[![PHP Version](https://img.shields.io/badge/PHP-8.0%2B-777bb4.svg)](https://php.net/)
[![License](https://img.shields.io/badge/license-GPL--2.0%2B-green.svg)](https://www.gnu.org/licenses/gpl-2.0.html)
[![Odoo](https://img.shields.io/badge/Odoo-14%2B-714B67.svg)](https://www.odoo.com/)

*Un plugin WordPress open source pour intégrer Odoo à votre site web.*

[Fonctionnalités](#-fonctionnalités) •
[Installation](#-installation) •
[Configuration](#-configuration) •
[Documentation](#-documentation) •
[Contribuer](#-contribuer)

</div>

---

## 🎯 À propos

**Katadoo** est un plugin WordPress open source qui permet de connecter facilement votre site WordPress à votre instance [Odoo](https://www.odoo.com/). Il offre une architecture modulaire permettant d'intégrer progressivement les fonctionnalités d'Odoo directement dans votre site web.

Ce projet est développé et maintenu par **[Katalyx](https://katalyx.fr)**, dans le cadre de notre initiative Startup Studio.

### 🚀 Qui est Katalyx ?

Chez **Katalyx**, nous aidons les entreprises B2B à transformer leur écosystème digital en moteur de croissance. Nous ne sommes pas une agence d'exécution : nous sommes votre **partenaire stratégique**, capable de concevoir, structurer et piloter votre performance digitale à chaque étape.

Nous sommes également un **Startup Studio** et ce projet open source fait partie des outils que nous développons pour nos startups et la communauté.

---

## ✨ Fonctionnalités

### 📧 Module Newsletter
- Formulaires d'inscription personnalisables
- Intégration directe avec le module **Email Marketing** d'Odoo
- Synchronisation automatique des contacts et listes de diffusion
- Support des shortcodes WordPress

### 🎫 Module Helpdesk
- Création de tickets de support depuis votre site
- Intégration avec le module **Helpdesk** d'Odoo (Enterprise)
- Sélection d'équipe de support
- Champs personnalisables (nom, email, sujet, message)

### 🎨 Intégration Elementor
- Widgets dédiés pour Newsletter et Helpdesk
- Contrôles de style complets (couleurs, typographie, espacements)
- Aperçu en temps réel dans l'éditeur
- Personnalisation sans code

### 🧩 Architecture Modulaire
- Activation/désactivation des modules à la carte
- Structure extensible pour de futurs modules
- Code propre suivant les standards WordPress
- Hooks et filtres pour la personnalisation

---

## 📋 Prérequis

| Composant | Version minimale |
|-----------|-----------------|
| WordPress | 6.0+ |
| PHP | 8.0+ |
| Odoo | 14+ |

### Modules Odoo requis

| Module Katadoo | Module Odoo | Édition Odoo |
|----------------|-------------|--------------|
| Newsletter | Email Marketing | Community / Enterprise |
| Helpdesk | Helpdesk | Enterprise uniquement |

---

## 📦 Installation

### Via GitHub (recommandé)

1. **Téléchargez** la dernière version depuis les [Releases](https://github.com/katalyxorg/katadoo/releases)

2. **Extrayez** l'archive dans votre répertoire de plugins WordPress :
   ```bash
   cd /path/to/wordpress/wp-content/plugins/
   unzip katadoo-1.0.0.zip
   ```

3. **Activez** le plugin via le menu **Extensions** de WordPress

### Via Git (développeurs)

```bash
cd /path/to/wordpress/wp-content/plugins/
git clone https://github.com/katalyxorg/katadoo.git
```

### Via WordPress Admin

1. Allez dans **Extensions > Ajouter**
2. Cliquez sur **Téléverser une extension**
3. Sélectionnez le fichier ZIP téléchargé
4. Cliquez sur **Installer maintenant** puis **Activer**

---

## ⚙️ Configuration

### 1. Connexion à Odoo

1. Accédez à **Katadoo > Connexion** dans l'administration WordPress
2. Renseignez les informations de connexion :
   - **URL Odoo** : L'adresse de votre instance (ex: `https://votre-instance.odoo.com`)
   - **Base de données** : Le nom de votre base de données Odoo
   - **Nom d'utilisateur** : Votre email de connexion Odoo
   - **Clé API** : Votre clé API Odoo (voir ci-dessous)
3. Cliquez sur **Tester la connexion**
4. Enregistrez les paramètres

### 2. Obtenir une clé API Odoo

1. Connectez-vous à votre instance Odoo
2. Ouvrez **Préférences utilisateur** (cliquez sur votre nom dans le coin supérieur droit)
3. Allez dans l'onglet **Sécurité du compte**
4. Dans la section **Clés API**, cliquez sur **Nouvelle clé API**
5. Donnez un nom à la clé (ex: "WordPress Katadoo")
6. Copiez la clé générée et collez-la dans Katadoo

> ⚠️ **Important** : La clé API n'est affichée qu'une seule fois. Conservez-la en lieu sûr !

### 3. Configuration des modules

Rendez-vous dans **Katadoo > Modules** pour activer et configurer chaque module selon vos besoins.

---

## 📖 Documentation

### Shortcodes

#### Newsletter
```php
[katadoo_newsletter]
```
Affiche le formulaire d'inscription à la newsletter.

#### Helpdesk
```php
[katadoo_helpdesk]
```
Affiche le formulaire de création de ticket de support.

### Widgets Elementor

Si Elementor est installé, vous trouverez les widgets **Katadoo** dans la catégorie dédiée de l'éditeur :
- **Katadoo Newsletter** - Formulaire d'inscription
- **Katadoo Helpdesk** - Formulaire de ticket

### Hooks disponibles

```php
// Modifier les données avant envoi à Odoo
add_filter('katadoo_newsletter_data', function($data) {
    // Personnalisation des données
    return $data;
});

// Action après inscription newsletter
add_action('katadoo_newsletter_subscribed', function($contact_id, $email) {
    // Votre code personnalisé
}, 10, 2);

// Action après création de ticket
add_action('katadoo_helpdesk_ticket_created', function($ticket_id, $data) {
    // Votre code personnalisé
}, 10, 2);
```

---

## 🛠️ Développement

### Structure du projet

```
katadoo/
├── assets/
│   ├── css/          # Feuilles de style
│   └── js/           # Scripts JavaScript
├── elementor/        # Intégration Elementor
│   └── widgets/      # Widgets Elementor
├── includes/
│   ├── admin/        # Interface d'administration
│   ├── core/         # Classes principales
│   ├── modules/      # Modules (newsletter, helpdesk, etc.)
│   └── public/       # Frontend
├── languages/        # Fichiers de traduction
├── katadoo.php       # Point d'entrée du plugin
├── readme.txt        # Readme WordPress.org
└── uninstall.php     # Script de désinstallation
```

### Standards de code

Ce plugin suit les [WordPress Coding Standards](https://developer.wordpress.org/coding-standards/wordpress-coding-standards/) :

- PHP : WordPress PHP Coding Standards
- JavaScript : WordPress JavaScript Coding Standards
- CSS : WordPress CSS Coding Standards

### Lancer les tests

```bash
# Installation des dépendances de test
composer install

# Exécution des tests PHPUnit
./vendor/bin/phpunit

# Vérification des standards de code
./vendor/bin/phpcs --standard=WordPress .
```

---

## 🤝 Contribuer

Les contributions sont les bienvenues ! Voici comment vous pouvez participer :

### Signaler un bug

1. Vérifiez que le bug n'a pas déjà été signalé dans les [Issues](https://github.com/katalyxorg/katadoo/issues)
2. Si ce n'est pas le cas, [créez une nouvelle issue](https://github.com/katalyxorg/katadoo/issues/new)
3. Décrivez le problème avec le maximum de détails :
   - Version de WordPress, PHP et Odoo
   - Étapes pour reproduire le bug
   - Comportement attendu vs comportement observé
   - Captures d'écran si applicable

### Proposer une fonctionnalité

1. Ouvrez une [issue](https://github.com/katalyxorg/katadoo/issues/new) avec le tag `enhancement`
2. Décrivez la fonctionnalité souhaitée et son cas d'usage

### Soumettre du code

1. **Forkez** le repository
2. **Créez** une branche pour votre fonctionnalité :
   ```bash
   git checkout -b feature/ma-nouvelle-fonctionnalite
   ```
3. **Développez** et testez vos modifications
4. **Commitez** avec des messages clairs :
   ```bash
   git commit -m "feat: ajoute le support des champs personnalisés"
   ```
5. **Pushez** vers votre fork :
   ```bash
   git push origin feature/ma-nouvelle-fonctionnalite
   ```
6. **Ouvrez** une Pull Request

### Convention de commits

Nous utilisons [Gitmoji](https://gitmoji.dev/) pour nos messages de commit. Voici quelques-uns des émojis les plus utilisés sur ce projet :

- ✨ `:sparkles:` pour une nouvelle fonctionnalité.
- 🐛 `:bug:` pour une correction de bug.
- 📝 `:memo:` pour de la documentation.
- 🎨 `:art:` pour des changements de structure/formatage du code.
- ♻️ `:recycle:` pour du refactoring.
- ✅ `:white_check_mark:` pour l'ajout ou la mise à jour de tests.
- 🔧 `:wrench:` pour des changements de configuration.

---

## 📄 Licence

Ce projet est distribué sous licence **GPL-2.0+**. Voir le fichier [LICENSE](LICENSE) pour plus de détails.

```
Katadoo - Plugin WordPress pour Connecter Odoo
Copyright (C) 2024 Katalyx

This program is free software; you can redistribute it and/or modify
it under the terms of the GNU General Public License as published by
the Free Software Foundation; either version 2 of the License, or
(at your option) any later version.

This program is distributed in the hope that it will be useful,
but WITHOUT ANY WARRANTY; without even the implied warranty of
MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the
GNU General Public License for more details.
```

---

## 💬 Support

- **Documentation** : [Wiki GitHub](https://github.com/katalyxorg/katadoo/wiki)
- **Issues** : [GitHub Issues](https://github.com/katalyxorg/katadoo/issues)
- **Discussions** : [GitHub Discussions](https://github.com/katalyxorg/katadoo/discussions)
- **Site web** : [katalyx.fr](https://katalyx.fr)

---

<div align="center">

**Développé avec ❤️ par [Katalyx](https://katalyx.fr)**

*Votre partenaire stratégique pour transformer votre écosystème digital en moteur de croissance.*

</div>
