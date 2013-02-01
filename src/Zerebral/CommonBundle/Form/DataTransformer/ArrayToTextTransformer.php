<?php

namespace Zerebral\CommonBundle\Form\DataTransformer;

use Symfony\Component\Form\DataTransformerInterface;

class ArrayToTextTransformer implements DataTransformerInterface
{
    private $delimiter = "\n";

    private $removeBlank = true;

    public function __construct($delimiter = "\n", $removeBlank = true)
    {
        $this->setDelimiter($delimiter);
        $this->setRemoveBlank($removeBlank);
    }

    public function transform($list)
    {
        if (is_null($list) || empty($list)) {
            return '';
        }

        return join($this->getDelimiter(), $list);
    }

    public function reverseTransform($text)
    {
        $lines = explode($this->getDelimiter(), str_replace(" ", "\n", $text));
        $list = array();
        foreach ($lines as $line) {
            $line = trim($line);
            if (!empty($line) || !$this->isRemoveBlank()) {
                $list[] = $line;
            }
        }
        return $list;
    }

    public function setDelimiter($delimiter)
    {
        $this->delimiter = $delimiter;
    }

    public function getDelimiter()
    {
        return $this->delimiter;
    }

    public function setRemoveBlank($removeBlank)
    {
        $this->removeBlank = $removeBlank;
    }

    public function isRemoveBlank()
    {
        return $this->removeBlank;
    }
}