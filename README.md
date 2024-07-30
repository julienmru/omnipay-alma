# Omnipay: Alma

**Alma driver for the Omnipay PHP payment processing library**

[Omnipay](https://github.com/thephpleague/omnipay) is a framework agnostic, multi-gateway payment
processing library for PHP 5.6+. This package implements Alma support for Omnipay.

## Install

Instal the gateway using require. Require the `league/omnipay` base package and this gateway.

``` bash
$ composer require league/omnipay julienmru/omnipay-alma
```

## Usage

The following gateways are provided by this package:

 * Alma

Alma requires to specify either shipping or billing address when submitting the payment which you can send in the `CreditCard` object.

You may check if your cart is eligible to Atma using the `eligibility` method and checking `isSuccessful` in the response.

For general usage instructions, please see the main [Omnipay](https://github.com/thephpleague/omnipay) repository.

## Support

If you believe you have found a bug, please report it using the [GitHub issue tracker](https://github.com/julienmru/omnipay-alma/issues),
or better yet, fork the library and submit a pull request.

## Contributing

Please see [CONTRIBUTING](CONTRIBUTING.md) for details.

## Security

If you discover any security related issues, please email julien@cari.agency instead of using the issue tracker.

## Credits

- [Julien Tessier](https://github.com/julienmru)

## License

The MIT License (MIT). Please see [License File](LICENSE.md) for more information.
