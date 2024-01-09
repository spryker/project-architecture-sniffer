<?php

namespace ProjectArchitectureSniffer\Project\Common;

use PHPMD\AbstractNode;
use PHPMD\AbstractRule;
use PHPMD\Rule\ClassAware;

class WeirdModuleNameRule extends AbstractRule implements ClassAware
{
    protected const WEIRD_MODULE_NAME_PATTER = '(^[\w]+\\\\(Zed|Client|Yves|Glue|Service|Shared)\\\\[\w]*(?i:test|dummy|example|antelope)[\w]*\\\\.+)';

    public function apply(AbstractNode $node): void
    {
        $classFullName = $node->getFullQualifiedName();

        if (preg_match(static::WEIRD_MODULE_NAME_PATTER, $classFullName) === 0) {
            return;
        }

        $this->addViolation(
            $node,
            [
                sprintf('Module name %s should not contain weird words: test|dummy|example|antelope.', $classFullName)
            ],
        );
    }
}
