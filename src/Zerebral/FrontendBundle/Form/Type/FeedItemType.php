<?php

namespace Zerebral\FrontendBundle\Form\Type;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;
use Symfony\Component\Validator\Constraints\Collection;


class FeedItemType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder->add('feedContent', new FeedContentType(), array('required' => true));
        $builder->add('courseId', 'integer', array('required' => false));
    }

    public function getName()
    {
        return 'feed_item';
    }

    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {

        $resolver->setDefaults(
            array(
                'data_class' => 'Zerebral\BusinessBundle\Model\Feed\FeedItem',
                'cascade_validation' => true
            )
        );
    }
}