<?php
namespace Zerebral\BusinessBundle\ContentFetcher\Validator\Constraints;

use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\Exception\ConstraintDefinitionException;

class FeedContent extends Constraint
{
    public $urlRegexpMessage = '%url% is not a valid URL';
    public $brokenUrlMessage = 'URL %url% is broken';
    public $longUrlMessage = '%url% is too long. Please use URL shortener';
    public $wrongUrlTypeMessage = 'URL %url% is not valid for %type%';
    public $missingUrlMessage = 'Please insert URL for post';

    public $typeField;
    public $linkUrlField;


    /** {@inheritDoc} */
    public function __construct($options = null)
    {
        parent::__construct($options);

        if (empty($this->typeField)) {
            throw new ConstraintDefinitionException("You should specify type field");
        }

        if (empty($this->linkUrlField)) {
            throw new ConstraintDefinitionException("You should specify linkUrl field");
        }
    }

    /** {@inheritDoc} */
    public function getRequiredOptions()
    {
        return array('typeField', 'linkUrlField');
    }

    /** {@inheritDoc} */
    public function getTargets()
    {
        return self::CLASS_CONSTRAINT;
    }
}