<?php
namespace OmekaTest\Model;

use Omeka\Model\Entity\Property;
use Omeka\Model\Entity\User;
use Omeka\Model\Entity\Vocabulary;
use Omeka\Test\TestCase;

class PropertyTest extends TestCase
{
    protected $property;

    public function setUp()
    {
        $this->property = new Property;
    }

    public function testInitialState()
    {
        $this->assertNull($this->property->getId());
        $this->assertNull($this->property->getOwner());
        $this->assertNull($this->property->getVocabulary());
        $this->assertNull($this->property->getLocalName());
        $this->assertNull($this->property->getLabel());
        $this->assertNull($this->property->getComment());
        $this->assertInstanceOf(
            'Doctrine\Common\Collections\ArrayCollection',
            $this->property->getValues()
        );
    }

    public function testSetOwner()
    {
        $owner = new User;
        $this->property->setOwner($owner);
        $this->assertSame($owner, $this->property->getOwner());
    }

    public function testSetVocabulary()
    {
        $vocabulary = new Vocabulary;
        $this->property->setVocabulary($vocabulary);
        $this->assertSame($vocabulary, $this->property->getVocabulary());
        $this->assertTrue($vocabulary->getProperties()->contains($this->property));
    }

    public function testSetLocalName()
    {
        $localName = 'test-localName';
        $this->property->setLocalName($localName);
        $this->assertEquals($localName, $this->property->getLocalName());
    }

    public function testSetLabel()
    {
        $label = 'test-label';
        $this->property->setLabel($label);
        $this->assertEquals($label, $this->property->getLabel());
    }

    public function testSetComment()
    {
        $comment = 'test-comment';
        $this->property->setComment($comment);
        $this->assertEquals($comment, $this->property->getComment());
    }
}
