<?php

namespace Zerebral\CommonBundle\Component;

use \PropelCollection;

class PropelCollectionBuilder
{

    const IGNORE_EMPTY = 1;

    public function __construct()
    {

    }

    /**
     * @param $modelClass
     * @param $attributesCollection
     * @param int $flags
     * @return \PropelCollection
     */
    public function fromAttributes($modelClass, $attributesCollection, $flags  = 0)
    {
        $collection = new PropelCollection();
        if ($flags & self::IGNORE_EMPTY)
            $attributesCollection = $this->ignoreEmpty($attributesCollection);

        foreach($attributesCollection as $attributes) {
            //update model by Query. Because model is new, id is set and it cause exception
            if (array_key_exists('id', $attributes) && !empty($attributes['id'])) {
                $modelName = $modelClass . 'Query';
                $model = $modelName::create()->findPk($attributes['id']);
            } else {
                $model = new $modelClass();
            }

            $model->fromArray($attributes, \BasePeer::TYPE_FIELDNAME);

            $collection->append($model);
        }
        return $collection;
    }

    protected function ignoreEmpty($attributes)
    {
        foreach($attributes as $key => $values) {
            $isEmpty = true;
            foreach($values as $value) {
                if ($value !== "" && !is_null($value)) {
                    $isEmpty = false;
                    break;
                }
            }
            if ($isEmpty)
                unset($attributes[$key]);
        }
        return $attributes;
    }
}
