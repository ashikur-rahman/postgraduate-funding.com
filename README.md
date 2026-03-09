# Postgraduate Funding Platform (AGO CMS)

A full-stack, subscription-based funding discovery platform built to
power **The Alternative Guide to Postgraduate Funding Online (AGO)**
used by postgraduate students across UK universities.

The platform enables students and universities to access funding
databases, manage subscriptions, and search scholarship opportunities
through a secure web system.

This entire system --- including CMS, architecture, authentication
system, subscription platform, and funding search engine --- was
designed and implemented independently using **CodeIgniter 4, MySQL, and
PayPal integration**.

------------------------------------------------------------------------

# Core Engineering Highlights

• Full CMS platform built from scratch\
• Multi-authentication system (Email / PIN / IP)\
• Subscription lifecycle management\
• Dynamic funding search engine\
• Admin dashboard with analytics\
• PayPal payment integration

------------------------------------------------------------------------

# System Architecture

Client Browser\
→ Frontend UI (Bootstrap + jQuery + AJAX)\
→ CodeIgniter 4 MVC Framework\
→ Controllers / Models / Views\
→ MySQL Database\
→ External API Integrations (PayPal)

------------------------------------------------------------------------

# Key Modules

### Funding Search Engine

Multi-criteria scholarship discovery engine allowing users to filter
funding opportunities by:

-   Age eligibility
-   Nationality
-   Subject area
-   Mode of study
-   Degree type
-   Location
-   Gender
-   Grant type

### Personal Grants Manager

Users can save funding opportunities with duplicate detection and
personal tracking.

### Subscription Management

Supports:

-   Multiple subscription plans
-   Duration-based pricing
-   PayPal integration
-   Automatic access control

### Institutional Access

Universities can provide access via:

-   Registered IP ranges
-   Email domain authentication
-   Institutional PIN access

------------------------------------------------------------------------
#System Architecture

       
       <img width="684" height="612" alt="image" src="https://github.com/user-attachments/assets/cff48582-d632-4783-97e7-297ebf977cc0" />

------------------------------------------------------------------------


# Admin CMS Modules

-   Content Manager
-   Menu Manager
-   Cover Image Manager
-   Resource Manager
-   User Manager
-   Email Manager
-   PIN Manager
-   IP Manager
-   Subscription Manager
-   Tracker Management

------------------------------------------------------------------------

# Database Core Tables

users\
products\
subscribed_users\
qtmso_cd\
personal_grant_management\
user_login_tracking\
university_management\
university_ip_management\
save_funding_search

------------------------------------------------------------------------

# Security Features

• Role-based access control\
• Session authentication\
• Campus IP verification\
• Duplicate entry prevention\
• Server-side validation

------------------------------------------------------------------------

# Performance Optimizations

• AJAX powered search results\
• DataTables server-side processing\
• Optimised database queries\
• Modular MVC architecture

------------------------------------------------------------------------

# DevOps Deployment

Typical deployment stack:

Client\
→ CDN / Cloudflare\
→ Nginx\
→ PHP-FPM\
→ CodeIgniter Application\
→ MySQL Database

------------------------------------------------------------------------

# Installation

Clone repository

git clone https://github.com/yourusername/postgraduate-funding-cms.git

Install dependencies

composer install

Configure environment

cp env .env

Run server

php spark serve

------------------------------------------------------------------------

# Author

**Md. Ashikur Rahman**\
Senior Software Engineer\
Senior Officer (ICT), Agrani Bank Limited

Specializations: - PHP (CodeIgniter / Laravel) - Java Spring Boot - REST
API Engineering - Database Architecture - DevOps Engineering
