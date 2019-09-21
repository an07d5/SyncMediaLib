<?php


namespace app\xml\types;


class XmlDate extends XmlBaseType
{

    function getData(): string
    {
        return $this->childrens[0]->getValue();
    }
}
