<?php

namespace Zerebral\CommonBundle\Component;

class DelegateBehavior extends \DelegateBehavior
{
    public function objectCall($builder)
    {
        return '';
//        $script = '';
//        foreach ($this->delegates as $delegate => $type) {
//            $delegateTable = $this->getDelegateTable($delegate);
//            if ($type == self::ONE_TO_ONE) {
//                $fks = $delegateTable->getForeignKeysReferencingTable($this->getTable()->getName());
//                $fk = $fks[0];
//                $ARFQCN = $builder->getNewStubObjectBuilder($fk->getTable())->getFullyQualifiedClassname();
//                $ARClassName = $builder->getNewStubObjectBuilder($fk->getTable())->getClassname();
//                $relationName = $builder->getRefFKPhpNameAffix($fk, $plural = false);
//            } else {
//                $fks = $this->getTable()->getForeignKeysReferencingTable($delegate);
//                $fk = $fks[0];
//                $ARFQCN = $builder->getNewStubObjectBuilder($delegateTable)->getFullyQualifiedClassname();
//                $ARClassName = $builder->getNewStubObjectBuilder($delegateTable)->getClassname();
//                $relationName = $builder->getFKPhpNameAffix($fk);
//            }
//                $script .= "
//if (is_callable(array('$ARFQCN', \$name))) {
//    if (!\$delegate = \$this->get$relationName()) {
//        \$delegate = new $ARClassName();
//        \$this->set$relationName(\$delegate);
//    }
//
//    return call_user_func_array(array(\$delegate, \$name), \$params);
//}";
//        }
//
//        return $script;
    }

    public function objectMethods($builder)
    {
        $script = '';
        foreach ($this->delegates as $delegate => $type) {
            $delegateTable = $this->getDelegateTable($delegate);
            if ($type == self::ONE_TO_ONE) {
                $fks = $delegateTable->getForeignKeysReferencingTable($this->getTable()->getName());
                $fk = $fks[0];
                $ARFQCN = $builder->getNewStubObjectBuilder($fk->getTable())->getFullyQualifiedClassname();
                $ARClassName = $builder->getNewStubObjectBuilder($fk->getTable())->getClassname();
                $relationName = $builder->getRefFKPhpNameAffix($fk, $plural = false);
            } else {
                $fks = $this->getTable()->getForeignKeysReferencingTable($delegate);
                $fk = $fks[0];
                $ARFQCN = $builder->getNewStubObjectBuilder($delegateTable)->getFullyQualifiedClassname();
                $ARClassName = $builder->getNewStubObjectBuilder($delegateTable)->getClassname();
                $relationName = $builder->getFKPhpNameAffix($fk);
            }

            /** @var \Column[] */
            foreach($delegateTable->getColumns() as $column) {

            }
                $script .= "
if (is_callable(array('$ARFQCN', \$name))) {
    if (!\$delegate = \$this->get$relationName()) {
        \$delegate = new $ARClassName();
        \$this->set$relationName(\$delegate);
    }

    return call_user_func_array(array(\$delegate, \$name), \$params);
}";
        }

        return $script;
    }
}
