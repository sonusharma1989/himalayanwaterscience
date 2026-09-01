<?php
return [
    [
        'key'        => 'projects',
        'name'       => 'Projects',
        'route'      => 'hws.admin.projects.dashboard',
        'sort'       => 2.5,
        'icon-class' => 'project-menu-icon',
    ],
    [
        'key' => 'projects.dashboard', 'name' => 'Dashboard',
        'route' => 'hws.admin.projects.dashboard', 'sort' => 1, 'icon-class' => '',
    ],
    [
        'key' => 'projects.leads', 'name' => 'Project Leads',
        'route' => 'hws.admin.projects.leads', 'sort' => 2, 'icon-class' => '',
    ],
    [
        'key' => 'projects.orders', 'name' => 'Orders',
        'route' => 'hws.admin.projects.orders', 'sort' => 3, 'icon-class' => '',
    ],
    [
        'key' => 'projects.invoices', 'name' => 'Invoices',
        'route' => 'hws.admin.projects.invoices', 'sort' => 4, 'icon-class' => '',
    ],
    [
        'key' => 'projects.shipments', 'name' => 'Shipments',
        'route' => 'hws.admin.projects.shipments', 'sort' => 5, 'icon-class' => '',
    ],
    [
        'key' => 'projects.refunds', 'name' => 'Refunds',
        'route' => 'hws.admin.projects.refunds', 'sort' => 6, 'icon-class' => '',
    ],
    [
        'key' => 'projects.transactions', 'name' => 'Transactions',
        'route' => 'hws.admin.projects.transactions', 'sort' => 7, 'icon-class' => '',
    ],
    [
        'key'        => 'field-service',
        'name'       => 'Service Operation',
        'route'      => 'hws.admin.dashboard.index',
        'sort'       => 1.5,
        'icon-class' => 'field-service-menu-icon',
    ],
    [
        'key'        => 'field-service.dashboard',
        'name'       => 'Dashboard',
        'route'      => 'hws.admin.dashboard.index',
        'sort'       => 1,
        'icon-class' => '',
    ],
    [
        'key'        => 'field-service.employees',
        'name'       => 'Employees',
        'route'      => 'hws.admin.employees.index',
        'sort'       => 2,
        'icon-class' => '',
    ],
    [
        'key'        => 'field-service.attendance',
        'name'       => 'Attendance',
        'route'      => 'hws.admin.attendance.index',
        'sort'       => 3,
        'icon-class' => '',
    ],
    [
        'key'        => 'field-service.service_requests',
        'name'       => 'Service Requests',
        'route'      => 'hws.admin.service-requests.index',
        'sort'       => 4,
        'icon-class' => '',
    ],
    [
        'key'        => 'field-service.sales_leads',
        'name'       => 'Sales & Leads',
        'route'      => 'hws.admin.sales-leads.index',
        'sort'       => 5,
        'icon-class' => '',
    ],
];
