<?php

namespace Hws\FieldService\Http\Controllers\Admin;

use Illuminate\Routing\Controller;

/**
 * Placeholder pages for sidebar sections that exist in the menu but
 * don't have real functionality built yet — Employees, Attendance,
 * Service Requests, Sales & Leads, Inventory, Expenses, Reports.
 *
 * Each of these is a real, separate build (list views, filters, detail
 * pages, forms) — this just keeps the navigation from 404ing while
 * that work is scoped out individually.
 */
class ComingSoonController extends Controller
{
    public function show(string $title)
    {
        return view('hws::admin.coming-soon', [
            'title' => $title,
        ]);
    }
}
