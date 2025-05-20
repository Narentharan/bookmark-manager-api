# Bookmark Manager API

A secure and scalable Laravel 12-based RESTful API for managing personal bookmarks.

## Features

- JWT Authentication
- Bookmark CRUD with tags & category
- Search & filter bookmarks
- Public + private bookmark sharing
- Admin-only view for all bookmarks
- Fully tested (unit & integration)

## Setup

```bash
composer install
cp .env.example .env
php artisan key:generate
php artisan migrate
php artisan serve

## Seeding the Database

Run the following command to seed an admin user and sample data:

```bash
php artisan db:seed
