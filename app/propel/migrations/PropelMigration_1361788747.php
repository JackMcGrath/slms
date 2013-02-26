<?php

/**
 * Data object containing the SQL and PHP code to migrate the database
 * up to version 1361788747.
 * Generated on 2013-02-25 12:39:07 
 */
class PropelMigration_1361788747
{

    public function preUp($manager)
    {
        // add the pre-migration code here
    }

    public function postUp($manager)
    {
        // add the post-migration code here
    }

    public function preDown($manager)
    {
        // add the pre-migration code here
    }

    public function postDown($manager)
    {
        // add the post-migration code here
    }

    /**
     * Get the SQL statements for the Up migration
     *
     * @return array list of the SQL strings to execute for the Up migration
     *               the keys being the datasources
     */
    public function getUpSQL()
    {
        return array (
            'default' => '
                ALTER TABLE `course_students`
                ADD COLUMN `is_active`  tinyint(1) UNSIGNED NOT NULL DEFAULT 1 AFTER `created_at`;
            ',
        );
    }

    /**
     * Get the SQL statements for the Down migration
     *
     * @return array list of the SQL strings to execute for the Down migration
     *               the keys being the datasources
     */
    public function getDownSQL()
    {
        return array (
            'default' => '
                ALTER TABLE `course_students`
                DROP COLUMN `is_active`;
            ',
        );
    }

}