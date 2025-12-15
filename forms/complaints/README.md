# LEYECO III Complaints System

A comprehensive complaint management system for LEYECO III (Leyte III Electric Cooperative) that allows customers to submit, track, and manage complaints.

## Features

- **Complaint Submission**: Submit complaints with detailed information including:

  - Complaint type (Billing, Service Quality, Power Outage, etc.)
  - Description and location details
  - Optional photo upload (max 5MB)
  - Interactive map for pinpointing exact location
  - Optional reporter contact information

- **Complaint Tracking**: Track complaint status using unique reference codes

  - Real-time status updates
  - Activity timeline
  - Location visualization on map

- **Statistics Dashboard**: View system-wide statistics
  - Total complaints
  - New complaints
  - Under investigation
  - Resolved complaints

## System Requirements

- PHP 7.4 or higher
- MySQL 5.7 or higher
- Apache/Nginx web server
- Modern web browser with JavaScript enabled

## Installation

### 1. Database Setup

Run the SQL schema to create required tables:

```bash
mysql -u root -p leyeco_db < sql/schema.sql
```

This will create three tables:

- `complaints` - Main complaints table
- `complaint_comments` - Activity timeline and comments
- `complaint_audit_logs` - Audit trail for all actions

### 2. Configuration

Edit `app/config.php` to match your database credentials:

```php
define('DB_HOST', 'localhost');
define('DB_NAME', 'leyeco_db');
define('DB_USER', 'root');
define('DB_PASS', '');
```

### 3. File Permissions

Ensure the uploads directory is writable:

```bash
chmod 755 public/assets/uploads
```

## Usage

### Accessing the System

Navigate to: `http://your-domain/forms/complaints_form.php`

This will redirect to the complaints system homepage.

### Submitting a Complaint

1. Click "Submit New Complaint" on the homepage
2. Fill out the required fields:
   - Complaint Type
   - Description
   - Municipality
   - Address
3. Optionally add:
   - Your name and contact information
   - Location on map
   - Photo evidence
4. Submit the form
5. Save the generated reference code

### Tracking a Complaint

1. Enter your reference code on the homepage
2. Click "View Complaint"
3. View detailed information including:
   - Current status
   - Activity timeline
   - Location (if provided)
   - Attached photo (if provided)

## Reference Code Format

Reference codes follow the format: `CLN{year}{month}{day}-{4 digit number}`

Example: `CLN20251211-0001`

## Complaint Statuses

- **NEW**: Newly submitted complaint
- **INVESTIGATING**: Under investigation by staff
- **IN_PROGRESS**: Being actively worked on
- **RESOLVED**: Issue has been resolved
- **CLOSED**: Complaint has been closed

## Complaint Types

- Billing Issues
- Service Quality
- Power Outage
- Personnel Conduct
- Equipment/Meter Issues
- Connection Issues
- Other Complaints

## Security Features

- CSRF token protection
- Input sanitization
- SQL injection prevention via prepared statements
- File upload validation
- Secure session management

## File Structure

```
complaints/
├── app/
│   ├── ComplaintController.php  # Main controller
│   ├── config.php               # Configuration
│   ├── db.php                   # Database connection
│   └── functions.php            # Helper functions
├── public/
│   ├── assets/
│   │   ├── uploads/             # Uploaded photos
│   │   └── images/              # System images
│   ├── homepage.php             # Landing page
│   ├── submit_complaint.php     # Submission form
│   ├── view_complaint.php       # View complaint details
│   └── homepage.css             # Stylesheet
└── sql/
    └── schema.sql               # Database schema
```

## Technologies Used

- **Backend**: PHP 7.4+
- **Database**: MySQL
- **Frontend**: HTML5, CSS3, JavaScript
- **Map Integration**: Leaflet.js
- **Styling**: Custom CSS with responsive design

## Support

For technical support or questions, contact LEYECO III:

- Phone: (053) 567-8000
- Website: [LEYECO III Official Site]

## License

© 2024 LEYECO III - Leyte III Electric Cooperative. All rights reserved.
