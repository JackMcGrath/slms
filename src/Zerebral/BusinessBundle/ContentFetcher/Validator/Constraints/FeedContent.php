<?php
namespace Zerebral\BusinessBundle\ContentFetcher\Validator\Constraints;

use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\Exception\ConstraintDefinitionException;

class FeedContent extends Constraint
{
    public $urlRegexpMessage = '%url% is not a valid URL';
    public $brokenUrlMessage = '%url% is broken';

    public $typeField;
    public $linkUrlField;
    public $linkDescriptionField;
    public $linkThumbnailUrlField;



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

        if (empty($this->linkDescriptionField)) {
            throw new ConstraintDefinitionException("You should specify linkDescription field");
        }

        if (empty($this->linkThumbnailUrlField)) {
            throw new ConstraintDefinitionException("You should specify linkThumbnailUrl field");
        }

    }

    /** {@inheritDoc} */
    public function getRequiredOptions()
    {
        return array('typeField', 'linkUrlField', 'linkDescriptionField', 'linkThumbnailUrlField');
    }

    /** {@inheritDoc} */
    public function getTargets()
    {
        return self::CLASS_CONSTRAINT;
    }
}