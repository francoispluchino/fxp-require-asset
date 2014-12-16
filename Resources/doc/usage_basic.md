Basic usage
===========

### Prerequisites

The `RequireAssetManager` works only with Assetic `LazyAssetManager` (not the basic
`AssetManager`), because it uses the resource loading and the filters.

For create a new Assetic `LazyAssetManager`:

```php
<?php

use Assetic\Factory\AssetFactory;
use Assetic\Factory\LazyAssetManager;

$factory = new AssetFactory('web');
$lam = new LazyAssetManager($factory);
```

### Add a configuration by default

You can set a default configuration that will be added to each package configuration.

#### Add a configuration by default for a file extension

You can set a default configuration for a file extension:

```php
<?php

use Fxp\Component\RequireAsset\Assetic\RequireAssetManager;

$ram = new RequireAssetManager();

$ram->getFileExtensionManager()
    ->addDefaultExtension('js')
    ->addDefaultExtension('css');
```

**Arguments available:**

| Name        | Type   | Default value | Description                                                       |
|-------------|--------|---------------|-------------------------------------------------------------------|
| `name`      | string |               | The name of file extension                                        |
| `options`   | array  | `[]`          | The assetic formulae options (`debug` option will always `true`)  |
| `filters`   | array  | `[]`          | The assetic formulae filters                                      |
| `extension` | string | `null`        | The output file extension, by default, the current file extension |
| `debug`     | bool   | `false`       | Include the file extension only in debug mode                     |
| `exclude`   | bool   | `false`       | Exclude the file extension                                        |

##### Add an existing configuration of the file extensions

There is an existing configuration of file extensions to filter files compatible with
the internet browsers.

```php
<?php

use Fxp\Component\RequireAsset\Assetic\RequireAssetManager;
use Fxp\Component\RequireAsset\Assetic\Util\FileExtensionUtils;

$ram = new RequireAssetManager();

$ram->getFileExtensionManager()->addDefaultExtensions(FileExtensionUtils::getDefaultConfigs());
```

With this list, only files below are copied:

- `map` (only when assetic debug is active)
- `js`
- `css`
- `eot`
- `svg`
- `ttf`
- `woff`
- `jpg`
- `jpeg`
- `png`
- `webp`
- `mp3`
- `aac`
- `wav`
- `ogg`
- `webm`
- `mp4`
- `ogv`

##### Configure the default filters

You can use all filters of Assetic. For example, `yuicompressor` in `debug`
mode, and `lessphp` for the `less` files (with changing name of file extension):

```php
<?php

use Fxp\Component\RequireAsset\Assetic\RequireAssetManager;

$ram = new RequireAssetManager();

$ram->getFileExtensionManager()
    ->addDefaultExtension('js',   array(), array('?yui_js')))
    ->addDefaultExtension('less', array(), array('lessphp'), 'css'));
```

#### Add a default list of patterns for to filter files

In addition to copy only files with the configured file extension, You can filter
files more precisely, using a pattern in the `Glob` format.

> The `!` allow you to exclude all assets matched by the pattern.

**Example:**

```php
<?php

use Fxp\Component\RequireAsset\Assetic\RequireAssetManager;

$ram = new RequireAssetManager();

$ram->getPatternManager()
    ->addDefaultPattern('*')
    ->addDefaultPattern('!test/*')
    ->addDefaultPattern('!bin/*');
```

This example will copy all files whose the file extension is allowed, but to exclude the folders
`test` and `bin`.

**Arguments available:**

| Name        | Type   | Default value | Description        |
|-------------|--------|---------------|--------------------|
| `pattern`   | string |               | The `Glob` pattern |

### Add an configuration of asset package

To add a configuration for an asset package, you need to use a `ConfigPackage` and
add it in  `PackageManager`.

```php
<?php

use Fxp\Component\RequireAsset\Assetic\Config\ConfigPackage;
use Fxp\Component\RequireAsset\Assetic\RequireAssetManager;

$ram = new RequireAssetManager();
$configPackage = new ConfigPackage('foo_bar', __DIR__ . '/vendor/assets/foobar');

$ram->getPackageManager()->addPackage($configPackage);
```

