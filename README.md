SpBowerBundle
=============

The SpBowerBundle adds support for handling asset dependencies with bower in a nice way.

Features included:

- Install bower packages in your bundles with one command
- Register all installed bower packages as assets so they can be used in templates
- Unit tested

**Caution:** This bundle is developed in sync with [symfony's repository](https://github.com/symfony/symfony).

:warning: This Project Has Been Deprecated :warning:
====================================================

Thank you for your interest in contributing to this bundle. Unfortunately, **I
do not plan to continue maintaining this bundle.**

With symfony 3.0 assetic was removed (at least in the standard edition) and some time 
later the new [Webpack Encore](https://github.com/symfony/webpack-encore) 
component was announced, thus (and the fact that I stopped using bower myself a while ago)
I will no longer maintain this bundle.

Feel free to use this bundle as-is but I strongly recommend using other tools
for asset management (like grunt, gulp or webpack). If you have a project depending on this 
bundle, I would recommend on migrating it to something else.

If someone wants to take over this bundle, let me know and I will gladly transfer the 
ownership.

Thanks for your support and using this bundle over the years.

---

### Bower >= 1.0.0

Since version [0.8](https://github.com/Spea/SpBowerBundle/releases/tag/v0.8) the bundle fully supports bower `>= 1.0.0`.
If you used version 0.7 before, you might want to read the [upgrade instructions to 0.8](Upgrade.md#07-to-08).

### Bower < 1.0.0

If you are still using a bower version `< 1.0.0`, you must use a Version lower or equal to
[0.7](https://github.com/Spea/SpBowerBundle/releases/tag/v0.7) from this bundle.

[![Build Status](https://secure.travis-ci.org/Spea/SpBowerBundle.png?branch=master)](https://travis-ci.org/Spea/SpBowerBundle) [![Latest Stable Version](https://poser.pugx.org/sp/bower-bundle/v/stable.png)](https://packagist.org/packages/sp/bower-bundle) [![Total Downloads](https://poser.pugx.org/sp/bower-bundle/downloads.png)](https://packagist.org/packages/sp/bower-bundle)

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
