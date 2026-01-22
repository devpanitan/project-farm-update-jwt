# Project Blueprint

## Overview

This project is a full-stack web application built with the Laravel framework. It is designed for development within the Firebase Studio environment and focuses on creating a fast, robust, and scalable application by leveraging Laravel's powerful features for routing, data handling, and backend logic.

## Project Outline

### Style and Design

*   **Frontend Framework:** Blade Templating Engine
*   **CSS Framework:** Tailwind CSS (default configuration)
*   **JavaScript:** ES6+ with Vite for asset bundling

### Features

*   **Authentication:** API authentication is implemented using Laravel Sanctum.
*   **Database:** The application uses a MySQL database with the Eloquent ORM.
*   **Real-time Broadcasting:** Real-time event broadcasting is set up using Laravel Reverb.

## Current Request: Set up Broadcasting

The user requested to set up real-time broadcasting. The following steps were taken to fulfill this request:

1.  **Initial Attempt:** The user tried to run `php artisan install:broadcasting`, which is not a standard Laravel command.
2.  **Package Installation:** Identified the user's intent to set up broadcasting and installed the `laravel/reverb` package using Composer.
3.  **Installation Command:** Attempted to run `php artisan reverb:install` but encountered an error due to the interactive nature of the command.
4.  **Manual Configuration:** Manually configured broadcasting by:
    *   Adding the `channels.php` route file to the `withRouting()` method in `bootstrap/app.php`.
    *   Registering the `App\Providers\BroadcastServiceProvider` in `bootstrap/app.php`.
