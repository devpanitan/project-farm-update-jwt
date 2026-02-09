# Project Blueprint

## 1. Overview

This project is a full-stack web application built with the Laravel framework. It is designed to be developed within the Firebase Studio environment and includes features for user authentication via JWT and real-time communication capabilities using MQTT.

## 2. Implemented Features & Design

*   **Framework:** Laravel
*   **Frontend:** Blade templates with Vite for asset bundling (CSS, JS).
*   **Authentication:** JWT-based authentication.
*   **Real-time:** MQTT integration for real-time features.
*   **Database:** MySQL.
*   **Styling:** Standard Laravel styles.

## 3. Current Task: Resolving Critical Errors

The application is currently facing a series of critical errors preventing it from running correctly in the production/preview environment.

**Plan & Steps:**

The root cause of all issues has been identified as an incorrect and incomplete `.env` configuration file. The following steps will be executed to fix the application:

1.  **Update `APP_URL`:** The `APP_URL` in the `.env` file will be changed from `http://localhost` to the correct public HTTPS URL of the preview environment. This will resolve the "Mixed Content" errors.
2.  **Generate `APP_KEY`:** The `php artisan key:generate` command will be run to create a secure application key, which is essential for encryption and session security.
3.  **Generate `JWT_SECRET`:** The `php artisan jwt:secret` command will be run to create a secure secret for JWT signing.
4.  **Clear Configuration Cache:** The `php artisan config:clear` command will be executed to ensure all parts of the application load the new, corrected configuration from the `.env` file.
