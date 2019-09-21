<?php


namespace app\xml\types;


use XMLReader;

abstract class XmlBaseType
{
    protected $childrens = [];
    private $name;
    private $isClose;
    private $isOpen;
    private $value;

    public function __construct(XMLReader $reader)
    {
        $this->name = $reader->name;
        $this->isClose = ($reader->nodeType == XMLReader::END_ELEMENT);
        $this->isOpen = ($reader->nodeType == XMLReader::ELEMENT);
        $this->value = $reader->value;

    }

    abstract public function getData();

    public function isClose(): bool
    {
        return $this->isClose;
    }

    public function isOpen(): bool
    {
        return $this->isOpen;
    }

    public function nameEquals(self $type): bool
    {
        return strcmp($type->getName(), $this->name) === 0;
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function addChild(self $child)
    {
        array_unshift($this->childrens, $child);
    }

    public function getValue(): string
    {
        return $this->value;
    }
}