**Constructor arguments available:**

| Name         | Type   | Default value | Description                                                   |
|--------------|--------|---------------|---------------------------------------------------------------|
| `name`       | string |               | The package name compatible with the name of Assetic formulae |
| `sourcePath` | string | `null` °      | The package name compatible with the name of Assetic formulae |
| `sourceBase` | string | `null`        | The source base (useful to rename the package in the output)  |

> ° The argument is required on at least one configuration of a same package

### Define multiple configurations for a same package

You can define multiple configurations for a same package, this configuration will be
merged in the order of addition.

**Example:**

```php
<?php

use Fxp\Component\RequireAsset\Assetic\Config\ConfigPackage;
use Fxp\Component\RequireAsset\Assetic\RequireAssetManager;

$ram = new RequireAssetManager();
$configPackage = new ConfigPackage('foo_bar');
$configPackageBis = new ConfigPackage('foo_bar', __DIR__ . '/vendor/assets/foobar');

$ram->getPackageManager()
    ->addPackage($configPackage)
    ->addPackage($configPackageBis);
```

### Configure an asset package

The configuration of a package allow to configure compatible file extensions (filtres,
options, debug mode, exclusion), and to configure the patterns for to filter files.
The configurations are identical to the default configurations, but it applies only to
the assets of the package.

#### Configure a file extension

You can use all filters of Assetic. For example, `yuicompressor` in `debug`
mode, and `lessphp` for the `less` files (with changing name of file extension):

```php
<?php

use Fxp\Component\RequireAsset\Assetic\Config\ConfigPackage;
use Fxp\Component\RequireAsset\Assetic\RequireAssetManager;

$ram = new RequireAssetManager();
$configPackage = new ConfigPackage('foo_bar', __DIR__ . '/vendor/assets/foobar');
$configPackage
    ->addExtension('js', array(), array('?yui_js'))
    ->addExtension('less', array(), array('lessphp'), 'css');

$ram->getPackageManager()->addPackage($configPackage);
```

**Arguments available:**

| Name        | Type   | Default value | Description                                                       |
|-------------|--------|---------------|-------------------------------------------------------------------|
| `name`      | string |               | The name of file extension                                        |
| `options`   | array  | `[]`          | The assetic formulae options (`debug` option will always `true`)  |
| `filters`   | array  | `[]`          | The assetic formulae filters                                      |
| `extension` | string | `null`        | The output file extension, by default, the current file extension |
| `debug`     | bool   | `false`       | Include the file extension only in debug mode                     |
| `exclude`   | bool   | `false`       | Exclude the file extension                                        |

#### Configure a filter pattern

In addition to copy only files with the configured file extension, You can filter
files more precisely, using a pattern in the `Glob` format.

> The `!` allow you to exclude all assets matched by the pattern.

**Example:**

```php
<?php

use Fxp\Component\RequireAsset\Assetic\Config\ConfigPackage;
use Fxp\Component\RequireAsset\Assetic\RequireAssetManager;

$ram = new RequireAssetManager();
$configPackage = new ConfigPackage('foo_bar', __DIR__ . '/vendor/assets/foobar');
$configPackage
    ->addPattern('*')
    ->addPattern('!test/*')
    ->addPattern('!bin/*');

$ram->getPackageManager()->addPackage($configPackage);
```

This example will copy all files whose the file extension is allowed, but to exclude the folders
`test` and `bin`.

#### Do not include the default configuration

By default, the default configuration for file extensions and patterns for to filter
the assets are automatically added to the configuration package (always placed first).
But it is possible not to include the default settings for a package:

```php
<?php

use Fxp\Component\RequireAsset\Assetic\Config\ConfigPackage;
use Fxp\Component\RequireAsset\Assetic\RequireAssetManager;

$ram = new RequireAssetManager();
$configPackage = new ConfigPackage('foo_bar', __DIR__ . '/vendor/assets/foobar');
$configPackage
    ->setReplaceDefaultExtensions(true)
    ->setReplaceDefaultPatterns(true);

$ram->getPackageManager()->addPackage($configPackage);
```

