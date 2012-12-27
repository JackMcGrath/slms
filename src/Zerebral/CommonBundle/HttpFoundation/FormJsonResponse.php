<?php

namespace Zerebral\CommonBundle\HttpFoundation;

use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Form\Form;

class FormJsonResponse extends JsonResponse
{

    /**
     * Constructor.
     *
     * @param Form   $form    The response form
     * @param integer $status  The response status code
     * @param array   $headers An array of response headers
     */
    public function __construct(Form $form, $status = 200, $headers = array())
    {
        parent::__construct('', $status, $headers);

        $this->setForm($form);
    }

//    /**
//     * {@inheritDoc}
//     */
//    public static function create(Form $form, $status = 200, $headers = array())
//    {
//        return new static($form, $status, $headers);
//    }

    private function getErrors($form, &$data, $name = '') {
        foreach($form->all() as $child) {
            $propertyPath = str_replace(array('[', ']'), '', $child->getPropertyPath());
            if (count($child->all()) > 0) {
                $this->getErrors($child, $data, ($name . '[' . $propertyPath . ']'));
            } else {
                if (count($child->getErrors()) > 0) {
                    $propertyPath = $name . '[' . $propertyPath . ']';
                    $data['errors'][$propertyPath] = array();
                    foreach($child->getErrors()as $error) {
                        $data['errors'][$propertyPath][] = $error->getMessage();
                    }
                }
            }
        }
    }

    private function setForm(Form $form)
    {

        $data = array();
        $data['is_empty'] = $form->isEmpty();
        $data['errors'] = array();
        $this->getErrors($form, $data, $form->getName());
        $data['has_errors'] = count($data['errors']) > 0;
        $this->setData($data);
    }
}
