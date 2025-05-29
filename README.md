<p align="center">
  <img src="public/images/logo.png" alt="Order-Services Logo" width="200"/>
</p>

<h1 align="center">Order-Services</h1>
<p align="center"><em>A Professional Services Marketplace — Inspired by Armut & Thumbtack</em></p>

<p align="center">
  <img src="https://img.shields.io/badge/Laravel-12-red?style=flat-square&logo=laravel" />
  <img src="https://img.shields.io/badge/TailwindCSS-3-blue?style=flat-square&logo=tailwindcss" />
  <img src="https://img.shields.io/badge/MySQL-Active-blue?style=flat-square&logo=mysql" />
</p>

---

## 🌟 Project Overview

**Order-Services** is a Laravel-based full-stack web application connecting clients with professional service providers. Built as a PFE (Projet de Fin d'Études), it offers seamless user role management, dynamic service browsing, intelligent request handling, and secure internal communication — all styled using **Tailwind CSS**.

---

## 🎯 Objectives

- Build a robust, user-friendly, and scalable service marketplace.
- Implement real-world features like provider-client messaging, service request workflows, and review systems.
- Apply full-stack technologies with Laravel 12 and Tailwind CSS.

---

## 🔑 Key Features

### 👥 Users & Roles

- **Multi-Role Authentication**
  - 👤 **Client** (default)
  - 🔧 **Provider** (upgradeable)
  - 🛡️ **Admin**
- Role-specific dashboards and permissions

### 🛒 Client Capabilities

- Browse & filter services
- Send detailed requests
- Track request statuses
- Chat with accepted providers
- Rate completed services
- Apply to become a provider

### 🧰 Provider Capabilities

- Create/manage service listings
- Respond to client requests
- Track service progress
- Internal messaging with clients
- Manage professional profile

### 🧑‍💼 Admin Capabilities

- Manage users, services, and categories
- Moderate reviews and contact requests
- Access dashboard statistics
- Intervene in service requests and chats

### 🧠 System Features

- Role-based access via middleware
- Dynamic average rating calculation
- Light/Dark mode (Tailwind toggle)
- Secure form handling & validation

---

## 🧱 Tech Stack

| Layer        | Technology                    |
| ------------ | ----------------------------- |
| Backend      | PHP 8+, Laravel 12            |
| Frontend     | Blade, Tailwind CSS, Vite     |
| Database     | MySQL                         |
| Messaging    | Laravel Channels (planned Echo/WebSockets) |
| Storage      | Laravel Filesystem + symbolic linking |
| Mail         | Gmail SMTP                    |

---

## 🚀 Installation & Setup

### Prerequisites

- PHP ≥ 8.1  
- Composer  
- Node.js & npm  
- MySQL  

### Steps

```bash
# 1. Clone the repo
git clone <your-repository-url> order-services
cd order-services

# 2. Install backend dependencies
composer install

# 3. Install frontend dependencies
npm install

# 4. Setup environment file
cp .env.example .env

# 5. Generate app key
php artisan key:generate

# 6. Configure .env (DB, Mail, APP_URL)
# Especially update:
# APP_URL=http://localhost:8000
# DB_DATABASE=your_db_name
# MAIL_USERNAME=your-gmail@gmail.com
# MAIL_PASSWORD=your-app-password

# 7. Run migrations
php artisan migrate

# 8. Seed the database
php artisan db:seed

# 9. Link storage
php artisan storage:link

# 10. Compile assets
npm run dev  # or npm run build for production

# 11. Serve the application
php artisan serve
