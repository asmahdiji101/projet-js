# NeonPass

Starter PHP + JavaScript project for an event and ticketing platform.

## Stack
- Vanilla PHP with MVC structure
- Vanilla JavaScript
- MySQL

## Structure
- `config/` database and bootstrap configuration
- `app/` controllers, models, views, core classes
- `public/` web entrypoint, assets, rewrite rules
- `sql/schema.sql` database schema

## Next steps
1. Import `sql/schema.sql` into MySQL
2. Configure database credentials in environment variables
3. Add authentication controllers and views
4. Add CRUD for artists, events, tickets, and bookings

## Local run without XAMPP/WAMP
PHP can run the app directly using SQLite fallback:

```powershell
php -S localhost:8000 -t public
```

Then open:

```text
http://localhost:8000
```

Demo accounts seeded for local testing:
- `admin@neonpass.local` / `admin123`
- `user@neonpass.local` / `user123`
