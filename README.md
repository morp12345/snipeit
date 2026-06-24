# ProcureNova

**ProcureNova** is a web-based asset and procurement management system for IT operations — track who has which device, when it was purchased and how it depreciates, manage software licenses, suppliers, and consumables.

It is built on [Laravel](http://laravel.com) and runs on any web server (Mac OS X, Linux, or Windows) accessed through a web browser.

> [!TIP]
> __This is web-based software.__ There is no executable to install — it runs on a web server and is accessed through a web browser.

-----

### Table of Contents
* [Installation](#installation)
* [OrangeHRM SSO Integration](#orangehrm-sso-integration)
* [Security](#security)

-----

### Installation

ProcureNova is a standard Laravel application. To set it up locally:

```bash
# Install PHP dependencies
composer install

# Install and build frontend assets
npm install
npm run prod

# Copy and configure your environment
cp .env.example .env
php artisan key:generate

# Run database migrations and seeders
php artisan migrate
php artisan db:seed

# Clear caches after config/route changes
php artisan optimize:clear
```

Configure your database and application settings in `.env` before running migrations.

-----
### OrangeHRM SSO Integration

ProcureNova adds a **"Login with OrangeHRM account"** button to the login page.
Users sign in with their OrangeHRM credentials, and their OrangeHRM role
determines their ProcureNova access level.

#### Role mapping

| OrangeHRM role | ProcureNova access |
| -------------- | ------------------ |
| `Admin`        | Superuser          |
| `ESS`          | Normal user        |

The role is re-synced on **every** login, so OrangeHRM remains the source of truth.

#### How it works (hybrid OAuth2 + DB)

This is a **hybrid** integration, not pure SSO:

* **Authentication** uses the genuine OAuth2 `authorization_code` flow. The user
  logs in at OrangeHRM and authorizes the client; their password never reaches
  ProcureNova.
* **Identity/role resolution** reads the OrangeHRM database directly. OrangeHRM
  exposes no `userinfo`/`/users/me` endpoint and its access tokens are opaque
  (Defuse-encrypted, not JWTs), so there is no API way to learn who a token
  belongs to. ProcureNova decrypts the token to recover its identifier, then maps
  it through `ohrm_oauth2_access_token` → `ohrm_user` → `ohrm_user_role`.

Because of this, ProcureNova must be able to reach the OrangeHRM database, and the
token encryption key must be shared.

#### Flow

```
Login page  ──click──▶  /orangehrm  ──redirect──▶  OrangeHRM /oauth2/authorize
                                                          │ (user logs in + consents)
ProcureNova  ◀──auth code──  /orangehrm/callback  ◀──────┘
        │
        ├─ exchange code for opaque access token (OAuth2)
        ├─ decrypt token (Defuse) ──▶ token identifier (jti)
        ├─ jti ─▶ ohrm_oauth2_access_token ─▶ user_id
        ├─ user_id ─▶ ohrm_user / ohrm_user_role / hs_hr_employee
        └─ create/sync ProcureNova user, map role, log in
```

#### Setup

1. In OrangeHRM, go to **Admin > Configuration > Register OAuth Client** and
   create a client with redirect URI `APP_URL` + `/orangehrm/callback`
   (e.g. `http://127.0.0.1:8000/orangehrm/callback`).
2. Copy OrangeHRM's `oauth.token_encryption_key` into
   `ORANGEHRM_TOKEN_ENCRYPTION_KEY`. Retrieve it from the OrangeHRM database with
   this **read-only** query (replace the db name if yours differs):

   ```sql
   SELECT value FROM hs_hr_config WHERE name = 'oauth.token_encryption_key';
   ```

   > This is a `SELECT` only — no schema changes or writes to OrangeHRM are
   > required for this integration.
3. Configure the OrangeHRM settings in `.env` (see `.env.example` for the full
   list and inline notes):

   ```dotenv
   ORANGEHRM_BASE_URL=http://127.0.0.1:8081/web/index.php
   ORANGEHRM_CLIENT_ID=your-client-id
   ORANGEHRM_CLIENT_SECRET=your-client-secret
   ORANGEHRM_TOKEN_ENCRYPTION_KEY=your-token-encryption-key
   ORANGEHRM_DB_DATABASE=orangehrm
   ORANGEHRM_DB_HOST=127.0.0.1
   ORANGEHRM_DB_PORT=3306
   ORANGEHRM_DB_USERNAME=your-db-user
   ORANGEHRM_DB_PASSWORD=your-db-password
   ```
4. Clear config so the changes take effect: `php artisan config:clear`.

#### Caveats

* **Tightly coupled to OrangeHRM internals.** It relies on OrangeHRM's database
  schema and token encryption. An OrangeHRM major upgrade may require updating
  the integration.
* **Keep the encryption key in sync.** If OrangeHRM regenerates its
  `oauth.token_encryption_key`, update `ORANGEHRM_TOKEN_ENCRYPTION_KEY` to match
  or logins will fail.
* The relevant code lives in `app/Services/OrangeHRMProvider.php`,
  `app/Http/Controllers/OrangeHRMAuthController.php`, the `orangehrm` connection
  in `config/database.php`, and the `orangehrm` block in `config/services.php`.

-----

### Security

> [!IMPORTANT]
> To report a security vulnerability, please contact the ProcureNova maintainers
> directly rather than opening a public issue.
