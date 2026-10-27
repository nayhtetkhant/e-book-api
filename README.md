# E-Book API — Digital Commerce & Asset Delivery

A Laravel REST API for a digital product storefront, covering product management, order creation, Stripe Checkout, payment records, and temporary AWS S3 upload and download URLs.

This backend forms one full-stack portfolio project with a Next.js customer storefront and administrator dashboard.

- **Backend:** [e-book-api](https://github.com/nayhtetkhant/e-book-api)
- **Frontend:** [e-book-web](https://github.com/nayhtetkhant/e-book-web)

## Technology Stack

| Component | Technology |
| --- | --- |
| Backend framework | Laravel 13 |
| Language | PHP |
| Authentication | Laravel Sanctum bearer tokens |
| Database | MySQL in the example environment, Eloquent ORM |
| Payment integration | Stripe Checkout through Laravel Cashier 16 |
| Storage | AWS S3 through Laravel Filesystem and Flysystem |
| Companion frontend | Next.js, React, TypeScript, Tailwind CSS |
| Test tooling | Pest; existing tests are framework examples |

## Features

### Authentication and Product Management

- Separate customer and administrator login endpoints.
- Sanctum authentication for protected API routes.
- Product listing and individual product retrieval.
- Administrator product creation and updates with service-level role checks.
- Form request validation and JSON resource responses.

### Orders and Payments

- Orders containing multiple products and quantities.
- Order totals calculated from database product prices.
- Pending payment records and hosted Stripe Checkout sessions.
- Checkout session IDs stored against payment records.
- Webhook listener for `checkout.session.completed` and `checkout.session.async_payment_succeeded`.
- Fulfillment only when the event's session reports `payment_status: paid` and its ID matches the stored session.
- Payment status updates, paid order status, and customer-product associations performed in a database transaction.
- Order and payment listing workflows for the companion dashboards.

### Cloud File Delivery

- Pre-signed S3 upload URLs for direct browser uploads.
- UUID-based storage object names.
- Temporary upload and download URLs that expire after **10 minutes**.
- Download responses configured with an attachment filename.
- A purchased-product listing based on the customer's product associations.

## Architecture

```text
Next.js frontend
    |
    | JSON requests + Sanctum bearer token
    v
Laravel API
    |-- Form Requests: input validation
    |-- Controllers: request handling
    |-- Services: products, orders, payments, authentication
    |-- Resources: JSON response formatting
    |
    |-- MySQL: users, products, orders, payments, purchase associations
    |-- Stripe: hosted Checkout and payment events
    |-- S3: temporary upload/download URLs
```

```text
app/
├── Enums/                  # User roles, order and payment statuses
├── Http/
│   ├── Controllers/        # Administrator and customer API controllers
│   ├── Requests/           # Request validation
│   ├── Resources/          # JSON response resources
│   └── Services/           # Application workflows
├── Listeners/              # Stripe webhook listener
└── Models/                 # Eloquent models and relationships
routes/
├── api.php                 # API route registration
├── admin.php               # Administrator endpoints
└── user.php                # Customer endpoints
database/
├── migrations/             # Database schema
└── seeders/                # Local development data
```

## Checkout Workflow

1. An authenticated customer submits product IDs and quantities.
2. `OrderService` loads product prices from the database and calculates the total.
3. The API creates an order, attaches order items, and creates a pending payment.
4. `PaymentService` creates a Stripe Checkout session and returns its URL.
5. The frontend sends the customer to Stripe for payment.
6. `StripeWebhookListener` handles supported paid checkout events.
7. `PaymentService` matches the session ID, marks payment and order as paid, and attaches the products to the customer.

The browser success redirect is separate from webhook fulfillment. Webhook delivery, signature verification configuration, and retry behavior must be verified in the target environment.

## Download Security

### Current Implementation

The download endpoint requires authentication and returns a short-lived S3 URL. However, it currently does **not** verify that the authenticated customer purchased the requested product. A signed URL alone does not prevent an unpaid authenticated user from requesting a link.

### Production Authorization Design

To prevent unpaid users from obtaining download URLs:

1. Keep original assets in a private S3 bucket with public access blocked.
2. Authenticate the request and check a valid paid entitlement for the exact requested product.
3. Return `403 Forbidden` when the entitlement is missing or revoked.
4. Generate a temporary signed GET URL only after authorization succeeds.
5. Grant entitlements through verified Stripe webhooks, with payment amount, currency, session, and order checks.
6. Add retry-safe payment processing, download rate limits, and audit logs.
7. Define refund and revocation rules for future download access.

Signed URLs can be shared until they expire. They limit access duration but cannot prevent redistribution of a file after download.

## Local Setup

### Prerequisites

- PHP compatible with Laravel 13 and the checked-in Composer lockfile.
- Composer and the PHP extensions required by the dependencies.
- MySQL.
- Stripe test credentials and an S3 bucket for payment and storage workflows.

### Installation

```bash
git clone https://github.com/nayhtetkhant/e-book-api.git
cd e-book-api
composer install
cp .env.example .env
php artisan key:generate
```

Create a MySQL database and configure `.env`:

```dotenv
APP_ENV=local
APP_URL=http://localhost:8000

DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=e_book_api
DB_USERNAME=your_database_user
DB_PASSWORD=your_database_password

AWS_ACCESS_KEY_ID=your_access_key
AWS_SECRET_ACCESS_KEY=your_secret_key
AWS_DEFAULT_REGION=your_bucket_region
AWS_BUCKET=your_private_bucket
AWS_USE_PATH_STYLE_ENDPOINT=false

STRIPE_KEY=your_stripe_test_publishable_key
STRIPE_SECRET=your_stripe_test_secret_key
STRIPE_WEBHOOK_SECRET=your_webhook_signing_secret
CASHIER_CURRENCY=usd
```

The application explicitly uses the `s3` disk for product files, so changing the default filesystem disk is not required for those operations. Keep credentials in the backend environment and out of source control.

```bash
php artisan migrate
php artisan db:seed
php artisan serve --port=8000
```

Local seeding creates sample products, users, and this administrator account:

```text
Email: admin@example.com
Password: password
```

Use these credentials only for local development. The default seeder runs its sample seeders only when `APP_ENV=local`.

### Stripe Configuration

- Configure test webhook delivery to the Cashier webhook route; confirm its registered path using `php artisan route:list --path=stripe`.
- Set the matching `STRIPE_WEBHOOK_SECRET`.
- Ensure delivery includes the checkout events handled by the listener.
- Verify that webhook signatures are enforced and the listener executes before testing fulfillment.

Checkout success and cancel URLs are currently hardcoded to `http://localhost:3000` in `app/Http/Services/PaymentService.php`. Update these for the deployment; environment-based redirect configuration remains to be added.

### S3 Configuration

Configure private bucket access and permissions for uploads and downloads. Allow the frontend origin and PUT requests in bucket CORS for direct browser uploads.

The current upload MIME mapping supports **JPEG, PNG, WebP, and GIF**. PDF/e-book support requires extending validation and MIME handling, despite the project's e-book focus.

## API Overview

Paths are relative to `/api`. Login endpoints are public; the other endpoints below require `Authorization: Bearer <token>`.

| Method | Endpoint | Purpose |
| --- | --- | --- |
| POST | `/user/v1/login` | Customer login |
| POST | `/admin/v1/login` | Administrator login |
| GET | `/user/v1/products` | List products |
| GET | `/user/v1/products/{product}` | Product details |
| POST | `/user/v1/orders` | Create an order and Checkout session |
| GET | `/user/v1/orders` | Customer order history |
| GET | `/user/v1/ordered` | Customer-associated products |
| GET | `/user/v1/products/{product}/download` | Generate a temporary download URL |
| POST | `/admin/v1/products` | Create a product and optionally request an upload URL |
| PUT/PATCH | `/admin/v1/products/{product}` | Update a product |
| GET | `/admin/v1/orders` | Administrator order listing |
| GET | `/admin/v1/payments` | Administrator payment listing |

Administrator route prefixes are not authorization controls by themselves. Consistent role and resource policies remain part of the production hardening work.

## Relevance to a Digital Asset Platform

This project demonstrates Laravel API development, relational order modeling, Stripe Checkout integration, cloud upload workflows, and time-limited asset delivery, alongside a React/Next.js frontend.

For extending this application, **Next.js + Laravel + MySQL + private S3 storage** builds on its existing implementation. Next.js serves the storefront and dashboards; Laravel handles validation, authorization, payment processing, and storage integration; MySQL stores transactional data and entitlements; S3 stores asset originals.

License records, preview generation, and watermarking can extend this foundation. Background jobs would be appropriate for expensive media processing. Dedicated 2D/3D viewers belong in the frontend, supported by backend preview metadata and access controls.

## Roadmap

- Enforce purchase entitlement checks on every download request.
- Apply consistent administrator and resource ownership policies.
- Strengthen payment validation and duplicate/concurrent webhook handling.
- Define refund, cancellation, and entitlement revocation workflows.
- Add PDF/e-book validation, file size limits, and upload completion checks.
- Add license generation and license lifecycle management.
- Add watermarked previews and metadata for 2D/3D viewers.
- Evaluate Cloudflare R2 and CDN delivery; neither is a demonstrated deployment here.
- Add PayPal if required; the current integration uses Stripe.
- Add pagination, background processing, and operational monitoring as usage grows.
- Add feature tests for authorization, checkout, payment events, and downloads.

## Validation Status

```bash
composer test
```

The repository includes Pest tooling, but its existing tests are framework examples rather than coverage of the commerce workflows. This README was prepared from source inspection; installation, Stripe payments, S3 transfers, and production behavior were not executed as part of this documentation update.
