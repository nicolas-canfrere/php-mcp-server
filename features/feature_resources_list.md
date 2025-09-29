# Feature: resources/list Implementation

## Overview
Implementation of the MCP `resources/list` method to expose available resources to clients. This feature allows clients to discover resources that provide contextual data (file contents, database information, etc.) to AI models.

## MCP Specification Summary

### Request Format
```json
{
  "jsonrpc": "2.0",
  "id": 1,
  "method": "resources/list",
  "params": {
    "cursor": "optional-cursor-value"
  }
}
```

### Response Format
```json
{
  "jsonrpc": "2.0",
  "id": 1,
  "result": {
    "resources": [
      {
        "uri": "file:///project/src/main.rs",
        "name": "main.rs",
        "title": "Rust Software Application Main File",
        "description": "Primary application entry point",
        "mimeType": "text/x-rust"
      }
    ],
    "nextCursor": "next-page-cursor"
  }
}
```

### Resource Structure
- `uri` (required): Unique identifier for the resource
- `name` (required): The name of the resource
- `title` (optional): Human-readable name for display purposes
- `description` (optional): Description of the resource
- `mimeType` (optional): MIME type of the resource

## Implementation Task List

### Phase 1: Core Data Structures
- [ ] Create `ResourceInterface` in `src/Mcp/` with methods:
  - `getUri(): string`
  - `getName(): string`
  - `getTitle(): ?string`
  - `getDescription(): ?string`
  - `getMimeType(): ?string`
  - `getDefinition(): array` (returns array representation for JSON response)

- [ ] Create `ResourceCapabilityInterface` extends `CapabilityInterface` in `src/Mcp/`
  - Add resource-specific methods if needed
  - Ensure compatibility with existing `CapabilityInterface`

- [ ] Create abstract `AbstractResource` class implements `ResourceInterface`
  - Constructor with required properties: `uri`, `name`
  - Constructor with optional properties: `title`, `description`, `mimeType`
  - Implement `getDefinition()` to return array with all properties

### Phase 2: Exception Handling
- [ ] Create `NoResourcesAvailableException` in `src/Mcp/Exception/`
  - Similar pattern to `NoToolsAvailableException`
  - Message: "No resources available"
  - Extends base exception

- [ ] Create `ResourceNotFoundException` in `src/Mcp/Exception/`
  - Similar pattern to `ToolNotFoundException`
  - Message: "Resource not found: {uri}"
  - Store resource URI in exception

### Phase 3: Registry Updates
- [ ] Verify `ResourcesRegistry` configuration
  - Ensure `getName()` returns `'resources'`
  - Update `getParameters()` to return `['listChanged' => false]`
  - Verify it extends `AbstractCapabilityRegistry` correctly
  - Add service configuration in `services.yaml` for resource autowiring

### Phase 4: Method Handler Implementation
- [ ] Create `ResourcesListHandler` in `src/Mcp/MethodHandler/`
  - Constructor: inject `ResourcesRegistry`
  - Implement `supports()`: return true for `McpMethodEnum::RESOURCES_LIST`
  - Implement `handle()`:
    - Check if `$resourcesRegistry->hasCapabilities()`, throw `NoResourcesAvailableException` if empty
    - Format response with `resources` key containing array of resource definitions
    - Handle optional `cursor` parameter for pagination (future enhancement)
    - Return `JsonRpcResultResponse` with formatted resources array

- [ ] Register `ResourcesListHandler` in `services.yaml`
  - Tag with `mcp.method_handler`
  - Ensure proper dependency injection

### Phase 5: Pagination Support (Optional)
- [ ] Add pagination handling in `ResourcesListHandler`
  - Extract `cursor` from request params
  - Implement cursor-based pagination logic
  - Add `nextCursor` to response when more results available
  - Document cursor format (e.g., base64-encoded page info)

### Phase 6: Testing
- [ ] Unit test `ResourceInterface` implementation
  - Test `getDefinition()` with all properties
  - Test `getDefinition()` with only required properties
  - Test getter methods

