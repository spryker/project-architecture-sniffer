<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

namespace ProjectArchitectureSniffer\Project\Common;

use PHPMD\AbstractNode;
use PHPMD\AbstractRule;
use PHPMD\Node\AbstractTypeNode;
use PHPMD\Node\MethodNode;
use PHPMD\Rule\ClassAware;

class TooManyPublicMethodsRule extends AbstractRule implements ClassAware
{
    /**
     * @var string
     */
    public const RULE = 'Too many public methods.';

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
    protected string $ignoreMethodRegexp;

    /**
     * @param \PHPMD\AbstractNode|\PHPMD\Node\AbstractTypeNode $node
     *
     * @return void
     */
    public function apply(AbstractNode $node): void
    {
        $ignoreClassRegexp = $this->getStringProperty('ignoreclasspattern');

        if (preg_match($ignoreClassRegexp, $node->getFullQualifiedName())) {
            return;
        }

        $this->ignoreMethodRegexp = $this->getStringProperty('ignoremethodpattern');

        $threshold = $this->getIntProperty('maxmethods');

        $nom = $this->countMethods($node);

        if ($nom <= $threshold) {
            return;
        }

        $this->addViolation(
            $node,
            [
                sprintf(
                    'The %s %s has %s public methods. Consider refactoring to keep number of public methods under %s.',
                    $node->getType(),
                    $node->getName(),
                    $nom,
                    $threshold,
                ),
            ],
        );
    }

    /**
     * @param \PHPMD\Node\AbstractTypeNode $node
     *
     * @return int
     */
    protected function countMethods(AbstractTypeNode $node): int
    {
        return array_reduce(
            $node->getMethods(),
            fn (int $count, MethodNode $method) => $this->isMethodCountable($method) ? ++$count : $count,
            0,
        );
    }

    /**
     * @param \PHPMD\Node\MethodNode $method
     *
     * @return bool
     */
    protected function isMethodCountable(MethodNode $method): bool
    {
        return $method->getNode()->isPublic() && !$this->isIgnoredMethodName($method->getName());
    }

    /**
     * @param string $methodName
     *
     * @return bool
     */
    private function isIgnoredMethodName(string $methodName): bool
    {
        return (bool)$this->ignoreMethodRegexp &&
            preg_match($this->ignoreMethodRegexp, $methodName) === 1;
    }
}
