<!--left panel start here-->
<?php $adminLoggedId = AdminAuth::getLoggedAdminId(); ?>
<span class="leftoverlay"></span>
<aside class="leftside">
    <div class="sidebar_inner">

        <ul class="leftmenu">
            <!--Dashboard-->
            <?php if ($objPrivilege->canViewAdminDashboard(true)) { ?>
                <li><a href="<?php echo MyUtility::makeUrl(); ?>"><?php echo Label::getLabel('LBL_DASHBOARD'); ?></a></li>
            <?php } ?>
            <?php
            if (
                $objPrivilege->canViewUsers(true) || $objPrivilege->canViewTeacherRequests(true) || $objPrivilege->canViewWithdrawRequests(true) ||
                $objPrivilege->canViewTeacherReviews(true) || $objPrivilege->canViewGdprRequests(true) || $objPrivilege->canViewAdminUsers(true)
            ) {
            ?>
                <li class="haschild"><a href="javascript:void(0);"><?php echo Label::getLabel('LBL_MANAGE_USERS'); ?></a>
                    <ul>
                        <?php if ($objPrivilege->canViewUsers(true)) { ?>
                            <li><a href="<?php echo MyUtility::makeUrl('Users'); ?>"><?php echo Label::getLabel('LBL_USERS'); ?></a></li>
                        <?php }
                        if ($objPrivilege->canViewTeacherRequests(true)) { ?>
                            <li><a href="<?php echo MyUtility::makeUrl('TeacherRequests'); ?>"><?php echo Label::getLabel('LBL_TEACHER_REQUESTS'); ?></a></li>
                        <?php }
                        if ($objPrivilege->canViewWithdrawRequests(true)) { ?>
                            <li><a href="<?php echo MyUtility::makeUrl('WithdrawRequests'); ?>"><?php echo Label::getLabel('LBL_WITHDRAW_REQUESTS'); ?></a></li>
                        <?php }
                        if ($objPrivilege->canViewTeacherReviews(true)) { ?>
                            <li><a href="<?php echo MyUtility::makeUrl('RatingReviews'); ?>"><?php echo Label::getLabel('LBL_TEACHER_REVIEWS'); ?></a></li>
                        <?php }
                        if ($objPrivilege->canViewGdprRequests(true)) { ?>
                            <li><a href="<?php echo MyUtility::makeUrl('GdprRequests') ?>"><?php echo Label::getLabel('LBL_GDPR_REQUESTS'); ?></a></li>
                        <?php }
                        if ($objPrivilege->canViewAdminUsers(true)) { ?>
                            <li><a href="<?php echo MyUtility::makeUrl('AdminUsers') ?>"><?php echo Label::getLabel('LBL_Manage_Admins'); ?></a></li>
                        <?php } ?>
                    </ul>
                </li>
            <?php }
            if ($objPrivilege->canViewGroupClasses(true) || $objPrivilege->canViewPackageClasses(true)) { ?>
                <li class="haschild"><a href="javascript:void(0);"><?php echo Label::getLabel('LBL_GROUP_CLASSES'); ?></a>
                    <ul>
                        <?php if ($objPrivilege->canViewGroupClasses(true)) { ?>
                            <li><a href="<?php echo MyUtility::makeUrl('GroupClasses'); ?>"><?php echo Label::getLabel('LBL_GROUP_CLASSES'); ?></a></li>
                        <?php }
                        if ($objPrivilege->canViewPackageClasses(true)) { ?>
                            <li><a href="<?php echo MyUtility::makeUrl('PackageClasses'); ?>"><?php echo Label::getLabel('LBL_PACKAGE_CLASSES'); ?></a></li>
                        <?php } ?>
                    </ul>
                </li>
            <?php } ?>
            
            <?php
            if (
                $objPrivilege->canViewCourses(true) ||
                $objPrivilege->canViewCategories(true) ||
                $objPrivilege->canViewCourseRequests(true) ||
                $objPrivilege->canViewCertificates(true) ||
                $objPrivilege->canViewCourseRefundRequests(true) || 
                $objPrivilege->canViewCourseLanguage(true) || 
                $objPrivilege->canViewCourseReviews(true)
            ) { ?>

<li class="haschild"><a href="javascript:void(0);">Courses</a>
                    <ul>
                        <li><a href="<?php echo MyUtility::makeUrl('Questions'); ?>"><?php echo Label::getLabel('LBL_QUESTIONS'); ?></a></li>
                        <li><a href="<?php echo MyUtility::makeUrl('Quizzes'); ?>"><?php echo Label::getLabel('LBL_QUIZZES'); ?></a></li>
                    </ul>
                </li>
                <li class="haschild">
                    <a href="javascript:void(0);"><?php echo Label::getLabel('LBL_MANAGE_COURSES'); ?></a>
                    <ul>
                        <?php if ($objPrivilege->canViewCourseLanguage(true)) { ?>
                            <li>
                                <a href="<?php echo MyUtility::makeUrl('CourseLanguages'); ?>"><?php echo Label::getLabel('LBL_COURSE_LANGUAGES'); ?></a>
                            </li>
                        <?php } ?>
                        <?php if ($objPrivilege->canViewCategories(true)) { ?>
                            <li>
                                <a href="<?php echo MyUtility::makeUrl('Categories'); ?>"><?php echo Label::getLabel('LBL_CATEGORIES'); ?></a>
                            </li>
                        <?php } ?>
                        <?php if ($objPrivilege->canViewCourses(true)) { ?>
                            <li>
                                <a href="<?php echo MyUtility::makeUrl('Courses'); ?>"><?php echo Label::getLabel('LBL_COURSES'); ?></a>
                            </li>
                        <?php } ?>
                        <?php if ($objPrivilege->canViewCourseRequests(true)) { ?>
                            <li>
                                <a href="<?php echo MyUtility::makeUrl('CourseRequests'); ?>"><?php echo Label::getLabel('LBL_APPROVAL_REQUESTS'); ?></a>
                            </li>
                        <?php } ?>
                        <?php if ($objPrivilege->canViewCertificates(true)) { ?>
                            <li>
                                <a href="<?php echo MyUtility::makeUrl('Certificates'); ?>"><?php echo Label::getLabel('LBL_CERTIFICATES'); ?></a>
                            </li>
                        <?php } ?>
                        <?php if ($objPrivilege->canViewCourseReviews(true)) { ?>
                            <li>
                                <a href="<?php echo MyUtility::makeUrl('RatingReviews', 'index', [AppConstant::COURSE]); ?>"><?php echo Label::getLabel('LBL_COURSE_REVIEWS'); ?></a>
                            </li>
                        <?php } ?>
                        <?php if ($objPrivilege->canViewCourseRefundRequests(true)) { ?>
                            <li>
                                <a href="<?php echo MyUtility::makeUrl('CourseRefundRequests'); ?>"><?php echo Label::getLabel('LBL_REFUND_REQUESTS'); ?></a>
                            </li>
                        <?php } ?>
                    </ul>
                </li>
            <?php } ?>



            

 
                <!-- <li class="haschild">
                    <a href="javascript:void(0);"><?php echo Label::getLabel('LBL_MANAGE_COURSES_NEW'); ?></a>
                    <ul>
                        <?php if ($objPrivilege->canViewCourseLanguage(true)) { ?>
                            <li>
                                <a href="<?php echo MyUtility::makeUrl('Coursemanagement'); ?>"><?php echo Label::getLabel('LBL_COURSE_CONTENT_MANAGEMENT'); ?></a>
                            </li>
                        <?php } ?>
                          <?php if ($objPrivilege->canViewCategories(true)) { ?>
                            <li>
                                <a href="<?php echo MyUtility::makeUrl('Questionbank'); ?>"><?php echo Label::getLabel('LBL_QUESTION_BANK_MANAGEMENT'); ?></a>
                            </li>
                        <?php } ?>  
                      
                         
                    </ul>
                </li> -->

                <!-- <li class="haschild">
                    <a href="<?php echo MyUtility::makeUrl('Coursemanagement'); ?>"><?php echo Label::getLabel('LBL_QUIZ_MANAGEMENT'); ?></a>
                   
                </li>  -->
           

