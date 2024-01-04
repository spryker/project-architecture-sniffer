# Project Architecture Sniffer

[//]: # ([![CI]&#40;https://github.com/spryker/architecture-sniffer/workflows/CI/badge.svg?branch=master&#41;]&#40;https://github.com/spryker/architecture-sniffer/actions/workflows/ci.yml&#41;)
[//]: # ([![Coverage]&#40;https://codecov.io/gh/spryker/architecture-sniffer/branch/master/graph/badge.svg?token=4AKCKMRg3G&#41;]&#40;https://codecov.io/gh/spryker/architecture-sniffer&#41;)
[//]: # ([![Latest Stable Version]&#40;https://poser.pugx.org/spryker/architecture-sniffer/v/stable.svg&#41;]&#40;https://packagist.org/packages/spryker/architecture-sniffer&#41;)
[![Minimum PHP Version](https://img.shields.io/badge/php-%3E%3D%208.0-8892BF.svg)](https://php.net/)
[![License](https://poser.pugx.org/spryker/architecture-sniffer/license.svg)](https://packagist.org/packages/spryker/architecture-sniffer)

[//]: # ([![Total Downloads]&#40;https://poser.pugx.org/spryker/architecture-sniffer/d/total.svg&#41;]&#40;https://packagist.org/packages/spryker/architecture-sniffer&#41;)

Architecture Sniffer for Spryker projects.


## Priority Levels


- `1`: Сritical (stop it)

- `2`: Major (it is not a Spryker project)

- `3`: Medium (better to have)

- `4`: Minor (nice to have)

- `5`: Spryker Core (code matches Spryker Architecture Rules and even more)


We recommend minimum priority `3` by default for local and CI checks.


Note: Lower priorities (higher numbers) always include the higher priorities (lower numbers).

## Usage

Make sure you include the sniffer as `require-dev` dependency:
```
composer require --dev vitaliiivanovspryker/project-architecture-sniffer:dev-main
```

### Spryker Usage

```php
namespace Pyz\Zed\Development;

class DevelopmentConfig extends \Spryker\Zed\Development\DevelopmentConfig
{
    public function getArchitectureSnifferRuleset(): string
    {
        $vendorDir = APPLICATION_VENDOR_DIR . DIRECTORY_SEPARATOR;

        return $vendorDir . 'vitaliiivanovspryker/project-architecture-sniffer/src/ruleset.xml';
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

vendor/bin/phpmd src/Pyz/ (xml|text|html) vendor/vitaliiivanovspryker/project-architecture-sniffer/src/ruleset.xml --minimumpriority 2

```

### Local Code Review Usage

```

vendor/bin/phpmd src json vendor/vitaliiivanovspryker/project-architecture-sniffer/src/ruleset.xml --minimumpriority 4 --reportfile results.json

cp vendor/vitaliiivanovspryker/project-architecture-sniffer/tools/script.php script.php

php script.php

```

### Debugging

```
docker/sdk cli -x

PHPMD_ALLOW_XDEBUG=true vendor/bin/phpmd src/Pyz/ (xml|text|html) vendor/vitaliiivanovspryker/project-architecture-sniffer/src/ruleset.xml --minimumpriority 2

```

## Roadmap (expected rules)

- Storage/Search modules are not supposed to make RPC calls to ZED. 



[//]: # ()
[//]: # (Note: Lower priorities always include the higher priorities in the validation process.)

[//]: # ()
[//]: # (### Including the sniffer in PHPStorm)

[//]: # (Add a new custom ruleset under `Editor -> Inspections -> PHP -> PHP Mess Detector validation`.)

[//]: # (Name it `Architecture Sniffer` for example.)

[//]: # ()
[//]: # (The customer ruleset is defined in `vendor/spryker/architecture-sniffer/src/ruleset.xml`)

[//]: # ()
[//]: # (### Check Mess Detector Settings)

[//]: # (Under `Framework & Languages -> PHP -> Mess Detector` you need to define the configuration and set the path to your phpmd &#40;vendor/bin/phpmd&#41;. Use local and run `Validate` to see if it works.)

[//]: # ()
[//]: # ()
[//]: # (## Writing new sniffs)

[//]: # (Add them to inside src folder and add tests in `tests` with the same folder structure.)

[//]: # (Don't forget to update `ruleset.xml`.)

[//]: # ()
[//]: # (Every sniff needs a description as full sentence:)

[//]: # (```php)

[//]: # (    protected const RULE = 'Every Foo needs Bar.';)

[//]: # ()
[//]: # (    /**)

[//]: # (     * @return string)

[//]: # (     */)

[//]: # (    public function getDescription&#40;&#41;: string)

[//]: # (    {)

[//]: # (        return static::RULE;)

[//]: # (    })

[//]: # (```)

[//]: # ()
[//]: # (Every sniff needs to implement either the `ClassAware`, `FunctionAware`, `InterfaceAware`, or `MethodAware` interface to be recognised.)

[//]: # (To validate that sniffer recognises your rule, check if your rule is listed in Zed UI > Maintenance > Architecture sniffer.)

[//]: # ()
[//]: # ()
[//]: # (Also note:)

[//]: # (- The rule names must be unique across the rulesets.)

[//]: # (- Each rule should contain only one "check".)

[//]: # (- Each rule always outputs also the reason &#40;violation&#41;, not just the occurrence.)

[//]: # ()
[//]: # (### Setup)

[//]: # (Run)

[//]: # (```)

[//]: # (./setup.sh)

[//]: # (```)

[//]: # (and)

[//]: # (```)

[//]: # (php composer.phar install)

[//]: # (```)

[//]: # ()
[//]: # (### Testing)

[//]: # (Don't forget to test your changes:)

[//]: # (```)

[//]: # (php phpunit.phar)

[//]: # (```)

[//]: # ()
[//]: # (### Running code-sniffer on this project)

[//]: # (Make sure this repository is Spryker coding standard conform:)

[//]: # (```)

[//]: # (php composer.phar cs-check)

[//]: # (```)

[//]: # (If you want to fix the fixable errors, use)

[//]: # (```)

[//]: # (php composer.phar cs-fix)

[//]: # (```)

[//]: # (Once everything is green you can make a PR with your changes.)
