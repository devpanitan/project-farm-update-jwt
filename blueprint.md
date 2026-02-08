# Project Blueprint

## 1. Overview

This project is a full-stack web application for a Smart Farm system, built with Laravel. It's designed to manage users, farms, IoT devices, and sensor data, providing a platform for monitoring and controlling agricultural environments.

## 2. Implemented Features & Design

### Backend (Laravel API)

*   **Authentication:** JWT-based authentication for securing API endpoints.
*   **User Management:** CRUD operations for users.
*   **Farm Management:** CRUD operations for farms and farm categories.
*   **IoT Device Management:** CRUD operations for IoT devices.
*   **Sensor Data:** Handling and storage of sensor data.
*   **Automation:** Rules-based automation for controlling actuators.
*   **Real-time Communication:** MQTT integration for real-time device communication.
*   **Logging:** Activity logging for tracking user actions.
*   **API Structure:**
    *   Public routes for authentication (`/api/auth/*`).
    *   Protected routes for all other resources, requiring `auth:api` middleware.

### Frontend

*   _Not yet implemented._

### Design

*   _Not yet implemented._

## 3. Current Plan

*   **Secure API Routes:** Group all relevant API endpoints under the `auth:api` middleware to ensure data is protected and accessible only by authenticated users. (Completed)
*   **Standardize Activity Log Routes:** Refactor the `ActivityLogController` routes to use `apiResource` for consistency with other controllers. (Completed)
*   **Develop Frontend UI:** Create a user interface to interact with the backend API.
*   **Database Seeding:** Populate the database with initial data for development and testing.

---
*This document is automatically generated and updated by Gemini AI to reflect the current state of the project.*
