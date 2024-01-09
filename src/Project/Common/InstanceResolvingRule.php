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
    public const RULE = 'Instance can not be initialized directly. Use Dependency Provider and Resolvers';

    /**
     * @var string
     */
    protected const INSTANCE_PATTERN = '([\w]+(Repository|EntityManager|QueryContainer|Facade|DependencyProvider|Bridge|Client|Service)$)';

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

                $referenceName = trim($reference->getName(), '\\');

                if (preg_match(static::INSTANCE_PATTERN, $referenceName)) {
                    $message = sprintf(
                        'Entity `%s` is initialized in method `%s`. %s',
                        $referenceName,
                        $methodName,
                        static::RULE,
                    );
                    $this->addViolation($methodNode, [$message]);
                }
            }
        }
    }
}
