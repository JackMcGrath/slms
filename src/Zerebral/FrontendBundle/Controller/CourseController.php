<?php

namespace Zerebral\FrontendBundle\Controller;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;

use Zerebral\BusinessBundle\Model as Model;

/**
 * @Route("/courses")
 */
class CourseController extends \Zerebral\CommonBundle\Component\Controller
{
    /**
     * @Route("/", name="courses")
     * @Template()
     */
    public function indexAction()
    {
        return array(
            'courses' => Model\Course\CourseQuery::create()->find(),
            'target' => 'courses'
        );
    }

    /**
     * @Route("/view/{id}", name="course_view")
     * @ParamConverter("course")
     * @Template()
     */
    public function viewAction(Model\Course\Course $course)
    {
        return array(
            'course' => $course
        );
    }

    /**
     * @Route("/edit/{id}", name="course_edit")
     * @ParamConverter("course")
     * @Template()
     */
    public function editAction(Model\Course\Course $course)
    {
        throw new \Exception('Not implemented!');
        return array(
            'course' => $course,
            'target' => 'courses'
        );
    }

    /**
     * @Route("/add", name="course_add")
     * @Template()
     */
    public function addAction()
    {
        $request = $this->getRequest();
        /**
         * @var \Zerebral\BusinessBundle\Model\User\User $user
         */
        $user = $this->getUser();
        $course = new Model\Course\Course();
        $grades = Model\Course\GradeLevelQuery::create()->find();
        $disciplines = Model\Course\DisciplineQuery::create()->find();

        if ($request->isMethod('post')) {
            try {
                $course->setName($request->get('name'));
                $course->setDescription($request->get('description'));
                $course->setGradeLevelId($request->get('grade_level_id'));
                $course->setDisciplineId($request->get('discipline_id'));
                $course->setCreatedBy($user->getTeacher()->getId());

                if ($course->validate()) {
                    $course->save();
                    return $this->redirect(
                        $this->generateUrl(
                            'course_view',
                            array(
                                'id' => $course->getId()
                            )
                        )
                    );
                }
            } catch (\Exception $e) {
                var_dump($e->getMessage());
                die;
            }
        }

        return array(
            'course' => $course,
            'grades' => $grades,
            'disciplines' => $disciplines,
            'target' => 'courses'
        );
    }

    /**
     * @Route("/delete/{id}", name="course_delete")
     * @ParamConverter("course")
     * @Template()
     */
    public function deleteAction(Model\Course\Course $course)
    {
        $course->delete();
        return $this->redirect($this->generateUrl('courses'));
    }
}
