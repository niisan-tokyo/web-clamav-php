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

$manager = new Niisan\ClamAV\Manager(['url' => 'clamav']);

$manager->ping();
echo $manager->getMessage(), "\n";

if (! $manager->scan(__DIR__ . '/README.md')) {
    echo 'A Virus is exists!!';
}
echo $manager->getMessage(), "\n";
```

And then we get the following result.

```
PONG

stream: OK
```

If a file have some virus, `Niisan\ClamAV\Manager::scan` return false.