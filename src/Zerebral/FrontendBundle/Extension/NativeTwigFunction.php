<?php

namespace Zerebral\FrontendBundle\Extension;

// TODO: sortBy is definitely not native twig function, rename to SortByTwigExtension
class NativeTwigFunction extends \Twig_Extension
{
    public function getFunctions()
    {
        return array(

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
        foreach($collection as $item) {
            $property->getValue($item);
        }

        uasort($collection, function($a, $b) use ($property) {
            return strcasecmp($property->getValue($a), $property->getValue($b));
        });

        return $collection;
    }

    public function getFilters()
    {
        return array(
            'sort_by' => new \Twig_Filter_Method($this, 'sortBy'),
        );
    }

    public function getName()
    {
        return 'native_twig_functions';
    }
}