### Add common asset (formulae)

You can create common assets, which are reality the assetic formulae dedicated to the require assets:

```php
<?php

use Fxp\Component\RequireAsset\Assetic\RequireAssetManager;

$ram = new RequireAssetManager();
// configure your packages of your require assets

$input = array(
    '@asset/source/path.js',
    '@asset/source/path2.js',
);
$ram->addCommonAsset('common_js', $inputs, '/common.js', array('?compiler', array('debug' => true));
```

### Add localized asset

You can add localized assets. Fir this, you must add each localized asset in the Require Asset
Manager, and set the locale of each asset in the Locale Manager:

```php
<?php

use Fxp\Component\RequireAsset\Assetic\RequireAssetManager;

$ram = new RequireAssetManager();
$lm = $ram->getLocaleManager();
// configure your packages of your require assets

$lm->addLocaliszedAsset('@package1/asset1.js', 'fr_fr', '@package1/locale/asset1-fr-fr.js');
$lm->addLocaliszedAsset('@package1/asset1.js', 'fr_ca', '@package1/locale/asset1-fr-ca.js');
$lm->addLocaliszedAsset('@package1/asset1.js', 'fr', '@package1/locale/asset1-fr.js');

$lm->addLocaliszedAsset('@package2/asset1.js', 'it', array(
    '@package2/locale/asset1-it-part1.js',
    '@package2/locale/asset1-it-part2.js',
));
```

### Add localized common asset

By default, the localized common assets are automatically added. Each localized common
asset is added for each local available for each input.

However, you can manually add or overwrite the configuration.

For override the localized config of an common asset, you must named the localized
common asset with the name of common asset, followed by `__` and the locale in lowercase
(`fr` or `fr_fr`):

```php
<?php

use Fxp\Component\RequireAsset\Assetic\RequireAssetManager;

$ram = new RequireAssetManager();
// configure your packages of your require assets

$inputs = array(
    '@asset/source/path.js',
    '@asset/source/path2.js',
);
$ram->addCommonAsset('common_js', $inputs, '/common.js', array('?compiler', array('debug' => true));

$localeInputs = array(
    '@asset/source/path-fr-fr.js',
);
$ram->addCommonAsset('common_js__fr_fr', $localeInputs, '/common-fr-fr.js', array('?compiler', array('debug' => true));
$ram->getLocaleManager()->addLocaliszedAsset('common_js', 'fr_fr', 'common_js__fr_fr');
```

### Add asset resources in Assetic Lazy Asset Manager

When all configuration of asset packages is made, you must add all assets in Assetic
`LazyAssetManager`. For this, it's very simple, just call the method `addAssetResources`:

```php
<?php

use Assetic\AssetWriter;
use Assetic\Factory\AssetFactory;
use Assetic\Factory\LazyAssetManager;
use Fxp\Component\RequireAsset\Assetic\Config\ConfigPackage;
use Fxp\Component\RequireAsset\Assetic\RequireAssetManager;

$factory = new AssetFactory('web');
$lam = new LazyAssetManager($factory);
$ram = new RequireAssetManager();

// your configuration of asset packages

$ram->addAssetResources($lam);

// launch the assetic compilation
$writer = new AssetWriter('web');
$writer->writeManagerAssets($lam);
```

The `RequireAssetManager` will search and filter all the compatible assets, configure each
Assetic Resource (which is in each case a single file) with the good configuration, and add
each resource in Assetic `LazyAssetManager`.

### Rewrite the output path of asset files

You can completely change the target path of each asset via a list of `Glob` pattern. In this way,
you master the directory of the asset, but also his name, as her name of file extension.

Beware, each pattern will be executed in order for each file.

**Example:**

```php
<?php

use Fxp\Component\RequireAsset\Assetic\RequireAssetManager;

$ram = new RequireAssetManager();

$ram->getOutputManager()->addOutputPattern('*/less/*', '*/css/*');
```

This example will rename all directories `/less/` to `/css/`.

