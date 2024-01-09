<?php

namespace ProjectArchitectureSniffer\Project\Zed;

use PHPMD\AbstractNode;
use PHPMD\AbstractRule;
use PHPMD\Rule\MethodAware;

class RepositoryReadOnlyRule extends AbstractRule implements MethodAware
{
    protected const REPOSITORY_PATTERN = '(^[\w]+\\\\Zed\\\\[\w]+\\\\Persistence\\\\[\w]+(Repository|RepositoryInterface))';

    protected const RESTRICTED_METHOD_POSTFIX = [
        'save',
        'update',
        'delete',
    ];

    public function apply(AbstractNode $node): void
    {
        if (!$this->isRepository($node)) {
            return;
        }

        foreach ($node->findChildrenOfType('MethodPostfix') as $classUsage) {
            $methodName = $classUsage->getNode()->getImage();

            if (!in_array($methodName, static::RESTRICTED_METHOD_POSTFIX)) {
                continue;
            }

            $this->addViolation(
                $node,
                [
                    sprintf(
                        'The method %s uses "%s" operation that is restricted for Repository pattern. Use Entity Manager!',
                        $node->getName(),
                        $methodName,
                    ),
                ],
            );
        }
    }

    protected function isRepository(AbstractNode $node): bool
    {
        $parent = $node->getNode()->getParent();
        $className = $parent->getNamespaceName() . '\\' . $parent->getName();

        return preg_match(static::REPOSITORY_PATTERN, $className) !== 0;
    }
}
