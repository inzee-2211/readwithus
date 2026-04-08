Restoration Setup Instructions
Step 1: To setup restore database, ensure that below list of files exists:
    - {root}/application/controllers/RestoreSystemController.php
    - {root}/application/models/Restore.php
    - {root}/restore/view/header-bar.php
Step 2: Create three databases and import your database to all.
    - Base database which will be copied to the other dbs.
    - Second database which will be connected with the application.
    - Third database which will be restored in the background.
Step 3: In /application/models/Restore.php file update the following settings:
    - replace the database names for constants: DATABASE_BASE, DATABASE_FIRST & DATABASE_SECOND.
    - Update the constant RESTORE_TIME_INTERVAL_HOURS value to set the number of hours after which the restoration process will be executed.
Step 4: Setup configurations:
    - Set CONF_RESTORED_SUCCESSFULLY = 0 in tbl_configurations for second table which is not connected.
    - Set conf_auto_restore_on=1 in tbl_configurations table in all imported databases.
Step 5: Edit {root}/public/settings.php
    - Update database name in define('CONF_DB_NAME', <database_to_be_connected>);
Step 6: Add the following query into your DATABASE_BASE file and also execute in the currently connected db.
    - INSERT INTO `tbl_cron_schedules` (`cron_name`, `cron_command`, `cron_duration`, `cron_active`) VALUES ('Database Restoration', 'restoreDb', 7200, 1);
Step 7: Set the restoration setup date & time in RESTORATION_SETUP_DATE variable in Restore.php file. This is required to update the sessions time.
Step 8: Execute restoration cron manually for fist time after setup the restoration. Hit the below
        URL : {domain}/cron/index/{database-restoration-cron-id}