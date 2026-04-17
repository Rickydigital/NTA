<!DOCTYPE html>
<html lang="{{ App::getLocale() }}">

<head>
    <meta charset="utf-8" />
    <title>{{ config('app.name', 'O3Plus') }} | @yield('title')</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta content="O3Plus Project-UNESCO" name="description" />
    <meta content="Erick Kinyamagoha & Ebenezer Douglas" name="author" />
    <meta http-equiv="X-UA-Compatible" content="IE=edge" />
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <link rel="shortcut icon" href="{{ asset('app-assets/images/logo-sm.png') }}">

    <!-- Google Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">

    <!-- Vendor CSS -->
    <link href="{{ asset('app-assets/libs/bootstrap-tagsinput/bootstrap-tagsinput.css') }}" rel="stylesheet" />
    <link href="{{ asset('app-assets/libs/switchery/switchery.min.css') }}" rel="stylesheet" type="text/css" />
    <link href="{{ asset('app-assets/libs/multiselect/multi-select.css') }}" rel="stylesheet" type="text/css" />
    <link href="{{ asset('app-assets/libs/sweetalert2/sweetalert2.min.css') }}" rel="stylesheet" type="text/css" />
    <link href="{{ asset('app-assets/select2/css/select2.min.css') }}" rel="stylesheet" type="text/css" />
    <link href="{{ asset('app-assets/libs/bootstrap-select/bootstrap-select.min.css') }}" rel="stylesheet"
        type="text/css" />
    <link href="{{ asset('app-assets/libs/bootstrap-touchspin/jquery.bootstrap-touchspin.css') }}" rel="stylesheet"
        type="text/css" />
    <link href="{{ asset('app-assets/libs/custombox/custombox.min.css') }}" rel="stylesheet" type="text/css" />
    <link href="{{ asset('app-assets/libs/rwd-table/rwd-table.min.css') }}" rel="stylesheet" type="text/css" />
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css">
    <link href="https://cdn.jsdelivr.net/npm/@mdi/font/css/materialdesignicons.min.css" rel="stylesheet">
    <link href="{{ asset('app-assets/css/bootstrap.min.css') }}" rel="stylesheet" type="text/css"
        id="bootstrap-stylesheet" />
    <link href="{{ asset('app-assets/css/icons.min.css') }}" rel="stylesheet" type="text/css" />
    <link href="{{ asset('app-assets/css/app.min.css') }}" rel="stylesheet" type="text/css" id="app-stylesheet" />

    <style>
        /* ─── Variables ─── */
        :root {
            --sidebar-w: 250px;
             --topbar-h: 72px;
            --sidebar-bg: #1a2332;
            --sidebar-border: rgba(255, 255, 255, 0.07);
            --nav-text: #8b9ab0;
            --nav-hover: #ffffff;
            --nav-active-bg: rgba(59, 130, 246, 0.14);
            --nav-active-accent: #3b82f6;
            --body-bg: #f0f4f8;
            --accent: #3b82f6;
            --radius: 8px;
        }
        

        /* ─── Reset / Base ─── */
        *,
        *::before,
        *::after {
            box-sizing: border-box;
        }

        body {
            font-family: 'Inter', sans-serif;
            background: var(--body-bg);
            color: #2d3a4a;
            margin: 0;
        }

        body {
            display: flex;
            flex-direction: column;
            min-height: 100vh;
        }

        #wrapper {
            flex: 1;
        }

        /* ════════════════════════════════
           SIDEBAR  –  full height, fixed
        ════════════════════════════════ */
        .left-side-menu {
            position: fixed !important;
            top: 0 !important;
            left: 0 !important;
            bottom: 0 !important;
            width: var(--sidebar-w) !important;
            background: var(--sidebar-bg) !important;
            border-right: 1px solid var(--sidebar-border) !important;
            box-shadow: 2px 0 8px rgba(0, 0, 0, 0.15) !important;
            z-index: 1040 !important;
            overflow-y: auto;
            overflow-x: hidden;
            display: flex;
            flex-direction: column;
        }

        /* Thin scrollbar */
        .left-side-menu::-webkit-scrollbar {
            width: 3px;
        }

        .left-side-menu::-webkit-scrollbar-track {
            background: transparent;
        }

        .left-side-menu::-webkit-scrollbar-thumb {
            background: rgba(255, 255, 255, 0.08);
            border-radius: 3px;
        }

        /* Brand / logo at top of sidebar */
        .sidebar-brand {
            height: var(--topbar-h);
            min-height: var(--topbar-h);
            display: flex;
            align-items: center;
            padding: 0 20px;
            border-bottom: 1px solid var(--sidebar-border);
            flex-shrink: 0;
        }

        .sidebar-brand a {
            display: flex;
            align-items: center;
            gap: 10px;
            text-decoration: none;
        }

        .sidebar-brand .brand-text {
            font-size: 1rem;
            font-weight: 700;
            color: #ffffff;
            letter-spacing: -0.01em;
        }

        .sidebar-brand .brand-sub {
            font-size: 0.62rem;
            font-weight: 500;
            color: #4a6080;
            letter-spacing: 0.06em;
            text-transform: uppercase;
            margin-top: 1px;
        }

        /* Nav area */
        .slimscroll-menu {
            flex: 1;
            padding: 8px 0 20px;
        }

        /* Section label */
        #sidebar-menu .menu-title {
            font-size: 0.6rem !important;
            font-weight: 700 !important;
            letter-spacing: 0.1em !important;
            text-transform: uppercase !important;
            color: #3d5068 !important;
            padding: 18px 20px 5px !important;
            margin: 0 !important;
        }

        .school-modal-card .modal-body {
            max-height: calc(100vh - 180px);
            overflow-y: auto;
            overflow-x: hidden;
        }

        .school-modal-card .modal-body::-webkit-scrollbar {
            width: 6px;
        }

        .school-modal-card .modal-body::-webkit-scrollbar-thumb {
            background: rgba(100, 116, 139, 0.35);
            border-radius: 999px;
        }

        .modal-dialog.modal-xl {
            max-width: 1400px;
        }

        /* Nav links */
        #sidebar-menu ul {
            list-style: none;
            margin: 0;
            padding: 0;
        }

        #sidebar-menu ul li>a {
            display: flex !important;
            align-items: center !important;
            gap: 10px !important;
            padding: 8px 14px !important;
            margin: 1px 10px !important;
            border-radius: 6px !important;
            font-size: 0.81rem !important;
            font-weight: 500 !important;
            color: var(--nav-text) !important;
            text-decoration: none !important;
            border-left: 3px solid transparent !important;
            transition: background 0.15s, color 0.15s !important;
            white-space: nowrap;
        }

        #sidebar-menu ul li>a:hover {
            background: rgba(255, 255, 255, 0.06) !important;
            color: var(--nav-hover) !important;
        }

        #sidebar-menu ul li.active>a {
            background: var(--nav-active-bg) !important;
            color: #ffffff !important;
            border-left-color: var(--nav-active-accent) !important;
        }

        #sidebar-menu ul li>a i {
            font-size: 1rem !important;
            width: 18px !important;
            text-align: center !important;
            flex-shrink: 0 !important;
            opacity: 0.6;
            transition: opacity 0.15s;
        }

        #sidebar-menu ul li>a:hover i,
        #sidebar-menu ul li.active>a i {
            opacity: 1;
        }

        /* Sub-menu */
        #sidebar-menu .nav-second-level {
            background: transparent !important;
            padding: 0 !important;
        }

        #sidebar-menu .nav-second-level li>a {
            padding-left: 44px !important;
            font-size: 0.78rem !important;
            font-weight: 400 !important;
        }

        #sidebar-menu .nav-second-level li.active>a {
            color: #ffffff !important;
            background: rgba(255, 255, 255, 0.05) !important;
            border-left-color: var(--nav-active-accent) !important;
        }

        /* Arrow icon */
        .menu-arrow {
            margin-left: auto !important;
            font-size: 0.7rem !important;
            opacity: 0.4;
        }

        /* ════════════════════════════════
           TOPBAR  –  white, right of sidebar
        ════════════════════════════════ */


        .navbar-custom {
            position: fixed !important;
            top: 0 !important;
            left: var(--sidebar-w) !important;
            right: 0 !important;
            min-height: var(--topbar-h) !important;
            height: auto !important;
            background: #fff !important;
            border-bottom: 1px solid #e2e8f0 !important;
            box-shadow: 0 1px 4px rgba(0, 0, 0, .06) !important;
            z-index: 1030 !important;

            display: flex !important;
            align-items: center !important;
            justify-content: space-between !important;
            gap: 16px;
            padding: 10px 20px !important;
        }

        .topbar-left-area,
        .topbar-right-area {
            display: flex;
            align-items: center;
            min-width: 0;
        }

        .topbar-left-area {
            flex: 1 1 auto;
            overflow: hidden;
            gap: 10px;
        }

        .topbar-right-area {
            flex: 0 0 auto;
            gap: 10px;
        }

        .topbar-page-info {
            min-width: 0;
            display: flex;
            flex-direction: column;
            justify-content: center;
            line-height: 1.15;
        }

        .topbar-page-info h5 {
            font-size: 16px;
            font-weight: 700;
            color: #374151;
            margin: 0;
            white-space: nowrap;
            overflow: hidden;
            text-overflow: ellipsis;
        }

        .topbar-page-info small {
            font-size: 13px;
            color: #6b7280;
            white-space: nowrap;
            overflow: hidden;
            text-overflow: ellipsis;
        }

        .app-search {
            width: 320px;
            margin: 0;
        }

        .app-search .form-control {
            height: 40px;
            border-radius: 999px 0 0 999px;
        }

        .app-search .btn {
            height: 40px;
            border-radius: 0 999px 999px 0;
        }

        .topbar-divider {
            width: 1px;
            height: 28px;
            background: #e5e7eb;
        }

        .navbar-custom .nav-link {
            height: auto !important;
            min-height: 44px;
            padding: 6px 8px !important;
            display: flex !important;
            align-items: center !important;
        }

        .nav-user {
            display: flex !important;
            align-items: center !important;
            gap: 10px;
        }

        .avatar-initials {
            width: 40px;
            height: 40px;
            border-radius: 50%;
            background: #2563eb;
            color: #fff;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            font-weight: 700;
            font-size: 13px;
            flex-shrink: 0;
        }

        .user-meta {
            display: flex;
            flex-direction: column;
            line-height: 1.1;
            min-width: 0;
        }

        .user-name {
            font-size: 13px;
            font-weight: 600;
            color: #1e293b;
            white-space: nowrap;
        }

        .user-role {
            font-size: 11px;
            color: #64748b;
            white-space: nowrap;
        }

        .content-page {
            margin-left: var(--sidebar-w) !important;
            margin-top: var(--topbar-h) !important;
        }

        @media (max-width: 1199px) {
            .user-meta {
                display: none !important;
            }

            .app-search {
                width: 240px;
            }
        }

        @media (max-width: 991px) {
            .topbar-page-info small {
                display: none;
            }

            .app-search {
                display: none !important;
            }
        }

        /* ════════════════════════════════
           CONTENT AREA
        ════════════════════════════════ */
        .content-page {
            margin-left: var(--sidebar-w) !important;
            margin-top: var(--topbar-h) !important;
            transition: margin-left 0.25s ease;
        }

        .content {
            padding: 24px 28px;
        }

        /* ─── Alerts ─── */
        .alert {
            border: none;
            border-radius: var(--radius);
            font-size: 0.84rem;
            font-weight: 500;
        }

        .alert-success {
            background: #f0fdf4;
            color: #15803d;
            border-left: 4px solid #22c55e;
        }

        .alert-danger {
            background: #fef2f2;
            color: #b91c1c;
            border-left: 4px solid #f87171;
        }

        /* ─── Cards ─── */
        .card,
        .card-box {
            border-radius: var(--radius) !important;
            box-shadow: 0 1px 4px rgba(0, 0, 0, 0.06) !important;
            border: 1px solid #e2e8f0 !important;
        }

        /* ════════════════════════════
           SHARED INDEX PAGE STYLES
           (available on every page)
        ════════════════════════════ */

        /* Page header */
        .page-header {
            display: flex;
            align-items: center;
            justify-content: space-between;
            flex-wrap: wrap;
            gap: 12px;
            margin-bottom: 20px;
        }

        .page-header h4 {
            font-size: 1.15rem;
            font-weight: 700;
            color: #1e293b;
            margin: 0 0 2px;
        }

        .page-header .breadcrumb {
            background: none;
            padding: 0;
            margin: 0;
            font-size: 0.76rem;
        }

        .page-header .breadcrumb-item+.breadcrumb-item::before {
            color: #94a3b8;
        }

        .page-header .breadcrumb-item a {
            color: #64748b;
            text-decoration: none;
        }

        .page-header .breadcrumb-item.active {
            color: #94a3b8;
        }

        /* Filter card */
        .filter-card {
            background: #fff;
            border: 1px solid #e2e8f0;
            border-radius: 10px;
            padding: 14px 20px;
            margin-bottom: 20px;
        }

        .filter-toggle {
            display: flex;
            align-items: center;
            justify-content: space-between;
            padding: 13px 20px;
            cursor: pointer;
            user-select: none;
        }

        .filter-toggle h6 {
            margin: 0;
            font-size: 0.82rem;
            font-weight: 600;
            color: #475569;
            display: flex;
            align-items: center;
            gap: 7px;
        }

        .filter-body {
            padding: 4px 20px 20px;
            border-top: 1px solid #f1f5f9;
        }

        .filter-body label {
            font-size: 0.72rem;
            font-weight: 600;
            color: #64748b;
            text-transform: uppercase;
            letter-spacing: 0.04em;
            margin-bottom: 4px;
            display: block;
        }

        .filter-body .form-control {
            font-size: 0.82rem;
            border-color: #e2e8f0;
            border-radius: 6px;
            height: 36px;
            color: #344767;
        }

        .filter-badge {
            background: #eff6ff;
            color: #3b82f6;
            border-radius: 20px;
            padding: 2px 9px;
            font-size: 0.68rem;
            font-weight: 700;
        }

        /* Desktop table card */
        .table-card {
            background: #fff;
            border: 1px solid #e2e8f0;
            border-radius: 10px;
            overflow-x: auto;
            -webkit-overflow-scrolling: touch;
            margin-bottom: 20px;
        }

        .table-card .table-responsive {
            overflow-x: auto;
            -webkit-overflow-scrolling: touch;
        }

        .table-card .table {
            margin-bottom: 0;
        }

        .table-card .table thead th {
            background: #f8fafc;
            border-bottom: 2px solid #e2e8f0;
            font-size: 0.68rem;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: 0.07em;
            color: #64748b;
            padding: 11px 14px;
            white-space: nowrap;
        }

        .table-card .table tbody tr {
            border-bottom: 1px solid #f1f5f9;
            transition: background 0.12s;
        }

        .table-card .table tbody tr:last-child {
            border-bottom: none;
        }

        .table-card .table tbody tr:hover {
            background: #f8fafc !important;
        }

        .table-card .table tbody td,
        .table-card .table tbody th {
            padding: 12px 14px;
            font-size: 0.82rem;
            color: #344767;
            vertical-align: middle;
            border: none;
            white-space: nowrap;
        }

        /*
         * Utility: add class="td-ellipsis" to a <td> or <th> to truncate
         * long content with "…" instead of overflowing.
         * The parent <table> must also have class="table-fixed" for this to work
         * (table-layout:fixed is required for overflow:hidden on cells).
         */
        .table-fixed {
            table-layout: fixed;
            width: 100%;
        }

        .td-ellipsis {
            overflow: hidden;
            text-overflow: ellipsis;
            white-space: nowrap;
            max-width: 0;
        }

        /* Track / reference pills */
        .ref-pill {
            font-family: 'Courier New', monospace;
            font-size: 0.72rem;
            font-weight: 700;
            background: #f1f5f9;
            color: #475569;
            border-radius: 5px;
            padding: 3px 8px;
            display: inline-block;
        }

        /* Action icon button */
        .btn-icon {
            width: 32px;
            height: 32px;
            border-radius: 7px;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            border: 1px solid #e2e8f0;
            background: #fff;
            color: #64748b;
            font-size: 0.95rem;
            text-decoration: none;
            transition: background 0.14s, color 0.14s, border-color 0.14s;
            cursor: pointer;
        }

        .btn-icon:hover {
            background: #eff6ff;
            border-color: #bfdbfe;
            color: #3b82f6;
        }

        .btn-icon.danger:hover {
            background: #fef2f2;
            border-color: #fecaca;
            color: #dc2626;
        }

        /* Status badges */
        .s-badge {
            display: inline-flex;
            align-items: center;
            gap: 5px;
            font-size: 0.72rem;
            font-weight: 600;
            border-radius: 20px;
            padding: 3px 10px;
            white-space: nowrap;
        }

        .s-received {
            background: #eff6ff;
            color: #3b82f6;
        }

        .s-investigation {
            background: #fffbeb;
            color: #d97706;
        }

        .s-reported {
            background: #f0fdf4;
            color: #16a34a;
        }

        .s-invalid {
            background: #fef2f2;
            color: #dc2626;
        }

        .s-active {
            background: #f0fdf4;
            color: #16a34a;
        }

        .s-inactive {
            background: #fef2f2;
            color: #dc2626;
        }

        .s-default {
            background: #f8fafc;
            color: #64748b;
        }

        .navbar-custom {
            background: #ffffff;
            border-bottom: 1px solid #f1f5f9;
        }

        .avatar-initials {
            width: 34px;
            height: 34px;
            border-radius: 50%;
            background: #2563eb;
            color: #fff;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            font-weight: 600;
            font-size: 13px;
        }

        .user-name {
            font-size: 13px;
            font-weight: 600;
            color: #1e293b;
        }

        .user-role {
            font-size: 11px;
            color: #64748b;
        }

        .app-search .form-control {
            border-radius: 8px 0 0 8px;
            border-right: 0;
        }

        .app-search .btn {
            border-radius: 0 8px 8px 0;
        }

        /* Mobile card list */
        .mob-card {
            background: #fff;
            border: 1px solid #e2e8f0;
            border-radius: 10px;
            padding: 14px 16px;
            margin-bottom: 10px;
            transition: box-shadow 0.15s;
        }

        .mob-card:hover {
            box-shadow: 0 4px 14px rgba(0, 0, 0, 0.07);
        }

        .mob-card-top {
            display: flex;
            align-items: center;
            justify-content: space-between;
            margin-bottom: 8px;
            gap: 8px;
            flex-wrap: wrap;
        }

        .mob-card-title {
            font-size: 0.88rem;
            font-weight: 600;
            color: #1e293b;
        }

        .mob-card-body {
            font-size: 0.81rem;
            color: #475569;
            margin-bottom: 8px;
            line-height: 1.45;
        }

        .mob-card-meta {
            display: flex;
            flex-wrap: wrap;
            gap: 10px;
            font-size: 0.74rem;
            color: #94a3b8;
        }

        .mob-card-meta span {
            display: flex;
            align-items: center;
            gap: 4px;
        }

        .mob-card-footer {
            display: flex;
            align-items: center;
            gap: 8px;
            margin-top: 10px;
            padding-top: 10px;
            border-top: 1px solid #f1f5f9;
            flex-wrap: wrap;
        }

        /* Empty state */
        .tbl-empty {
            padding: 50px 20px;
            text-align: center;
        }

        .tbl-empty i {
            font-size: 2rem;
            color: #cbd5e1;
            display: block;
            margin-bottom: 8px;
        }

        .tbl-empty p {
            font-size: 0.84rem;
            color: #94a3b8;
            margin: 0;
        }

        /* Chart card (shared between dashboard + statistics) */
        .chart-card {
            background: #fff;
            border: 1px solid #e2e8f0;
            border-radius: 10px;
            padding: 20px 20px 16px;
            margin-bottom: 20px;
        }

        .chart-card-title {
            font-size: 0.82rem;
            font-weight: 700;
            color: #1e293b;
            margin-bottom: 16px;
            text-transform: uppercase;
            letter-spacing: .05em;
        }

        /* Modal */
        .modal-card {
            border: none !important;
            border-radius: 12px !important;
            box-shadow: 0 20px 60px rgba(0, 0, 0, 0.15) !important;
        }

        .modal-card .modal-header {
            border-bottom: 1px solid #f1f5f9;
            padding: 18px 24px;
        }

        .modal-card .modal-title {
            font-size: 0.95rem;
            font-weight: 700;
            color: #1e293b;
        }

        .modal-card .modal-body {
            padding: 20px 24px;
        }

        .modal-card .modal-footer {
            border-top: 1px solid #f1f5f9;
            padding: 14px 24px;
        }

        /* ─── Chat FAB ─── */
        .chat-fab {
            position: fixed;
            bottom: 28px;
            right: 28px;
            width: 50px;
            height: 50px;
            border-radius: 50%;
            background: var(--accent);
            color: #fff;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 1.15rem;
            box-shadow: 0 4px 16px rgba(59, 130, 246, 0.4);
            z-index: 1050;
            text-decoration: none;
            transition: transform 0.2s, box-shadow 0.2s;
        }

        .chat-fab:hover {
            color: #fff;
            transform: translateY(-3px);
            box-shadow: 0 8px 24px rgba(59, 130, 246, 0.5);
        }

        /* ─── Transitions ─── */
        .left-side-menu {
            transition: transform 0.25s ease, width 0.25s ease;
        }

        .navbar-custom {
            transition: left 0.25s ease;
        }

        .content-page {
            transition: margin-left 0.25s ease;
        }

        /* ════════════════════════════
           DESKTOP  ≥ 992px  (default)
           Full sidebar 250px
        ════════════════════════════ */

        /* Collapsed by user on desktop */
        body.sidebar-collapsed .left-side-menu {
            transform: translateX(-250px);
        }

        body.sidebar-collapsed .navbar-custom {
            left: 0 !important;
        }

        body.sidebar-collapsed .content-page {
            margin-left: 0 !important;
        }

        /* Small logo in topbar: visible only when sidebar is gone */
        .topbar-logo-sm {
            display: none;
        }

        body.sidebar-collapsed .topbar-logo-sm {
            display: flex !important;
        }

        /* ════════════════════════════
           TABLET  768px – 991px
           Icon-only sidebar 70px
        ════════════════════════════ */
        @media (min-width: 768px) and (max-width: 991px) {

            :root {
                --sidebar-w: 70px;
            }

            .left-side-menu {
                width: 70px !important;
            }

            .navbar-custom {
                left: 70px !important;
            }

            .content-page {
                margin-left: 70px !important;
            }

            /* Brand: show small logo only */
            .sidebar-brand .brand-logo-full {
                display: none !important;
            }

            .sidebar-brand .brand-logo-sm {
                display: block !important;
            }

            .sidebar-brand {
                justify-content: center;
                padding: 0 !important;
            }

            /* Hide text & arrows, center icon */
            #sidebar-menu .menu-title {
                display: none !important;
            }

            #sidebar-menu ul li>a>span:not(.menu-arrow) {
                display: none !important;
            }

            #sidebar-menu ul li>a>.menu-arrow {
                display: none !important;
            }

            #sidebar-menu ul li>a {
                justify-content: center !important;
                padding: 10px 0 !important;
                margin: 2px 8px !important;
                border-left: none !important;
                border-radius: 8px !important;
            }

            #sidebar-menu ul li.active>a {
                border-left: none !important;
                background: var(--nav-active-bg) !important;
            }

            #sidebar-menu ul li>a i {
                width: auto !important;
                font-size: 1.2rem !important;
                opacity: 0.85;
            }

            /* Hide sub-menus entirely on tablet */
            #sidebar-menu .nav-second-level {
                display: none !important;
            }

            /* Collapsed on tablet: fully hide */
            body.sidebar-collapsed .left-side-menu {
                transform: translateX(-70px);
            }

            body.sidebar-collapsed .navbar-custom {
                left: 0 !important;
            }

            body.sidebar-collapsed .content-page {
                margin-left: 0 !important;
            }

            body.sidebar-collapsed .topbar-logo-sm {
                display: flex !important;
            }

            /* Hover expand: overlays content, no layout shift */
            .left-side-menu.sidebar-hovered {
                width: 250px !important;
                z-index: 1040;
                box-shadow: 4px 0 20px rgba(0, 0, 0, 0.12);
            }

            .left-side-menu.sidebar-hovered .sidebar-brand .brand-logo-full {
                display: flex !important;
            }

            .left-side-menu.sidebar-hovered .sidebar-brand .brand-logo-sm {
                display: none !important;
            }

            .left-side-menu.sidebar-hovered .sidebar-brand {
                justify-content: flex-start !important;
                padding: 0 16px !important;
            }

            .left-side-menu.sidebar-hovered #sidebar-menu .menu-title {
                display: block !important;
            }

            .left-side-menu.sidebar-hovered #sidebar-menu ul li>a>span:not(.menu-arrow) {
                display: inline !important;
            }

            .left-side-menu.sidebar-hovered #sidebar-menu ul li>a>.menu-arrow {
                display: inline-block !important;
            }

            .left-side-menu.sidebar-hovered #sidebar-menu ul li>a {
                justify-content: flex-start !important;
                padding: 10px 20px !important;
                margin: 2px 0 !important;
                border-radius: 0 !important;
            }
        }

        /* ════════════════════════════
           MOBILE  < 768px
           Sidebar hidden, overlay on open
        ════════════════════════════ */
        @media (max-width: 767px) {

            /* Sidebar off-screen by default */
            .left-side-menu {
                transform: translateX(-250px) !important;
                width: 250px !important;
                z-index: 1050 !important;
            }

            .navbar-custom {
                left: 0 !important;
            }

            .content-page {
                margin-left: 0 !important;
            }

            /* Always show small logo on mobile topbar */
            .topbar-logo-sm {
                display: flex !important;
            }

            /* Sidebar visible (overlay) */
            body.sidebar-open .left-side-menu {
                transform: translateX(0) !important;
                box-shadow: 4px 0 24px rgba(0, 0, 0, 0.35) !important;
            }

            body.sidebar-open .topbar-logo-sm {
                display: none !important;
            }

            /* Backdrop */
            .sidebar-backdrop {
                display: none;
                position: fixed;
                inset: 0;
                background: rgba(0, 0, 0, 0.45);
                z-index: 1045;
                cursor: pointer;
            }

            body.sidebar-open .sidebar-backdrop {
                display: block;
            }
        }
    </style>

    @stack('styles')
