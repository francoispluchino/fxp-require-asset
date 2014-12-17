Fxp Require Asset
=================

[![Latest Version](https://img.shields.io/packagist/v/fxp/require-asset.svg)](https://packagist.org/packages/fxp/require-asset)
[![Build Status](https://travis-ci.org/francoispluchino/fxp-require-asset.svg)](https://travis-ci.org/francoispluchino/fxp-require-asset)
[![Coverage Status](https://img.shields.io/coveralls/francoispluchino/fxp-require-asset.svg)](https://coveralls.io/r/francoispluchino/fxp-require-asset?branch=master)
[![Scrutinizer Code Quality](https://scrutinizer-ci.com/g/francoispluchino/fxp-require-asset/badges/quality-score.png)](https://scrutinizer-ci.com/g/francoispluchino/fxp-require-asset)
[![SensioLabsInsight](https://insight.sensiolabs.com/projects/43b207f9-6d4c-4d99-927d-e7bbd710d6ee/mini.png)](https://insight.sensiolabs.com/projects/43b207f9-6d4c-4d99-927d-e7bbd710d6ee)

The Fxp Require Asset is a helper for assetic and twig to manage automatically the
required assets. It allows to define the required assets (script, style) directly
in the Twig template and adds the HTML links of the assets automatically to the
right place in the template, while removing duplicates.

##### Features include:

- Filter the copy of the assets of each packages by:
  - file extensions (and debug mode)
  - glob patterns
- Configure:
  - the assetic filters of asset package by the extensions
  - the assetic filters for all asset packages
  - the custom asset package
  - the rewrite output path of asset
  - the common assets (assetic formulae dedicated to the require assets)
  - the locale asset defined by each asset and/or common assets
- Automatic addition of localized commons assets
- Compiling the final list of asset in cache for increase performance
- Assetic filters:
  - `requirecssrewrite`: for rewrite the url of another require asset in css file
  - `lessvariable`: for inject the asset package paths as variables
- Twig features:
  - possibility to defined the asset in one or more template Twig:
    - reference to the source file of the asset in the Twig template, and not the target path of the asset defined in the Assetic Manager
    - one only link will be added in the final Twig template (no duplicates)
    - the link will be placed in the right place in the final Twig template (defined in the twig base template)
    - the generated link will corresponding to the link defined by the rewrite rules of assets (the asset target path in Assetic Manager)
    - automatically add the localized assets after the common assets or individual assets, without duplication
    - automatically add the localized assets for the inputs of the common asset, even if the common asset does not exist, or that it does not include all the localized inputs
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

[Read the Release Notes](https://github.com/francoispluchino/fxp-require-asset/releases)

Installation
------------

All the installation instructions are located in [documentation](Resources/doc/index.md).

License
-------

This library is under the MIT license. See the complete license in the bundle:

[Resources/meta/LICENSE](Resources/meta/LICENSE)

About
-----

Fxp Require Asset is a [François Pluchino](https://github.com/francoispluchino) initiative.
See also the list of [contributors](https://github.com/francoispluchino/fxp-require-asset/contributors).

Reporting an issue or a feature request
---------------------------------------

Issues and feature requests are tracked in the [Github issue tracker](https://github.com/francoispluchino/fxp-require-asset/issues).