- [ ] Unit test `ResourcesRegistry`
  - Test `getName()` returns 'resources'
  - Test `getParameters()` returns correct structure
  - Test `hasCapabilities()` with/without resources
  - Test `getAllCapabilities()` returns all resources

- [ ] Unit test `ResourcesListHandler`
  - Test `supports()` returns true for `RESOURCES_LIST` method
  - Test `supports()` returns false for other methods
  - Test `handle()` returns `JsonRpcResultResponse`
  - Test `handle()` throws `NoResourcesAvailableException` when no resources
  - Test `handle()` formats response correctly with one resource
  - Test `handle()` formats response correctly with multiple resources
  - Test `handle()` includes all resource properties in response

- [ ] Integration test
  - Test POST /mcp with `resources/list` request
  - Verify response structure matches MCP specification
  - Test with empty resources registry
  - Test with multiple registered resources

### Phase 7: Documentation
- [ ] Update `todo.md` with completed tasks
  - Mark ResourcesRegistry tests as completed
  - Add ResourcesListHandler tests to the list

- [ ] Create example resource implementation (optional)
  - Example: `FileResource` for file system resources
  - Example: `DatabaseResource` for database records
  - Document how to create custom resources

## Architecture Notes

### Current Architecture Pattern
The implementation follows the existing MCP architecture:
1. **Registry Pattern**: `ResourcesRegistry` manages resource capabilities
2. **Handler Pattern**: `ResourcesListHandler` processes `resources/list` requests
3. **Capability Pattern**: Resources implement `CapabilityInterface` (or `ResourceCapabilityInterface`)
4. **Response Format**: Use `JsonRpcResultResponse` for successful responses

### Key Design Decisions
1. **Separation of Concerns**: Resources are capabilities, handlers process requests
2. **Extensibility**: `ResourceInterface` allows different resource types (file, database, API, etc.)
3. **Pagination**: Initial implementation may skip pagination, add later as enhancement
4. **Error Handling**: Follow existing exception patterns for consistency

### Dependencies
- `ResourcesRegistry` (already exists)
- `JsonRpcRequest` (existing)
- `JsonRpcResultResponse` (existing)
- `McpMethodEnum::RESOURCES_LIST` (already defined)
- `AbstractCapabilityRegistry` (existing base class)

## Acceptance Criteria
- ✅ `resources/list` request returns valid JSON-RPC response
- ✅ Response includes array of resources with required fields (uri, name)
- ✅ Response includes optional fields when present (title, description, mimeType)
- ✅ Handler throws exception when no resources available
- ✅ All unit tests pass
- ✅ Integration test validates against MCP specification
- ✅ Code follows PSR-12 and Symfony coding standards
- ✅ PHPStan analysis passes at max level

## Estimated Effort
- Phase 1-4 (Core Implementation): 2-3 hours
- Phase 5 (Pagination): 1-2 hours (optional, can be deferred)
- Phase 6 (Testing): 2-3 hours
- Phase 7 (Documentation): 30 minutes

**Total**: 5-8 hours (without pagination)

## Related Files
- `src/Mcp/McpMethodEnum.php` - Contains `RESOURCES_LIST` enum case
- `src/Mcp/Registry/ResourcesRegistry.php` - Registry for resources
- `src/Mcp/Registry/AbstractCapabilityRegistry.php` - Base registry class
- `src/Mcp/MethodHandler/ToolsListHandler.php` - Similar handler pattern reference
- `src/Mcp/CapabilityInterface.php` - Base capability interface
- `tests/Mcp/MethodHandler/ToolsListHandlerTest.php` - Test pattern reference (to be created)

## Implementation Order
1. Start with data structures (Phase 1) - foundation for everything else
2. Add exception handling (Phase 2) - needed by handler
3. Verify registry setup (Phase 3) - ensure proper configuration
4. Implement handler (Phase 4) - core functionality
5. Add tests (Phase 6) - validate implementation
6. Update documentation (Phase 7) - track progress
7. Consider pagination (Phase 5) - future enhancement if needed