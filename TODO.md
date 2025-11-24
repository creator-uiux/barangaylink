# Database Migration: SQLite to PostgreSQL for Render.com

## Pending Tasks
- [ ] Update config.php to use PostgreSQL with proper env vars (PGHOST, PGPORT, PGDATABASE, PGUSER, PGPASSWORD)
- [ ] Add PostgreSQL support to db.php Database class
- [ ] Create database/schema_pgsql.sql with PostgreSQL-compatible schema
- [ ] Update render.yaml to use PostgreSQL service instead of disk mount
- [ ] Modify init_db.php to work with PostgreSQL and use new schema
- [ ] Update Dockerfile to include PostgreSQL extensions
- [ ] Test database connection and initialization

## Completed Tasks
- [x] Analyze current codebase and identify migration requirements
- [x] Create migration plan
