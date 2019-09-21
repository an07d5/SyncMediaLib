<?php


namespace app\xml\types;


class XmlFalse extends XmlBaseType
{

    function getData(): bool
    {
        return false;
    }
}
