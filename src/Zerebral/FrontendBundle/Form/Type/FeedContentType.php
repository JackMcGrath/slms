<?php

namespace Zerebral\FrontendBundle\Form\Type;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;
use Symfony\Component\Validator\Constraints\Collection;


class FeedContentType extends AbstractType {
    public function buildForm(FormBuilderInterface $builder, array $options) {
        $builder->add('type', 'hidden', array('required' => true));
        $builder->add('text', 'text', array('required' => false));
        $builder->add('link_url', 'text', array('required' => false));
        $builder->add('link_title', 'text', array('required' => false));
        $builder->add('link_description', 'text', array('required' => false));
        $builder->add('link_thumbnail_url', 'text', array('required' => false));

    }

    public function getName() {
        return 'feed_content';
    }

    public function setDefaultOptions(OptionsResolverInterface $resolver) {

        $resolver->setDefaults(
            array(
                'data_class' => 'Zerebral\BusinessBundle\Model\Feed\FeedContent'
            )
        );
    }
}