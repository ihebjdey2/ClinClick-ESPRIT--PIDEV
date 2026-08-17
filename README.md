<div align="center">

# CliniClic

### Clinic Management Platform · Symfony · PHP · MySQL

A web application for managing clinic workflows including appointments, doctor availability, consultations, prescriptions, users, inventory and events.

**Academic Project — ESPRIT**

</div>

---

## Overview

CliniClic is a clinic management application built with **Symfony 7.4**.

The project focuses on real clinic workflows rather than simple CRUD operations. It includes role-based access control, appointment scheduling rules, medical consultations, prescriptions, inventory management and administrative tools.

The application supports four user roles:

- Administrator
- Doctor
- Receptionist
- Patient

> All accounts and data provided with this project are fictional and intended for demonstration purposes only.

---

## Screenshots

| Home Page | Admin Dashboard |
|---|---|
| ![CliniClic Home](docs/screenshots/home.png) | ![Admin Dashboard](docs/screenshots/admin-dashboard.png) |

The application also includes a responsive mobile layout.

[View mobile screenshot](docs/screenshots/home-mobile.png)

---

## Main Features

### Authentication & Users

- User registration and login
- Role-based access control
- Secure profile management
- CSRF-protected logout
- Login rate limiting
- Administrator user management

---

### Appointments

- Create and manage appointments
- Doctor availability schedules
- Appointment status management
- Filtering, sorting and pagination
- Prevention of appointments in the past
- Prevention of overlapping doctor appointments
- Prevention of overlapping patient appointments
- Availability validation before booking

Supported statuses:

```text
pending
confirmed
completed
cancelled
no_show
```

---

### Consultations & Prescriptions

Doctors can:

- create consultations;
- record medical information;
- create prescriptions;
- complete assigned appointments.

Access to medical information is restricted to the appropriate doctor and patient.

---

### Doctor Availability

Doctors can define their weekly availability.

The scheduling system checks availability before accepting an appointment.

```text
Doctor Availability
        ↓
Appointment Request
        ↓
Conflict Validation
        ↓
Available?
     /       \
   Yes        No
    ↓          ↓
 Booking     Rejected
```

---

### Inventory

The application includes medical inventory management with:

- products;
- categories;
- quantities;
- stock alerts.

---

### Events

Users can interact with clinic events through:

- event listings;
- participation;
- capacity limits;
- unique participation rules;
- validated event images.

---

### Complaints

The application includes:

- complaint submission;
- administrative responses;
- PDF export.

---

## Roles & Permissions

| Feature | Admin | Doctor | Receptionist | Patient |
|---|:---:|:---:|:---:|:---:|
| Manage users | ✓ | — | — | — |
| View appointments | All | Assigned | All | Own |
| Manage appointments | ✓ | Limited | ✓ | Own |
| Manage doctor availability | All | Own | View | — |
| Create consultations | — | Assigned patients | — | — |
| View medical records | — | Assigned patients | — | Own |
| Manage inventory | ✓ | — | Limited | — |
| Manage events | ✓ | — | Limited | — |
| Edit own profile | ✓ | ✓ | ✓ | ✓ |

Authorization is enforced through Symfony Security, controllers and voters rather than only hiding interface elements.

---

## Tech Stack

<p>
  <img src="https://cdn.jsdelivr.net/gh/devicons/devicon@latest/icons/php/php-original.svg" width="42" height="42" alt="PHP" />
  &nbsp;&nbsp;
  <img src="https://cdn.jsdelivr.net/gh/devicons/devicon@latest/icons/symfony/symfony-original.svg" width="42" height="42" alt="Symfony" />
  &nbsp;&nbsp;
  <img src="https://cdn.jsdelivr.net/gh/devicons/devicon@latest/icons/mysql/mysql-original.svg" width="42" height="42" alt="MySQL" />
  &nbsp;&nbsp;
  <img src="https://cdn.jsdelivr.net/gh/devicons/devicon@latest/icons/bootstrap/bootstrap-original.svg" width="42" height="42" alt="Bootstrap" />
</p>

### Backend

- PHP 8.2+
- Symfony 7.4 LTS
- Doctrine ORM 3.6
- Doctrine DBAL 4.4
- Symfony Forms
- Symfony Validator
- Symfony Security

### Frontend

- Twig
- Bootstrap 5
- Bootstrap Icons
- Custom CSS

### Database

- MySQL
- MariaDB

### Additional Libraries

- KnpPaginatorBundle
- Dompdf

---

## Architecture

The application follows a traditional layered Symfony architecture.

```text
HTTP Request
     │
     ▼
Controller
     │
     ├── Voter
     │     └── Authorization
     │
     ├── Form
     │     └── Validation
     │
     ├── Service
     │     └── Business Logic
     │
     └── Repository
           │
           ▼
         Entity
           │
           ▼
        Database
```

