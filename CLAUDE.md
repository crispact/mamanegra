# CLAUDE.md

This file provides guidance to Claude Code (claude.ai/code) when working with code in this repository.

## Project overview

Website for La Comparsa de la Mama Negra, a traditional cultural festival in Latacunga, Ecuador (`mamanegra.com.ec`).

**Active project**: `app/` (Slim PHP 4 — migration target, PHP 8.2+)
**Legacy reference**: `laravel/` (Lumen 7 — kept for reference, no longer the active app)
`public_html_old/` holds the old web root assets; `public_html/` is the live web root.

## Directory layout

```
app/                    PHP app code (not web-accessible)
  bootstrap/app.php     Slim app factory — loads env, DI container, middleware, routes
  bootstrap/routes.php  All route definitions
  config/container.php  PHP-DI bindings (Twig, PDO, Twig functions)
  src/Controllers/      HomeController.php
  src/Models/           Post.php (PDO-based, FETCH_ASSOC)
  templates/            Twig templates
    layouts/base.html.twig
    partials/           top-header, sidebar, footer
    *.html.twig         one per page
  composer.json
  .env / .env.example

public_html/            Web root
  index.php             Front controller — bootstraps app/ and calls $app->run()
  .htaccess             mod_rewrite: HTTPS redirect + route to index.php
  assets/               Static files (copy from public_html_old/assets/)
```

## Setup

```bash
# From app/
composer install

# Copy env and fill in DB credentials
cp .env.example .env

# Copy static assets from old web root (one-time)
# cp -r public_html_old/assets public_html/assets
# cp public_html_old/favicon* public_html/
# cp public_html_old/apple-icon* public_html/
# cp public_html_old/android-icon* public_html/
# cp public_html_old/ms-icon* public_html/
# cp public_html_old/manifest.json public_html/
# cp public_html_old/robots.txt public_html/
# cp public_html_old/sitemap.xml public_html/
```

No build step — there is no frontend compilation in the new app (Bootstrap and JS are loaded from CDN).

## Architecture

**Framework**: Slim 4 with PHP-DI container and Twig templates (`slim/twig-view`).

**Request flow**: `public_html/index.php` → `app/bootstrap/app.php` → routes → `HomeController` → Twig render.

**Templates**: Twig inheritance via `{% extends 'layouts/base.html.twig' %}`. Partials are included with `{% include '...' with { var: val } %}`. The `body` column contains raw HTML — always render it with `{{ post.body|raw }}`.

**Database**: PDO with `FETCH_ASSOC`. The `posts` table has a hyphenated column `meta-description` — access it in PHP as `$post['meta-description']` and in Twig as `post['meta-description']`.

**Post URL rule**: if `op1` is `personajes` or `la-comparsa`, the URL is `/{op1}/{slug}`; otherwise `/{slug}`. This logic is duplicated in `HomeController::showPost()` and in the Twig templates (sidebar, search, article). The `share_link(path, network, title, image)` Twig function is registered in `config/container.php`.

**Slider photos**: `HomeController::sliderPhotos()` scans `public_html/assets/img/slider-img/` at runtime and picks 7 at random. The path is computed relative to `__DIR__` in the controller (3 levels up + `/public_html`).

## Database

MySQL database `mamanegra_jos`. Schema is in `mamanegra_jos.sql` at the repo root. Active tables: `posts` and `users` (users only for the legacy admin, not used in the new app).

`posts` columns: `id`, `title`, `slug`, `body`, `image`, `meta-description`, `meta-keywords`, `op1`–`op5`, `created_at`, `updated_at`.