<?php


namespace app\xml\types;


class XmlKey extends XmlBaseType
{

    function getData(): string
    {
        return $this->childrens[0]->getValue();
    }
}
