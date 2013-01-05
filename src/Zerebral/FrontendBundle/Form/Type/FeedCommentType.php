<?php

namespace Zerebral\FrontendBundle\Form\Type;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;
use Symfony\Component\Validator\Constraints\Collection;

use Zerebral\FrontendBundle\Form\Type\FeedContentType;

class FeedCommentType extends AbstractType {
    public function buildForm(FormBuilderInterface $builder, array $options) {
        $builder->add('feedContent', new FeedContentType(), array('required' => true));
    }

    public function getName() {
        return 'feed_comment';
    }

    public function setDefaultOptions(OptionsResolverInterface $resolver) {

        $resolver->setDefaults(
            array(
                'data_class' => 'Zerebral\BusinessBundle\Model\Feed\FeedComment',
                'csrf_protection' => false,
            )
        );
    }
}