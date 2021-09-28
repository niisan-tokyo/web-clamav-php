# Using clamav through web.

We can use clamav scan through remote network by this library.

Before using this library, we prepare a remote clamav server. For example, we can use a clamav container following:

```
docker run -d dinkel/clamavd
```

# Usage

Execute following code.

```php
require 'vendor/autoload.php';

$scanner = \Niisan\ClamAV\ScannerFactory::create([
    'driver' => 'remote',
    'url' => 'example.com'
]);

if (! $scanner->scan($_FILE['userfile']['tmp_name'])) {
    echo 'User file has a virus!';
}
```

If a file have some virus, `Scanner::scan` return false.

# config

When you want know a config that is an argument of ScannerFactory, See example.config.php.

You can select driver, which 'remote' or 'local'.
When yor select 'remote', the config need 'host' or 'remote.host' that means clamd host: following

```php
[
    'driver' => 'remote',
    'url' => 'examle.com'
];
```

```php
[
    'driver' => 'remote'
    'remote' => [
        'host' => 'example.com'
    ]
];
```

Or you select 'local', the config need 'path' or 'local.path', that means a unix socket of clamd.

```php
[
    'driver' => 'local',
    'path' => '/var/run/clamav/clamd.ctl'
]
```

# for developing
If you want to develop this package, some tests will fail for not starting clamd server.
So you command `clamd start` to start clamav daemon before testing. 