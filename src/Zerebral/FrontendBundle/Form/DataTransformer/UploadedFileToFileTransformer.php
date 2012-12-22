<?php
namespace Zerebral\FrontendBundle\Form\DataTransformer;

use Symfony\Component\Form\DataTransformerInterface;
use Symfony\Component\Form\Exception\TransformationFailedException;

use Zerebral\BusinessBundle\Model\File;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Zerebral\BusinessBundle\Model\File\FileQuery;

class UploadedFileToFileTransformer implements DataTransformerInterface
{
    public function transform($file) {
        return $file;
    }


    public function reverseTransform($file)
    {
        if (is_null($file->getUploadedFile())) {
            return null;
        }

        return $file;
    }
}