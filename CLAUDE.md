# CLAUDE.md

This file provides guidance to Claude Code (claude.ai/code) when working with code in this repository.

## What this project is

LCNL is a community organisation website built on **CodeIgniter 4** (PHP 8.1+). It handles public pages (events, committees, FAQs, contact), a member self-service portal, Stripe-powered life membership upgrades, and an admin back-office for membership management and content.

This is **not** the Maharajah project (also at `/var/www/html/maharajah`). Keep context for the two projects separate.

## Common commands

```bash
# Run the dev server (local vhost: http://lcnl.local/)
php spark serve

# Database migrations
php spark migrate
php spark migrate:rollback

# Spark CLI commands (app-specific)
php spark emails:send              # send queued emails (Fasthosts-throttled)
php spark emails:send --test       # dry-run, redirects all email to sunnychotai@me.com
php spark emails:requeue           # requeue failed emails
php spark members:activate         # bulk-activate pending members
php spark members:import           # import members from staging
php spark cron:purge               # purge old cron logs

# Tests
composer test                      # or: ./vendor/bin/phpunit
./vendor/bin/phpunit --filter TestName   # single test
```

## Architecture

### Two independent auth systems

| System | Session key | Filter | Routes |
|--------|-------------|--------|--------|
| Admin | `isAdminLoggedIn`, `admin_role` | `authAdmin` | `/auth/*`, `/admin/*` |
| Member | `isMemberLoggedIn` | `authMember` | `/membership/*`, `/account/*` |

Admin roles: `ADMIN`, `MEMBERSHIP`, `EVENTS`, `WEBSITE`. The `authAdmin` filter accepts role arguments, e.g. `filter' => 'authAdmin:ADMIN,MEMBERSHIP'`.

### Route groups

- **Public** — home, events, FAQs, committee pages, contact, membership registration
- **`/account/*`** — member self-service (dashboard, profile, family, Stripe upgrade); requires `authMember`
- **`/admin/system/*`** — system dashboard, email queue, cron logs; requires `authAdmin`
- **`/admin/membership/*`** — member CRUD, DataTables data endpoints, reports, exports; requires `authAdmin:ADMIN,MEMBERSHIP`
- **`/admin/content/*`** — events, FAQs, committee CRUD; requires `authAdmin:ADMIN,EVENTS,WEBSITE`

Auto-routing is **disabled** (`$routes->setAutoRoute(false)`). Every route must be explicit.

### Membership lifecycle

`pending` → email verified → admin activates → `active` → optional Stripe checkout → Life membership

- Registration creates a `members` row + a `memberships` row (type `Standard`).
- Activation is done via `MemberService::activate()` which also clears disabled fields and writes an audit log entry.
- Disabling requires a reason from `Config\MemberStatus::$disableReasons` (deceased, moved_house, manual).
- Every status change must go through `MemberService` — never update the `members` table directly for status changes.

### Email queue

Emails are never sent inline. They are inserted into the `email_queue` table and dispatched by the `emails:send` Spark command (run via server cron). The command enforces hard caps (5/run, 50/10 min, 950/day) for Fasthosts SMTP limits and auto-pauses on 5 consecutive failures. Config lives in `Config\EmailQueue` and is overridable via `.env` (`queue.email.*`).

Email bodies support `{{current_year}}` and `{{member_name}}` placeholders. Emails of type `member_activation` also inject `{{activation_link}}` at send time (generates a fresh password-reset token).

### Stripe

`Config\Stripe` reads keys from `.env` (`stripe.*`). `StripeService` wraps the SDK client. Webhooks are verified and handled in `StripeWebhookController`. Life membership upgrade flow lives in `Account\MembershipUpgradeController`.

### Audit logging

`BaseController::auditMemberChange()` writes to `member_audit_log`. All member status transitions via `MemberService` write audit rows automatically.

### Custom config classes

Several app-specific configs extend `BaseConfig` and read from `.env`:
- `Config\Stripe` — Stripe keys and price IDs
- `Config\EmailQueue` — send throttle settings
- `Config\MemberStatus` — disable reason labels/icons
- `Config\Family` — relation types and gender options
- `Config\LiveStreams` — live stream URLs
- `Config\R2` — Cloudflare R2 credentials and bucket for gallery images (`r2.*`)
- `Config\Gallery` — gallery page sizes, image variants, default photo credit

### Photo gallery

Public at `/gallery`, managed at `/admin/gallery` (ADMIN only). Images live in a public Cloudflare R2 bucket on a custom domain (`photos.lcnl.co.uk`; `photos-dev.` locally) and are never served from Hostinger. The browser hashes each original (dedup and resume), resizes it to 1600px and posts it alone; `GalleryService::addPhoto()` re-encodes with GD into `-web.webp` and `-thumb.webp` and PUTs both via `App\Libraries\R2Client`, a hand-written SigV4 client (no AWS SDK). Originals are not kept. The LCNL logo is stamped top-right on the web variant only (`Config\Gallery::$watermark*`); photos uploaded before that have `watermarked = 0` and are marked later from the album admin page via `GalleryService::watermarkExisting()`, which saves them under a new key because the old URL is cached immutably. Always change photos through `GalleryService` so R2, `photo_count` and the cover stay in step. Tests swap in `Tests\Support\Libraries\FakeR2Client` via `Services::injectMock('r2', ...)`.

### Views / layout

Views use a shared layout at `app/Views/layout/main.php` (includes `_header.php`, `_navbar.php`, `_footer.php`). Email templates share `app/Views/emails/layout.php`. Admin views are under `app/Views/admin/`.
