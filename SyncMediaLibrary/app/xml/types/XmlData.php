<?php


namespace app\xml\types;


class XmlData extends XmlBaseType
{

    function getData(): string
    {
        return $this->childrens[0]->getValue();
    }
}