</head>

<body>
    <div id="wrapper">

        @include('components.top-bar')
        @include('components.side-bar')
        <div class="sidebar-backdrop" id="sidebarBackdrop"></div>

        <div class="content-page">
            <div class="content">

                @if (session('success'))
                <div class="alert alert-success alert-dismissible mb-4" role="alert">
                    <i class="fas fa-check-circle me-2"></i>{{ session('success') }}
                    <button type="button" class="btn-close ms-2" data-bs-dismiss="alert"></button>
                </div>
                @endif

                @if ($errors->any())
                <div class="alert alert-danger alert-dismissible mb-4" role="alert">
                    <i class="fas fa-exclamation-circle me-2"></i>
                    <ul class="mb-0 ps-3">
                        @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                    <button type="button" class="btn-close ms-2" data-bs-dismiss="alert"></button>
                </div>
                @endif

                @yield('content')
            </div>
            @include('components.footer')
        </div>

        @auth
        <a href="" class="chat-fab" title="Chat">
            <i class="fas fa-comments"></i>
        </a>
        @endauth

    </div>

    <!-- Vendor JS -->
    <script src="{{ asset('app-assets/js/vendor.min.js') }}"></script>
    <script src="{{ asset('app-assets/bootstrap-5.0.2/js/bootstrap.bundle.min.js') }}"></script>
    <script src="{{ asset('app-assets/js/app.min.js') }}"></script>
    <script src="{{ asset('app-assets/libs/sweetalert2/sweetalert2.min.js') }}"></script>
    <script src="{{ asset('app-assets/libs/jquery-steps/jquery.steps.min.js') }}"></script>
    <script src="{{ asset('app-assets/libs/jquery-validation/jquery.validate.min.js') }}"></script>
    <script src="{{ asset('app-assets/libs/switchery/switchery.min.js') }}"></script>
    <script src="{{ asset('app-assets/libs/bootstrap-tagsinput/bootstrap-tagsinput.min.js') }}"></script>
    <script src="{{ asset('app-assets/libs/multiselect/jquery.multi-select.js') }}"></script>
    <script src="{{ asset('app-assets/libs/jquery-quicksearch/jquery.quicksearch.min.js') }}"></script>
    <script src="{{ asset('app-assets/libs/autocomplete/jquery.autocomplete.min.js') }}"></script>
    <script src="{{ asset('app-assets/libs/bootstrap-select/bootstrap-select.min.js') }}"></script>
    <script src="{{ asset('app-assets/libs/bootstrap-touchspin/jquery.bootstrap-touchspin.min.js') }}"></script>
    <script src="{{ asset('app-assets/libs/bootstrap-maxlength/bootstrap-maxlength.min.js') }}"></script>
    <script src="{{ asset('app-assets/libs/bootstrap-filestyle2/bootstrap-filestyle.min.js') }}"></script>
    <script src="{{ asset('app-assets/libs/custombox/custombox.min.js') }}"></script>
    <script src="{{ asset('app-assets/libs/rwd-table/rwd-table.min.js') }}"></script>
    <script src="{{ asset('app-assets/libs/raphael/raphael.min.js') }}"></script>
    <script src="{{ asset('app-assets/libs/morris-js/morris.min.js') }}"></script>
    <script src="{{ asset('app-assets/select2/js/select2.full.min.js') }}"></script>
    {{-- dashboard.init.js loaded only from dashboard.blade.php to avoid double chart render --}}
    <script src="{{ asset('app-assets/js/pages/form-wizard.init.js') }}"></script>
    {{-- form-advanced.init.js removed: it calls $.mockjax() which is not loaded globally --}}
    <script src="{{ asset('app-assets/js/pages/sweetalerts.init.js') }}"></script>
    <script src="{{ asset('app-assets/select2/js/select2Init.js') }}"></script>
    @stack('scripts')

    <script>
        $(document).ready(function () {

        function isMobile() { return window.innerWidth < 768; }

        // Hamburger toggle
        $('.button-menu-mobile').on('click', function (e) {
            e.stopImmediatePropagation();
            if (isMobile()) {
                $('body').toggleClass('sidebar-open');
            } else {
                $('body').toggleClass('sidebar-collapsed');
            }
        });

        // Close sidebar when clicking backdrop (mobile)
        $('#sidebarBackdrop').on('click', function () {
            $('body').removeClass('sidebar-open');
        });

        // On resize: clean up stale classes
        $(window).on('resize', function () {
            if (!isMobile()) {
                $('body').removeClass('sidebar-open');
            } else {
                $('body').removeClass('sidebar-collapsed');
            }
        });

        // Auto-collapse on mobile on page load
        if (isMobile()) {
            $('body').removeClass('sidebar-collapsed');
        }

        // Tablet hover: temporarily expand icon-only sidebar
        function isTablet() { return window.innerWidth >= 768 && window.innerWidth <= 991; }

        $('.left-side-menu').on('mouseenter', function () {
            if (isTablet() && !$('body').hasClass('sidebar-collapsed')) {
                $(this).addClass('sidebar-hovered');
            }
        }).on('mouseleave', function () {
            $(this).removeClass('sidebar-hovered');
        });
    });
    </script>
</body>

</html>