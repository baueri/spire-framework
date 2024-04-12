<?php

declare(strict_types=1);

namespace Baueri\Spire\Framework\Support;

use SimpleXMLElement;

class XmlObject extends SimpleXMLElement
{
    public function getParentsWithCurrentNode(): array
    {
        return array_merge($this->getParents(), [$this]);
    }

    public function getParents(): array
    {
        $parents = [];

        $node = $this->getParentNode();

        $parents[] = $node;

        while ($node = $node->getParentNode()) {
            array_unshift($parents, $node);
        }

        return $parents;
    }

    public function getParentNode()
    {
        return current($this->xpath('parent::*'));
    }
}
