Fxp Require Asset
=================

[![Latest Stable Version](https://poser.pugx.org/fxp/require-asset/v/stable.svg)](https://packagist.org/packages/fxp/require-asset)
[![Latest Unstable Version](https://poser.pugx.org/fxp/require-asset/v/unstable.svg)](https://packagist.org/packages/fxp/require-asset)
[![Build Status](https://travis-ci.org/francoispluchino/fxp-require-asset.svg)](https://travis-ci.org/francoispluchino/fxp-require-asset)
[![Coverage Status](https://coveralls.io/repos/francoispluchino/fxp-require-asset/badge.png)](https://coveralls.io/r/francoispluchino/fxp-require-asset)
[![Scrutinizer Code Quality](https://scrutinizer-ci.com/g/francoispluchino/fxp-require-asset/badges/quality-score.png)](https://scrutinizer-ci.com/g/francoispluchino/fxp-require-asset)
[![SensioLabsInsight](https://insight.sensiolabs.com/projects/43b207f9-6d4c-4d99-927d-e7bbd710d6ee/mini.png)](https://insight.sensiolabs.com/projects/43b207f9-6d4c-4d99-927d-e7bbd710d6ee)

The Fxp Require Asset is a manager for the required assets. It allows to define the
required assets (javascript, stylesheet) directly in the Twig template and adds the HTML
links of the assets automatically to the right place in the template, while removing
duplicates.

##### Features include:

- Filter the copy of the assets of each packages by:
  - file extensions (and debug mode)
  - glob patterns
- Configure:
  - the assetic filters of asset package by the extensions
  - the assetic filters for all asset packages
  - the custom asset package
  - the rewrite output path of asset
- Compiling the final list of asset in cache for increase performance
- Assetic filters:
  - `requirecssrewrite`: for rewrite the url of another require asset in css file
- Twig extension for Automatically move all inline:
  - javascript in the same place defined in the twig base template
  - stylesheet in the same place defined in the twig base template

Documentation
-------------

The bulk of the documentation is located in the `Resources/doc/index.md`:

[Read the Documentation](Resources/doc/index.md)

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
