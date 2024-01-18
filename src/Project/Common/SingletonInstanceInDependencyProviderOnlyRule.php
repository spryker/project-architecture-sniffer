<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

namespace ProjectArchitectureSniffer\Project\Common;

use PHPMD\AbstractNode;
use PHPMD\AbstractRule;
use PHPMD\Rule\MethodAware;

class SingletonInstanceInDependencyProviderOnlyRule extends AbstractRule implements MethodAware
{
    /**
     * @var string
     */
    public const RULE = 'Singleton getInstance() initialisation should be in Dependency Provider only.';

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
    protected const GET_INSTANCE_METHOD_NAME = '/^(getInstance)$/';

    /**
     * @param \PHPMD\AbstractNode $node
     *
     * @return void
     */
    public function apply(AbstractNode $node): void
    {
        if ($this->isDependencyProvider($node)) {
            return;
        }

        foreach ($node->findChildrenOfType('MethodPostfix') as $classUsage) {
            $methodName = $classUsage->getNode()->getImage();

            if ($this->isGetInstance($methodName) === false) {
                continue;
            }

            if ($this->isStaticCall($classUsage->getNode()->getParent()->getImage()) === false) {
                continue;
            }

            $this->addViolation(
                $node,
                [
                    sprintf(
                        'The method %s uses ::getInstance. It can not be used outside of DependencyProvider',
                        $node->getName(),
                    ),
                ],
            );
        }
    }

    /**
     * @param \PHPMD\AbstractNode $node
     *
     * @return bool
     */
    protected function isDependencyProvider(AbstractNode $node): bool
    {
        $parent = $node->getNode()->getParent();
        $className = $parent->getNamespaceName() . '\\' . $parent->getName();

        return preg_match('/\\\\' . '(?:Client|Yves|Glue|Zed|Service)' . '\\\\.*\\\\\w+DependencyProvider$/', $className) === 1;
    }

    /**
     * @param string $methodName
     *
     * @return bool
     */
    protected function isGetInstance(string $methodName): bool
    {
        return preg_match(static::GET_INSTANCE_METHOD_NAME, $methodName) === 1;
    }

    /**
     * @param string $callSymbol
     *
     * @return bool
     */
    protected function isStaticCall(string $callSymbol): bool
    {
        return preg_match('::', $callSymbol) === 1;
    }
}
