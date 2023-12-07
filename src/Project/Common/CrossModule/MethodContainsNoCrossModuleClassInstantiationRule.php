<?php

namespace ProjectArchitectureSniffer\Project\Common\CrossModule;

use PHPMD\AbstractNode;
use PHPMD\Rule\MethodAware;

class MethodContainsNoCrossModuleClassInstantiationRule extends AbstractCrossModuleRule implements MethodAware
{
    public function apply(AbstractNode $node): void
    {
        if ($this->isClassAllowedToUseCrossModule($node->getNamespaceName(), $node->getParentName())) {
            return;
        }

        foreach ($node->findChildrenOfType('AllocationExpression') as $class) {
            $fullClassName = $class->getNode()->getChild(0)->getImage();

            if ($this->isClassAllowedToBeUsed($fullClassName)) {
                continue;
            }

            if (!$this->crossModuleViolationExists($node, $fullClassName)) {
                continue;
            }

            $this->addViolation(
                $node,
                [
                    sprintf(
                        'The method %s contains cross-module new %s. Please, move it to the factory',
                        $node->getName(),
                        $fullClassName,
                    ),
                ],
            );
        }
    }
}
