# CLAUDE.md

This file provides guidance to Claude Code (claude.ai/code) when working with code in this repository.

## Overview

AIDRC v2 is an internal Laravel 5.8 (PHP ^7.1.3) app for document change requests ("AIDRC applications") that runs as a module inside the company's **RapidX** portal. An originator submits an application with a PDF/Excel attachment. It then goes through DCC validation, QS inspection, head and e-sign approvals, and finally gets "for control" tracking of the affected documents. Notification emails are sent at each step.

The repo sits on a network share (`\\192.168.3.188\RapidX Systems\aidrc_v2`) and is served from the project root: `index.php` and `.htaccess` are at the root, not in `public/`. Asset URLs therefore include the `public/` prefix, e.g. `asset('public/images/...')`.

## Commands

```bash
composer install
php artisan serve                       # local dev server
php artisan migrate                     # only the default `mysql` connection (db_aidrc_v2) has migrations here
php artisan approval:send-reminder      # run the daily approver reminder manually
php artisan schedule:run                # scheduler: reminder runs daily 07:30 Asia/Manila, logs to storage/logs/scheduler.log
vendor/bin/phpunit                      # tests (only Laravel example tests exist)
vendor/bin/phpunit --filter testName    # single test
```

`npm run dev`/`prod` (Laravel Mix) only builds `resources/js/app.js` and `resources/sass/app.scss`, which the app barely uses. The real frontend JS lives in `public/js/my_js/*.js` and in inline `<script>` blocks in the Blade views. Neither needs a build step.

## Architecture

### Authentication is external (RapidX PHP session)
Laravel auth is not used. The layouts (`resources/views/layouts/admin_layout.blade.php`, `user_layout.blade.php`) call `session_start()` and read native `$_SESSION` keys that the RapidX portal sets: `rapidx_user_id`, `rapidx_user_level_id`, and `rapidx_user_accesses`, where `module_id == 2` grants AIDRC access. Unauthorized users are redirected to `../RapidX/`. Controllers also read `$_SESSION['rapidx_user_id']` directly. You can only exercise the app locally with a RapidX session present.

### Multiple databases
Each model sets `$connection` explicitly (see `config/database.php` and `.env`):
- `mysql` → `db_aidrc_v2`: this app's own tables (`applications`, `affected_documents`, `qs_validations`, `head_approvals`, `dcc_validations`, `application_revisions`, `section_heads`, `esign_approvers`, `patch_data_pdfs`, `external_applications`, `access_level`).
- `mysql_rapidx` → `db_rapidx`: RapidX `users` and `departments` (`RapidXUser`, `RapidXDepartment`).
- `mysql_rapid` → `db_acdcs` on 192.168.3.235: the ACDCS document control system (`AcdcsDocs` = `tbl_active_docs`, `AcdcsPendingDocuments` = `tbl_documents`).
- `mysql_rapid_users` → `db_rapid`: legacy `tbl_useraccounts` (`RapidUser`).

Cross-database relations (e.g. `Applications::originator_details` → `RapidXUser`) work through Eloquent eager loading, not SQL joins. Most tables use a `logdel` soft-delete flag (`where('logdel', 0)`). Newer tables use `deleted_at`.

### Controllers
Routes in `routes/web.php` are flat AJAX endpoints (`load_*` for GET/DataTables, `submit_*` for POST) that return JSON. Pages are rendered by closure routes returning views.
- `NewApplicationController`: the **current** application workflow (`submit_new_application`, `submit_new_*_validation/approval`, external applications, `send_new_mailer`, for-control emails). Put new workflow work here.
- `ApplicationController`: the older v1-style workflow. Many routes still point to it.
- `PdfController`: PDF handling with FPDI/TCPDF. It stamps e-signatures and patch data onto attachments (`edit_pdf_attachment`, `save_pdf_patch_data`) and serves downloads.
- `MasterListController`: master lists and Excel exports (`app/Exports`, maatwebsite/excel).
- `AccessLevelController`: user and role lists drawn from RapidX.
- Routes reference `UserController`, which does not exist in `app/Http/Controllers`. Those routes are dead.
- `Backups/` and `*_orig.php` files are kept as copies and are not wired into routing.

### Application status codes (`applications.status`)
These are inferred from `NewApplicationController`: `0` draft/returned to start, `1` submitted for DCC validation, `2` minor revision, `3` major revision, `4` for approval (e-sign approvers in `esign_approvers`, processed by ascending `approval_order`; per-approver status `0` pending, `1` approved, `2` disapproved), `7` approved and waiting for PDF edit, `9` for control. Check the controller before relying on the other codes (5, 6, 8). `application_type` `2` marks an external application, which has an `external_applications` row.

### Frontend
The views use the AdminLTE template (`public/template`), jQuery, DataTables (yajra server-side), and Bootstrap modals. `resources/views/dashboard.blade.php` (~4.5k lines) is the main page and holds most page logic inline. Shared JS is in `public/js/my_js/` (`Common.js`, `Application.js`, `Pdf.js`, `User.js`). Files such as `old_dashboard`, `dashboard_07072021` and `Common_orig.js` are legacy copies.

### Files and mail
Attachments are stored under `storage/app/public/file_attachments/`. Modified, signed PDFs get a `modified_` prefix. Mail templates are in `resources/views/mail/`. Mail is sent with `Mail::send` through the company SMTP server configured in `.env`.
