<?php

namespace ProjectArchitectureSniffer\Project\Common\CrossModule;

use PHPMD\AbstractNode;
use PHPMD\Rule\MethodAware;

class MethodContainsNoCrossModuleConstantRule extends AbstractCrossModuleRule implements MethodAware
{
    protected const CLASSES_WHICH_CONSTANTS_ALLOWED_TO_BE_USED = [
        '/(Config|Constants|Events|ConstantsInterface)$/',
        '/^\\\\Spryker\\\\Zed\\\\(Gui|Kernel)\\\\/',
    ];

    public function apply(AbstractNode $node): void
    {
        if ($this->isClassAllowedToUseCrossModule($node->getNamespaceName(), $node->getParentName()) === true) {
            return;
        }

        $classOrInterfaceReference = $node->findChildrenOfType('ClassOrInterfaceReference');
        foreach ($node->findChildrenOfType('ConstantPostfix') as $classUsage) {
            $class = $this->findClosestClass($classOrInterfaceReference, $classUsage);
            if ($class === null) {
                continue;
            }

            $fullClassName = $class->getNode()->getImage();
            if ($this->isClassAllowedToBeUsed($fullClassName) === true) {
                continue;
            }

            if ($this->isClassConstantAllowedToBeUsed($fullClassName) === true) {
                continue;
            }

            if ($this->crossModuleViolationExists($node, $fullClassName) === false) {
                continue;
            }

            $this->addViolation(
                $node,
                [
                    sprintf(
                        'The method %s uses cross-module constant %s. Please, define the const in current class with @uses tag',
                        $node->getName(),
                        $class->getNode()->getImage(),
                    ),
                ],
            );
        }
    }

    protected function isClassConstantAllowedToBeUsed(string $fullClassName): bool
    {
        foreach (static::CLASSES_WHICH_CONSTANTS_ALLOWED_TO_BE_USED as $classRegExp) {
            if (preg_match($classRegExp, $fullClassName)) {
                return true;
            }
        }

        return false;
    }
}
