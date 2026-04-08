# SecondHand Marketplace

**COSC-2956 Internet Tools — Term Project**
Algoma University

---

## Project Description

A full-stack web application for buying and selling second-hand items including electronics, clothing, books, vinyl records, and collectibles. Built with PHP, MySQL, Bootstrap 5, and JavaScript.

### Features
- User registration and login with hashed passwords
- Browse and search listings by category
- Product detail pages with add-to-cart
- Shopping cart with quantity management
- Checkout and order placement
- Order history per user
- Seller listing form with image URL preview
- Admin dashboard: manage listings, orders, and users

---

## Tech Stack

| Layer      | Technology                     |
|------------|--------------------------------|
| Frontend   | HTML5, Bootstrap 5, JavaScript |
| Backend    | PHP 8+                         |
| Database   | MySQL (via PDO)                |
| Dev Tools  | XAMPP, phpMyAdmin              |

---

## Setup Instructions

### 1. Clone the repository
```bash
git clone https://github.com/SaifVhora/marketplace.git
```

### 2. Move to XAMPP htdocs
Copy the `marketplace/` folder into:
```
C:/xampp/htdocs/marketplace/
```

### 3. Import the database
1. Open **phpMyAdmin** → `http://localhost/phpmyadmin`
2. Click **Import** → select `marketplace_db.sql` → click **Go**

The SQL file includes `CREATE DATABASE` — no manual setup needed.

### 4. Configure database connection
Open `includes/db.php` — update port if needed (default is `3307` for XAMPP):
```php
"mysql:host=127.0.0.1;port=3307;dbname=marketplace_db;charset=utf8mb4"
```

### 5. Run the app
Visit: `http://localhost/marketplace`

---

## Default Accounts

| Role  | Email                 | Password  |
|-------|-----------------------|-----------|
| Admin | admin@marketplace.com | admin123  |
| User  | john@example.com      | user123   |

---

## Project Structure

```
marketplace/
├── index.php
├── products.php
├── product.php
├── cart.php
├── checkout.php
├── orders.php
├── sell.php
├── login.php
├── register.php
├── logout.php
├── admin/
│   ├── index.php
│   ├── products.php
│   ├── orders.php
│   └── users.php
├── includes/
│   ├── db.php
│   ├── auth.php
│   ├── header.php
│   └── footer.php
├── css/style.css
├── js/main.js
├── images/
└── marketplace_db.sql
```

---

## Student Information

- **Name:** Saif Vhora
- **Course:** COSC-2956 Internet Tools
- **Institution:** Algoma University
- **Semester:** Winter 2026

---

## Demo Video

[Link to video](#)
