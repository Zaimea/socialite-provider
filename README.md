<p align="center"><img src=".github/logo.svg" alt="Zaimea Provider" width="300">

<p align="center">
    <a href="https://github.com/zaimea/socialite-provider/actions"><img src="https://github.com/zaimea/socialite-provider/actions/workflows/tests/badge.svg" alt="Build Status"></a>
    <a href="https://packagist.org/packages/zaimea/socialite-provider"><img src="https://img.shields.io/packagist/dt/zaimea/socialite-provider" alt="Total Downloads"></a>
    <a href="https://packagist.org/packages/zaimea/socialite-provider"><img src="https://img.shields.io/packagist/v/zaimea/socialite-provider" alt="Latest Stable Version"></a>
    <a href="https://packagist.org/packages/zaimea/socialite-provider"><img src="https://img.shields.io/packagist/l/zaimea/socialite-provider" alt="License"></a>
</p>
<div align="center">
  Hey 👋 thanks for considering making a donation, with these donations I can continue working to contribute to zaimea projects.
  
  [![Donate](https://img.shields.io/badge/Via_PayPal-blue)](https://www.paypal.com/donate/?hosted_button_id=V6YPST5PUAUKS)
</div>

## Introduction

``Zaimea Socialite Provider`` .

## Official Documentation

Documentation for Zaimea Socialite Provider can be found on the [Zaimea website](https://zaimea.com/docs/socialite-provider).

## Contributing

Thank you for considering contributing to Socialite-Extender! The contribution guide can be found in the [Zaimea documentation](https://zaimea.com/docs/open-source/contributions).

## Code of Conduct

To ensure that the Zaimea open-source ecosystem remains welcoming and respectful, please review and follow our [Code of Conduct](https://zaimea.com/docs/open-source/contributions#code-of-conduct).

## Security Vulnerabilities

Please review [our security policy](https://github.com/zaimea/socialite-provider/security/policy) on how to report security vulnerabilities.

## Support

For issues or suggestions: [GitHub Issues](https://github.com/zaimea/socialite-provider/issues)

## License

Zaimea Socialite Provider is open-sourced software licensed under the [MIT license](LICENSE).


```php
<?php
return [
    'client_id' => env('ZAIMEA_CLIENT_ID'),
    'client_secret' => env('ZAIMEA_CLIENT_SECRET'),
    'redirect' => env('ZAIMEA_REDIRECT_URI'),
    'pkce' => env('ZAIMEA_PKCE', true),
    'scopes' => env('ZAIMEA_SCOPES', 'openid profile email')
];
```

