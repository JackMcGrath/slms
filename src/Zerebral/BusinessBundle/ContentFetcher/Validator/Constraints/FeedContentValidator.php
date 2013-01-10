<?php
namespace Zerebral\BusinessBundle\ContentFetcher\Validator\Constraints;

use Symfony\Component\Validator\ConstraintValidator;
use Symfony\Component\Validator\Constraint;
use Symfony\Component\Form\Util\PropertyPath;

class FeedContentValidator extends ConstraintValidator
{


    private function validateUrl($linkUrl)
    {
        return filter_var($linkUrl, FILTER_VALIDATE_URL);
    }

    /**
     * Get object property value by property path
     *
     * @param object $object
     * @param string $field property path
     * @return string
     */
    protected function getPropertyValue($object, $field)
    {
        $path = new PropertyPath($field);
        return $path->getValue($object);
    }

    /**
     * @param mixed $object
     * @param \Symfony\Component\Validator\Constraint|FeedContent $constraint
     */
    public function validate($object, Constraint $constraint)
    {
        $typeValue = $this->getPropertyValue($object, $constraint->typeField);
        $linkUrlValue = $this->getPropertyValue($object, $constraint->linkUrlField);
        $linkDescriptionValue = $this->getPropertyValue($object, $constraint->linkDescriptionField);
        $linkThumnailUrlValue = $this->getPropertyValue($object, $constraint->linkThumbnailUrlField);


        if (!in_array($typeValue, array('assignment', 'text'))) {
            if (!$this->validateUrl($linkUrlValue)) {
                $this->context->addViolationAtSubPath($constraint->linkUrlField, $constraint->urlRegexpMessage, array('%url%' => $linkUrlValue));
            }
        }
    }
}