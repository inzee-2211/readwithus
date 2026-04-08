<?php

/**
 * This class is used to handle Themes
 * 
 * @package YoCoach
 * @author Fatbit Team
 */
class Theme extends MyAppModel
{

    const DB_TBL = 'tbl_themes';
    const DB_TBL_PREFIX = 'theme_';

    public function __construct(int $id = 0)
    {
        parent::__construct(static::DB_TBL, 'theme_id', $id);
    }

}
