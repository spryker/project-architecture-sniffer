<?php

namespace ProjectArchitectureSniffer\Project\Common\CrossModule;

use PHPMD\AbstractNode;
use PHPMD\Rule\MethodAware;

class MethodContainsNoCrossModuleStaticMethodRule extends AbstractCrossModuleRule implements MethodAware
{
    public function apply(AbstractNode $node): void
    {
        if ($this->isClassAllowedToUseCrossModule($node->getNamespaceName(), $node->getParentName()) === true) {
            return;
        }

        $classOrInterfaceReference = $node->findChildrenOfType('ClassOrInterfaceReference');

        foreach ($node->findChildrenOfType('MethodPostfix') as $classUsage) {
            $class = $this->findClosestClass($classOrInterfaceReference, $classUsage);
            if ($class === null) {
                continue;
            }

            $fullClassName = $class->getNode()->getImage();
            if ($this->isClassAllowedToBeUsed($fullClassName) === true) {
                continue;
            }

            if ($this->crossModuleViolationExists($node, $fullClassName) === false) {
                continue;
            }

            $this->addViolation(
                $node,
                [
                    sprintf(
                        'The method %s uses cross-module static method %s',
                        $node->getName(),
                        $class->getNode()->getImage(),
                    ),
                ],
            );
        }
    }
}
