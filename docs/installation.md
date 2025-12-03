---
title: How to install package
description: How to install package
github: https://github.com/zaimea/socialite-provider/edit/main/docs/
onThisArticle: true
sidebar: true
rightbar: true
---

# Zaimea Socialite Provider

[[TOC]]

## Introduction

`zaimea/socialite-provider` is a custom Socialite provider which allows your Laravel applications to authenticate users through **accounts.zaimea.com**.

It works exactly like any other Socialite driver (GitHub, Google, GitLab, etc.).

- Supported: Laravel 12+
- Uses OAuth2 Authorization Code + PKCE
- Designed to be plug-and-play with Socialite.

## Instalation

You can install the package via composer:

```bash
composer require zaimea/socialite-provider
```

or via composer.json

```json
"repositories": [
        {
            "type": "vcs",
            "url": "https://github.com/zaimea/socialite-provider"
        }
    ]
```

### Add driver config

In `config/services.php`:

```php
'zaimea' => [
    'client_id' => env('ZAIMEA_CLIENT_ID'),
    'client_secret' => env('ZAIMEA_CLIENT_SECRET'),
    'redirect' => env('ZAIMEA_REDIRECT_URI'),
    'version' => env('ZAIMEA_VERSION', 'v1'),
],
```

### Add in `.env`:

```env
ZAIMEA_CLIENT_ID=your_client_id
ZAIMEA_CLIENT_SECRET=your_client_secret
ZAIMEA_REDIRECT_URI=https://your-app.com/auth/callback
ZAIMEA_VERSION=v1
```
