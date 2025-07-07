# MediCare+ V2.0 - Version PHP

## Vue d'ensemble

MediCare+ V2.0 est la version PHP complète de l'écosystème de santé digital comprenant :
- **TensioCare** : Surveillance de la tension artérielle
- **DiabetoCare** : Gestion complète du diabète  
- **Consultations** : Plateforme de rendez-vous médicaux

## Conversion depuis TypeScript/React

Cette version PHP reproduit intégralement toutes les fonctionnalités de la version TypeScript/React originale, adaptée pour un déploiement sur hébergeur PHP standard.

## Architecture Technique

### Stack Technologique
- **Backend** : PHP 8.0+ avec architecture MVC
- **Base de données** : MySQL 8.0+ avec PDO
- **Frontend** : HTML5, CSS3 (Tailwind CSS), JavaScript ES6+
- **Sécurité** : Sessions PHP natives, protection CSRF
- **Responsive** : Design mobile-first avec Tailwind CSS

### Structure des Fichiers
```
php-version/
├── index.php                 # Page d'accueil MediCare+
├── config/
│   ├── database.php          # Configuration base de données
│   └── config.php            # Configuration générale
├── includes/
│   └── auth.php              # Système d'authentification
├── tensiocare/
│   ├── login.php             # Connexion TensioCare
│   ├── dashboard.php         # Dashboard TensioCare
│   ├── admin-login.php       # Administration TensioCare
│   └── logout.php            # Déconnexion
├── diabetocare/
│   ├── login.php             # Connexion DiabetoCare
│   ├── dashboard.php         # Dashboard DiabetoCare
│   ├── admin-login.php       # Administration DiabetoCare
│   └── logout.php            # Déconnexion
├── consultations/
│   ├── login.php             # Connexion Consultations
│   ├── admin-login.php       # Administration Consultations
│   └── logout.php            # Déconnexion
└── sql/
    └── schema.sql            # Schéma complet base de données
```

## Installation

### Prérequis
- PHP 8.0 ou supérieur
- MySQL 8.0 ou supérieur
- Serveur web (Apache/Nginx)
- Extension PHP PDO activée

### Configuration Base de Données
1. Créer la base de données :
```sql
CREATE DATABASE medicare_plus CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
```

2. Importer le schéma :
```bash
mysql -u username -p medicare_plus < sql/schema.sql
```

3. Configurer la connexion dans `config/database.php`

### Configuration Serveur Web
- Document root : pointer vers le dossier `php-version/`
- Activation du mod_rewrite (Apache) ou équivalent (Nginx)

## Fonctionnalités

### Classification Médicale
- **TensioCare** : Classification OMS 2023 (8 catégories)
- **DiabetoCare** : Classification ADA 2025 (3 catégories)

### Authentification
- Système de sessions PHP sécurisées
- Protection contre les attaques par force brute
- Gestion des rôles (patient, médecin, admin)

### Applications Séparées
Chaque application dispose de :
- Interface de connexion dédiée
- Dashboard spécialisé par rôle
- Administration indépendante
- Données isolées

### Paiements Mobile Money
Support complet des opérateurs africains :
- Orange Money
- MTN Mobile Money  
- Moov Money

## Comptes de Démonstration

### TensioCare
- Patient : `patient / patient`
- Médecin : `medecin / medecin`
- Admin : `admin / admin`

### DiabetoCare
- Patient : `patient / patient`
- Médecin : `medecin / medecin`
- Admin : `admin / admin`

### Consultations
- Patient : `patient / patient`
- Médecin : `medecin / medecin`
- Admin : `admin / admin`

## Interface Responsive

Interface complètement adaptative :
- **Mobile** : Menus hamburger, layout vertical
- **Tablette** : Interface hybride optimisée
- **Desktop** : Interface complète avec sidebars

## Sécurité

### Mesures Implémentées
- Protection CSRF avec tokens
- Validation et échappement des données
- Sessions sécurisées avec timeout
- Limitation des tentatives de connexion
- Logs d'audit complets

### Configuration SSL
Recommandé pour la production :
```apache
# .htaccess
RewriteCond %{HTTPS} off
RewriteRule ^(.*)$ https://%{HTTP_HOST}%{REQUEST_URI} [L,R=301]
```

## Base de Données

### Tables Principales
- `users` : Utilisateurs tous profils
- `blood_pressure_measurements` : Mesures tension
- `glucose_measurements` : Mesures glycémie
- `consultations` : Rendez-vous médicaux
- `subscriptions` : Abonnements
- `payments` : Paiements Mobile Money
- `messages` : Communication
- `audit_logs` : Journalisation

### Optimisations
- Index de performance optimisés
- Vues pour requêtes fréquentes
- Contraintes de clés étrangères

## Déploiement

### Hébergeur Partagé
1. Upload des fichiers via FTP
2. Import du schéma de base de données
3. Configuration des paramètres de connexion
4. Test des fonctionnalités

### Serveur Dédié
1. Configuration Apache/Nginx
2. Installation PHP 8.0+
3. Configuration MySQL
4. Mise en place SSL/TLS

## Support et Maintenance

### Logs d'Erreur
Activer les logs PHP :
```php
error_reporting(E_ALL);
ini_set('display_errors', 1);
ini_set('log_errors', 1);
```

### Monitoring
- Suivi des performances base de données
- Monitoring des sessions utilisateurs
- Alertes système automatiques

## Évolutions Futures

### Fonctionnalités Prévues
- API REST pour applications mobiles
- Notifications push
- Intégration télémédecine
- Analyses prédictives IA

### Compatibilité
- PHP 8.0+ maintenu
- MySQL 8.0+ supporté
- Support multi-tenant

## Auteur

Développé dans le cadre de la conversion de MediCare+ vers PHP pour compatibilité hébergeur standard.

Version 2.0.0 - Janvier 2025