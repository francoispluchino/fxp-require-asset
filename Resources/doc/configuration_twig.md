Twig configuration
==================

To use the Asset Require [Twig](http://twig.sensiolabs.org/) extension you must
register it to your Twig environment:

```php
<?php

use Fxp\Component\RequireAsset\Twig\Extension\CoreAssetExtension;

// twig environment
$loader = new Twig_Loader_Filesystem('/path/to/templates');
$twig = new Twig_Environment($loader, array(
    'cache' => '/path/to/compilation_cache',
));

// require asset twig extension
$twig->addExtension(new CoreAssetExtension('/path/to/webpack/manifest.json', 'manifest'));
```

If you prefer to create your asset extension from scratch,
you can use the `Fxp\Component\RequireAsset\Twig\Extension\AssetExtension` class directly.

```php
<?php

use Fxp\Component\RequireAsset\Twig\Extension\AssetExtension;

// twig environment
$loader = new Twig_Loader_Filesystem('/path/to/templates');
$twig = new Twig_Environment($loader, array(
    'cache' => '/path/to/compilation_cache',
));

// require asset twig extension
$twig->addExtension(new AssetExtension());
```
