---
title: Contributing
---

# Contributing

* Coding standard for the project is [PSR-12](https://github.com/php-fig/fig-standards/blob/master/accepted/PSR-12-extended-coding-style-guide.md)
* Any contribution must provide tests for additional introduced conditions
* Any un-confirmed issue needs a failing test case before being accepted
* Pull requests must be sent from a new hotfix/feature branch, not from `main`.

## Installation

To install the project and run the tests, you need to clone it and run a composer install.

## Testing

The PHPUnit version to be used is the one installed as a dev-dependency via composer:

```sh
$ composer test
```

Please ensure all new features or conditions are covered by unit tests.