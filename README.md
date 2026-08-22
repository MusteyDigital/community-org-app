# Community Org

A multi-tenant platform for churches, mosques, and community groups to manage members, events, announcements, and contributions — with a public page for each organization that visitors can check without logging in.

Built as a submission for brief **SD-15 (Community Org App)**.

## Features

- **Multi-tenant organizations** — anyone can register their organization; a super admin approves new organizations before they go live
- **Membership management** — join requests, approval workflow, admin/member roles per organization, one organization per user
- **Members** — full CRUD, scoped to each organization
- **Events** — full CRUD with date/time/location, shown on the public organization page and admin dashboard
- **Announcements** — full CRUD with pinning and a general/burial notice type; posting one emails every approved member of that organization, with SMS notifications also sent via Twilio (custom message content requires a paid Twilio account — see note below)
- **Contributions/Donations** — admins record contributions on behalf of members, categorized by type (general, tithe, zakat, building fund, charity); members can also self-serve pay via Paystack checkout, with automatic server-side verification and recording
- **Admin dashboard** — organization admins see extra stats (pending member requests, contributions this month/this year) that regular members don't see
- **Authentication** — email/password registration and login via Laravel Breeze, plus one-click Google Sign-In (auto-creates or links accounts by email)
- **Public pages** — a marketing homepage, an organization directory, and a dedicated public page per organization (/org/{slug}) showing upcoming events and pinned announcements, no login required
- **Role-based access** — organization admins manage their org's data; a platform-level super admin approves new organizations
- **Custom design system** — a distinct teal/gold visual identity (Sora, Inter, IBM Plex Mono) instead of default Tailwind styling

## Tech Stack

- **Backend:** Laravel 12, PHP 8.2
- **Frontend:** Blade, Tailwind CSS, Vite
- **Auth:** Laravel Breeze + Laravel Socialite (Google OAuth)
- **Database:** MySQL (local), PostgreSQL (production)
- **Mail:** Laravel Notifications (SMTP via Mailtrap)
- **SMS:** Twilio
- **Payments:** Paystack

## Getting Started

Clone the repo, then run composer install, npm install, copy .env.example to .env, and run php artisan key:generate.

Set your database credentials in .env, then add credentials for the third-party services you want to use: GOOGLE_CLIENT_ID, GOOGLE_CLIENT_SECRET, GOOGLE_REDIRECT_URI, TWILIO_SID, TWILIO_AUTH_TOKEN, TWILIO_FROM_NUMBER, PAYSTACK_PUBLIC_KEY, PAYSTACK_SECRET_KEY.

Then run php artisan migrate, npm run build, and php artisan serve. Visit http://127.0.0.1:8000.

## Known Limitations

SMS on Twilio trial accounts: Twilio trial accounts can only send a small set of predefined message templates and cannot send custom SMS body content. The SMS integration is fully implemented and tested for connectivity/delivery, but sending the actual announcement text requires upgrading the Twilio account from trial to a paid tier. No code changes are needed once upgraded.

## Project Structure Highlights

app/Models/Organization.php — multi-tenant core, auto-generates a unique slug on creation. app/Http/Controllers/PublicOrganizationController.php — public-facing landing page, directory, and per-org pages. app/Http/Controllers/Auth/GoogleController.php — Google OAuth redirect/callback and account linking. app/Http/Controllers/ContributionController.php — contribution CRUD and admin-recorded donations. app/Http/Controllers/PaystackController.php — self-service contribution payments via Paystack. app/Services/TwilioSmsService.php — SMS sending wrapper around the Twilio SDK. app/Notifications/AnnouncementPosted.php — emails approved members when an announcement is posted. app/Http/Middleware/EnsureUserIsSuperAdmin.php — gates the platform-level organization approval panel.

## Author

Mustapha Adamu (Mustey Digital Academy — https://github.com/MusteyDigital)
