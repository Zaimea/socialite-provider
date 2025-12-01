---
title: How to use package
description: How to use package
github: https://github.com/zaimea/socialite-provider/edit/main/docs/
onThisArticle: true
sidebar: true
rightbar: true
---

# Zaimea Socialite Provider Usage

[[TOC]]

## Redirect user to login

```php
use Laravel\Socialite\Facades\Socialite;

return Socialite::driver('zaimea')->redirect();
```

## Handle callback

```php
$user = Socialite::driver('zaimea')->user();
```

Returned user object contains:

```php
$user->getId();
$user->getEmail();
$user->getName();
$user->getAvatar();
```

## Example inside controller

```php
class AuthController
{
    public function redirect()
    {
        return Socialite::driver('zaimea')->redirect();
    }

    public function callback()
    {
        $user = Socialite::driver('zaimea')->user();

        // your logic here
    }
}
```

## Logout (optional)

If your app supports central logout, redirect users to:

```
https://accounts.zaimea.com/logout
```

------------------------------------------------------------------------

## Support

For issues or suggestions: [GitHub Issues](https://github.com/zaimea/socialite-provider/issues)
