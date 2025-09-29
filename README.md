# PHP MCP Server

**⚠️ AVERTISSEMENT : Projet d'apprentissage uniquement - Ne pas utiliser en production**

Ce projet est un serveur MCP (Model Context Protocol) implémenté en PHP avec Symfony 7.3. Il s'agit d'un projet à but éducatif pour comprendre et expérimenter avec le protocole MCP.

## Qu'est-ce que MCP ?

Le Model Context Protocol (MCP) est un protocole standardisé qui permet aux modèles d'IA d'interagir avec des outils externes de manière structurée. Ce serveur implémente ce protocole en PHP et fournit des exemples d'outils comme la météo et le calcul de distance entre villes.

## ⚠️ Limitations importantes

- **Non testé** : Ce projet n'inclut pas de tests automatisés
- **Apprentissage uniquement** : Code non optimisé pour la production
- **Sécurité** : Aucune mesure de sécurité implémentée
- **Performance** : Non optimisé pour les charges importantes

## Fonctionnalités

### Outils disponibles

- **GetWeather** : Récupération de données météorologiques pour une localisation
- **DistanceBetweenTowns** : Calcul de distance entre deux villes

### Architecture

- Implémentation complète du protocole JSON-RPC 2.0
- Architecture modulaire avec registres de capacités
- Support des outils, ressources et prompts MCP
- Pattern Factory pour la création des requêtes JSON-RPC

## Prérequis

- Docker et Docker Compose
- Make (optionnel mais recommandé)

## Installation

1. Cloner le projet :
```bash
git clone <url-du-repo>
cd php-mcp-server
```

2. Installer les dépendances :
```bash
make install
```

## Utilisation

### Démarrer le serveur de développement

```bash
make start
```

Le serveur sera accessible sur `http://localhost:8000`

### Arrêter le serveur

```bash
make stop
```

### Commandes utiles

```bash
# Accéder au CLI PHP
make php-cli

# Accéder au CLI Composer
make composer-cli

# Analyser le code avec PHPStan
make static-code-analysis

# Appliquer les standards de codage
make apply-cs

# Lancer les tests (non implémentés)
make test
```

## Structure du projet

```
src/
├── Capability/Tool/          # Implémentations des outils MCP
├── Controller/               # Contrôleurs Symfony
├── Mcp/                     # Core MCP implementation
│   ├── JsonRpc/            # JSON-RPC 2.0 implementation
│   ├── MethodHandler/      # Gestionnaires de méthodes MCP
│   └── Registry/           # Registres des capacités
└── Kernel.php              # Kernel Symfony
```

## Standards de développement

- **PHP 8.2+** avec types stricts
- **PSR-12** et standards Symfony
- **SOLID principles**
- **Design patterns** (Factory, Registry, Strategy)
- **Analyse statique** avec PHPStan niveau max

## Test avec l'inspecteur MCP

Pour tester facilement ce serveur MCP, vous pouvez utiliser l'inspecteur officiel MCP.

voir https://github.com/modelcontextprotocol/inspector

1. Lancer l'inspecteur MCP :
```bash
npx @modelcontextprotocol/inspector
```

L'inspecteur vous permettra de :
- Explorer les outils disponibles
- Tester les appels d'outils de manière interactive
- Visualiser les réponses JSON-RPC
- Déboguer les échanges de messages

## Exemple d'utilisation

Le serveur expose un endpoint `/mcp` qui accepte les requêtes JSON-RPC 2.0 :

```json
{
  "jsonrpc": "2.0",
  "id": 1,
  "method": "tools/call",
  "params": {
    "name": "get_weather",
    "arguments": {
      "location": "Paris"
    }
  }
}
```

## Technologies utilisées

- **Symfony 7.3** - Framework PHP
- **PHP 8.2+** - Langage
- **Docker** - Containerisation
- **PHPStan** - Analyse statique
- **PHP CS Fixer** - Standards de code
- **Monolog** - Logging

## Contribution

Ce projet étant à but éducatif, les contributions sont bienvenues pour :
- Ajouter des tests
- Améliorer la documentation
- Implémenter de nouveaux outils MCP
- Corriger les bugs

## Licence

Propriétaire - Usage éducatif uniquement

---

**Rappel** : Ce projet n'est pas destiné à un usage en production. Il sert uniquement à comprendre et expérimenter avec le protocole MCP en PHP.
