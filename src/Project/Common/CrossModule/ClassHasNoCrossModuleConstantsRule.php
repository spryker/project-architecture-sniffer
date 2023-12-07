<?php

namespace ProjectArchitectureSniffer\Project\Common\CrossModule;

use PDepend\Source\AST\ASTMemberPrimaryPrefix;
use PHPMD\AbstractNode;
use PHPMD\Rule\ClassAware;

class ClassHasNoCrossModuleConstantsRule extends AbstractCrossModuleRule implements ClassAware
{
    public function apply(AbstractNode $node): void
    {
        if ($this->isClassAllowedToUseCrossModule($node->getNamespaceName(), $node->getName()) === true) {
            return;
        }

        foreach ($node->findChildrenOfType('ConstantDeclarator') as $classUsage) {
            if ($this->constantValueIsClassReference($classUsage) === false) {
                continue;
            }

            $fullClassName = $classUsage->getNode()->getValue()->getValue()->getChild(0)->getImage();
            if ($this->crossModuleViolationExists($node, $fullClassName) === false) {
                continue;
            }

            $this->addViolation(
                $node,
                [
                    sprintf(
                        'The constant is cross-module %s',
                        $fullClassName,
                    ),
                ],
            );
        }
    }

    protected function constantValueIsClassReference(AbstractNode $classUsage): bool
    {
        return $classUsage->getNode()->getValue()->getValue() instanceof ASTMemberPrimaryPrefix;
    }
}
