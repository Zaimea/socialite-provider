---
title: Examples
description: Examples for Zaimea Socialite Provider
github: https://github.com/zaimea/socialite-provider/edit/main/docs/
onThisArticle: true
sidebar: true
rightbar: true
---

# Examples

## Basic Login Flow

### 1. Add service config in `config/services.php`

```php
'zaimea' => [
    'client_id' => env('ZAIMEA_CLIENT_ID'),
    'client_secret' => env('ZAIMEA_CLIENT_SECRET'),
    'redirect' => env('ZAIMEA_REDIRECT_URI', 'http://zaimea.com/auth/callback'),
    'version' => env('ZAIMEA_VERSION', 'v1'),
],
```

### 2. Add routes in `web.php`

```php
Route::get('/auth/redirect', function () {
    return Socialite::driver('zaimea')->redirect();
});

Route::get('/auth/callback', function () {
    $user = Socialite::driver('zaimea')->user();

    dd($user);
});
```

## Login + Store User

```php
Route::get('/auth/callback', function () {
    $zaimeaUser = Socialite::driver('zaimea')->user();

    $user = User::updateOrCreate([
        'email' => $zaimeaUser->email,
    ], [
        'name' => $zaimeaUser->name,
        'avatar' => $zaimeaUser->avatar,
    ]);

    Auth::login($user);

    return redirect('/dashboard');
});
```

## Getting User Token

```php
$token = $zaimeaUser->token;
```

## Getting Refresh Token

```php
$refresh = $zaimeaUser->refreshToken;
```

## Custom Scopes

```php
Socialite::driver('zaimea')
    ->scopes(['user'])
    ->redirect();
```

## Example in Controller

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

        dd($user->id, $user->email);
    }
}
```

## Example in Livewire

```php
public function login()
{
    return redirect()->away(Socialite::driver('zaimea')->redirect()->getTargetUrl());
}
```
