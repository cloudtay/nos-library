## Nos

> "PHP is a programming language, but PHP itself can be considered as a framework"

If you know PHP, you know everything about `Nos`.

### Install

```bash
composer require cloudtay/nos
```

### run your application

```php
NOS_APP_PATH=app vendor/bin/nos
```

### use Nos in your application

```php
use Cloudtay\Nos\Kernel;
use Cloudtay\Nos\Package;

// file monitor
Kernel::monitor();

// worker manager
Kernel::manager();

// package import
Package::import('module');

// launch
Kernel::manager()->run();
```
