<?php

namespace Zerebral\FrontendBundle\Extension;

// TODO: sortBy is definitely not native twig function, rename to SortByTwigExtension
class NativeTwigFunction extends \Twig_Extension
{
    public function getFunctions()
    {
        return array();
    }

    public function getFilters()
    {
        return array(
            'sort_by' => new \Twig_Filter_Method($this, 'sortBy'),
            'max' => new \Twig_Filter_Method($this, 'max'),
            'max_key' => new \Twig_Filter_Method($this, 'maxKey'),
            'strtotime' => new \Twig_Filter_Method($this, 'stringToTime')
        );
    }

    /**
     * @param array $collection
     * @param $field
     * @return array
     */
    public function sortBy(array $collection, $field)
    {
        $property = new \Symfony\Component\Form\Util\PropertyPath($field);

        // hook for models
        // uasort couldn't work with lazy-loaded properties because they modify array
        foreach ($collection as $item) {
            $property->getValue($item);
        }

        uasort(
            $collection,
            function ($a, $b) use ($property) {
                return strcasecmp($property->getValue($a), $property->getValue($b));
            }
        );

        return $collection;
    }

    public function max($collection)
    {
        if (empty($collection)) {
            return -1;
        }
        return max($collection);
    }

    public function maxKey($collection)
    {
        return $this->max(array_keys((array)$collection));
    }



    public function stringToTime($time, $str)
    {
        return strtotime($str, strtotime($time));
    }

    public function getName()
    {
        return 'native_twig_functions';
    }
}
