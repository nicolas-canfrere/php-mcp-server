# Tests Unitaires PHPUnit à implementer

Voici la liste des tests à implementer avec PHPUnit dans ce projet.  

A chaque test implementé, coche sa checkbox.

## 1. JSON-RPC Layer

### JsonRpcRequestFactory
- [x] Test création requête valide avec tous les paramètres
- [x] Test création requête valide sans paramètres optionnels
- [x] Test création requête avec ID string
- [x] Test création requête avec ID integer
- [x] Test création requête avec ID null
- [x] Test erreur PARSE_ERROR avec JSON invalide
- [x] Test erreur INVALID_REQUEST avec jsonrpc manquant
- [x] Test erreur INVALID_REQUEST avec jsonrpc invalide
- [x] Test erreur INVALID_REQUEST avec method manquant
- [x] Test erreur INVALID_REQUEST avec method non-string
- [x] Test erreur METHOD_NOT_FOUND avec method inconnue
- [x] Test erreur INVALID_REQUEST avec ID invalide (ni string, ni int, ni null)
- [x] Test gestion des paramètres optionnels

### JsonRpcRequest
- [x] Test getters (getJsonRpc, getId, getMethod, getParams)
- [x] Test isNotification() retourne true quand id est null
- [x] Test isNotification() retourne false quand id existe
- [x] Test construction avec différents types d'ID

### JsonRpcResultResponse
- [x] Test getters (getJsonRpc, getId, getResult)
- [x] Test jsonSerialize() retourne structure correcte
- [x] Test jsonSerialize() avec result vide
- [x] Test jsonSerialize() avec result complexe

### JsonRpcErrorResponse
- [x] Test getters (getJsonRpc, getId, getError)
- [x] Test jsonSerialize() retourne structure correcte
- [x] Test jsonSerialize() avec différents types d'erreurs

### JsonRpcError
- [x] Test getters (getCode, getMessage, getData)
- [x] Test jsonSerialize() avec data
- [x] Test jsonSerialize() sans data (null)
- [x] Test construction avec chaque code d'erreur disponible

## 2. MCP Server

### McpServer
- [x] Test handle() délègue au bon handler
- [x] Test handle() avec JsonRpcErrorResponse du factory
- [x] Test handle() retourne METHOD_NOT_FOUND si aucun handler ne supporte la méthode
- [x] Test handle() itère sur tous les handlers
- [x] Test handle() s'arrête au premier handler qui supporte
- [x] Test handle() avec plusieurs handlers configurés

### McpMethodEnum
- [x] Test toutes les méthodes MCP existent (initialize, tools/list, tools/call)
- [x] Test tryFrom() avec valeurs valides
- [x] Test tryFrom() avec valeur invalide retourne null

### McpController
- [x] Test __invoke() appelle server->handle() avec le contenu de la requête
- [x] Test __invoke() retourne JsonResponse avec status 200
- [x] Test __invoke() sérialise correctement la réponse
- [x] Test __invoke() avec requête vide
- [x] Test __invoke() avec requête invalide

## 3. Method Handlers

### InitializeHandler
- [ ] Test supports() retourne true pour méthode INITIALIZE
- [ ] Test supports() retourne false pour autres méthodes
- [ ] Test handle() retourne JsonRpcResultResponse
- [ ] Test handle() inclut protocolVersion
- [ ] Test handle() inclut serverInfo (name, version, title)
- [ ] Test handle() inclut capabilities de tous les registres
- [ ] Test handle() exclut registres sans capabilities
- [ ] Test handle() avec registres vides
- [ ] Test handle() avec plusieurs registres actifs

### ToolsListHandler
- [ ] Test supports() retourne true pour méthode TOOLS_LIST
- [ ] Test supports() retourne false pour autres méthodes
- [ ] Test handle() retourne JsonRpcResultResponse
- [ ] Test handle() lance NoToolsAvailableException si pas de tools
- [ ] Test handle() format correct avec un tool
- [ ] Test handle() format correct avec plusieurs tools
- [ ] Test handle() inclut toutes les définitions des capabilities

### ToolsCallHandler
- [ ] Test supports() retourne true pour méthode TOOLS_CALL
- [ ] Test supports() retourne false pour autres méthodes
- [ ] Test handle() retourne JsonRpcResultResponse en succès
- [ ] Test handle() appelle le bon tool avec les bons arguments
- [ ] Test handle() retourne JsonRpcErrorResponse si tool non trouvé
- [ ] Test handle() retourne JsonRpcErrorResponse si tool lance exception
- [ ] Test handle() avec arguments manquants
- [ ] Test handle() avec tool name invalide
- [ ] Test handle() propage le résultat du tool

## 4. Registry System

### AbstractCapabilityRegistry
- [ ] Test hasCapabilities() retourne true avec capabilities
- [ ] Test hasCapabilities() retourne false sans capabilities
- [ ] Test getCapability() retourne la bonne capability par nom
- [ ] Test getCapability() retourne null si non trouvé
- [ ] Test getAllCapabilities() retourne toutes les capabilities
- [ ] Test getAllCapabilities() avec iterable vide

### ToolsRegistry
- [ ] Test getName() retourne 'tools'
- [ ] Test getParameters() retourne ['listChanged' => false]
- [ ] Test hérite correctement de AbstractCapabilityRegistry

### PromptsRegistry
- [ ] Test getName() retourne 'prompts'
- [ ] Test getParameters() retourne structure correcte
- [ ] Test hérite correctement de AbstractCapabilityRegistry

### ResourcesRegistry
- [ ] Test getName() retourne 'resources'
- [ ] Test getParameters() retourne structure correcte
- [ ] Test hérite correctement de AbstractCapabilityRegistry

## 5. Tests d'intégration

### Controller intégration
- [ ] Test POST /mcp avec requête initialize valide
- [ ] Test POST /mcp avec requête tools/list
- [ ] Test POST /mcp avec requête tools/call
- [ ] Test POST /mcp avec JSON invalide
- [ ] Test POST /mcp avec méthode invalide

---

**Total estimé: ~100 tests unitaires**
