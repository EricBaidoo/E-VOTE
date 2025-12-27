# E-VOTING SYSTEM

A complete web-based voting system built with PHP, Bootstrap, HTML, CSS, and JavaScript. This build uses a JSON data store (no MySQL required).

## Features

- **User Authentication**: Simple Name + PIN login for admins and voters
- **Admin Dashboard**: Manage positions and view voter participation
- **Online Voting**: Simple and intuitive voting interface
- **Real-time Results**: View election results as votes are cast
- **Voter Management**: Track voter participation and turnout
- **Responsive Design**: Works perfectly on desktop and mobile devices
- **Security**: Session-based authentication and input sanitization

## System Requirements

- **Server**: Apache or Nginx (shared hosting-friendly)
- **PHP**: Version 7.4 or higher
- **Data Store**: JSON files in `data/`
- **Browser**: Modern browser with JavaScript enabled

## Installation

### Step 1: Extract Files
Upload the `E-VOTE` folder to your hosting root (e.g., `public_html/` on Hostinger).

### Step 2: Verify JSON Data
- Ensure the `data/` folder contains:
	- `admins.json` — Admin accounts (id, name, pin)
	- `voters.json` — Voter list (id, name, pin)
	- `aspirants.json` — Candidates (id, name, pin, position, image)
	- `positions_config.json` — Seat counts per position
	- `votes.json` — Will be created automatically when voting starts

### Step 3: Start Application
- Ensure your hosting web server is running (MySQL not required)
- Navigate to your domain (e.g., `https://your-domain.com/`)

## Default Login Credentials

### Admin Account
- **Name**: Admin User
- **PIN**: admin123

### Sample Voter Account
- **Name**: Example Voter Name (see `data/voters.json`)
- **PIN**: pinXXXX (from `data/voters.json`)
- **ID**: numeric/string id from `data/voters.json`

## File Structure

```
E-VOTE/
├── includes/
│   ├── json_utils.php      # JSON helpers (load/save, tally, grouping)
│   ├── config.php          # Configuration settings
│   └── functions.php       # Helper functions
├── admin/
│   ├── dashboard.php       # Admin dashboard
│   ├── positions.php       # Manage positions
│   ├── candidates.php      # Manage candidates
│   ├── voters.php          # Manage voters
│   ├── results.php         # View results
│   └── profile.php         # Admin profile
├── voter/
│   ├── dashboard.php       # Voter dashboard
│   ├── vote.php            # Voting interface
│   ├── results.php         # View results
│   ├── vote_success.php    # Vote confirmation
│   └── profile.php         # Voter profile
├── css/
│   └── style.css           # Main stylesheet
├── js/
│   └── voting.js           # JavaScript functions
├── index.php               # Home page
├── login.php               # Login page
├── register.php            # Redirects to login (registration removed)
├── logout.php              # Logout handler
├── setup.php               # Setup guide (JSON)
└── data/                   # JSON data store
```

## Usage

### For Administrators:
1. Login with admin Name + PIN
2. Go to Dashboard to view stats
3. Manage seat counts in `Admin > Positions`
4. Candidates and positions are read from `data/aspirants.json`
5. View voter list and results

### For Voters:
1. Login with your Name + PIN (from `data/voters.json`)
2. Go to Vote page to cast your vote
3. Select candidates per position based on seat limits
4. Confirm and submit your vote
5. View election results

## Security Features

- Session-based authentication
- User role-based access control
- Anonymous vote recording

## Customization

### Manage Data:
Edit JSON files under `data/` to modify admins, voters, and aspirants. Adjust seat counts in `data/positions_config.json`.

### Change Site Title:
Edit `includes/config.php`:
```php
define('SITE_TITLE', 'E-Voting System');
```

### Modify Positions and Candidates:
Use the admin panel to add/edit positions and candidates.

## Troubleshooting

### Data File Error
- Ensure JSON files exist in `data/`
- Validate JSON format if you manually edit files

### Login Failed
- Check username and password
-- Ensure your Name + PIN exist in `voters.json`
- Clear browser cache and cookies

### Voting Not Working
- Ensure voter hasn't already voted
- Check that positions and candidates exist in `aspirants.json`

## Future Enhancements

- Email verification for voters
- Two-factor authentication
- Advanced reporting and analytics
- Vote encryption/blockchain integration
- Multi-language support
- Online audit log

## License

This project is provided as-is for educational and development purposes.

## Support

For issues or questions, refer to the setup.php file for detailed setup instructions.

---

**Version**: 1.1 (JSON-only)  
**Last Updated**: December 2025  
**Developer**: E-Voting System Team
