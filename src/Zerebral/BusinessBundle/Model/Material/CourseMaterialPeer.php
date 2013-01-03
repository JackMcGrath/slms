<?php

namespace Zerebral\BusinessBundle\Model\Material;

use Zerebral\BusinessBundle\Model\Material\om\BaseCourseMaterialPeer;

class CourseMaterialPeer extends BaseCourseMaterialPeer
{
    public static function getGrouped($course, $materialGroupingType, $folder = null)
    {
        $dayMaterials = array();
        $c = new \Criteria();
        if ($folder) {
            $c->add('folder_id', $folder->getId(), \Criteria::EQUAL);
        }
        $c->addJoin(self::FILE_ID, \Zerebral\BusinessBundle\Model\File\FilePeer::ID, \Criteria::LEFT_JOIN);
        $c->addAscendingOrderByColumn('LOWER(files.name)');

        foreach ($course->getCourseMaterials($c) as $material) {
            if ($materialGroupingType == 'date') {
                $dayMaterials[strtotime($material->getCreatedAt('Y-m-d'))][] = $material;
            } else if ($materialGroupingType == 'folder') {
                $folderName = $material->getCourseFolder() ? $material->getCourseFolder()->getName() : 'No folder';
                $dayMaterials[$folderName][] = $material;
            } else {
                $dayMaterials[][] = $material;
            }
        }

        ksort($dayMaterials);
        //Move array without folder to end of array
        if ($materialGroupingType == 'folder' && array_key_exists('No folder', $dayMaterials)) {
            $noFolder = $dayMaterials['No folder'];
            unset($dayMaterials['No folder']);
            $dayMaterials['No folder'] = $noFolder;
        }

        return $dayMaterials;
    }
}
