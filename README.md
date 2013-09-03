SpBowerBundle
=============

The SpBowerBundle adds support for handling asset dependencies with bower in a nice way.

Features included:

- Install bower packages in your bundles with one command
- Register all installed bower packages as assets so they can be used in templates
- Unit tested

**Caution:** This bundle is developed in sync with [symfony's repository](https://github.com/symfony/symfony).

**Note:** The master branch only works if you are using bower >= 1.0.0. For bower < 1.0.0 you must use <= 0.7 from this bundle. Also take a look at the [upgrade instructions](Upgrade.md)

**Note:** The configuration for this bundle slightly changed from 0.1 to 0.2, please take a look at the [upgrade notes](https://github.com/Spea/SpBowerBundle/blob/master/Upgrade.md)

[![Build Status](https://secure.travis-ci.org/Spea/SpBowerBundle.png?branch=master)](https://travis-ci.org/Spea/SpBowerBundle)

Documentation
-------------

[Read the Documentation for master](https://github.com/Spea/SpBowerBundle/blob/master/Resources/doc/index.md)

[Read the Documentation for 0.1](https://github.com/Spea/SpBowerBundle/blob/v0.1/Resources/doc/index.md)

Installation
------------

All the installation instructions are located in the [documentation](https://github.com/Spea/SpBowerBundle/blob/master/Resources/doc/index.md).

License
-------

This bundle is under the MIT license. See the complete license in the bundle:

    Resources/meta/LICENSE

Reporting an issue or a feature request
---------------------------------------

Issues and feature requests are tracked in the [Github issue tracker](https://github.com/Spea/SpBowerBundle/issues).

When reporting a bug, it may be a good idea to reproduce it in a basic project
built using the [Symfony Standard Edition](https://github.com/symfony/symfony-standard)
to allow developers of the bundle to reproduce the issue by simply cloning it
and following some steps.
