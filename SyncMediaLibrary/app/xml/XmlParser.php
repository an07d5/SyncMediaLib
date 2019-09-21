<?php


namespace app\xml;


use app\Conf;
use app\xml\types\XmlBaseType;
use ErrorException;
use XMLReader;

class XmlParser
{
    private $path;

    public function __construct()
    {
        $this->path = Conf::getMediaLibPath();
        if (!is_file($this->path)) {
            throw new ErrorException('Media library file not specified');
        }
        if (!is_readable($this->path)) {
            throw new ErrorException('Media library file is not accessible');
        }
    }

    public function parse(): array
    {
        $list = [];
        $reader = new XMLReader();
        $reader->open($this->path);

        $reader->setParserProperty(XMLReader::VALIDATE, true);
        if (!$reader->isValid()) {
            throw new ErrorException('Media library file is not valid');
        }

        while ($reader->read()) {
            if (in_array($reader->nodeType, [XMLReader::SIGNIFICANT_WHITESPACE, XMLReader::DOC_TYPE])) {
                continue;
            }
            $className = $this->getClassName($reader);
            /** @var XmlBaseType $item */
            $item = new $className($reader);
            if ($item->isClose()) {
                while (true) {
                    /** @var XmlBaseType $last */
                    $last = array_pop($list);
                    if ($last->isOpen() && $last->nameEquals($item)) {
                        break;
                    } else {
                        $item->addChild($last);
                    }
                }
            }
            array_push($list, $item);
        }
        return $list[0]->getData();
    }

    private function getClassName(XMLReader $reader): string
    {
        $name = $reader->name;
        $name = ltrim($name, '#');
        $name = mb_convert_case($name, MB_CASE_TITLE_SIMPLE);
        $name = "\\" . __NAMESPACE__ . '\types\Xml' . $name;
        return $name;
    }
}