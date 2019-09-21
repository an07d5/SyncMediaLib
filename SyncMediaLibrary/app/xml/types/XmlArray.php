<?php


namespace app\xml\types;


class XmlArray extends XmlBaseType
{

    function getData(): array
    {
        $result = [];
        foreach ($this->childrens as $child) {
            array_push($result, $child->getData());
        }
        return $result;
    }
}
