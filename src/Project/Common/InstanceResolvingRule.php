<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

namespace ProjectArchitectureSniffer\Project\Common;

use PHPMD\AbstractNode;
use PHPMD\AbstractRule;
use PHPMD\Rule\ClassAware;

class InstanceResolvingRule extends AbstractRule implements ClassAware
{
    /**
     * @var string
     */
    public const RULE = 'Repository|EntityManager|QueryContainer|Facade|DependencyProvider|Client|Service instances can not be initialized directly with "new". Use Dependency Provider and Resolvers';

    /**
     * @return string
     */
    public function getDescription(): string
    {
        return static::RULE;
    }

    /**
     * @var string
     */
    protected const INSTANCE_PATTERN = '/\w+(Repository|EntityManager|QueryContainer|Facade|DependencyProvider|Client|Service)$/';

    /**
     * @param \PHPMD\AbstractNode $node
     *
     * @return void
     */
    public function apply(AbstractNode $node): void
    {
        foreach ($node->getMethods() as $methodNode) {
            $methodName = $methodNode->getImage();
            $allocatedExpressions = $methodNode->findChildrenOfType('AllocationExpression');

            foreach ($allocatedExpressions as $expression) {
                if ($expression->getImage() !== 'new') {
                    continue;
                }

                $reference = $expression->getFirstChildOfType('ClassReference');

                if (!$reference) {
                    continue;
                }

                $referenceName = trim($reference->getName(), '\\');

                if (preg_match(static::INSTANCE_PATTERN, $referenceName) !== 1) {
                    continue;
                }

                $this->addViolation(
                    $methodNode,
                    [
                        sprintf(
                            'Entity `%s` is initialized in method `%s`. %s',
                            $referenceName,
                            $methodName,
                            static::RULE,
                        ),
                    ],
                );
            }
        }
    }
}
