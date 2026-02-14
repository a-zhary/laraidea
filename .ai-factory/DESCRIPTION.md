# Project: Laraidea

## Overview
Laravel 12 skeleton application with modern PHP development practices, TailwindCSS 4, and Pest testing (including browser testing).

## Tech Stack
- **Language:** PHP 8.2+
- **Framework:** Laravel 12
- **Frontend:** TailwindCSS 4 + Vite + Alpine.js
- **Testing:** Pest 4 with Browser Testing
- **Code Quality:** Pint (formatting), Rector (refactoring), PHPStan

## Architecture Notes
- Standard Laravel 12 directory structure
- Uses Laravel Boost MCP for Laravel-specific functionality
- Browser testing with Playwright
- SQLite for development, PostgreSQL-ready

## MCP Servers Configured
- laravel-boost (local) - Laravel-specific tools
- github - GitHub integration
- filesystem - File operations
- postgres - Database queries
- chrome-devtools - Browser automation
