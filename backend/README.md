# Verbena & Co. — Backend (PHP + MySQL)

This adds a working backend to the static site: appointment bookings, newsletter
signups, a live product shelf, and a small admin panel — all backed by MySQL.

## 1. Requirements

- PHP 8+ with the **mysqli** extension (bundled with any standard PHP install)
- MySQL or MariaDB
- Easiest local setup: **XAMPP** / **WAMP** / **MAMP** (all three include PHP + MySQL + phpMyAdmin)

## 2. Folder structure added

```
beauty salon/
├── index.html                  (now calls the backend via fetch)
├── css/style.css
├── js/script.js                (updated: talks to backend/api/*)
├── images/...
└── backend/
    ├── config.php               ← DB credentials live here
    ├── api/
    │   ├── book_appointment.php
    │   ├── subscribe_newsletter.php
    │   ├── get_products.php
    │   ├── get_services.php
    │   └── add_to_cart.php
    ├── admin/
    │   ├── login.php
    │   ├── dashboard.php
    │   ├── appointments.php
    │   ├── subscribers.php
    │   ├── logout.php
    │   ├── admin.css
    │   └── includes/
    │       ├── auth_check.php
    │       └── nav.php
    └── sql/
        └── schema.sql            ← run this once to create the database
```

## 3. Setup steps

1. **Install XAMPP** (or similar) and start Apache + MySQL from its control panel.
2. Copy the whole `beauty salon` folder into your server's web root
   (e.g. `C:\xampp\htdocs\beauty-salon` on Windows, or `/opt/lampp/htdocs/beauty-salon` on Linux).
3. **Create the database** — open phpMyAdmin (`http://localhost/phpmyadmin`), click
   *Import*, and select `backend/sql/schema.sql`. This creates the `verbena_salon`
   database, all tables, and seeds the services + products + a default admin login.
   (Or via terminal: `mysql -u root -p < backend/sql/schema.sql`.)
4. **Set your DB credentials** in `backend/config.php` — the defaults
   (`root` / no password / `localhost`) match a fresh XAMPP install, so you may not
   need to change anything.
5. Visit `http://localhost/beauty-salon/` — the site now saves bookings and
   subscribers to MySQL, and the product shelf loads live from the database.

## 4. Admin panel

Visit `http://localhost/beauty-salon/backend/admin/login.php`

- **Username:** `admin`
- **Password:** `admin123`

From there you can see booking stats, confirm/cancel/complete appointments, and
view newsletter subscribers. **Change the default password** before putting this
anywhere public — the simplest way is to generate a new hash and update the
`admins` table:

```php
<?php echo password_hash('your-new-password', PASSWORD_BCRYPT);
```

```sql
UPDATE admins SET password_hash = 'paste-the-hash-here' WHERE username = 'admin';
```

## 5. What each endpoint does

| Endpoint | Method | Purpose |
|---|---|---|
| `backend/api/book_appointment.php` | POST | Validates and saves an appointment request |
| `backend/api/subscribe_newsletter.php` | POST | Saves a newsletter email (ignores duplicates) |
| `backend/api/get_products.php` | GET | Returns the shop's products as JSON, read live from MySQL |
| `backend/api/get_services.php` | GET | Returns the price list grouped by category |
| `backend/api/add_to_cart.php` | POST | Logs an "add to bag" action tied to the visitor's session |

All endpoints return JSON like `{ "success": true/false, "message": "..." }`.

## 6. Notes

- Every query uses prepared statements (`mysqli::prepare` + `bind_param`), so
  user input can't be used for SQL injection.
- Admin passwords are stored as bcrypt hashes (`password_hash` / `password_verify`),
  never in plain text.
- The product grid on the homepage fetches from the database on page load; if the
  backend isn't reachable (e.g. you're just opening `index.html` as a local file),
  it silently falls back to the static product cards already in the HTML — so the
  frontend still works stand-alone if you ever need to demo it without a server.
