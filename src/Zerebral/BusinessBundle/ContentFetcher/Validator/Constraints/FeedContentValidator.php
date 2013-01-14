<?php
namespace Zerebral\BusinessBundle\ContentFetcher\Validator\Constraints;

use Symfony\Component\Validator\ConstraintValidator;
use Symfony\Component\Validator\Constraint;
use Symfony\Component\Form\Util\PropertyPath;

use Zerebral\BusinessBundle\ContentFetcher\Fetcher;

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
     * @param mixed|\Zerebral\BusinessBundle\Model\Feed\FeedContent $object
     * @param \Symfony\Component\Validator\Constraint|FeedContent $constraint
     */
    public function validate($object, Constraint $constraint)
    {
        $typeValue = $this->getPropertyValue($object, $constraint->typeField);
        $linkUrlValue = $this->getPropertyValue($object, $constraint->linkUrlField);

        if (!in_array($typeValue, array('assignment', 'text'))) {

            // TODO: remove, empty url should be validate by EmptyValidator or similar
            if (trim(strlen($linkUrlValue)) == 0) {
                $this->context->addViolationAtSubPath($constraint->linkUrlField, $constraint->missingUrlMessage);
                return;
            }

            if (!$this->validateUrl($linkUrlValue)) {
                $this->context->addViolationAtSubPath($constraint->linkUrlField, $constraint->urlRegexpMessage, array('%url%' => $linkUrlValue));
                return;
            }

            // TODO: remove, url length should be validate by LengthValidator or similar
            if (mb_strlen($linkUrlValue) > 150) {
                $this->context->addViolationAtSubPath($constraint->linkUrlField, $constraint->longUrlMessage, array('%url%' => $linkUrlValue));
                return;
            }

            $fetcher = new Fetcher($linkUrlValue);
            if (!$fetcher->isLoaded()) {
                $this->context->addViolationAtSubPath($constraint->linkUrlField, $constraint->brokenUrlMessage, array('%url%' => $linkUrlValue));
                return;
            }

            if (!$fetcher->isMatchType($typeValue)) {
                $this->context->addViolationAtSubPath($constraint->linkUrlField, $constraint->wrongUrlTypeMessage, array('%url%' => $linkUrlValue, '%type%' => $typeValue));
                return;
            }

            $fetcher->parse();

            $object->setLinkTitle($fetcher->getTitle());
            $object->setLinkDescription($fetcher->getDescription());
            $object->setLinkThumbnailUrl($fetcher->getThumbmnailUrl());
        }
    }
}