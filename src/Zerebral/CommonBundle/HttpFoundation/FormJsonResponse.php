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

    private function setForm(Form $form)
    {
        $data = array();
        $data['is_empty'] = $form->isEmpty();
        $data['errors'] = array();
        foreach($form->all() as $child) {
            if (count($child->getErrors()) > 0) {
                $propertyPath = (string) $child->getPropertyPath();
                if (strpos($propertyPath, '[') === 0) {
                    $propertyPath = $form->getName() . $propertyPath;
                } else {
                    $propertyPath = $form->getName() . "[" . $propertyPath . "]";
                }

                $data['errors'][$propertyPath] = array();
                foreach($child->getErrors()as $error) {
                    $data['errors'][$propertyPath][] = $error->getMessage();
                }
            }
        }
        $data['has_errors'] = count($data['errors']) > 0;
        $this->setData($data);
    }
}
