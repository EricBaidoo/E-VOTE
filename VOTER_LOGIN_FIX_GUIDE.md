# Voter Login Issues - Fix Guide

## Problem
Some voters cannot log in with their name and email/phone. They get "incorrect login" errors.

## Root Cause
The CSV file (`LIST_OF_VOTERS-AMOSA_2025_ELECTIONS.csv`) contains:
1. **Line breaks in the middle of email addresses** - causing emails to be split across multiple lines
2. **Encoding issues** - special characters that break the import
3. **Extra whitespace** - spaces and tabs in email/phone fields
4. **Missing contact information** - some voters have no email or phone

## Quick Fix Steps

### Step 1: Diagnose the Problem
1. Open your browser and go to: `http://yourdomain.com/diagnose_voters.php`
2. This will show you:
   - Voters with missing email/phone
   - Voters with invalid email formats
   - Voters with whitespace in emails

### Step 2: Fix Voter Data
1. Open your browser and go to: `http://yourdomain.com/fix_voters.php`
2. This will automatically:
   - Remove all whitespace from emails
   - Clean phone numbers
   - Fix common formatting issues

### Step 3: Re-import from CSV (if needed)
1. If the fix didn't work, go to: `http://yourdomain.com/reimport_voters.php`
2. This will:
   - Re-read the CSV file with proper encoding
   - Handle line breaks in email addresses
   - Update all voter records

### Step 4: Verify
1. Go to: `http://yourdomain.com/diagnose_voters.php` again
2. Confirm all issues are resolved
3. Test login with a few voter accounts

## Files Created

| File | Purpose |
|------|---------|
| `diagnose_voters.php` | Shows all voter login issues |
| `fix_voters.php` | Automatically fixes email/phone formatting |
| `reimport_voters.php` | Re-imports voters from CSV with proper parsing |

## Manual Fix for Specific Voters

If some voters still can't log in:

1. Go to **Admin Dashboard** → **Voters**
2. Find the voter by ID or name
3. Click **Edit**
4. Ensure they have:
   - **Valid email** (no spaces, proper format: `user@example.com`)
   - **OR valid phone** (numbers only or with country code)
5. Save changes

## Testing Login

To test if a voter can log in:
1. Open login page: `http://yourdomain.com/login.php`
2. Select **Voter** role
3. Enter:
   - **Name**: Exact name from database (case-insensitive)
   - **Email or Phone**: Their email OR phone number

## Common Issues

### Issue: Email has line breaks
**Example**: `esmequagrainie@gmail`<br>`.com`

**Fix**: The `fix_voters.php` script removes all line breaks and spaces from emails.

### Issue: No email or phone
**Example**: Voter has empty email and empty phone fields

**Fix**: You must manually add email or phone for these voters:
1. Contact the voter to get their email or phone
2. Update their record in the database via Admin → Voters → Edit

### Issue: Email format invalid
**Example**: `user@gmail` (missing `.com`)

**Fix**: Manually correct the email address via Admin → Voters → Edit

## Prevention

To prevent this in future:
1. **Clean CSV before import**:
   - Open CSV in Excel
   - Remove all line breaks in cells: Find & Replace `Alt+Enter` with ` ` (space)
   - Save as CSV UTF-8
   
2. **Validate data**:
   - Ensure every voter has email OR phone
   - Check email format: `name@domain.com`
   - Remove extra spaces

## Database Structure

Voters table:
```sql
CREATE TABLE voters (
    id INT PRIMARY KEY,
    name VARCHAR(255) NOT NULL,
    email VARCHAR(255) DEFAULT '',
    phone VARCHAR(50) DEFAULT ''
);
```

## Login Logic

The login system works as follows:
1. User enters **Name** + **Email or Phone**
2. System searches for voter by name (case-insensitive)
3. System checks if entered email/phone matches database:
   - Email: compared after removing spaces (case-insensitive)
   - Phone: compared after removing spaces (exact match)
4. If match found → Login successful
5. If no match → "Invalid credentials" error

## Support

If issues persist after running all fix scripts:
1. Check `logs/php_errors.log` for errors
2. Verify database connection in `includes/db_connect.php`
3. Manually check voter records in database
4. Contact developer with specific error messages