Controllers remain focused on HTTP concerns while business rules are handled by dedicated services.

---

## Main Services

### `AppointmentService`

Handles:

- appointment validation;
- availability;
- scheduling conflicts;
- status transitions;
- concurrency protection.

### `DoctorAvailabilityService`

Handles weekly doctor availability and schedule consistency.

### `ConsultationService`

Handles consultation creation and appointment completion.

### `DashboardService`

Provides role-specific dashboard information.

### `EventParticipationService`

Handles event capacity and unique participation.

### `EventImageUploader`

Validates and stores uploaded event images.

---

## Project Structure

```text
src/
├── Controller/        HTTP controllers
├── Entity/            Doctrine entities
├── Repository/        Database queries
├── Service/           Business logic
├── Security/Voter/    Object-level authorization
├── Form/              Symfony forms
└── Enum/              Controlled application states

templates/             Twig views
public/assets/css/     Application styles
migrations/            Database migrations
docs/                  Architecture, audit and screenshots
```

---

## Security

The project includes several application-level security measures:

- Symfony password hashing
- CSRF protection
- role-based authorization
- ownership-based voters
- login rate limiting
- parameterized Doctrine queries
- Twig auto-escaping
- protected medical responses
- validated image uploads
- local secrets stored outside version control

Medical and appointment-related responses use restricted cache policies where appropriate.

---

## Appointment Rules

An appointment must satisfy the following rules:

1. The requested date must be in the future.
2. The doctor must be available during the requested time.
3. The doctor cannot have another overlapping appointment.
4. The patient cannot have another overlapping appointment.
5. Cancelled appointments release their time slot.
6. Only the assigned doctor can create the associated consultation.
7. Patients can only access their own medical information.

---

## Requirements

- PHP `>= 8.2`
- Composer 2
- MySQL or MariaDB
- Git

Recommended PHP extensions include:

```text
ctype
iconv
pdo_mysql
dom
json
tokenizer
zip
intl
```

---

## Installation

### 1. Clone the repository

```bash
git clone https://github.com/ihebjdey2/PFA-3eme-ESPRIT-Projet-integration-web-mobile-desktop-PIDEV.git cliniclic
cd cliniclic
```

---

### 2. Install dependencies

```bash
composer install
```

---

### 3. Create local environment configuration

Linux / macOS:

```bash
cp .env.example .env.local
```

Windows PowerShell:

```powershell
Copy-Item .env.example .env.local
```

Configure at least:

```dotenv
APP_ENV=dev
APP_DEBUG=1
APP_SECRET=your_local_secret

DATABASE_URL="mysql://clinic_user:password@127.0.0.1:3306/cliniclic?serverVersion=10.6.0-MariaDB&charset=utf8mb4"
```

---

### 4. Create the database

```bash
php bin/console doctrine:database:create --if-not-exists
```

---

### 5. Run migrations

```bash
php bin/console doctrine:migrations:migrate --no-interaction
```

---

### 6. Load demo users

```bash
php bin/console app:demo:users
```

---

## Demo Accounts

| Role | Email |
|---|---|
| Administrator | `admin@cliniclic.test` |
| Doctor | `doctor@cliniclic.test` |
| Receptionist | `reception@cliniclic.test` |
| Patient | `patient@cliniclic.test` |

Demo password:

```text
ClinicDemo!2026
```

These credentials are intended only for local development and demonstration.

---

## Run Locally

Using Symfony CLI:

```bash
symfony server:start
```

Or using PHP's built-in server:

```bash
php -S 127.0.0.1:8000 -t public public/router.php
```

Then open:

```text
http://127.0.0.1:8000/home
```

---

## Main Routes

| Area | Route |
|---|---|
| Home | `/home` |
| Login | `/login` |
| Registration | `/register` |
| Dashboard | `/` |
| Appointments | `/rendez-vous/` |
| Doctor Availability | `/doctor/availability` |
| Consultations | `/medical/consultations/` |
| Events | `/evenement` |
| Complaints | `/addReclamation` |
| User Management | `/admin/users` |
| Inventory | `/stock` |

---

## Maintenance Commands

Useful Symfony checks:

```bash
php bin/console lint:container
php bin/console lint:twig templates
php bin/console lint:yaml config
php bin/console doctrine:schema:validate
php bin/console doctrine:migrations:up-to-date
composer validate --strict
composer audit
```

---

## Author

**Iheb Jdey**

Software Engineer · Full-Stack · Mobile · Applied AI

[Portfolio](https://ihebjdey.tn) ·
[LinkedIn](https://www.linkedin.com/in/jdey-iheb) ·
[GitHub](https://github.com/ihebjdey2)
