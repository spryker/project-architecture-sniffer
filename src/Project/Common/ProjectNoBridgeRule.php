<?php

namespace ProjectArchitectureSniffer\Project\Common;

use PHPMD\AbstractNode;
use PHPMD\AbstractRule;
use PHPMD\Rule\ClassAware;

// PHPMD_ALLOW_XDEBUG=true vendor/bin/phpmd src/Pyz/Glue/CategoriesRestApi/CategoriesRestApiDependencyProvider.php text vendor/vitaliiivanovspryker/project-architecture-sniffer/src/Project/Common/ruleset.xml --minimumpriority 1

class ProjectNoBridgeRule extends AbstractRule implements ClassAware
{
    public function apply(AbstractNode $node): void
    {
        if (preg_match('([\w]+Bridge$)', $node->getFullQualifiedName()) !== 0) {
            $this->addViolation(
                $node,
                [
                    sprintf('Project should not use bridges: %s.', $node->getFullQualifiedName())
                ],
            );
        }

        foreach ($node->getMethods() as $method) {
            foreach ($method->getDependencies() as $dependency) {
                $targetQName = sprintf('%s\\%s', $dependency->getNamespaceName(), $dependency->getName());

                if (preg_match('([\w]+Bridge$)', $targetQName) !== 0) {
                    $this->addViolation(
                        $method,
                        [
                            sprintf('Project should not depend on bridges: %s.', $targetQName)
                        ],
                    );
                }
            }
        }
    }
}
