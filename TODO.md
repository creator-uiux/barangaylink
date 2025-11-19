# TODO: Convert BarangayLink to MySQL for Render.com Deployment

## Tasks
- [x] Update composer.json to require ext-pdo_mysql instead of ext-mysqli
- [x] Update config.php to add MySQL database constants
- [x] Update db.php to use MySQL PDO connection
- [x] Update init_db.php to initialize MySQL database with schema.sql
- [x] Update render.yaml to enable MySQL service and set environment variables
- [x] Test database connection and initialization
- [ ] Deploy to Render.com and verify

## Notes
- The app currently uses SQLite, needs conversion to MySQL for Render.com
- Fatal error "mysqli" likely due to missing pdo_mysql extension
- Render.com supports MySQL databases but not SQLite persistence
