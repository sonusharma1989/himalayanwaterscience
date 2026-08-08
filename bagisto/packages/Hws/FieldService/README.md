# Hws\FieldService — Bagisto package

Database layer *and admin dashboard* for the Himalayan Water Science employee app: tasks, materials, photos, attendance, site surveys, leave requests, and expense claims — plus a manager-facing dashboard inside Bagisto's own admin panel. Built as a standalone Bagisto package, same pattern as `packages/Webkul/*`, so it installs alongside your existing Bagisto core without touching it.

## What's in here

```
packages/Hws/FieldService/
├── composer.json
├── README.md
└── src/
    ├── Providers/
    │   └── FieldServiceServiceProvider.php   registers migrations, routes, menu, views
    ├── Config/
    │   ├── admin-menu.php                    sidebar entries (Field Service section)
    │   └── acl.php                           permission nodes matching the menu
    ├── Routes/
    │   └── admin-routes.php                  /admin/field-service/* routes
    ├── Http/Controllers/Admin/
    │   ├── DashboardController.php           real queries powering the dashboard
    │   └── ComingSoonController.php          placeholder for unbuilt sections
    ├── Resources/views/admin/
    │   ├── dashboard.blade.php               HWS-branded dashboard, x-admin::layouts shell
    │   └── coming-soon.blade.php             shared placeholder view
    ├── Database/Migrations/                  the 8 core tables + sale_amount/amc_renewal_date
    └── Models/                               matching Eloquent models
```

## Admin dashboard

Visit `/admin/field-service/dashboard` after installing. Every KPI card, the weekly chart, the attendance donut, and the live employee status list are wired to real queries against the `hws_*` tables — nothing on that page is placeholder data.

Two columns exist specifically to power this dashboard and aren't used anywhere else yet: `hws_tasks.sale_amount` (feeds "Sales this month") and `hws_tasks.amc_renewal_date` (feeds "AMC renewals due"). Neither is populated automatically — something in your task-completion flow needs to actually set these values when a sale closes or an AMC service happens, or both cards will permanently read ₹0 / 0.

**One assumption worth checking:** "Employees online" and "Attendance today" are both calculated against `Admin::count()` as the total headcount — i.e., *all* admin users, not specifically field technicians. If office-only admin accounts exist alongside field staff, this will under-report the percentage. If Bagisto's role system already distinguishes field technicians from other admin roles, that's a better filter than counting every admin.

The sidebar also registers Employees, Attendance, Service Requests, Sales & Leads, Inventory, Expenses, and Reports — each currently a "coming soon" placeholder so navigation doesn't break, not a built-out feature. Inventory in particular has no backing data model in this package at all yet.

## Installing into your Bagisto app

1. Copy this whole `packages/Hws/FieldService` folder into your Bagisto project root, so it sits next to `packages/Webkul`.

2. In your **root** `composer.json` (not this one), add a path repository and require entry — this is the same mechanism Bagisto uses for its own packages:

   ```json
   {
       "repositories": [
           {
               "type": "path",
               "url": "packages/Hws/FieldService"
           }
       ],
       "require": {
           "hws/field-service": "*"
       }
   }
   ```

3. From your Bagisto project root:

   ```bash
   composer update hws/field-service
   php artisan migrate
   php artisan optimize:clear
   ```

   That creates the 8 core tables plus the 2 dashboard-only columns on `hws_tasks`. `assigned_to`, `employee_id`, and `reviewed_by` foreign keys point at Bagisto's existing `admins` table, so technicians are just admins with a restricted role — no separate employee table or auth system needed. The `optimize:clear` matters here specifically — Bagisto merges package menu/ACL config at boot, and a stale config cache is the most common reason a newly installed package's menu item doesn't show up.

## After this

The dashboard is real, but six of its seven sidebar siblings aren't. Each "coming soon" page is its own scoped build — list views, filters, detail pages, forms — not something to treat as one remaining task. Also still open:

- **REST/GraphQL resources** via the `bagisto-api` package we set up earlier, so the Next.js employee app can actually read/write these tables over the network — the admin dashboard reads the database directly, but the mobile app still needs its own API layer.
- **Populating `sale_amount` and `amc_renewal_date`** from wherever tasks actually get marked complete, or the two dashboard cards that depend on them stay at zero.

Happy to take any of these next.
