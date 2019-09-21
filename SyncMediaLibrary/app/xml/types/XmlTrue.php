<?php


namespace app\xml\types;


class XmlTrue extends XmlBaseType
{

    function getData(): bool
    {
        return true;
    }
}