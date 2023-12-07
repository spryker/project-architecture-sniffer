<?php

namespace ProjectArchitectureSniffer\Project\Common\CrossModule;

use PHPMD\AbstractNode;
use PHPMD\Rule\MethodAware;

class MethodContainsNoCrossModuleStaticPropertyRule extends AbstractCrossModuleRule implements MethodAware
{
    public function apply(AbstractNode $node): void
    {
        if ($this->isClassAllowedToUseCrossModule($node->getNamespaceName(), $node->getParentName()) === true) {
            return;
        }

        $classOrInterfaceReference = $node->findChildrenOfType('ClassOrInterfaceReference');

        foreach ($node->findChildrenOfType('PropertyPostfix') as $classUsage) {
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
                        'The method %s uses cross-module property %s',
                        $node->getName(),
                        $class->getNode()->getImage(),
                    ),
                ],
            );
        }
    }
}
