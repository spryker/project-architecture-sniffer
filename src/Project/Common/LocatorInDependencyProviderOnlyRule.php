<?php

namespace ProjectArchitectureSniffer\Project\Common;

use PHPMD\AbstractNode;
use PHPMD\AbstractRule;
use PHPMD\Rule\MethodAware;

class LocatorInDependencyProviderOnlyRule extends AbstractRule implements MethodAware
{
    protected const LOCATOR_METHOD_NAMES = '/^(getLocator|locator)$/';
    protected const CLASSES_ALLOWED_TO_USE_LOCATOR = '/DependencyProvider$/';

    public function apply(AbstractNode $node): void
    {
        if ($this->isClassAllowedToUseLocator($node) === true) {
            return;
        }
        

        foreach ($node->findChildrenOfType('MethodPostfix') as $classUsage) {
            $methodName = $classUsage->getNode()->getImage();
            
            if ($this->isLocator($methodName) === false) {
                continue;
            }

            $this->addViolation(
                $node,
                [
                    sprintf(
                        'The method %s uses Locator. Locator can be used in DependencyProvider only',
                        $node->getName(),
                    ),
                ],
            );
        }
    }

    protected function isLocator(string $methodName): bool
    {
        if (preg_match(static::LOCATOR_METHOD_NAMES, $methodName)) {
            return true;
        }

        return false;
    }

    protected function isClassAllowedToUseLocator(AbstractNode $node): bool
    {
        if (preg_match(static::CLASSES_ALLOWED_TO_USE_LOCATOR, $node->getParentName())) {
            return true;
        }

        return false;
    }
}
