<p align="center"><a href="https://v3.yo-coach.com/" target="_blank"><img src="https://v3.yo-coach.com/images/yocoach-logo.svg" width="400"></a></p>

**<p align="center">Version RV-3.0.0</p>**

## About YoCoach

Yo!Coach is a ready-made software to create a tutoring and consultation platform based on video conferencing functionality. The eLearning software is integrated with a suite of interactive features to enable smooth navigation and workflows for both learners and tutors. As a fully customizable solution, Yo!Coach can be deployed to cater to many common and vital functionalities in platforms like Verbling, Preply, Italki, and Cambly.

Yo!Coach powered online tutoring or consulting platforms incorporate a full-blown marketing module instrumental in gaining wider reach and business promotion.

## YoCoach V2 to V3

YoCoach V3’s software architecture is re-designed to handle and manage high user data, While taking care of quality code and system performance. Data independent code architecture is key to YoCoach V3 performance.

## Features
- **Teachers Availability:** We have two availability types: General Availability and Weekly Availability. Managing tutors availability for user specific timezone and conversion to convert system date according to user’s timezone was a challenging task for 70+ countries supporting DST(Daylight Saving Time). Completely redesigned the database structure, and have also added another table to maintain exact availability of the user(which Convert general to weekly availability) which inturn solved the time difference we face in countries with DST enabled.
- **Teacher & Group Class Search:** A major update on performance optimization to Teacher Search and Group Classes Search has been performed by optimizing database structure & queries and has created search models for teacher and class search by extending Yo!Coach base search model.  Redone complete Search form design to improve UX/UI.
- **Seamless Checkout:** Checkout is now on a single popup which is used for different order types. One step checkout has been used for booking Group class, Group Class package, Adding Money to Wallet and Purchasing Gift Cards.
- Inline & Unlimited Scheduling: Till YoCoach V2.4 We only had single session scheduling after booking. Now Learners can check Tutor’s exact availability and schedule unlimited sessions before booking a Tutor or making any payment.
- **Payment Gateways:** Redefined structure of payment gateways. Now Payment gateways are considered as separate entities which can be attached and detached as a plugin. Bank Transfer(Offline) added as a new payment gateway. We have an option to provide only required payment gateway to clients.
- **Meeting Tools:** Same as Payment Gateways we redeveloped Meeting Tools in V3. Any Meeting tool can be added/removed as per client’s requirements. Integrated meeting tools are Zoom Meetings, Lesson Space and AtomChat/CometChat.
- Fully Manageable Search Engines friendly URLs: URL rewriting allows URLs to be more easily remembered by the user. Option to replace the complete url path with any other desired url is possible now. Ability to add different custom URLs for different languages.
- **User Transactions:** Categorized user's Transactions for each type of order payment, Student Refund, Teacher Paid, Money Withdrawn, Money Deposit, Gift Card redeemed, Support credit and Support debit. Ability to calculate Total IN and OUT of a user’s money in the platform. We can have every type of report for every type of user.
- **Reports Sales & Settlements:** Database structure has been changed completely for the orders section and V3 has the ability to provide any type of reports. As of now we have Lessons Top Languages, Classes Top Languages, Teacher Performance, Lesson Stats,  Sales Report and  Settlements Report. Admin can check everyday’s Gross Sale, Discount, Net Sale, Paid to Teacher and Refunded to Students.
- **Group Classes:** Group Classes are now treated as separate entities, It may be a pre scheduled event by the Teacher and any Learner/Student can book and join it. Earlier it was mixed with one to one lesson/session and calculation of Teacher payment on success and refund to student was complex.
- **Manageable Themes:** Theme management is available with six basic colors Primary Color & Inverse Color,  Secondary Color & Inverse Color And Footer Color & Inverse Color.
- **Email Templates:** Updated all Email templates with new HTML structure. Email save and preview option available now which Admin can use to preview email templates. Header and Footer for all email templates are manageable at a single place.
- **Manage Orders:** Manage all 6 types of orders and order’s status with all details. Keyword Search for Main and all other order types. More detail has been added in the orders table which makes reporting more efficient. Order Id, User Name, Order Type, Items, Total, Discount, Net Total, Payment, Status, Pay Method, Datetime
- **Learner & Teacher Dashboard:** Seperate dashboard for Learners & Teachers. New Base Search model has been created to list Lessons, Subscriptions, Group classes, Order classes, Package classes, Gift Cards, All Orders and Report Issues.Note: Base Search model is well structured and designed to make searches for bulk data.


## System Requirements

The following technical requirements are needed to set up Yo!Coach:

