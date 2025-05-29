<p align="center">
  <img src="public/images/logo.png" alt="Order-Services Logo" width="150" />
</p>

<h1 align="center">Order-Services</h1>

<p align="center">
  <b>Inspired by Armut / Thumbtack</b><br>
  Connect clients with service providers through a powerful Laravel 12 platform.
</p>

<p align="center">
  <img src="https://img.shields.io/badge/Laravel-12-red?logo=laravel" alt="Laravel Version" />
  <img src="https://img.shields.io/badge/license-MIT-blue.svg" alt="License: MIT" />
  <img src="https://img.shields.io/badge/status-in%20development-yellow" alt="Project Status" />
</p>

---

## 🎯 Project Goal

To build a service marketplace where clients easily find and book providers. This version focuses on key functionality, excluding payment systems for simplicity.

---

## 🚀 Core Features

### 👥 Multi-Role System
- **Client**: Browse and request services.
- **Provider**: Offer services, accept requests.
- **Admin**: Manage users, services, reviews, and messages.

### 🧑 Client Capabilities
- Filter services/providers by **location**, **category**, and **ratings**.
- Send requests with **details, address, and proposed budget**.
- Track request status and message providers.
- Leave **reviews** and manage profile.
- Option to **become a provider**.

### 🧑‍🔧 Provider Tools
- Manage own **service listings** (with price, images, availability).
- Accept or refuse incoming requests.
- Update request status, message clients, and manage profile.
- View client reviews and respond.

### 🛡️ Admin Powers
- Full dashboard: **user & service analytics**.
- Moderate: users, services, categories, reviews.
- View all activity and communicate with any user.

### ⚙️ System Features
- Middleware-based **role access** control.
- **Notification system** (planned).
- CSRF protection, validations, and theme toggling.
- Image uploads and dynamic rating calculations.

---

## 🧰 Technologies

| Layer       | Tech Stack                                      |
|-------------|-------------------------------------------------|
| Backend     | PHP, **Laravel 12**                             |
| Frontend    | HTML, Tailwind CSS, JavaScript                  |
| Database    | MySQL                                           |
| Tools       | Laravel Sail / Valet / XAMPP (Dev Envs)         |
| Build Tool  | Vite (Frontend Asset Compilation)               |

---

## 🛠️ Installation

```bash
# 1. Clone the repo
git clone <your-repository-url> order-services
cd order-services

# 2. Install PHP dependencies
composer install

# 3. Install JS dependencies
npm install

# 4. Environment setup
cp .env.example .env
php artisan key:generate
