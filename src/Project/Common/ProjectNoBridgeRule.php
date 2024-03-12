<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

namespace ProjectArchitectureSniffer\Project\Common;

use PHPMD\AbstractNode;
use PHPMD\AbstractRule;
use PHPMD\Rule\ClassAware;

class ProjectNoBridgeRule extends AbstractRule implements ClassAware
{
    /**
     * @var string
     */
    public const RULE = 'Project should not use and depend on Bridge pattern.';

    /**
     * @var string
     */
    public const REGEX_BRIDGE = '/\w+Bridge$/';

    /**
     * @return string
     */
    public function getDescription(): string
    {
        return static::RULE;
    }

    /**
     * @param \PHPMD\AbstractNode $node
     *
     * @return void
     */
    public function apply(AbstractNode $node): void
    {
        $this->applyNotUseBridge($node);
        $this->applyNotDependOnBridges($node);
    }

    /**
     * @param \PHPMD\AbstractRule $node
     *
     * @return void
     */
    protected function applyNotUseBridge(AbstractNode $node): void
    {
        if (!$this->isBridge($node->getFullQualifiedName())) {
            return;
        }

        $this->addViolation(
            $node,
            [
                sprintf('Project should not use bridges: %s.', $node->getFullQualifiedName()),
            ],
        );
    }

    /**
     * @param \PHPMD\AbstractNode $node
     *
     * @return void
     */
    protected function applyNotDependOnBridges(AbstractNode $node): void
    {
        foreach ($node->getMethods() as $method) {
            foreach ($method->getDependencies() as $dependency) {
                $targetQName = sprintf('%s\\%s', $dependency->getNamespaceName(), $dependency->getName());

                if (!$this->isBridge($targetQName)) {
                    continue;
                }

                $this->addViolation(
                    $method,
                    [
                        sprintf('Project should not depend on bridges: %s.', $targetQName),
                    ],
                );
            }
        }
    }

    /**
     * @param string $nodeName
     *
     * @return bool
     */
    protected function isBridge(string $nodeName): bool
    {
        return preg_match(static::REGEX_BRIDGE, $nodeName) === 1;
    }
}
