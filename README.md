# Project Architecture Sniffer

[![Minimum PHP Version](https://img.shields.io/badge/php-%3E%3D%208.0-8892BF.svg)](https://php.net/)
[![License](https://poser.pugx.org/spryker/architecture-sniffer/license.svg)](https://packagist.org/packages/spryker/architecture-sniffer)

[//]: # ([![Total Downloads]&#40;https://poser.pugx.org/spryker/architecture-sniffer/d/total.svg&#41;]&#40;https://packagist.org/packages/spryker/architecture-sniffer&#41;)

Architecture Sniffer for Spryker Projects. Over `80` rules adapted for Spryker Projects.


## Priority Levels


- `1`: Сritical

- `2`: Major

- `3`: Medium

- `4`: Minor


We recommend minimum priority `3` by default for local and CI checks.


Note: Lower priorities (higher numbers) always include the higher priorities (lower numbers).

## Content

- `29` adapted [PHPMD rules](documentations/PHPMDrules.md)
- `39` adapted [Spryker Architecture sniffer rules](documentations/SPRYKERrules.md)
- `13` new [Project Architecture sniffer rules](documentations/PROJECTrules.md)

## Usage

Make sure you include the sniffer as `require-dev` dependency:
```
composer require --dev spryker/project-architecture-sniffer
```

### Running

Find [command line option](https://phpmd.org/documentation/index.html).

You can run the Project Architecture Sniffer from console by using:
```
vendor/bin/phpmd src/Pyz/ text vendor/spryker/project-architecture-sniffer/src/ruleset.xml --minimumpriority 3
```

### Baseline

Existing projectsand demo-shops may contain rule violations.
The decision to refactor existing violations may be at the discretion of each project individually.
It is recommended to approach this in a differentiated manner.
To integrate rules into the project immediately, there recommended to generate a [baseline](https://phpmd.org/documentation/#baseline) and move forward.
It is also permissible to [suppress rules](https://phpmd.org/documentation/suppress-warnings.html) on a case-by-case basis.

### Ruleset

#### Ruleset that contains all documented rules
```
vendor/spryker/project-architecture-sniffer/src/ruleset.xml
```
#### Ruleset that contains [Spryker Architecture sniffer rules](documentations/SPRYKERrules.md) and [Project Architecture sniffer rules](documentations/PROJECTrules.md)
```
vendor/spryker/project-architecture-sniffer/src/Project/ruleset.xml
```
#### Ruleset that contains [PHPMD rules](documentations/PHPMDrules.md)
```
vendor/spryker/project-architecture-sniffer/src/PhpMd/ruleset.xml
```
#### More
Find `vendor/spryker/project-architecture-sniffer/src/`
```
├── PhpMd
│   ├── cleancode.xml
│   ├── codesize.xml
│   ├── controversial.xml
│   ├── design.xml
│   ├── naming.xml
│   ├── ruleset.xml
│   └── unusedcode.xml
├── Project
│   ├── Client
│   │   └── ruleset.xml
│   ├── Common
│   │   └── ruleset.xml
│   ├── Glue
│   │   └── ruleset.xml
│   ├── Service
│   │   └── ruleset.xml
│   ├── Shared
│   │   └── ruleset.xml
│   ├── Yves
│   │   └── ruleset.xml
│   ├── Zed
│   │   └── ruleset.xml
│   └── ruleset.xml
└── ruleset.xml
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

PHPMD_ALLOW_XDEBUG=true vendor/bin/phpmd src/Pyz/ text vendor/spryker/project-architecture-sniffer/src/ruleset.xml --minimumpriority 3
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
