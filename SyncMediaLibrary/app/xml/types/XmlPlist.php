<?php


namespace app\xml\types;


class XmlPlist extends XmlBaseType
{
    function getData(): array
    {
        return $this->childrens[0]->getData();
    }
}
