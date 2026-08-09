# Community Org

A multi-tenant platform for churches, mosques, and community groups to manage members, events, and announcements — with a public page for each organization that visitors can check without logging in.

Built as a submission for brief **SD-15 (Community Org App)**.

## Features

- **Multi-tenant organizations** — anyone can register their organization; a super admin approves new organizations before they go live
- **Membership management** — join requests, approval workflow, admin/member roles per organization
- **Members** — full CRUD, scoped to each organization
- **Events** — full CRUD with date/time/location, shown on the public organization page
- **Announcements** — full CRUD with pinning and a general/burial notice type; posting one emails every approved member of that organization
- **Public pages** — a marketing homepage, an organization directory, and a dedicated public page per organization (/org/{slug}) showing upcoming events and pinned announcements, no login required
- **Role-based access** — organization admins manage their org's data; a platform-level super admin approves new organizations
- **Custom design system** — a distinct teal/gold visual identity (Sora, Inter, IBM Plex Mono) instead of default Tailwind styling

## Tech Stack

- **Backend:** Laravel 12, PHP 8.2
- **Frontend:** Blade, Tailwind CSS, Vite
- **Auth:** Laravel Breeze
- **Database:** MySQL
- **Mail:** Laravel Notifications (SMTP via Mailtrap for local testing)

## Getting Started

```bash
git clone <repo-url>
cd community-org-app
composer install
npm install
cp .env.example .env
php artisan key:generate
```

Set your database credentials in `.env`, then:

```bash
php artisan migrate
npm run build
php artisan serve
```

Visit http://127.0.0.1:8000.

## Project Structure Highlights

- `app/Models/Organization.php` — multi-tenant core, auto-generates a unique slug on creation
- `app/Http/Controllers/PublicOrganizationController.php` — public-facing landing page, directory, and per-org pages
- `app/Notifications/AnnouncementPosted.php` — emails approved members when an announcement is posted
- `app/Http/Middleware/EnsureUserIsSuperAdmin.php` — gates the platform-level organization approval panel

## Author

Mustapha Adamu ([Mustey Digital Academy](https://github.com/MusteyDigital))
