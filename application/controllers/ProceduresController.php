<?php

/**
 * Procedures Controller
 *  
 * @package YoCoach
 * @author Fatbit Team
 */
class ProceduresController extends MyAppController
{

    public function index()
    {
        $db = FatApp::getDb();
        $con = $db->getConnectionObject();
        $queries = [
            "DROP FUNCTION IF EXISTS `GETBLOGCATCODE`",
            "CREATE FUNCTION `GETBLOGCATCODE`(`id` INT) RETURNS varchar(255) CHARSET utf8
            BEGIN
                    DECLARE code VARCHAR(255);
                    DECLARE catid INT(11);
                    SET catid = id;
                    SET code = '';
                    WHILE catid > 0  AND LENGTH(code) < 240 DO
                            SET code = CONCAT(RIGHT(CONCAT('000000', catid), 6), '_', code);
                            SELECT bpcategory_parent INTO catid FROM tbl_blog_post_categories WHERE bpcategory_id = catid;
                    END WHILE;
                    RETURN code;
            END",
            "DROP FUNCTION IF EXISTS `GETBLOGCATORDERCODE`",
            "CREATE FUNCTION `GETBLOGCATORDERCODE`(`id` INT) RETURNS varchar(500) CHARSET utf8
            BEGIN
                    DECLARE code VARCHAR(255);
                    DECLARE catid INT(11);
                    DECLARE myorder INT(11);
                    SET catid = id;
                    SET code = '';
                    set myorder = 0;
                    WHILE catid > 0   AND LENGTH(code) < 240 DO
                            SELECT bpcategory_parent, bpcategory_order  INTO catid, myorder FROM tbl_blog_post_categories WHERE bpcategory_id = catid;
                            SET code = CONCAT(RIGHT(CONCAT('000000', myorder), 6), code);
                    END WHILE;
                    RETURN code;
            END"
        ];
        foreach ($queries as $qry) {
            if (!$con->query($qry)) {
                die($con->error);
            }
        }
        echo 'Created All the Procedures.';
    }

}
