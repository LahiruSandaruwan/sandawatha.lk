# Sandawatha.lk

Sri Lankan Matrimonial Website

## Setup Instructions

1. Clone the repository
2. Create a MySQL database named 'sandawatha'
3. Import database/schema.sql
4. Copy .env.example to .env and update the values
5. Ensure storage directories are writable
6. Point your web server to the public directory

## Directory Structure

- public/: Web root directory
- app/: Application code
- api/: API endpoints
- config/: Configuration files
- database/: Database schema and migrations
- storage/: File uploads and logs

## Requirements

- PHP 7.4 or higher
- MySQL 5.7 or higher
- mod_rewrite enabled
- GD Library for image processing
- FileInfo extension for file uploads
