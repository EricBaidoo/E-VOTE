# Hosting the E-VOTE System Online

## Step 1: Export Your Database Locally

1. Visit: `http://localhost/E-VOTE/export_database.php`
2. Download the generated `evote_db_export.sql` file
3. This file contains all 1,175 voters and system data

---

## Step 2: Setup Database on Your Hosting

### Using cPanel (Most Common):

1. **Login to cPanel**
2. **Go to MySQL Databases**
   - Create new database: `yourusername_evote` (hosting adds prefix)
   - Create new user with password
   - Add user to database with ALL PRIVILEGES

3. **Go to phpMyAdmin**
   - Select your new database
   - Click **Import** tab
   - Choose the `evote_db_export.sql` file
   - Click **Go**
   - Wait for import to complete (1,175 voters)

---

## Step 3: Update Database Credentials

Edit `includes/db_connect.php` on your server:

```php
// Online hosting credentials
$db_host = 'localhost';  // Usually 'localhost'
$db_username = 'yourusername_evoteuser';  // From cPanel
$db_password = 'your_secure_password';    // From cPanel
$db_name = 'yourusername_evote';          // From cPanel
```

**Important:** 
- Hosting providers usually add your username as prefix (e.g., `john_evote`)
- Get exact credentials from your hosting cPanel

---

## Step 4: Upload Files

### Upload these folders/files:
```
/admin/
/assets/
/css/
/data/              (contains aspirants.json with candidate images)
/includes/
/js/
/voter/
index.php
login.php
logout.php
database_schema.sql (keep for reference)
LIST_OF_VOTERS-AMOSA_2025_ELECTIONS.csv (keep for backup)
```

### DO NOT upload:
- `.git/` folder
- `.venv/` folder
- `logs/` folder
- `export_database.php` (delete after export)
- Local backup files

---

## Step 5: Set Permissions

Via cPanel File Manager or FTP:
- `data/` folder: **755** (read/write for server)
- `includes/` folder: **755**
- All `.php` files: **644**
- `data/aspirants.json`: **644**

---

## Step 6: Test Online

1. Visit: `https://yourdomain.com/login.php`
2. Test voter login: Name + Email/Phone
3. Test admin login: `admin` / `admin123`

---

## Security Checklist:

✅ Change admin PIN after first login  
✅ Use strong database password  
✅ Enable HTTPS/SSL on your domain  
✅ Delete `export_database.php` after use  
✅ Backup database regularly via cPanel  

---

## Troubleshooting:

**"Connection failed"** → Check database credentials in `db_connect.php`  
**"Database not found"** → Verify database name includes hosting prefix  
**"Access denied"** → Check user has ALL PRIVILEGES on database  
**"Can't read aspirants.json"** → Check file uploaded and permissions are 644  

---

## Database Backup (Ongoing):

- Use cPanel's automatic backup feature
- Or: phpMyAdmin → Export → SQL
- Backup before any major changes
