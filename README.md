<div align="center">

# <img src="assets/images/logoL3iii.webp" alt="LEYECO III Logo" height="50" style="vertical-align: middle; margin-bottom: 5px;"> **LEYECO III FORMS MANAGEMENT SYSTEM**

[![License: MIT](https://img.shields.io/badge/License-MIT-yellow.svg)](https://opensource.org/licenses/MIT)
[![PHP Version](https://img.shields.io/badge/PHP-8.2%2B-blue.svg)](https://php.net/)
[![Docker](https://img.shields.io/badge/Docker-✓-blue.svg)](https://www.docker.com/)
[![MySQL](https://img.shields.io/badge/MySQL-8.0+-blue.svg)](https://www.mysql.com/)

A modern web-based forms management system developed by Computer Science Seniors at Holy Cross College of Carigara Incorporated for LEYECO III (Leyte III Electric Cooperative), enabling efficient online submission and management of service requests, reconnections, complaints, and meter replacements.

</div>

## Project Overview

The LEYECO III Forms Management System simplifies the service request process for customers by providing a fully digital platform, eliminating the need for paper forms or office visits. Whether you need a new connection, service reconnection, to file a complaint, or request meter replacement, everything can be done conveniently from your phone or computer. All submissions are tracked automatically, ensuring transparency and real-time status updates.

> **Note**: The form submission functionalities are currently under development and will be implemented in upcoming releases.

## Key Features

### Public Access
- Submit service requests without login required
- Track requests using reference numbers
- Real-time status updates and processing timeline
- Auto-generated tracking numbers (Format: REF-YYYYMMDD-XXXX)
- Mobile-responsive design for all devices

### Form Types
- **Service Request Form** - New service connections and applications
- **Reconnection Form** - Electric service reconnection requests
- **Complaints Form** - Service issues and complaint reporting
- **Meter Replacement Form** - Electric meter replacement/upgrade requests

### System Features
- Modern, responsive homepage with form selection cards
- Smooth animations and hover effects
- Accessibility-first design (keyboard navigation, ARIA labels)
- Real-time statistics display
- Professional LEYECO III branding and color scheme
- Complete audit trail for all submissions

## Quick Start

### <img src="https://skillicons.dev/icons?i=docker" height="30" style="vertical-align: middle;"> With Docker (Recommended)
```bash
git clone https://github.com/frostjade71/leyeco_form_sys.git
cd leyeco_form_sys
docker-compose up -d
```

Access the application:
- **Web Interface**: http://localhost:8080
- **phpMyAdmin**: http://localhost:8081

### Default Database Credentials
- **Database Name**: leyeco_forms_db
- **Username**: leyeco_user
- **Password**: leyeco_pass
- **Root Password**: root

> **Security Note**: Change default passwords immediately in production environments!

## Technology Stack

<p align="center">
  <a href="https://skillicons.dev">
    <img src="https://skillicons.dev/icons?i=html,css,js,php,mysql,docker,vscode" />
  </a>
</p>

## Security Features

- **Password Hashing**: bcrypt encryption for all passwords
- **Prepared Statements**: PDO with parameterized queries
- **Input Sanitization**: All user inputs are sanitized and validated
- **Session Management**: Secure session handling with HTTP-only cookies
- **CSRF Protection**: Token-based CSRF prevention
- **SQL Injection Prevention**: Prepared statements throughout the application

## Development

### Docker Services
The application uses three Docker services:
- **web**: PHP 8.2 with Apache (Port 8080)
- **db**: MySQL 8.0 (Port 3306)
- **phpmyadmin**: Database management interface (Port 8081)

## License

This project is licensed under the MIT License. See the [LICENSE](LICENSE) file for details.

---

## **Credits** <img src="assets/images/HCCCI.webp" alt="HCCCI Logo" height="24" style="vertical-align: middle;"> HCCCI Computer Science Seniors

> #### Documentation & QA/Testers:
- Sophia Caridad
- Loren Mae Pascual
- Fauna Dea Opetina
- Agnes Osip
- Zxyrah Mae Indemne

> #### **Developer**
- **Jaderby Peñaranda**

  [![Website](https://img.shields.io/badge/🌏-jaderbypenaranda.link-1e88e5)](https://jaderbypenaranda.link/) [![Email](https://img.shields.io/badge/📩-Contact-4caf50)](mailto:jaderbypenaranda@gmail.com)

---

<div align="left">
<span><b>Built for</b></span>
  <img src="assets/images/logo_leyeco3.webp" alt="LEYECO III Logo" height="40" style="vertical-align: middle; margin-right: 12px;">
  <span><i>Lighting Houses, Lighting Homes, Lighting Hopes</i></span>
</div>

---

**Version**: 1.0.0  
**Last Updated**: December 10, 2025
