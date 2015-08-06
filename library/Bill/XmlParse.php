<?php

class Bill_XmlParse
{
    public function __construct()
    {

    }

    /**
     * Parse xml by dom.(Not recommend, time cost)
     * @param string $filename
     * @param array $xmlKeys
     * @param string $subRootTagName
     * @return multitype:multitype:NULL
     */
    public function DomParse($filename, array $xmlKeys, $subRootTagName)
    {
        $result = array();

        $domDoc = new DOMDocument();
        $domDoc->load($filename);
        $items = $domDoc->getElementsByTagName($subRootTagName);

        foreach ($items as $item)
        {
            $data = array();

            foreach ($xmlKeys as $xmlkey)
            {
                $rs = $item->getElementsByTagName($xmlkey);
                $data[$xmlkey] = $rs->item(0)->nodeValue;

            }

            $result[] = $data;
        }

        return $result;
    }

    /**
     * Parse xml through simplexml(for small file, recommend.)
     * @param string $filename
     * @param array $xmlKeys
     */
    public function SimpleXmlParse($filename, array $xmlKeys)
    {
        $xml = simplexml_load_file($filename);

        foreach ($xml->children() as $child)
        {
            //node data process...
        }
    }

    /**
     * Parse xml through xmlreader(for huge size, recommend.)
     * @param string $filename
     * @param string $subRootTagName
     */
    public function XmlReaderParse($filename, $subRootTagName)
    {
        $xml = new XMLReader();
        if (!$xml->open($filename, null, 1 << 19))
        {
            echo "Failed to open input file: {$filename}.<br>";
            return;
        }

        while ($xml->read())
        {
            $productData = array();

            if ($xml->nodeType == XMLReader::ELEMENT && $xml->name == $subRootTagName)
            {
                $productData = NULL;

                while ($xml->read())
                {
                    if ($xml->nodeType == XMLReader::ELEMENT)
                    {
                        $key = $xml->name;
                        $xml->read();
                        $productData[$key] = $xml->value;
                    }

                    if ($xml->nodeType == XMLReader::END_ELEMENT && $xml->name == $subRootTagName)
                    {
                        //node data process...
                    }
                }
            }
        }
    }

}