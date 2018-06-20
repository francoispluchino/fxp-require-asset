Fxp Require Asset
=================

[![Latest Version](https://img.shields.io/packagist/v/fxp/require-asset.svg)](https://packagist.org/packages/fxp/require-asset)
[![Build Status](https://img.shields.io/travis/fxpio/fxp-require-asset/master.svg)](https://travis-ci.org/fxpio/fxp-require-asset)
[![Coverage Status](https://img.shields.io/coveralls/fxpio/fxp-require-asset/master.svg)](https://coveralls.io/r/fxpio/fxp-require-asset?branch=master)
[![Scrutinizer Code Quality](https://img.shields.io/scrutinizer/g/fxpio/fxp-require-asset/master.svg)](https://scrutinizer-ci.com/g/fxpio/fxp-require-asset?branch=master)
[![SensioLabsInsight](https://img.shields.io/sensiolabs/i/43b207f9-6d4c-4d99-927d-e7bbd710d6ee.svg)](https://insight.sensiolabs.com/projects/43b207f9-6d4c-4d99-927d-e7bbd710d6ee)

The Fxp Require Asset is a helper for twig to manage automatically the required assets
with Webpack. It allows to define the required assets (script, style) directly
in the Twig template and adds the HTML links of the assets automatically to the
right place in the template, while removing duplicates.

##### Features include:

- Compatible with [Webpack](https://webpack.js.org) and source maps (require the plugin
  [webpack-manifest-plugin](https://github.com/danethurber/webpack-manifest-plugin) or
  [assets-webpack-plugin](https://github.com/kossnocorp/assets-webpack-plugin))
- Configure:
  - the locale asset defined by each entry
  - the replacement of assets by other assets
- Compiling the final list of asset in cache to increase performance for `assets-webpack-plugin`
- Twig features:
  - possibility to defined the asset in one or more template Twig:
    - one only link will be added in the final Twig template (no duplicates)
    - the link will be placed in the right place in the final Twig template (defined in the twig base template)
    - the generated link will corresponding to the link defined by the asset target path in Webpack Manifest/Assets
    - ability to define an require asset as an optional
    - automatically add the localized assets after the assets, without duplication
  - tags:
    - `require_script`: for require a script and inject the link in the good place defined in the twig base template
    - `require_style`: for require a style and inject the link in the good place defined in the twig base template
    - `inline_script`: for automatically move all inline script in the same place defined in the twig base template
    - `inline_style`: for automatically move all inline style in the same place defined in the twig base template
  - functions:
    - `requireScriptsPosition`: to position the require scripts in the Twig template
    - `requireStylesPosition`: to position the require styles in the Twig template
    - `inlineScriptsPosition`: to position the inline scripts in the Twig template
    - `inlineStylesPosition`: to position the inline styles in the Twig template

Documentation
-------------

The bulk of the documentation is located in the `Resources/doc/index.md`:

[Read the Documentation](Resources/doc/index.md)

[Read the Release Notes](https://github.com/fxpio/fxp-require-asset/releases)

Installation
------------

All the installation instructions are located in [documentation](Resources/doc/index.md).

License
-------

This library is under the MIT license. See the complete license in the bundle:

[LICENSE](LICENSE)

About
-----

Fxp Require Asset is a [François Pluchino](https://github.com/francoispluchino) initiative.
See also the list of [contributors](https://github.com/fxpio/fxp-require-asset/contributors).

Reporting an issue or a feature request
---------------------------------------

Issues and feature requests are tracked in the [Github issue tracker](https://github.com/fxpio/fxp-require-asset/issues).