#### Using variables in the output pattern

You can use the ordered variables in the pattern of public output, if your pattern rewriting using
the wildcard (`*`). However, The wildcards used in first and last position are not considered.

**Example:**

```php
<?php

use Fxp\Component\RequireAsset\Assetic\RequireAssetManager;

$ram = new RequireAssetManager();

$ram->getOutputManager()->addOutputPattern('acmedemo/*/*.css', 'css/$1.css');
```

In this example, the variable `$0` is the folder name, and the variable `$1` is the file name.

### Rewrite the url reference in the CSS assets

By default, Assetic has the filter `cssrewrite` to do this work, but alas, it may not be compatible
with the system of rewriting the public output of the asset.

For get the good path of the asset, you must use the filter `RequireCssRewriteFilter`.

**Example of configuration for used the Require Css Rewrite Filter:**

```php
<?php

use Assetic\Factory\AssetFactory;
use Assetic\Factory\LazyAssetManager;
use Assetic\FilterManager;
use Fxp\Component\RequireAsset\Assetic\RequireAssetManager;

$fm = new FilterManager();
$factory = new AssetFactory('web');
$factory->setFilterManager($fm);
$lam = new LazyAssetManager($factory);
$ram = new RequireAssetManager();

$fm->set('requirecssrewrite', new RequireCssRewriteFilter($lam));

$ram->getFileExtensionManager()
    ->addDefaultExtension('css', array(), array('requirecssrewrite'));
```

### Using cache of the search of assets

To avoid to search and create the Assetic Resources to each execution, you can use a
cache. In this way, if the Assetic Resources are in the cache, the `RequireAssetManager`
will retrieve this list.

**Example:**

```php
<?php

use Assetic\AssetWriter;
use Assetic\Factory\AssetFactory;
use Assetic\Factory\LazyAssetManager;
use Fxp\Component\RequireAsset\Assetic\Cache\RequireAssetCache;
use Fxp\Component\RequireAsset\Assetic\RequireAssetManager;

$rac = new RequireAssetCache('cache');
$ram = new RequireAssetManager();

$ram->setCache($rac);

// your configuration of asset packages

// assetic lazy asset manager
$factory = new AssetFactory('web');
$lam = new LazyAssetManager($factory);

// add require assets in lazy asset manager
$ram->addAssetResources($lam);

// launch the assetic compilation
$writer = new AssetWriter('web');
$writer->writeManagerAssets($lam);
```

You can create your own Cache using the interface
`Fxp\Component\RequireAsset\Assetic\Cache\RequireAssetCacheInterface`.

#### Do not configure the Require Asset Manager to each execution

In addition to not do the search and the creation of the Assetic Resources, it is
also interesting to not relaunch the configuration of packages. For this, you can
use the method `hasResources` in the cache.

**Example:**

```php
<?php

use Assetic\AssetWriter;
use Assetic\Factory\AssetFactory;
use Assetic\Factory\LazyAssetManager;
use Fxp\Component\RequireAsset\Assetic\Cache\RequireAssetCache;
use Fxp\Component\RequireAsset\Assetic\RequireAssetManager;

$rac = new RequireAssetCache('cache');
$ram = new RequireAssetManager();
$ram->setCache($rac);

if (!$rac->hasResources()) {
    // your configuration of asset packages
}

// assetic lazy asset manager
$factory = new AssetFactory('web');
$lam = new LazyAssetManager($factory);

// add require assets in lazy asset manager
$ram->addAssetResources($lam);

// launch the assetic compilation
$writer = new AssetWriter('web');
$writer->writeManagerAssets($lam);
```

#### Cache invalidation

To invalidate the cache, you must call the method `invalidate` in the cache.

**Example:**

```php
<?php

use Fxp\Component\RequireAsset\Assetic\Cache\RequireAssetCache;
use Fxp\Component\RequireAsset\Assetic\RequireAssetManager;

$rac = new RequireAssetCache('cache');
$ram = new RequireAssetManager();
$ram->setCache($rac);

$ram->getCache()->invalidate();

// ...
```
