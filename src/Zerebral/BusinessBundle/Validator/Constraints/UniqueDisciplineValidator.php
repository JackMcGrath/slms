<?php

namespace Zerebral\BusinessBundle\Validator\Constraints;

use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidator;

use Zerebral\BusinessBundle\Model\Course\DisciplineQuery;
use Zerebral\BusinessBundle\Model\Course\DisciplinePeer;
use Zerebral\BusinessBundle\Model\Course\Discipline;

class UniqueDisciplineValidator extends ConstraintValidator
{
    /**
     * {@inheritdoc}
     * @param Discipline $discipline
     * @param UniqueDiscipline $constraint
     */
    public function validate($discipline, Constraint $constraint)
    {
        $existDiscipline = DisciplineQuery::create()
                ->where('TRIM(LOWER(' . DisciplinePeer::NAME . ')) = ?', trim(strtolower($discipline->getName())))
                ->where('(' . DisciplinePeer::TEACHER_ID . ' = ? OR ' . DisciplinePeer::TEACHER_ID . ' IS NULL)', $discipline->getTeacherId() ?: '')
                ->findOne();

        if (!empty($existDiscipline) && $existDiscipline->getId() != $discipline->getId()) {
            $this->context->addViolation($constraint->message);
        }
    }
}
