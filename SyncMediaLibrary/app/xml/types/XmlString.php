<?php


namespace app\xml\types;


class XmlString extends XmlBaseType
{

    function getData(): string
    {
        if (!empty($this->childrens[0])) {
            return $this->childrens[0]->getValue();
        }
        return '';
    }
}