| SrNo | Software | Version | Help|
| ------------ | ------------ | ------------ | ------------ |
| 1 | Ubuntu x86, x86-64 | 20.04+  | [Ubuntu](https://ubuntu.com/)  |
| 2 | Web Servers | Apache 2.4.x | [Apache](https://httpd.apache.org/)  | 
| 3 | PHP Version | 7.4.x |  [PHP](https://www.php.net/) | 
| 4 | MySQL Version | 8.0.x | [MySQL](https://www.mysql.com/)  | 

## Required PHP Extentions

GD with Free Font support, Zlib with zip support, DOM, Mbstring should be enabled, Iconv function should be enabled, Fileinfo function should be enabled, Ioncube Loader

- i. Safe_mode off
- ii. Memory_limit 64M or more (Some pages with advanced feature may need upto 128M)

## Version History

- **Version 3.0**: YoCoach V3’s software architecture is re-designed to handle and manage high user data, While taking care of quality code and system performance
- **Version 2.4**: Theme management for Website and Email Templates.
- **Version 2.3**: Added new features Multiple Price Slab & GDPR - Right to Erasure.
- **Version 2.2**: Payment Gateways(Paystack,PayGate), Progressive Web Apps (PWA), Session Duration Management. New Theme and User dashbords
- **Version 2.1**: Added new features Zoom, Lessonspace, Google Analytics and PayPal Payouts

## Documentation & Updates

- [Recent Updates](https://www.yo-coach.com/recent-updates.html) Recent Versions and Updates.
- [Documentation](https://www.yo-coach.com/documentation.html) Find All The Resources At One Place To Help You Setup Your Online Tutoring & Consultation Platform Successfully.

## Installation Instructions

1. **Prerequisite**

	- You are able to aceess the server using **SSH**.
	- You have installed **Apache**, **MySql** and **PHP** on linux server.
	- You have installed ioncube loader compatible to **Fatbit library V2.3**

1. **Clone YoCoachV3**

    Install git and clone Clone YoCoachV3 to your root directory.

		sudo apt install git
		git --version
		cd ./path/to/your/rootdir
		git clone git@github.com:your/repository.git .
		
    **Note:** OR if you have script files, Upload script files to the root directory and continue.

2. **Copy setup-files files**

    Copy `conf` `user-uploads` `public` `mbs-errors.log` and everything from `setup-files` to root directory.

		cp -r setup-files/* .

3. **Install Fatbit Library**

    Download and install Fatbit library and unzip to library directory and then remove `core.zip`.

		wget http://fatlib.4livedemo.com/download/v2.3/core.zip
		unzip core.zip -d library
		rm core.zip

4. **Install Dependencies**

    Never use composer update, It may break your application. It will update versions of packages to be installed and may not be compatible with your system.

		composer install

5. **Import Database**

    We have `sample.sql` and `blank.sql` database in `database` directory. Database can be imported as per requirement.

		mysql -u mysqlUsername -p mysqlDatabase < database/sample.sql
		mysqlPassword

6. **Connect Database**

    Configure MySQL database connection settings in `public/settings.php`

		nano public/settings.php
    `settings.php` file will look like below

		<?php
		define('CONF_WEBROOT_FRONTEND', '/');
		define('CONF_WEBROOT_DASHBOARD', '/dashboard/');
		define('CONF_WEBROOT_BACKEND', '/admin/');
		define('CONF_DB_SERVER', 'localhost');
		define('CONF_DB_USER', 'mysqlUsername');
		define('CONF_DB_PASS', 'mysqlPassword');
		define('CONF_DB_NAME', 'mysqlDatabase');
    Save and Exit (Ctrl+x and Shift+y)

7. **Grant Permissions**

		chmod -R 777 user-uploads
		chmod -R 777 public/cache
		chmod 777 mbs-errors.log
		chmod 777 public/error_log
		chmod 777 public/robots.txt

8. **Upload License**

		nano license.txt
    Paste license key and Save and Exit (Ctrl+x and Shift+y)

9. **Setup Cron Job**

		crontab -e
		*/2 * * * * /usr/bin/curl  -s https://yourdomain.com/cron > /dev/null 2>&1
    Save and Exit (Ctrl+x and Shift+y)

    **Note:** The command may vary depending upon the products and their version.

10. **Create Procedure**

    Login to admin dashboard and Open url https://yourdomain.com/admin/procedures to create procedures

11. **Custom Configuration**

    Update `{root}/conf/conf-common.php` as per your requirements

		define('SEARCH_MAX_COUNT', 10000);
		define('CONF_DEVELOPMENT_MODE', false);
		define('CONF_USE_FAT_CACHE', true);
		define('ALLOW_EMAILS', true);

