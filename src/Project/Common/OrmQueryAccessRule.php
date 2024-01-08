<?php

namespace ProjectArchitectureSniffer\Project\Common;

use PHPMD\AbstractNode;
use PHPMD\AbstractRule;
use PHPMD\Node\ClassNode;
use PHPMD\Rule\ClassAware;

class OrmQueryAccessRule extends AbstractRule implements ClassAware
{
    /**
     * @var array
     */
    protected $patterns = [
        [
            '(^[\w]+\\\\Zed\\\\(?!.*(?:DataImport|Storage|Search))[\w]+\\\\Business\\\\.+)',
            '(^Orm\\\\Zed\\\\[\w]+\\\\Persistence\\\\[\w]+Query)',
            '{type} {source} accesses {target} which violates rule "No call from Orm Query to Zed Business"',
        ],
        [
            '(^[\w]+\\\\Zed\\\\[\w]+\\\\Communication\\\\.+)',
            '(^Orm\\\\Zed\\\\[\w]+\\\\Persistence\\\\[\w]+Query)',
            '{type} {source} accesses {target} which violates rule "No call from  Orm Query to Zed Communication"',
        ],
    ];

    public function apply(AbstractNode $node)
    {
        $patterns = $this->collectPatterns($node);

        $this->applyPatterns($node, $patterns);

        foreach ($node->getMethods() as $method) {
            $this->applyPatterns(
                $method,
                $patterns,
            );
        }
    }

    protected function applyPatterns(AbstractNode $node, array $patterns)
    {
        foreach ($node->getDependencies() as $dependency) {
            $targetQName = sprintf('%s\\%s', $dependency->getNamespaceName(), $dependency->getName());

            foreach ($patterns as [$srcPattern, $targetPattern, $message]) {
                if (preg_match($srcPattern, $node->getFullQualifiedName()) === 0) {
                    continue;
                }
                if (preg_match($targetPattern, $targetQName) === 0) {
                    continue;
                }

                $this->addViolation(
                    $node,
                    [
                        str_replace(
                            ['{type}', '{source}', '{target}'],
                            [ucfirst($node->getType()), $node->getFullQualifiedName(), $targetQName],
                            $message,
                        ),
                    ],
                );
            }
        }
    }

    protected function collectPatterns(ClassNode $class): array
    {
        $patterns = [];
        foreach ($this->patterns as [$srcPattern, $targetPattern, $message]) {
            if (preg_match($srcPattern, $class->getNamespaceName())) {
                $patterns[] = [$srcPattern, $targetPattern, $message];
            }
        }

        return $patterns;
    }
}
