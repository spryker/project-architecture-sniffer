# Project Architecture Sniffer

[![Minimum PHP Version](https://img.shields.io/badge/php-%3E%3D%208.0-8892BF.svg)](https://php.net/)
[![License](https://poser.pugx.org/spryker/architecture-sniffer/license.svg)](https://packagist.org/packages/spryker/architecture-sniffer)

[//]: # ([![Total Downloads]&#40;https://poser.pugx.org/spryker/architecture-sniffer/d/total.svg&#41;]&#40;https://packagist.org/packages/spryker/architecture-sniffer&#41;)

Architecture Sniffer for Spryker Projects. Over `80` rules adapted for Spryker Projects.


## Priority Levels


- `1`: Сritical (stop it)

- `2`: Major (it is not a Spryker project)

- `3`: Medium (better to have)

- `4`: Minor (nice to have)

- `5`: Spryker Core (code matches Spryker Architecture Rules and even more)


We recommend minimum priority `3` by default for local and CI checks.


Note: Lower priorities (higher numbers) always include the higher priorities (lower numbers).

## Content

- `36` adapted [PHPMD rules](documentations/PHPMDrules.md)
- `39` adapted [Spryker Architecture sniffer rules](documentations/SPRYKERrules.md)
- `10` new [Project Architecture sniffer rules](documentations/PROJECTrules.md)

## Usage

Make sure you include the sniffer as `require-dev` dependency:
```
composer require --dev spryker/project-architecture-sniffer:dev-main
```

### Spryker Usage

```php
namespace Pyz\Zed\Development;

class DevelopmentConfig extends \Spryker\Zed\Development\DevelopmentConfig
{
    public function getArchitectureSnifferRuleset(): string
    {
        $vendorDir = APPLICATION_VENDOR_DIR . DIRECTORY_SEPARATOR;

        return $vendorDir . 'spryker/project-architecture-sniffer/src/ruleset.xml';
    }
}
```

When using Spryker you can use the Spryker CLI console command for it:

```

console code:sniff:architecture [-m ModuleName] [optional-sub-path] -v [-p priority]

```

Verbose output is recommended here.


### Manual Usage

You can also manually run the Project Architecture Sniffer from console by using:

```

vendor/bin/phpmd src/Pyz/ (xml|text|html) vendor/spryker/project-architecture-sniffer/src/ruleset.xml --minimumpriority 2

```

### Local Code Review Usage

```

vendor/bin/phpmd src json vendor/spryker/project-architecture-sniffer/src/ruleset.xml --minimumpriority 4 --reportfile results.json

cp vendor/spryker/project-architecture-sniffer/tools/script.php script.php

php script.php

```

### Debugging

```
docker/sdk cli -x

PHPMD_ALLOW_XDEBUG=true vendor/bin/phpmd src/Pyz/ (xml|text|html) vendor/spryker/project-architecture-sniffer/src/ruleset.xml --minimumpriority 2

```

## Roadmap (expected rules)
feel free to suggest

## Writing new sniffs

Add them to inside src/Project folder with the same folder structure.

Don't forget to update `ruleset.xml`.

Every sniff needs to implement either the `ClassAware`, `FunctionAware`, `InterfaceAware`, or `MethodAware` interface to be recognised.

### Setup

Run

```

composer install

```


### Testing

no testing at this moment

### Running code-sniffer on this project

Make sure this repository is Spryker coding standard conform:

```

composer cs-check

```

If you want to fix the fixable errors, use

```

composer cs-fix

```

If you want to run phpstan

```

composer stan

```

Once everything is green you can make a PR with your changes.
