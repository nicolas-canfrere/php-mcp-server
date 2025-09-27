# CLAUDE.md

This file provides guidance to Claude Code (claude.ai/code) when working with code in this repository.

## Project Overview

This is a Symfony 7.3-based PHP MCP (Model Context Protocol) server built with PHP 8.2+. The project uses Docker for containerization and includes a comprehensive development workflow with quality tools.

## Development Commands

### Essential Commands
- `make install` - Install dependencies via Composer in Docker
- `make start` - Start the development environment (nginx + php-fpm)
- `make stop` - Stop the development environment
- `make test` - Run PHPUnit tests in isolated test environment
- `make static-code-analysis` - Run PHPStan static analysis
- `make apply-cs` - Apply PHP CS Fixer coding standards

### Docker Environment
- **Development**: Uses `compose.yaml` with nginx (port 8000) + php-fpm
- **Testing**: Uses `compose.test.yaml` with isolated PHP container
- **PHP Version**: 8.4.12-fpm-alpine3.22 with intl, pdo_pgsql, opcache extensions

### CLI Access
- `make composer-cli` - Access Composer CLI in Docker container
- `make php-cli` - Access PHP CLI in Docker container
- `./bin/console` - Symfony console commands (after dependencies installed)

## Code Quality Standards

### Static Analysis
- **PHPStan**: Level max analysis with Symfony and PHPUnit extensions
- Configuration: `phpstan.dist.neon`
- Scans: bin/, config/, public/, src/, tests/

### Code Style
- **PHP CS Fixer**: Symfony + PSR2 standards with strict types enforcement
- Configuration: `.php-cs-fixer.dist.php`
- Key rules: declare_strict_types, array_syntax short, ordered imports

### Testing
- **PHPUnit**: Configured with strict error reporting (fail on deprecation/notice/warning)
- Configuration: `phpunit.dist.xml`
- Bootstrap: `tests/bootstrap.php`

## Architecture

### Symfony Structure
- **Kernel**: Standard Symfony MicroKernelTrait implementation
- **Services**: Autowired with PSR-4 autoloading (App\ namespace)
- **Configuration**: YAML-based in config/ directory

### Container Setup
- **Base Platform**: Alpine Linux with timezone configuration
- **Development**: Includes Xdebug 3.4.3 for debugging
- **Environment**: APP_ENV=dev for development container

### File Organization
- `src/` - Application code (PSR-4: App\)
- `tests/` - Test files (PSR-4: App\Tests\)
- `config/` - Symfony configuration files
- `public/` - Web entry point (index.php)
- `bin/` - Console executables

## Development Workflow

1. **Setup**: `make install` to install dependencies
2. **Development**: `make start` to run development environment
3. **Code Quality**: Run `make apply-cs` before commits
4. **Testing**: Use `make test` for full test suite
5. **Analysis**: Run `make static-code-analysis` for type checking

## Environment Files
- `.env` - Base environment configuration
- `.env.dev` - Development-specific settings
- `.env.test` - Test environment configuration (for testing container)