<li class="haschild">
                    <a href="javascript:void(0);"><?php echo Label::getLabel('LBL_MANAGE_QUIZ'); ?></a>
                    <ul>
                        <?php if ($objPrivilege->canViewCategories(true)) { ?>
                            <li>
                                <a href="<?php echo MyUtility::makeUrl('Quizsetting'); ?>"><?php echo Label::getLabel('LBL_QUIZ_SETTINGS'); ?></a>
                            </li>
                        <?php } ?> 
                      
                        <?php if ($objPrivilege->canViewCategories(true)) { ?>
                            <li>
                                <a href="<?php echo MyUtility::makeUrl('Quiztopic'); ?>"><?php echo Label::getLabel('LBL_QUIZ_TOPICS'); ?></a>
                            </li>
                        <?php } ?>  
                        <!-- lines added by rehan end here -->
                        <?php if ($objPrivilege->canViewCourseLanguage(true)) { ?>
                            <li>
                                <a href="<?php echo MyUtility::makeUrl('Coursemanagement'); ?>"><?php echo Label::getLabel('LBL_QUIZ_MANAGEMENT'); ?></a>
                            </li>
                        <?php } ?>
                          <?php if ($objPrivilege->canViewCategories(true)) { ?>
                            <li>
                                <a href="<?php echo MyUtility::makeUrl('Quizvisiter'); ?>"><?php echo Label::getLabel('LBL_VISITER_DETAILS'); ?></a>
                            </li>
                        <?php } ?>  

                         <?php if ($objPrivilege->canViewCategories(true)) { ?>
                            <li>
                                <a href="<?php echo MyUtility::makeUrl('Tutorrequest'); ?>"><?php echo Label::getLabel('LBL_TUTOR_REQUEST'); ?></a>
                            </li>
                        <?php } ?>  
                      
                         
                    </ul>
                </li>






            <?php
            if (
                $objPrivilege->canViewOrders(true) ||
                $objPrivilege->canViewLessonsOrders(true) || 
                $objPrivilege->canViewPackagesOrders(true) ||
                $objPrivilege->canViewClassesOrders(true) ||
                $objPrivilege->canViewWalletOrders(true) ||
                $objPrivilege->canViewGiftcardOrders(true) ||
                $objPrivilege->canViewCoursesOrders(true) ||
                $objPrivilege->canViewSubscriptionOrders(true)
            ) {
            ?>
                <li class="haschild"><a href="javascript:void(0);"><?php echo Label::getLabel('LBL_MANAGE_ORDERS'); ?></a>
                    <ul>
                        <?php if ($objPrivilege->canViewOrders(true)) { ?>
                            <li><a href="<?php echo MyUtility::makeUrl('Orders'); ?>"><?php echo Label::getLabel('LBL_ALL_ORDERS'); ?></a></li>
                        <?php }
                        if ($objPrivilege->canViewLessonsOrders(true)) { ?>
                            <li><a href="<?php echo MyUtility::makeUrl('Lessons'); ?>"><?php echo Label::getLabel('LBL_LESSONS_ORDERS'); ?></a></li>
                        <?php }
                        if ($objPrivilege->canViewSubscriptionOrders(true)) { ?>
                            <li><a href="<?php echo MyUtility::makeUrl('Subscriptions'); ?>"><?php echo Label::getLabel('LBL_SUBSCRIPTIONS_ORDERS'); ?></a></li>
                        <?php }
                        if ($objPrivilege->canViewClassesOrders(true)) { ?>
                            <li><a href="<?php echo MyUtility::makeUrl('Classes'); ?>"><?php echo Label::getLabel('LBL_CLASSES_ORDERS'); ?></a></li>
                        <?php } if ($objPrivilege->canViewCoursesOrders(true)) { ?>
                            <li><a href="<?php echo MyUtility::makeUrl('CourseOrders'); ?>"><?php echo Label::getLabel('LBL_COURSE_ORDERS'); ?></a></li>
                        <?php }
                        if ($objPrivilege->canViewPackagesOrders(true)) { ?>
                            <li><a href="<?php echo MyUtility::makeUrl('Packages'); ?>"><?php echo Label::getLabel('LBL_PACKAGES_ORDERS'); ?></a></li>
                        <?php }
                        if ($objPrivilege->canViewGiftcardOrders(true)) { ?>
                            <li><a href="<?php echo MyUtility::makeUrl('Giftcards'); ?>"><?php echo Label::getLabel('LBL_GIFTCARD_ORDERS'); ?></a></li>
                        <?php }
                        if ($objPrivilege->canViewWalletOrders(true)) { ?>
                            <li><a href="<?php echo MyUtility::makeUrl('Wallet'); ?>"><?php echo Label::getLabel('LBL_WALLET_ORDERS'); ?></a></li>
                        <?php } ?>
                    </ul>
                </li>
            <?php }
            
            
            if ($objPrivilege->canViewIssuesReported(true)) { ?>
             
                
                <li class="haschild"><a href="javascript:void(0);"><?php echo Label::getLabel('LBL_ISSUES_REPORTED'); ?></a>
                    <ul>
                        <li><a href="<?php echo MyUtility::makeUrl('ReportedIssues', 'escalated'); ?>"><?php echo Label::getLabel('LBL_ESCALATED_ISSUES'); ?></a></li>
                        <li><a href="<?php echo MyUtility::makeUrl('ReportedIssues'); ?>"><?php echo Label::getLabel('LBL_ALL_REPORTED_ISSUES'); ?></a></li>
                    </ul>
                </li>
            <?php }
            if (
                $objPrivilege->canViewPreferences(true) || $objPrivilege->canViewSpeakLanguage(true) ||
                $objPrivilege->canViewTeachLanguage(true) || $objPrivilege->canViewIssueReportOptions(true) ||
                $objPrivilege->canViewPriceSlab(true)
            ) { ?>
                <li class="haschild"><a href="javascript:void(0);"><?php echo Label::getLabel('LBL_Teacher_Preferences'); ?></a>
                    <ul>
                        <?php if ($objPrivilege->canViewPreferences(true)) { ?>
                            <li><a href="<?php echo MyUtility::makeUrl('preferences', 'index', [1]); ?>"><?php echo Label::getLabel('LBL_ACCENTS'); ?></a></li>
                            <li><a href="<?php echo MyUtility::makeUrl('preferences', 'index', [2]); ?>"><?php echo Label::getLabel('LBL_TEACHES_LEVEL'); ?></a></li>
                            <li><a href="<?php echo MyUtility::makeUrl('preferences', 'index', [3]); ?>"><?php echo Label::getLabel('LBL_LEARNERS_AGES'); ?></a></li>
                            <li><a href="<?php echo MyUtility::makeUrl('preferences', 'index', [4]); ?>"><?php echo Label::getLabel('LBL_LESSONS_INCLUDE'); ?></a></li>
                            <li><a href="<?php echo MyUtility::makeUrl('preferences', 'index', [5]); ?>"><?php echo Label::getLabel('LBL_SUBJECTS'); ?></a></li>
                            <li><a href="<?php echo MyUtility::makeUrl('preferences', 'index', [6]); ?>"><?php echo Label::getLabel('LBL_TEST_PREPARATION'); ?></a></li>
                        <?php }
                        if ($objPrivilege->canViewSpeakLanguage(true)) { ?>
                            <li><a href="<?php echo MyUtility::makeUrl('SpeakLanguage'); ?>"><?php echo Label::getLabel('LBL_SPOKEN_LANGUAGE'); ?></a></li>
                        <?php }
                        if ($objPrivilege->canViewTeachLanguage(true)) { ?>
                            <li><a href="<?php echo MyUtility::makeUrl('TeachLanguage'); ?>"><?php echo Label::getLabel('LBL_TEACHING_LANGUAGE'); ?></a></li>
                        <?php }
                        if ($objPrivilege->canViewIssueReportOptions(true)) { ?>
                            <li><a href="<?php echo MyUtility::makeUrl('issueReportOptions'); ?>"><?php echo Label::getLabel('LBL_ISSUE_REPORT_OPTIONS'); ?></a></li>
                        <?php }
                        if ($objPrivilege->canViewPriceSlab(true)) { ?>
                            <li><a href="<?php echo MyUtility::makeUrl('PriceSlabs'); ?>"><?php echo Label::getLabel('LBL_PRICE_SLABS'); ?></a></li>
                        <?php } ?>
                    </ul>
                </li>
            <?php } ?>
            <!--CMS[-->
            <?php
            if (
                $objPrivilege->canViewSlides(true) || $objPrivilege->canViewContentPages(true) || $objPrivilege->canViewContentBlocks(true) ||
                $objPrivilege->canViewNavigationManagement(true) || $objPrivilege->canViewCountries(true) || $objPrivilege->canViewBibleContent(true) ||
                $objPrivilege->canViewTestimonial(true) || $objPrivilege->canViewLanguageLabel(true) || $objPrivilege->canViewFaqCategory(true) ||
                $objPrivilege->canViewFaq(true) || $objPrivilege->canViewEmailTemplates(true)
            ) {
            ?>
                <li class="haschild"><a href="javascript:void(0);"><?php echo Label::getLabel('LBL_MANAGE_CMS'); ?></a>
                    <ul>
                        <?php if ($objPrivilege->canViewSlides(true)) { ?>
                            <li><a href="<?php echo MyUtility::makeUrl('slides'); ?>"><?php echo Label::getLabel('LBL_HOMEPAGE_SLIDES'); ?></a></li>
                        <?php }
                        if ($objPrivilege->canViewContentPages(true)) { ?>
                            <li><a href="<?php echo MyUtility::makeUrl('ContentPages'); ?>"><?php echo Label::getLabel('LBL_CONTENT_PAGES'); ?></a></li>
                        <?php }
                        if ($objPrivilege->canViewContentBlocks(true)) { ?>
                            <li><a href="<?php echo MyUtility::makeUrl('ContentBlock'); ?>"><?php echo Label::getLabel('LBL_CONTENT_BLOCKS'); ?></a></li>
                        <?php }
                        if ($objPrivilege->canViewNavigationManagement(true)) { ?>
                            <li><a href="<?php echo MyUtility::makeUrl('Navigations'); ?>"><?php echo Label::getLabel('LBL_NAVIGATION'); ?></a></li>
                        <?php }
                        if ($objPrivilege->canViewCountries(true)) { ?>
                            <li><a href="<?php echo MyUtility::makeUrl('Countries'); ?>"><?php echo Label::getLabel('LBL_COUNTRIES'); ?></a></li>
                        <?php }
                        if ($objPrivilege->canViewBibleContent(true)) { ?>
                            <li><a href="<?php echo MyUtility::makeUrl('BibleContent'); ?>"><?php echo Label::getLabel('LBL_BIBLE_CONTENT'); ?></a></li>
                        <?php }
                        if ($objPrivilege->canViewTestimonial(true)) { ?>
                            <li><a href="<?php echo MyUtility::makeUrl('Testimonials'); ?>"><?php echo Label::getLabel('LBL_TESTIMONIALS'); ?></a></li>
                        <?php }
                        if ($objPrivilege->canViewLanguageLabel(true)) { ?>
                            <li><a href="<?php echo MyUtility::makeUrl('Label'); ?>"><?php echo Label::getLabel('LBL_LANGUAGE_LABEL'); ?></a></li>
                        <?php }
                        if ($objPrivilege->canViewFaqCategory(true)) { ?>
                            <li><a href="<?php echo MyUtility::makeUrl('FaqCategories'); ?>"><?php echo Label::getLabel('LBL_FAQ_CATEGORIES'); ?></a></li>
                        <?php }
                        if ($objPrivilege->canViewFaq(true)) { ?>
                            <li><a href="<?php echo MyUtility::makeUrl('faq'); ?>"><?php echo Label::getLabel('LBL_MANAGE_FAQS'); ?></a></li>
                        <?php }
                        if ($objPrivilege->canViewEmailTemplates(true)) { ?>
                            <li><a href="<?php echo MyUtility::makeUrl('EmailTemplates'); ?>"><?php echo Label::getLabel('LBL_EMAIL_TEMPLATES'); ?></a></li>
                        <?php } ?>
                    </ul>
                </li>
            <?php } ?>
            <!-- ] -->
            <!--Settings-->
            <?php
            if (
                $objPrivilege->canViewGeneralSettings(true) || $objPrivilege->canViewPwaSettings(true) || $objPrivilege->canViewPaymentMethods(true) || $objPrivilege->canViewSocialPlatforms(true) || $objPrivilege->canViewDiscountCoupons(true) ||
                $objPrivilege->canViewCurrencyManagement(true) || $objPrivilege->canViewCommissionSettings(true) || $objPrivilege->canViewThemeManagement(true)
            ) {
            ?>
                <li class="haschild"><a href="javascript:void(0);"><?php echo Label::getLabel('LBL_Manage_Settings'); ?></a>
                    <ul>
                        <?php if ($objPrivilege->canViewGeneralSettings(true)) { ?>
                            <li><a href="<?php echo MyUtility::makeUrl('configurations'); ?>"><?php echo Label::getLabel('LBL_General_Settings'); ?></a></li>
                        <?php }
                        if ($objPrivilege->canViewPwaSettings(true)) { ?>
                            <li><a href="<?php echo MyUtility::makeUrl('Pwa'); ?>"><?php echo Label::getLabel('LBL_PWA_Settings'); ?></a></li>
                        <?php }
                        if ($objPrivilege->canViewMeetingTool(true)) { ?>
                            <li><a href="<?php echo MyUtility::makeUrl('MeetingTools'); ?>"><?php echo Label::getLabel('LBL_Meeting_Tools'); ?></a></li>
                        <?php }
                        if ($objPrivilege->canViewPaymentMethods(true)) { ?>
                            <li><a href="<?php echo MyUtility::makeUrl('PaymentMethods'); ?>"><?php echo Label::getLabel('LBL_Payment_Methods'); ?></a></li>
                        <?php }
                        if ($objPrivilege->canViewSocialPlatforms(true)) { ?>
                            <li><a href="<?php echo MyUtility::makeUrl('SocialPlatform'); ?>"><?php echo Label::getLabel('LBL_SOCIAL_PLATFORMS'); ?></a></li>
                        <?php }
                        if ($objPrivilege->canViewDiscountCoupons(true)) { ?>
                            <li><a href="<?php echo MyUtility::makeUrl('Coupons'); ?>"><?php echo Label::getLabel('LBL_Discount_Coupons'); ?></a></li>
                        <?php }
                        if ($objPrivilege->canViewCommissionSettings(true)) { ?>
                            <li><a href="<?php echo MyUtility::makeUrl('Commission'); ?>"><?php echo Label::getLabel('LBL_Commission_Settings'); ?></a></li>
                        <?php }
                        if ($objPrivilege->canViewCurrencyManagement(true)) { ?>
                            <li><a href="<?php echo MyUtility::makeUrl('CurrencyManagement'); ?>"><?php echo Label::getLabel('LBL_Currency_Management'); ?></a></li>
                        <?php }
                        if ($objPrivilege->canViewThemeManagement(true)) { ?>
                            <li><a href="<?php echo MyUtility::makeUrl('Themes') ?>"><?php echo Label::getLabel('LBL_Theme_Management'); ?></a></li>
                        <?php } ?>
                    </ul>
                </li>
            <?php }
            if ($objPrivilege->canViewBlogPostCategories(true) || $objPrivilege->canViewBlogPosts(true) || $objPrivilege->canViewBlogComments(true) || $objPrivilege->canViewBlogContributions(true)) { ?>
                <li class="haschild"><a href="javascript:void(0);"><?php echo Label::getLabel('LBL_Manage_Blogs'); ?></a>
                    <ul>
                        <?php if ($objPrivilege->canViewBlogPostCategories(true)) { ?>
                            <li><a href="<?php echo MyUtility::makeUrl('BlogPostCategories'); ?>"><?php echo Label::getLabel('LBL_BLOG_Categories'); ?></a></li>
                        <?php }
                        if ($objPrivilege->canViewBlogPosts(true)) { ?>
                            <li><a href="<?php echo MyUtility::makeUrl('BlogPosts'); ?>"><?php echo Label::getLabel('LBL_Blog_Posts'); ?></a></li>
                        <?php }
                        if ($objPrivilege->canViewBlogComments(true)) { ?>
                            <li><a href="<?php echo MyUtility::makeUrl('BlogComments'); ?>"><?php echo Label::getLabel('LBL_Blog_Comments'); ?> <?php /* if($blogCommentsCount){ ?><span class='badge'>(<?php echo $blogCommentsCount; ?>)</span><?php } */ ?></a></li>
                        <?php }
                        if ($objPrivilege->canViewBlogContributions(true)) { ?>
                            <li><a href="<?php echo MyUtility::makeUrl('BlogContributions'); ?>"><?php echo Label::getLabel('LBL_Blog_Contributions'); ?> <?php /* if($blogContrCount){ ?><span class='badge'>(<?php echo $blogContrCount; ?>)</span><?php } */ ?></a></li>
                        <?php } ?>
                    </ul>
                </li>
            <?php }
            if ($objPrivilege->canViewMetaTags(true) || $objPrivilege->canViewSeoUrl(true) || $objPrivilege->canViewSiteMap(true) || $objPrivilege->canEditSiteMap(true)) { ?>
                <li class="haschild"><a href="javascript:void(0);"><?php echo Label::getLabel('LBL_MANAGE_SEO'); ?></a>
                    <ul>
                        <?php if ($objPrivilege->canViewMetaTags(true)) { ?>
                            <li><a href="<?php echo MyUtility::makeUrl('MetaTags'); ?>"><?php echo Label::getLabel('LBL_META_TAGS'); ?></a></li>
                        <?php }
                        if ($objPrivilege->canViewSeoUrl(true)) { ?>
                            <li><a href="<?php echo MyUtility::makeUrl('UrlRewriting'); ?>"><?php echo Label::getLabel('LBL_SEO_URLS'); ?></a></li>
                        <?php }
                        if ($objPrivilege->canViewRobotsSection(true)) { ?>
                            <li><a href="<?php echo MyUtility::makeUrl('Bots'); ?>"><?php echo Label::getLabel('LBL_ROBOTS.TXT'); ?></a></li>
                        <?php }
                        if ($objPrivilege->canEditSiteMap(true)) { ?>
                            <li><a href="javascript:generateSitemap();"><?php echo Label::getLabel('LBL_UPDATE_SITEMAP'); ?></a></li>
                        <?php }
                        if ($objPrivilege->canViewSiteMap(true)) { ?>
                            <li><a href="<?php echo CONF_WEBROOT_FRONT_URL ?>sitemap.xml" target="_blank"><?php echo Label::getLabel('LBL_XML_SITEMAP'); ?></a></li>
                            <li><a href="<?php echo MyUtility::makeUrl('Sitemap', '', [], CONF_WEBROOT_FRONT_URL) ?>" target="_blank"><?php echo Label::getLabel('LBL_HTML_SITEMAP'); ?></a></li>
                        <?php } ?>
                    </ul>
                </li>
            <?php } ?>
            <!-- Report [ -->
            <?php
            if (
                $objPrivilege->canViewLessonLanguages(true) || $objPrivilege->canViewClassLanguages(true) ||
                $objPrivilege->canViewTeacherPerformance(true) || $objPrivilege->canViewLessonStatsReport(true) ||
                $objPrivilege->canViewSalesReport(true) || $objPrivilege->canViewSettlementsReport(true)
            ) {
            ?>
                <li class="haschild">
                    <a href="javascript:void(0);"><?php echo Label::getLabel('LBL_VIEW_REPORTS'); ?></a>
                    <ul>
                        <?php if ($objPrivilege->canViewLessonLanguages(true)) { ?>
                            <li><a href="<?php echo MyUtility::makeUrl('LessonLanguages'); ?>"><?php echo Label::getLabel('LBL_Lessons_Top_Languages'); ?></a></li>
                        <?php }
                        if ($objPrivilege->canViewClassLanguages(true)) { ?>
                            <li><a href="<?php echo MyUtility::makeUrl('ClassLanguages'); ?>"><?php echo Label::getLabel('LBL_Classes_Top_Languages'); ?></a></li>
                        <?php }
                        if ($objPrivilege->canViewTeacherPerformance(true)) { ?>
                            <li><a href="<?php echo MyUtility::makeUrl('TeacherPerformance'); ?>"><?php echo Label::getLabel('LBL_Teacher_Performance'); ?></a></li>
                        <?php }
                        if ($objPrivilege->canViewLessonStatsReport(true)) { ?>
                            <li><a href="<?php echo MyUtility::makeUrl('LessonStats'); ?>"><?php echo Label::getLabel('LBL_LESSON_STATS'); ?></a></li>
                        <?php }
                        if ($objPrivilege->canViewSalesReport(true)) { ?>
                            <li><a href="<?php echo MyUtility::makeUrl('SalesReport'); ?>"><?php echo Label::getLabel('LBL_SALES_REPORT'); ?></a></li>
                        <?php }
                        if ($objPrivilege->canViewSettlementsReport(true)) { ?>
                            <li><a href="<?php echo MyUtility::makeUrl('Settlements'); ?>"><?php echo Label::getLabel('LBL_SETTLEMENTS'); ?></a></li>
                        <?php } ?>
                    </ul>
                </li>
            <?php } ?>
            <!--  ] -->
        </ul>
    </div>
</aside>
<!--left panel end here-->