<?php


namespace app\xml\types;


class XmlInteger extends XmlBaseType
{

    function getData(): int
    {
        return intval($this->childrens[0]->getValue());
    }
}
