<?php

namespace ProjectArchitectureSniffer\Project\Common\CrossModule;

use PHPMD\AbstractNode;
use PHPMD\Rule\ClassAware;

class ClassIsNotCrossModuleExtendedRule extends AbstractCrossModuleRule implements ClassAware
{
    protected const CLASSES_ALLOWED_TO_BE_EXTENDED = [
        '/^\\\\Spryker\\\\[A-Za-z]+\\\\Kernel\\\\/',
        '/^\\\\Spryker\\\\Client\\\\ZedRequest\\\\Stub\\\\ZedRequestStub$/',
        '/\\\\Abstract[A-Za-z]+$/',
        '/^\\\\Spryker\\\\Zed\\\\Giu\\\\/',
        '/^\\\\Symfony\\\\Component\\\\/',
        '/^\\\\[A-Za-z]*Exception$/',
        '/^\\\\Propel\\\\Runtime\\\\Adapter\\\\Pdo\\\\[A-Za-z]+Adapter/',
        '/^\\\\League/',
        '/\\\\SearchElasticsearch\\\\/',
        '/\\\\Elastica\\\\/',
        '/\\\\Firebase\\\\JWT\\\\Key/',
    ];

    public function apply(AbstractNode $node): void
    {
        if ($this->isClassAllowedToUseCrossModule($node->getNamespaceName(), $node->getName()) === true) {
            return;
        }

        try {
            $firstChild = $node->getChild(0);
        } catch (\OutOfBoundsException $e) {
            return;
        }
        if ($firstChild->isInstanceOf('ClassReference') === false) {
            return;
        }

        $fullClassName = $firstChild->getNode()->getImage();
        if ($this->isClassAllowedToBeExtended($fullClassName) === true) {
            return;
        }

        if ($this->crossModuleViolationExists($node, $fullClassName) === false) {
            return;
        }

        $this->addViolation(
            $node,
            [
                sprintf(
                    'The class extends cross-module %s',
                    $fullClassName,
                ),
            ],
        );
    }

    protected function isClassAllowedToBeExtended(string $fullClassName): bool
    {
        foreach (static::CLASSES_ALLOWED_TO_BE_EXTENDED as $classRegExp) {
            if (preg_match($classRegExp, $fullClassName)) {
                return true;
            }
        }

        return false;
    }
}
