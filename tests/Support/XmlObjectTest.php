<?php

declare(strict_types=1);

namespace Baueri\Spire\Framework\Tests\Support;

use PHPUnit\Framework\TestCase;
use Baueri\Spire\Framework\Support\XmlObject;

class XmlObjectTest extends TestCase
{
    public function testGetParentsWithCurrentNode(): void
    {
        $xml = new XmlObject('<?xml version="1.0"?>
            <root>
                <child>
                    <grandchild/>
                    </child>
                    </root>'
        );

        $grandchild = $xml->child->grandchild;
        $parents = $grandchild->getParentsWithCurrentNode();
        $this->assertCount(3, $parents);
        $this->assertSame('root', $parents[0]->getName());
        $this->assertSame('child', $parents[1]->getName());
    }

    public function testGetParents(): void
    {
        $xml = new XmlObject('<?xml version="1.0"?>
            <root>
                <child>
                    <grandchild/>
                    </child>
                    </root>'
        );

        $grandchild = $xml->child->grandchild;
        $parents = $grandchild->getParents();
        $this->assertCount(2, $parents);
        $this->assertSame('root', $parents[0]->getName());
        $this->assertSame('child', $parents[1]->getName());
    }

    public function testGetParentNode(): void
    {
        $xml = new XmlObject('<?xml version="1.0"?>
            <root>
                <child>
                    <grandchild/>
                    </child>
                    </root>'
        );

        $grandchild = $xml->child->grandchild;
        $parentNode = $grandchild->getParentNode();
        $this->assertSame('child', $parentNode->getName());
    }
}
