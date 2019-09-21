<?php


namespace app\xml\types;


class XmlDict extends XmlBaseType
{

    function getData(): array
    {
        $result = [];
        for ($i = 0; $i < count($this->childrens) - 1; $i += 2) {
            $key = $this->childrens[$i]->getData();
            $data = $this->childrens[$i + 1]->getData();
            $result[$key] = $data;
        }
        return $result;
    }
}
