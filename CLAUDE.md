# Report Cards (Day 3 lab)

## Environment
- Mac, PHP 8.4, Laravel 13, MariaDB. Database `report_cards`, user `laravel`.
- Run the app with `php artisan serve` (http://localhost:8000). Tests: `php artisan test`.
- Styling uses Tailwind CSS through the Play CDN script tag in `layouts/app.blade.php`. No npm and no Vite.
- `public/css/school.css` stays only for custom rules that Tailwind utility classes do not cover.

## Architecture (the restaurant)
- Routes only in `routes/web.php`.
- Controllers (`app/Http/Controllers`) stay thin: get the models, call the service, return a view. No calculations.
- School rules live only in `app/Services/ReportCardService.php`.
- Models (`app/Models`) hold relationships and small helpers such as `fullName()`. No grading rules.
- Views (`resources/views`) only display data. No queries, no calculations. Every page extends `layouts/app.blade.php`. Style with Tailwind utility classes in the views.
- Change the database only with migrations. Never edit a migration that has already run; tell me instead.
- Load relationships with `with()` or `load()` so a page never runs one query per row.

## School rules
- General average: plain average of every grade that is not NULL, rounded to 2 decimals.
- Any NULL grade: remark INC. No grades at all: NO GRADES. Average 75 or above: PASSED, else FAILED.

## How to work with me
- I am learning Laravel. For every build request, show a short plan first and wait for my OK. Work in small steps.
- After each change, explain each new or changed file in 2 or 3 plain sentences, and tell me one command or URL to check it works.
- Ask before installing anything (`composer require`, npm, Laravel Boost) and before adding any other external CSS or JS (Tailwind via the Play CDN is already approved) and before running `php artisan migrate:fresh`.
- Do not edit `.env` or anything in `vendor/`.
