<?php


namespace app\xml\types;


class XmlText extends XmlBaseType
{

    function getData(): string
    {
        return $this->childrens[0]->getValue();
    }
}