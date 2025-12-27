# QUICK REFERENCE GUIDE - E-VOTING SYSTEM

## 📍 Important URLs

```
Home Page:       http://localhost/E-VOTE/
Installation:    http://localhost/E-VOTE/install.php
Setup Guide:     http://localhost/E-VOTE/setup.php
Getting Started: http://localhost/E-VOTE/getting-started.html
```

## 🔑 Default Login Credentials

### Admin Account
- **Username**: admin
- **Password**: admin123
- **Access**: Admin Dashboard at `/admin/dashboard.php`

### Sample Voter Account
- **Username**: voter1
- **Password**: password123
- **Voter ID**: VOTER_00000001
- **Access**: Voter Dashboard at `/voter/dashboard.php`

## 📂 File Locations

### Root Directory Files
| File | Purpose |
|------|---------|
| index.php | Home/Landing page |
| login.php | Login page for all users |
| register.php | Voter registration page |
| logout.php | Session termination |
| install.php | Installation wizard |
| setup.php | Setup instructions |
| getting-started.html | Quick start guide |
| database.sql | Database schema and sample data |
| README.md | Full documentation |
| PROJECT_OVERVIEW.md | Project details |

### Admin Pages (`/admin/`)
| Page | Function |
|------|----------|
| dashboard.php | Admin statistics and overview |
| positions.php | Create/edit voting positions |
| candidates.php | Add/remove candidates |
| voters.php | View all registered voters |
| results.php | View election results |
| profile.php | Admin profile information |

### Voter Pages (`/voter/`)
| Page | Function |
|------|----------|
| dashboard.php | Voter statistics and status |
| vote.php | Cast vote for all positions |
| results.php | View election results |
| vote_success.php | Vote confirmation page |
| profile.php | Voter profile information |

### Backend Files (`/includes/`)
| File | Contains |
|------|----------|
| db_connect.php | Database connection |
| config.php | Configuration constants |
| functions.php | Helper functions |

### Styling & Scripts
| File | Purpose |
|------|---------|
| css/style.css | All CSS styling |
| js/voting.js | JavaScript functions |

## 🗄️ Database Information

**Database Name**: `evoting_system`

**Tables**:
- `users` - Admin and voter accounts
- `voters` - Voter information and voting status
- `positions` - Election positions/offices
- `candidates` - Candidates for positions
- `votes` - Vote records

**Default Connection**:
```
Host: localhost
Username: root
Password: (empty)
```

## 🔧 Configuration Files to Modify

### Database Connection
**File**: `includes/db_connect.php`

```php
$db_host = 'localhost';
$db_username = 'root';
$db_password = '';
$db_name = 'evoting_system';
```

### Site Configuration
**File**: `includes/config.php`

```php
define('SITE_TITLE', 'E-Voting System');
define('SITE_URL', 'http://localhost/E-VOTE');
define('VOTING_ACTIVE', true);
```

## 🎯 Common Tasks

### Add a New Voting Position
1. Login as admin
2. Go to Positions page
3. Fill in position name and description
4. Click "Add Position"

### Add Candidates to a Position
1. Login as admin
2. Go to Candidates page
3. Select position from dropdown
4. Enter candidate name and party
5. Click "Add Candidate"

### Register a New Voter
1. Go to Register page
2. Enter first name, last name, email
3. Enter valid Voter ID
4. Create username and password
5. Click Register

### Cast a Vote
1. Login as voter
2. Go to Vote page
3. Select one candidate per position
4. Check confirmation box
5. Click "Submit Vote"

### View Results
1. Go to Results page (available to both admin and voters)
2. See vote counts and percentages
3. Identify leading candidates

### Reset Admin Password
1. Stop Apache/MySQL
2. Manually update users table in database
3. Change admin password hash using online bcrypt tool

## 📊 Key Functions

### Authentication Functions (functions.php)
```php
is_logged_in()           // Check if logged in
is_admin()               // Check if admin user
is_voter()               // Check if voter user
require_login($role)     // Force login
```

### Data Functions (functions.php)
```php
get_all_positions()           // Get all positions
get_position_candidates()     // Get candidates for position
get_election_results()        // Get vote results
cast_vote()                   // Submit vote
get_total_votes_cast()        // Get vote count
get_total_registered_voters() // Get voter count
has_voted()                   // Check if voted
```

### Security Functions (functions.php)
```php
hash_password()         // Hash a password
verify_password()       // Verify password
sanitize()              // Sanitize input
escape_input()          // Escape for database
```

## 🚨 Troubleshooting Checklist

### System Won't Start
- [ ] XAMPP Apache is running
- [ ] XAMPP MySQL is running
- [ ] Project is in C:\xampp\htdocs\E-VOTE\

### Database Connection Error
- [ ] MySQL is running
- [ ] Database 'evoting_system' exists
- [ ] Check db_connect.php credentials
- [ ] Run install.php to create database

### Login Failed
- [ ] Check username (case-sensitive)
- [ ] Clear browser cache
- [ ] Database has been imported
- [ ] User exists in database

### Voting Issues
- [ ] Positions are created
- [ ] Candidates are assigned
- [ ] Voter hasn't already voted
- [ ] All required fields selected

### Database Not Created
- [ ] Run install.php
- [ ] Or use phpMyAdmin to import database.sql
- [ ] Verify all tables created

## 📱 Responsive Design

The system uses Bootstrap 5.3 and is responsive for:
- Desktop (1200px and up)
- Tablet (768px to 1199px)
- Mobile (below 768px)

All pages automatically adjust to screen size.

## 🔐 Security Features

- Bcrypt password hashing
- SQL prepared statements (no SQL injection)
- HTML escaping (no XSS attacks)
- Session-based authentication
- Role-based access control
- Vote anonymity
- Unique vote enforcement

## 💾 Backup & Restore

### Backup Database
1. Open phpMyAdmin
2. Select evoting_system database
3. Click Export
4. Save as SQL file

### Restore Database
1. Open phpMyAdmin
2. Click Import
3. Choose the SQL file
4. Click Go

## 🎨 Customization

### Change Site Title
Edit `includes/config.php`:
```php
define('SITE_TITLE', 'Your Election Name');
```

### Change Colors
Edit `css/style.css`:
```css
:root {
    --primary-color: #007bff;    /* Change this */
    --success-color: #28a745;
    /* etc */
}
```

### Change Default Credentials
Manually edit database or use admin panel to change password.

## 📞 Support Resources

- README.md - Complete documentation
- PROJECT_OVERVIEW.md - Detailed overview
- setup.php - Setup instructions
- getting-started.html - Quick start guide
- Code comments - Function documentation

## ✅ Pre-Deployment Checklist

- [ ] Database is working
- [ ] All tables created
- [ ] Admin can login
- [ ] Voter can register
- [ ] Voting interface works
- [ ] Results display correctly
- [ ] Responsive on mobile
- [ ] No console errors
- [ ] Default passwords changed
- [ ] Backup created

## 🎓 Learning Resources

The code includes examples of:
- PHP OOP principles
- Database design
- Authentication systems
- Bootstrap responsive design
- JavaScript event handling
- Form validation
- Security best practices

---

**Quick Start**: Visit `http://localhost/E-VOTE/install.php` to begin setup.

**Version**: 1.0  
**Last Updated**: December 2025
