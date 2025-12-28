# Deploy Voter Login Fix to Online Server

## Files to Upload

Upload these NEW files to your online server (same directory as your main site):

```
diagnose_voters.php
fix_voters.php
repair_voters.php
test_login_matching.php
reimport_voters.php
VOTER_LOGIN_FIX_SUMMARY.md
VOTER_LOGIN_FIX_GUIDE.md
```

## Step-by-Step Deployment

### Method 1: Using FTP/cPanel File Manager (Recommended)

1. **Login to your hosting control panel (cPanel/Plesk)**

2. **Open File Manager**
   - Navigate to your E-VOTE directory (usually `public_html` or `htdocs`)

3. **Upload the fix files**
   - Upload all 5 PHP files listed above
   - Place them in the root directory (same level as `index.php`)

4. **Access the repair tool online**
   ```
   https://yourdomain.com/repair_voters.php
   ```

5. **Run the fix**
   - Click "Find Login Issues" - see which voters are affected
   - Click "Clean All Email/Phone" - fix all data
   - Click "Find Login Issues" again - verify all fixed

6. **Test voter login**
   - Have a voter try logging in
   - Confirm they can use their email/phone

### Method 2: Using Git (if you use version control)

```bash
# On your local machine
git add diagnose_voters.php fix_voters.php repair_voters.php test_login_matching.php reimport_voters.php
git commit -m "Add voter login data cleanup tools"
git push origin main

# On your server (via SSH)
cd /path/to/your/evote
git pull origin main
```

### Method 3: Direct FTP Upload

Use FileZilla or any FTP client:

1. **Connect to your server**
   - Host: your-ftp-hostname.com
   - Username: your-ftp-username
   - Password: your-ftp-password

2. **Navigate to your E-VOTE folder**
   - Usually: `/public_html/` or `/htdocs/`

3. **Upload files**
   - Drag and drop the 5 PHP files from local to server
   - Ensure they're in the root directory

## Quick Access URLs

After uploading, access these URLs:

| Tool | URL |
|------|-----|
| **Main Repair Dashboard** | `https://yourdomain.com/repair_voters.php` |
| Find Issues | `https://yourdomain.com/diagnose_voters.php` |
| Fix Data | `https://yourdomain.com/fix_voters.php` |
| Test Matching | `https://yourdomain.com/test_login_matching.php` |

Replace `yourdomain.com` with your actual domain.

## Running the Fix Online

### Option A: Use the Web Interface (Easiest)

1. Go to: `https://yourdomain.com/repair_voters.php`
2. Follow the 3-step process on screen
3. Done!

### Option B: Run Scripts Individually

1. **Diagnose**: `https://yourdomain.com/diagnose_voters.php`
   - See how many voters are affected
   - Shows before/after comparison

2. **Fix**: `https://yourdomain.com/fix_voters.php`
   - Automatically cleans all voter data
   - Shows results

3. **Verify**: `https://yourdomain.com/test_login_matching.php`
   - Confirms voters can now login

## Important Notes

### Database Connection
The scripts use your existing `includes/db_connect.php`, so they'll automatically connect to your online database. No configuration needed.

### Permissions
Ensure the uploaded PHP files have correct permissions:
- Files: `644` (read/write for owner, read for others)
- If you get "403 Forbidden", set permissions to `755`

### Security Considerations

**After fixing the voter data**, for security:

1. **Option 1: Delete the fix files** (recommended after use)
   ```
   diagnose_voters.php
   fix_voters.php
   repair_voters.php
   test_login_matching.php
   reimport_voters.php
   ```

2. **Option 2: Protect with .htaccess**
   Create a file named `.htaccess` with:
   ```apache
   <Files "diagnose_voters.php">
       Require ip YOUR_IP_ADDRESS
   </Files>
   <Files "fix_voters.php">
       Require ip YOUR_IP_ADDRESS
   </Files>
   <Files "repair_voters.php">
       Require ip YOUR_IP_ADDRESS
   </Files>
   <Files "test_login_matching.php">
       Require ip YOUR_IP_ADDRESS
   </Files>
   ```
   Replace `YOUR_IP_ADDRESS` with your actual IP

3. **Option 3: Add password protection**
   Add to top of each file (after `<?php`):
   ```php
   // Simple password protection
   if (!isset($_GET['key']) || $_GET['key'] !== 'your_secret_key_here') {
       die('Access denied');
   }
   ```
   Then access: `https://yourdomain.com/repair_voters.php?key=your_secret_key_here`

## Troubleshooting

### "500 Internal Server Error"
- Check file permissions (should be 644)
- Check PHP error log in cPanel
- Ensure files are uploaded in ASCII mode (not binary)

### "Database connection failed"
- Your `includes/db_connect.php` is already configured for online
- No changes needed

### "Cannot find voters"
- Ensure you're accessing the correct domain
- Check that database has voter data

### "Permission denied"
- Files need 644 permissions
- Check with your hosting provider

## Verification Checklist

After deploying and running:

- [ ] Uploaded all 5 PHP files to server
- [ ] Accessed `repair_voters.php` successfully
- [ ] Ran diagnostic - saw affected voters
- [ ] Ran fix - cleaned voter data
- [ ] Ran diagnostic again - confirmed all fixed
- [ ] Tested voter login - works!
- [ ] Deleted/protected fix files (security)

## Example: Complete Process

```
1. Upload files via FTP/cPanel
   ✓ diagnose_voters.php uploaded
   ✓ fix_voters.php uploaded
   ✓ repair_voters.php uploaded
   ✓ test_login_matching.php uploaded

2. Access repair dashboard
   https://yourdomain.com/repair_voters.php

3. Click "Find Login Issues"
   Result: 47 voters have email with whitespace

4. Click "Clean All Email/Phone"
   Result: 47 voters fixed!

5. Click "Find Login Issues" again
   Result: 0 issues found ✓

6. Test login as voter
   Name: John Doe
   Email: johndoe@gmail.com
   Result: Login successful ✓

7. Delete fix files (security)
   ✓ All temporary fix files removed
```

## Need Help?

If you encounter issues:
1. Check your hosting's PHP error log (cPanel → Error Log)
2. Ensure PHP version is 7.4 or higher
3. Verify database credentials in `includes/db_connect.php`
4. Contact your hosting provider if database access issues persist

## After Fixing

Once all voters can login:
1. **Delete the fix files** (or protect them)
2. **Test with several voters** to confirm
3. **Monitor for any remaining issues**
4. **Keep backup** of working database

Done! Your online voters should now be able to login with their email/phone.
