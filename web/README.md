# Dirt Scorekeeper — Web Starter (PHP + MySQL)

This is a starter web project for the "Dirt" card game scorekeeper.

## Features
- Start / manage games
- Enter bids and hands won per round
- Edit past rounds
- Player management
- Dashboard (resume last game)
- Player statistics
- Admin login (simple, file-based credential for starter)
- Responsive mobile-first UI and CSS
- PHP backend, MySQL schema
- API endpoints under `public/api/`

## Install
1. Put the `public/` folder under your web server root (e.g., `/var/www/html/dirt`).
2. Update `config/db.php` with your DB credentials.
3. Import `schema.sql` into your MySQL server.
4. Ensure PHP PDO extension and MySQL are available.

## Default admin
- username: `admin`
- password: `adminpass`

Change these in `config/admin.php` before production.

## Files of interest
- `public/index.php` — Dashboard
- `public/new_game.php` — Create a new game
- `public/game.php` — Game screen
- `public/edit_round.php` — Edit an existing round
- `public/stats.php` — Player stats
- `public/js/app.js` — Client JS
- `public/css/styles.css` — Styling
- `config/db.php` — DB connection
- `config/admin.php` — Admin credentials (starter only)
- `schema.sql` — Database schema to import

This is a starter. For production: add proper authentication, CSRF protection, input validation, prepared statements (already used), and hardened session handling.
