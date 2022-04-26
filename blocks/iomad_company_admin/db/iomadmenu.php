<?php
// This file is part of Moodle - http://moodle.org/
//
// Moodle is free software: you can redistribute it and/or modify
// it under the terms of the GNU General Public License as published by
// the Free Software Foundation, either version 3 of the License, or
// (at your option) any later version.
//
// Moodle is distributed in the hope that it will be useful,
// but WITHOUT ANY WARRANTY; without even the implied warranty of
// MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
// GNU General Public License for more details.
//
// You should have received a copy of the GNU General Public License
// along with Moodle.  If not, see <http://www.gnu.org/licenses/>.

// Define the Iomad menu items that are defined by this plugin

function block_iomad_company_admin_menu() {
    global $DB;
    $systemcontext = context_system::instance();
    $companyid = iomad::get_my_companyid($systemcontext);
    $company = $DB->get_record('company',array('id' => $companyid))->shortname;
    $courseid = $DB->get_record('company_course_mapping',array('companyid' => $companyid))->courseid;
    // print_r($courseid);exit();
    if($companyid == 1){
        return array(
            'addcompany' => array(
                'category' => 'CompanyAdmin',
                'tab' => 1,
                'name' => get_string('createcompany', 'block_iomad_company_admin'),
                'url' => 'company_edit_form.php?createnew=1',
                'cap' => 'block/iomad_company_admin:company_add',
                'icondefault' => 'newcompany',
                'style' => 'company',
                'icon' => 'fa-building',
                'iconsmall' => 'fa-plus-square'
            ),
            'editcompany' => array(
                'category' => 'CompanyAdmin',
                'tab' => 1,
                'name' => get_string('editcompany', 'block_iomad_company_admin'),
                'url' => 'company_edit_form.php',
                'cap' => 'block/iomad_company_admin:company_edit',
                'icondefault' => 'editcompany',
                'style' => 'company',
                'icon' => 'fa-building',
                'iconsmall' => 'fa-edit'
            ),
            'managecompanies' => array(
                'category' => 'CompanyAdmin',
                'tab' => 1,
                'name' => get_string('managecompanies', 'block_iomad_company_admin'),
                'url' => 'editcompanies.php',
                'cap' => 'block/iomad_company_admin:company_add_child',
                'icondefault' => 'editcompany',
                'style' => 'company',
                'icon' => 'fa-building',
                'iconsmall' => 'fa-gear'
            ),
            'editdepartments' => array(
                'category' => 'CompanyAdmin',
                'tab' => 1,
                'name' => get_string('editdepartment', 'block_iomad_company_admin'),
                'url' => 'company_departments.php',
                'cap' => 'block/iomad_company_admin:edit_departments',
                'icondefault' => 'managedepartment',
                'style' => 'department',
                'icon' => 'fa-group',
                'iconsmall' => 'fa-gear'
            ),
            'userprofiles' => array(
                'category' => 'CompanyAdmin',
                'tab' => 1,
                'name' => get_string('userprofiles', 'block_iomad_company_admin'),
                'url' => 'company_user_profiles.php',
                'cap' => 'block/iomad_company_admin:company_user_profiles',
                'icondefault' => 'optionalprofiles',
                'style' => 'user',
                'icon' => 'fa-user',
                'iconsmall' => 'fa-info-circle'
            ),
            'restrictcapabilities' => array(
                'category' => 'CompanyAdmin',
                'tab' => 1,
                'name' => get_string('restrictcapabilities', 'block_iomad_company_admin'),
                'url' => 'company_capabilities.php',
                'cap' => 'block/iomad_company_admin:restrict_capabilities',
                'icondefault' => 'users',
                'style' => 'user',
                'icon' => 'fa-user',
                'iconsmall' => 'fa-info-circle'
            ),
            'rslrhrriuser' => array(
                'category' => 'CompanyAdmin',
                'tab' => 1,
                'name' => "Create RHR & RI",
                'url' => '/local/custompage/create_company_user_form.php',
                'cap' => 'block/iomad_company_admin:company_manager',
                'icondefault' => 'usernew',
                'style' => 'user',
                'icon' => 'fa-group',
                'iconsmall' => 'fa-plus-circle'
            ),

            // 'rsluser' => array(
            //     'category' => 'CompanyAdmin',
            //     'tab' => 1,
            //     'name' => "Manage Users Score ",
            //     'url' => '/report/rslscore/index.php',
            //     'cap' => 'block/iomad_company_admin:company_edit',
            //     'icondefault' => 'useredit',
            //     'style' => 'user',
            //     'icon' => 'fa-user',
            //     'iconsmall' => 'fa-gear'
            // ),

            'rslscore' => array(
                'category' => 'CompanyAdmin',
                'tab' => 1,
                'name' => "Manage RSL User ",
                'url' => '/local/custompage/ru_list.php',
                'cap' => 'block/iomad_company_admin:company_edit',
                'icondefault' => 'userenrolements',
                'style' => 'user',
                'icon' => 'fa-user',
                'iconsmall' => 'fa-user'
            ),

            'rsldrivelist' => array(
                'category' => 'CompanyAdmin',
                'tab' => 1,
                'name' => "RSL Drive List ",
                'url' => '/local/listcoursefiles/drivelist.php',
                'cap' => 'block/iomad_company_admin:company_edit',
                'icondefault' => 'courses',
                'style' => 'user',
                'icon' => 'fa-user',
                'iconsmall' => 'fa-info-circle'
            ),

            'rslreport' => array(
                'category' => 'CompanyAdmin',
                'tab' => 1,
                'name' => "RSL Report ",
                'url' => '/local/listcoursefiles/rsluserreport.php',
                'cap' => 'block/iomad_company_admin:company_edit',
                'icondefault' => 'report',
                'style' => 'user',
                'icon' => 'fa-user',
                'iconsmall' => 'fa-bar-chart-o'
            ),

            'recruitmentdrrive' => array(
                'category' => 'CompanyAdmin',
                'tab' => 1,
                'name' => "Create RSL Recruitment Drive ",
                'url' => '/local/custompage/create_rsl_recruitment_drive_form.php',
                'cap' => 'block/iomad_company_admin:company_edit',
                'icondefault' => 'createcourse',
                'style' => 'user',
                'icon' => 'fa-user',
                'iconsmall' => 'fa-plus-square'
            ),

            'rsltest' => array(
                'category' => 'CompanyAdmin',
                'tab' => 1,
                'name' => "Create RSL Test ",
                'url' => '/local/custompage/create_test_form.php',
                'cap' => 'block/iomad_company_admin:company_edit',
                'icondefault' => 'newcourse',
                'style' => 'user',
                'icon' => 'fa-user',
                'iconsmall' => 'fa-plus-square'
            ),

            
            'itracquserupload' => array(
                'category' => 'CompanyAdmin',
                'tab' => 1,
                'name' => "Upload iTracQ Users",
                'url' => '/local/custompage/itracquserupload.php',
                'cap' => 'block/iomad_company_admin:company_manager',
                'icondefault' => 'up',
                'style' => 'user',
                'icon' => 'fa-user',
                'iconsmall' => 'fa-upload'
            ),

            'itracquserupdate' => array(
                'category' => 'CompanyAdmin',
                'tab' => 1,
                'name' => "Update iTracQ Users",
                'url' => '/local/custompage/itracquserupdate.php',
                'cap' => 'block/iomad_company_admin:company_manager',
                'icondefault' => 'up',
                'style' => 'user',
                'icon' => 'fa-user',
                'iconsmall' => 'fa-download'
            ),

            'skillsoftusers' => array(
                'category' => 'CompanyAdmin',
                'tab' => 1,
                'name' => "Skill Soft Users",
                'url' => '/local/custompage/skilsoftusers.php',
                'cap' => 'block/iomad_company_admin:company_manager',
                'icondefault' => 'up',
                'style' => 'user',
                'icon' => 'fa-user',
                'iconsmall' => 'fa-download'
            ),

            'updateskillsoftusers' => array(
                'category' => 'CompanyAdmin',
                'tab' => 1,
                'name' => "Update Skill Soft Users",
                'url' => '/local/custompage/updateskillsoftusers.php',
                'cap' => 'block/iomad_company_admin:company_manager',
                'icondefault' => 'up',
                'style' => 'user',
                'icon' => 'fa-user',
                'iconsmall' => 'fa-download'
            ),

            'uploadrsluser' => array(
                'category' => 'CompanyAdmin',
                'tab' => 1,
                'name' => "User Bulkupload",
                'url' => '/local/custompage/rslbulkupload.php',
                'cap' => 'block/iomad_company_admin:company_manager',
                'icondefault' => 'up',
                'style' => 'user',
                'icon' => 'fa-user',
                'iconsmall' => 'fa-upload'
            ),

            'rslusermanagement' => array(
                'category' => 'CompanyAdmin',
                'tab' => 1,
                'name' => "RSL User Management",
                'url' => '/local/custompage/usermanagement.php',
                'cap' => 'block/iomad_company_admin:company_edit',
                'icondefault' => 'userenrolements',
                'style' => 'user',
                'icon' => 'fa-user',
                'iconsmall' => 'fa-user'
            ),


            'rsltestlist' => array(
                'category' => 'CompanyAdmin',
                'tab' => 1,
                'name' => "Manage RSL Tests",
                'url' => '/local/custompage/test.php',
                'cap' => 'block/iomad_company_admin:company_edit',
                'icondefault' => 'newcourse',
                'style' => 'user',
                'icon' => 'fa-user',
                'iconsmall' => 'fa-edit'
            ),

            'managersldrive' => array(
                'category' => 'CompanyAdmin',
                'tab' => 1,
                'name' => "Manage RSL Recruitment Drives ",
                'url' => '/local/custompage/recruitment_drive.php',
                'cap' => 'block/iomad_company_admin:company_edit',
                'icondefault' => 'courses',
                'style' => 'user',
                'icon' => 'fa-user',
                'iconsmall' => 'fa-edit'
            ),

            'rslcms' => array(
                'category' => 'CompanyAdmin',
                'tab' => 1,
                'name' => "RSL CMS ",
                'url' => '/local/custompage/cms.php',
                'cap' => 'block/iomad_company_admin:company_edit',
                'icondefault' => 'managecoursesettings',
                'style' => 'user',
                'icon' => 'fa-user',
                'iconsmall' => 'fa-edit'
            ),

            'rslquestionbank' => array(
                'category' => 'CompanyAdmin',
                'tab' => 1,
                'name' => "RSL Question Bank ",
                'url' => '/course/admin.php?courseid='.$courseid,
                'cap' => 'block/iomad_company_admin:company_edit',
                'icondefault' => 'managecoursesettings',
                'style' => 'user',
                'icon' => 'fa-user',
                'iconsmall' => 'fa-edit'
            ),

            'createuser' => array(
                'category' => 'UserAdmin',
                'tab' => 2,
                'name' => get_string('createuser', 'block_iomad_company_admin'),
                'url' => 'company_user_create_form.php',
                'cap' => 'block/iomad_company_admin:user_create',
                'icondefault' => 'usernew',
                'style' => 'user',
                'icon' => 'fa-user',
                'iconsmall' => 'fa-plus-square',
            ),
            'edituser' => array(
                'category' => 'UserAdmin',
                'tab' => 2,
                'name' => get_string('edituser', 'block_iomad_company_admin'),
                'url' => 'editusers.php',
                'cap' => 'block/iomad_company_admin:user_create',
                'icondefault' => 'useredit',
                'style' => 'user',
                'icon' => 'fa-user',
                'iconsmall' => 'fa-gear',
            ),
            'assignmanagers' => array(
                'category' => 'CompanyAdmin',
                'tab' => 2,
                'name' => get_string('assignmanagers', 'block_iomad_company_admin'),
                'url' => 'company_managers_form.php',
                'cap' => 'block/iomad_company_admin:company_manager',
                'icondefault' => 'assigndepartmentusers',
                'style' => 'department',
                'icon' => 'fa-group',
                'iconsmall' => 'fa-chevron-circle-right'
            ),
            'assignusertocompany' => array(
                'category' => 'UserAdmin',
                'tab' => 2,
                'name' => get_string('assigntocompany', 'block_iomad_company_admin'),
                'url' => 'company_users_form.php',
                'cap' => 'block/iomad_company_admin:company_user',
                'icondefault' => '',
                'style' => 'user',
                'icon' => 'fa-building',
                'iconsmall' => 'fa-chevron-circle-left',
            ),
            'uploadfromfile' => array(
                'category' => 'UserAdmin',
                'tab' => 2,
                'name' => get_string('user_upload_title', 'block_iomad_company_admin'),
                'url' => 'uploaduser.php',
                'cap' => 'block/iomad_company_admin:user_upload',
                'icondefault' => 'up',
                'style' => 'user',
                'icon' => 'fa-file',
                'iconsmall' => 'fa-upload',

            ),
            'downloadusers' => array(
                'category' => 'UserAdmin',
                'tab' => 2,
                'name' => get_string('users_download', 'block_iomad_company_admin'),
                'url' => 'user_bulk_download.php',
                'cap' => 'block/iomad_company_admin:user_upload',
                'icondefault' => 'down',
                'style' => 'user',
                'icon' => 'fa-group',
                'iconsmall' => 'fa-download',
            ),
            'createcourse' => array(
                'category' => 'CourseAdmin',
                'tab' => 3,
                'name' => get_string('createcourse', 'block_iomad_company_admin'),
                'url' => 'company_course_create_form.php',
                'cap' => 'block/iomad_company_admin:createcourse',
                'icondefault' => 'createcourse',
                'style' => 'course',
                'icon' => 'fa-file-text',
                'iconsmall' => 'fa-plus-square',
            ),
            'enroluser' => array(
                'category' => 'UserAdmin',
                'tab' => 3,
                'name' => get_string('enroluser', 'block_iomad_company_admin'),
                'url' => 'company_course_users_form.php',
                'cap' => 'block/iomad_company_admin:company_course_users',
                'icondefault' => 'userenrolements',
                'style' => 'course',
                'icon' => 'fa-file-text',
                'iconsmall' => 'fa-user',
            ),
            'managecourses' => array(
                'category' => 'CourseAdmin',
                'tab' => 3,
                'name' => get_string('iomad_courses_title', 'block_iomad_company_admin'),
                'url' => 'iomad_courses_form.php',
                'cap' => 'block/iomad_company_admin:viewcourses',
                'icondefault' => 'managecoursesettings',
                'style' => 'course',
                'icon' => 'fa-file-text',
                'iconsmall' => 'fa-gear',
            ),
            'assigntocompany' => array(
                'category' => 'CourseAdmin',
                'tab' => 3,
                'name' => get_string('assigntocompany', 'block_iomad_company_admin'),
                'url' => 'company_courses_form.php',
                'cap' => 'block/iomad_company_admin:company_course',
                'icondefault' => 'assigntocompany',
                'style' => 'course',
                'icon' => 'fa-building',
                'iconsmall' => 'fa-chevron-circle-left'
            ),
            'managegroups' => array(
                'category' => 'CourseAdmin',
                'tab' => 3,
                'name' => get_string('managegroups', 'block_iomad_company_admin'),
                'url' => 'company_groups_create_form.php',
                'cap' => 'block/iomad_company_admin:edit_groups',
                'icondefault' => 'groupsedit',
                'style' => 'group',
                'icon' => 'fa-group',
                'iconsmall' => 'fa-gear',
            ),
            'assigngroups' => array(
                'category' => 'CourseAdmin',
                'tab' => 3,
                'name' => get_string('assigncoursegroups', 'block_iomad_company_admin'),
                'url' => 'company_groups_users_form.php',
                'cap' => 'block/iomad_company_admin:assign_groups',
                'icondefault' => 'groupsassign',
                'style' => 'group',
                'icon' => 'fa-group',
                'iconsmall' => 'fa-plus-square',
            ),
            'classrooms' => array(
                'category' => 'CourseAdmin',
                'tab' => 3,
                'name' => get_string('classrooms', 'block_iomad_company_admin'),
                'url' => 'classroom_list.php',
                'cap' => 'block/iomad_company_admin:classrooms',
                'icondefault' => 'teachinglocations',
                'style' => 'company',
                'icon' => 'fa-map-marker',
                'iconsmall' => 'fa-gear',
            ),
            'manageiomadlicenses' => array(
                'category' => 'LicenseAdmin',
                'tab' => 4,
                'name' => get_string('managelicenses', 'block_iomad_company_admin'),
                'url' => 'company_license_list.php',
                'cap' => 'block/iomad_company_admin:edit_my_licenses',
                'icondefault' => 'licensemanagement',
                'style' => 'license',
                'icon' => 'fa-legal',
                'iconsmall' => 'fa-gear',
            ),
            'licenseusers' => array(
                'category' => 'LicenseAdmin',
                'tab' => 4,
                'name' => get_string('licenseusers', 'block_iomad_company_admin'),
                'url' => 'company_license_users_form.php',
                'cap' => 'block/iomad_company_admin:allocate_licenses',
                'icondefault' => 'userlicenseallocations',
                'style' => 'license',
                'icon' => 'fa-legal',
                'iconsmall' => 'fa-user'
            ),
            'iomadframeworksettings' => array(
                'category' => 'CompetencyAdmin',
                'tab' => 5,
                'name' => get_string('frameworksettings', 'block_iomad_company_admin'),
                'url' => 'iomad_frameworks_form.php',
                'cap' => 'block/iomad_company_admin:manageframeworks',
                'icondefault' => 'managecoursesettings',
                'style' => 'competency',
                'icon' => 'fa-list',
                'iconsmall' => 'fa-cog'
            ),
            'companytemplates' => array(
                'category' => 'CompetencyAdmin',
                'tab' => 5,
                'name' => get_string('companytemplates', 'block_iomad_company_admin'),
                'url' => 'company_competency_templates_form.php',
                'cap' => 'block/iomad_company_admin:company_template',
                'icondefault' => 'assigntocompany',
                'style' => 'competency',
                'icon' => 'fa-cubes',
                'iconsmall' => 'fa-chevron-circle-right'
            ),
            'iomadtemplatesettings' => array(
                'category' => 'CompetencyAdmin',
                'tab' => 5,
                'name' => get_string('templatesettings', 'block_iomad_company_admin'),
                'url' => 'iomad_templates_form.php',
                'cap' => 'block/iomad_company_admin:managetemplates',
                'icondefault' => 'managecoursesettings',
                'style' => 'competency',
                'icon' => 'fa-cubes',
                'iconsmall' => 'fa-cog'
            ),
            'edittemplates' => array(
                'category' => 'CompetencyAdmin',
                'tab' => 5,
                'name' => get_string('templates', 'tool_lp'),
                'url' => '/admin/tool/lp/learningplans.php?pagecontextid=1',
                'cap' => 'block/iomad_company_admin:templateview',
                'icondefault' => 'userenrolements',
                'style' => 'competency',
                'icon' => 'fa-cubes',
                'iconsmall' => 'fa-eye'
            ),
            
        );
    }else if($companyid == 2){
        return array(
            'addcompany' => array(
                'category' => 'CompanyAdmin',
                'tab' => 1,
                'name' => get_string('createcompany', 'block_iomad_company_admin'),
                'url' => 'company_edit_form.php?createnew=1',
                'cap' => 'block/iomad_company_admin:company_add',
                'icondefault' => 'newcompany',
                'style' => 'company',
                'icon' => 'fa-building',
                'iconsmall' => 'fa-plus-square'
            ),
            'editcompany' => array(
                'category' => 'CompanyAdmin',
                'tab' => 1,
                'name' => get_string('editcompany', 'block_iomad_company_admin'),
                'url' => 'company_edit_form.php',
                'cap' => 'block/iomad_company_admin:company_edit',
                'icondefault' => 'editcompany',
                'style' => 'company',
                'icon' => 'fa-building',
                'iconsmall' => 'fa-edit'
            ),
            'managecompanies' => array(
                'category' => 'CompanyAdmin',
                'tab' => 1,
                'name' => get_string('managecompanies', 'block_iomad_company_admin'),
                'url' => 'editcompanies.php',
                'cap' => 'block/iomad_company_admin:company_add_child',
                'icondefault' => 'editcompany',
                'style' => 'company',
                'icon' => 'fa-building',
                'iconsmall' => 'fa-gear'
            ),
            'editdepartments' => array(
                'category' => 'CompanyAdmin',
                'tab' => 1,
                'name' => get_string('editdepartment', 'block_iomad_company_admin'),
                'url' => 'company_departments.php',
                'cap' => 'block/iomad_company_admin:edit_departments',
                'icondefault' => 'managedepartment',
                'style' => 'department',
                'icon' => 'fa-group',
                'iconsmall' => 'fa-gear'
            ),
            'userprofiles' => array(
                'category' => 'CompanyAdmin',
                'tab' => 1,
                'name' => get_string('userprofiles', 'block_iomad_company_admin'),
                'url' => 'company_user_profiles.php',
                'cap' => 'block/iomad_company_admin:company_user_profiles',
                'icondefault' => 'optionalprofiles',
                'style' => 'user',
                'icon' => 'fa-user',
                'iconsmall' => 'fa-info-circle'
            ),
            'restrictcapabilities' => array(
                'category' => 'CompanyAdmin',
                'tab' => 1,
                'name' => get_string('restrictcapabilities', 'block_iomad_company_admin'),
                'url' => 'company_capabilities.php',
                'cap' => 'block/iomad_company_admin:restrict_capabilities',
                'icondefault' => 'users',
                'style' => 'user',
                'icon' => 'fa-user',
                'iconsmall' => 'fa-info-circle'
            ),
            'urdcuserreport' => array(
                'category' => 'CompanyAdmin',
                'tab' => 1,
                'name' => "URDC Users Report ",
                'url' => '/local/custompage/urdcuserreport.php',
                'cap' => 'block/iomad_company_admin:company_edit',
                'icondefault' => 'report',
                'style' => 'user',
                'icon' => 'fa-user',
                'iconsmall' => 'fa-bar-chart-o'
            ),

            'urdcuserscore' => array(
                'category' => 'CompanyAdmin',
                'tab' => 1,
                'name' => "URDC Users Score ",
                'url' => '/local/custompage/uu_drive_list.php',
                'cap' => 'block/iomad_company_admin:company_edit',
                'icondefault' => 'userlicenseallocations',
                'style' => 'user',
                'icon' => 'fa-user',
                'iconsmall' => 'fa-bar-chart-o'
            ),

            // 'assignurm' => array(
            //     'category' => 'CompanyAdmin',
            //     'tab' => 1,
            //     'name' => "Assign to URDC Reporting Manager ",
            //     'url' => '/local/custompage/assign_urm.php',
            //     'cap' => 'block/iomad_company_admin:restrict_capabilities',
            //     'icondefault' => 'useredit',
            //     'style' => 'user',
            //     'icon' => 'fa-user',
            //     'iconsmall' => 'fa-gear'
            // ),

            // 'createurm' => array(
            //     'category' => 'CompanyAdmin',
            //     'tab' => 1,
            //     'name' => "Create URDC Reporting Manager ",
            //     'url' => '/local/custompage/create_urm_form.php',
            //     'cap' => 'block/iomad_company_admin:restrict_capabilities',
            //     'icondefault' => 'useredit',
            //     'style' => 'user',
            //     'icon' => 'fa-user',
            //     'iconsmall' => 'fa-gear'
            // ),

                'manageurdcuser' => array(
                    'category' => 'CompanyAdmin',
                    'tab' => 1,
                    'name' => "Manage URDC User ",
                    'url' => '/local/custompage/uu_list.php',
                    'cap' => 'block/iomad_company_admin:company_edit',
                    'icondefault' => 'userenrolements',
                    'style' => 'user',
                    'icon' => 'fa-user',
                    'iconsmall' => 'fa-user'
                ),

            'urdcrecruitmentdrrive' => array(
                'category' => 'CompanyAdmin',
                'tab' => 1,
                'name' => "Create URDC Recruitment Drive ",
                'url' => '/local/custompage/create_urdc_recruitment_drive_form.php',
                'cap' => 'block/iomad_company_admin:company_edit',
                'icondefault' => 'createcourse',
                'style' => 'user',
                'icon' => 'fa-user',
                'iconsmall' => 'fa-plus-square'
            ),

            'urdctest' => array(
                'category' => 'CompanyAdmin',
                'tab' => 1,
                'name' => "Create URDC Test ",
                'url' => '/local/custompage/create_urdc_test_form.php',
                'cap' => 'block/iomad_company_admin:company_edit',
                'icondefault' => 'newcourse',
                'style' => 'user',
                'icon' => 'fa-user',
                'iconsmall' => 'fa-plus-square'
            ),

            // 'urdcusermanagement' => array(
            //     'category' => 'CompanyAdmin',
            //     'tab' => 1,
            //     'name' => "URDC User Management",
            //     'url' => '/local/custompage/usermanagement.php',
            //     'cap' => 'block/iomad_company_admin:company_edit',
            //     'icondefault' => 'userenrolements',
            //     'style' => 'user',
            //     'icon' => 'fa-user',
            //     'iconsmall' => 'fa-user'
            // ),

            'urdcbulkupload' => array(
                'category' => 'CompanyAdmin',
                'tab' => 1,
                'name' => "Users Bulk Upload",
                'url' => '/local/custompage/urdcbulkupload.php',
                'cap' => 'block/iomad_company_admin:company_manager',
                'icondefault' => 'down',
                'style' => 'user',
                'icon' => 'fa-user',
                'iconsmall' => 'fa-download'
            ),

            'urdccms' => array(
                'category' => 'CompanyAdmin',
                'tab' => 1,
                'name' => "URDC CMS ",
                'url' => '/local/custompage/cms.php',
                'cap' => 'block/iomad_company_admin:company_edit',
                'icondefault' => 'managecoursesettings',
                'style' => 'user',
                'icon' => 'fa-user',
                'iconsmall' => 'fa-edit'
            ),

            'urdcquestionbank' => array(
                'category' => 'CompanyAdmin',
                'tab' => 1,
                'name' => "URDC Question Bank ",
                'url' => '/course/admin.php?courseid='.$courseid,
                'cap' => 'block/iomad_company_admin:company_edit',
                'icondefault' => 'managecoursesettings',
                'style' => 'user',
                'icon' => 'fa-user',
                'iconsmall' => 'fa-edit'
            ),
            'manageurdctest' => array(
                'category' => 'CompanyAdmin',
                'tab' => 1,
                'name' => "Manage URDC Tests",
                'url' => '/local/custompage/test.php',
                'cap' => 'block/iomad_company_admin:company_edit',
                'icondefault' => 'newcourse',
                'style' => 'user',
                'icon' => 'fa-user',
                'iconsmall' => 'fa-edit'
            ),

            'manageurdcdrive' => array(
                'category' => 'CompanyAdmin',
                'tab' => 1,
                'name' => "Manage URDC Recruitment Drives ",
                'url' => '/local/custompage/recruitment_drive.php',
                'cap' => 'block/iomad_company_admin:company_edit',
                'icondefault' => 'courses',
                'style' => 'user',
                'icon' => 'fa-user',
                'iconsmall' => 'fa-edit'
            ),
            

            'createuser' => array(
                'category' => 'UserAdmin',
                'tab' => 2,
                'name' => get_string('createuser', 'block_iomad_company_admin'),
                'url' => 'company_user_create_form.php',
                'cap' => 'block/iomad_company_admin:user_create',
                'icondefault' => 'usernew',
                'style' => 'user',
                'icon' => 'fa-user',
                'iconsmall' => 'fa-plus-square',
            ),
            'edituser' => array(
                'category' => 'UserAdmin',
                'tab' => 2,
                'name' => get_string('edituser', 'block_iomad_company_admin'),
                'url' => 'editusers.php',
                'cap' => 'block/iomad_company_admin:user_create',
                'icondefault' => 'useredit',
                'style' => 'user',
                'icon' => 'fa-user',
                'iconsmall' => 'fa-gear',
            ),
            'assignmanagers' => array(
                'category' => 'CompanyAdmin',
                'tab' => 2,
                'name' => get_string('assignmanagers', 'block_iomad_company_admin'),
                'url' => 'company_managers_form.php',
                'cap' => 'block/iomad_company_admin:company_manager',
                'icondefault' => 'assigndepartmentusers',
                'style' => 'department',
                'icon' => 'fa-group',
                'iconsmall' => 'fa-chevron-circle-right'
            ),
            'assignusertocompany' => array(
                'category' => 'UserAdmin',
                'tab' => 2,
                'name' => get_string('assigntocompany', 'block_iomad_company_admin'),
                'url' => 'company_users_form.php',
                'cap' => 'block/iomad_company_admin:company_user',
                'icondefault' => '',
                'style' => 'user',
                'icon' => 'fa-building',
                'iconsmall' => 'fa-chevron-circle-left',
            ),
            'uploadfromfile' => array(
                'category' => 'UserAdmin',
                'tab' => 2,
                'name' => get_string('user_upload_title', 'block_iomad_company_admin'),
                'url' => 'uploaduser.php',
                'cap' => 'block/iomad_company_admin:user_upload',
                'icondefault' => 'up',
                'style' => 'user',
                'icon' => 'fa-file',
                'iconsmall' => 'fa-upload',

            ),
            'downloadusers' => array(
                'category' => 'UserAdmin',
                'tab' => 2,
                'name' => get_string('users_download', 'block_iomad_company_admin'),
                'url' => 'user_bulk_download.php',
                'cap' => 'block/iomad_company_admin:user_upload',
                'icondefault' => 'down',
                'style' => 'user',
                'icon' => 'fa-group',
                'iconsmall' => 'fa-download',
            ),
            'createcourse' => array(
                'category' => 'CourseAdmin',
                'tab' => 3,
                'name' => get_string('createcourse', 'block_iomad_company_admin'),
                'url' => 'company_course_create_form.php',
                'cap' => 'block/iomad_company_admin:createcourse',
                'icondefault' => 'createcourse',
                'style' => 'course',
                'icon' => 'fa-file-text',
                'iconsmall' => 'fa-plus-square',
            ),
            'enroluser' => array(
                'category' => 'UserAdmin',
                'tab' => 3,
                'name' => get_string('enroluser', 'block_iomad_company_admin'),
                'url' => 'company_course_users_form.php',
                'cap' => 'block/iomad_company_admin:company_course_users',
                'icondefault' => 'userenrolements',
                'style' => 'course',
                'icon' => 'fa-file-text',
                'iconsmall' => 'fa-user',
            ),
            'managecourses' => array(
                'category' => 'CourseAdmin',
                'tab' => 3,
                'name' => get_string('iomad_courses_title', 'block_iomad_company_admin'),
                'url' => 'iomad_courses_form.php',
                'cap' => 'block/iomad_company_admin:viewcourses',
                'icondefault' => 'managecoursesettings',
                'style' => 'course',
                'icon' => 'fa-file-text',
                'iconsmall' => 'fa-gear',
            ),
            'assigntocompany' => array(
                'category' => 'CourseAdmin',
                'tab' => 3,
                'name' => get_string('assigntocompany', 'block_iomad_company_admin'),
                'url' => 'company_courses_form.php',
                'cap' => 'block/iomad_company_admin:company_course',
                'icondefault' => 'assigntocompany',
                'style' => 'course',
                'icon' => 'fa-building',
                'iconsmall' => 'fa-chevron-circle-left'
            ),
            'managegroups' => array(
                'category' => 'CourseAdmin',
                'tab' => 3,
                'name' => get_string('managegroups', 'block_iomad_company_admin'),
                'url' => 'company_groups_create_form.php',
                'cap' => 'block/iomad_company_admin:edit_groups',
                'icondefault' => 'groupsedit',
                'style' => 'group',
                'icon' => 'fa-group',
                'iconsmall' => 'fa-gear',
            ),
            'assigngroups' => array(
                'category' => 'CourseAdmin',
                'tab' => 3,
                'name' => get_string('assigncoursegroups', 'block_iomad_company_admin'),
                'url' => 'company_groups_users_form.php',
                'cap' => 'block/iomad_company_admin:assign_groups',
                'icondefault' => 'groupsassign',
                'style' => 'group',
                'icon' => 'fa-group',
                'iconsmall' => 'fa-plus-square',
            ),
            'classrooms' => array(
                'category' => 'CourseAdmin',
                'tab' => 3,
                'name' => get_string('classrooms', 'block_iomad_company_admin'),
                'url' => 'classroom_list.php',
                'cap' => 'block/iomad_company_admin:classrooms',
                'icondefault' => 'teachinglocations',
                'style' => 'company',
                'icon' => 'fa-map-marker',
                'iconsmall' => 'fa-gear',
            ),
            'manageiomadlicenses' => array(
                'category' => 'LicenseAdmin',
                'tab' => 4,
                'name' => get_string('managelicenses', 'block_iomad_company_admin'),
                'url' => 'company_license_list.php',
                'cap' => 'block/iomad_company_admin:edit_my_licenses',
                'icondefault' => 'licensemanagement',
                'style' => 'license',
                'icon' => 'fa-legal',
                'iconsmall' => 'fa-gear',
            ),
            'licenseusers' => array(
                'category' => 'LicenseAdmin',
                'tab' => 4,
                'name' => get_string('licenseusers', 'block_iomad_company_admin'),
                'url' => 'company_license_users_form.php',
                'cap' => 'block/iomad_company_admin:allocate_licenses',
                'icondefault' => 'userlicenseallocations',
                'style' => 'license',
                'icon' => 'fa-legal',
                'iconsmall' => 'fa-user'
            ),
            'iomadframeworksettings' => array(
                'category' => 'CompetencyAdmin',
                'tab' => 5,
                'name' => get_string('frameworksettings', 'block_iomad_company_admin'),
                'url' => 'iomad_frameworks_form.php',
                'cap' => 'block/iomad_company_admin:manageframeworks',
                'icondefault' => 'managecoursesettings',
                'style' => 'competency',
                'icon' => 'fa-list',
                'iconsmall' => 'fa-cog'
            ),
            'companytemplates' => array(
                'category' => 'CompetencyAdmin',
                'tab' => 5,
                'name' => get_string('companytemplates', 'block_iomad_company_admin'),
                'url' => 'company_competency_templates_form.php',
                'cap' => 'block/iomad_company_admin:company_template',
                'icondefault' => 'assigntocompany',
                'style' => 'competency',
                'icon' => 'fa-cubes',
                'iconsmall' => 'fa-chevron-circle-right'
            ),
            'iomadtemplatesettings' => array(
                'category' => 'CompetencyAdmin',
                'tab' => 5,
                'name' => get_string('templatesettings', 'block_iomad_company_admin'),
                'url' => 'iomad_templates_form.php',
                'cap' => 'block/iomad_company_admin:managetemplates',
                'icondefault' => 'managecoursesettings',
                'style' => 'competency',
                'icon' => 'fa-cubes',
                'iconsmall' => 'fa-cog'
            ),
            'edittemplates' => array(
                'category' => 'CompetencyAdmin',
                'tab' => 5,
                'name' => get_string('templates', 'tool_lp'),
                'url' => '/admin/tool/lp/learningplans.php?pagecontextid=1',
                'cap' => 'block/iomad_company_admin:templateview',
                'icondefault' => 'userenrolements',
                'style' => 'competency',
                'icon' => 'fa-cubes',
                'iconsmall' => 'fa-eye'
            ),
            
        );
    }else if($companyid == 3){
        return array(
            'addcompany' => array(
                'category' => 'CompanyAdmin',
                'tab' => 1,
                'name' => get_string('createcompany', 'block_iomad_company_admin'),
                'url' => 'company_edit_form.php?createnew=1',
                'cap' => 'block/iomad_company_admin:company_add',
                'icondefault' => 'newcompany',
                'style' => 'company',
                'icon' => 'fa-building',
                'iconsmall' => 'fa-plus-square'
            ),
            'editcompany' => array(
                'category' => 'CompanyAdmin',
                'tab' => 1,
                'name' => get_string('editcompany', 'block_iomad_company_admin'),
                'url' => 'company_edit_form.php',
                'cap' => 'block/iomad_company_admin:company_edit',
                'icondefault' => 'editcompany',
                'style' => 'company',
                'icon' => 'fa-building',
                'iconsmall' => 'fa-edit'
            ),
            'managecompanies' => array(
                'category' => 'CompanyAdmin',
                'tab' => 1,
                'name' => get_string('managecompanies', 'block_iomad_company_admin'),
                'url' => 'editcompanies.php',
                'cap' => 'block/iomad_company_admin:company_add_child',
                'icondefault' => 'editcompany',
                'style' => 'company',
                'icon' => 'fa-building',
                'iconsmall' => 'fa-gear'
            ),
            'editdepartments' => array(
                'category' => 'CompanyAdmin',
                'tab' => 1,
                'name' => get_string('editdepartment', 'block_iomad_company_admin'),
                'url' => 'company_departments.php',
                'cap' => 'block/iomad_company_admin:edit_departments',
                'icondefault' => 'managedepartment',
                'style' => 'department',
                'icon' => 'fa-group',
                'iconsmall' => 'fa-gear'
            ),
            'userprofiles' => array(
                'category' => 'CompanyAdmin',
                'tab' => 1,
                'name' => get_string('userprofiles', 'block_iomad_company_admin'),
                'url' => 'company_user_profiles.php',
                'cap' => 'block/iomad_company_admin:company_user_profiles',
                'icondefault' => 'optionalprofiles',
                'style' => 'user',
                'icon' => 'fa-user',
                'iconsmall' => 'fa-info-circle'
            ),
            'restrictcapabilities' => array(
                'category' => 'CompanyAdmin',
                'tab' => 1,
                'name' => get_string('restrictcapabilities', 'block_iomad_company_admin'),
                'url' => 'company_capabilities.php',
                'cap' => 'block/iomad_company_admin:restrict_capabilities',
                'icondefault' => 'users',
                'style' => 'user',
                'icon' => 'fa-user',
                'iconsmall' => 'fa-info-circle'
            ),

            'reportleadusers' => array(
                'category' => 'CompanyAdmin',
                'tab' => 1,
                'name' => "Lead Users Report ",
                'url' => '/local/custompage/leaduserreport.php',
                'cap' => 'block/iomad_company_admin:company_edit',
                'icondefault' => 'report',
                'style' => 'user',
                'icon' => 'fa-user',
                'iconsmall' => 'fa-bar-chart-o'
            ),
            
            'listleadusers' => array(
                'category' => 'CompanyAdmin',
                'tab' => 1,
                'name' => "Lead Users",
                'url' => '/local/custompage/lu_list.php',
                'cap' => 'block/iomad_company_admin:company_edit',
                'icondefault' => 'manageuser',
                'style' => 'user',
                'icon' => 'fa-user',
                'iconsmall' => 'fa-user'
            ),

            'enrolbatchtocourses' => array(
                'category' => 'CompanyAdmin',
                'tab' => 1,
                'name' => "Assign Batch to Course",
                'url' => '/local/custompage/assign_batch_to_course_form.php',
                'cap' => 'block/iomad_company_admin:company_edit',
                'icondefault' => 'assigncourses',
                'style' => 'user',
                'icon' => 'fa-user',
                'iconsmall' => 'fa-user'
            ),

            'assigntrainers' => array(
                'category' => 'CompanyAdmin',
                'tab' => 1,
                'name' => "Manage Trainers",
                'url' => '/local/custompage/assign_lead_trainer.php',
                'cap' => 'block/iomad_company_admin:company_edit',
                'icondefault' => 'assignusers',
                'style' => 'user',
                'icon' => 'fa-user',
                'iconsmall' => 'fa-user'
            ),
            'assignstudents' => array(
                'category' => 'CompanyAdmin',
                'tab' => 1,
                'name' => "Manage Students",
                'url' => '/local/custompage/assign_lead_student.php',
                'cap' => 'block/iomad_company_admin:company_edit',
                'icondefault' => 'userenrolements',
                'style' => 'user',
                'icon' => 'fa-user',
                'iconsmall' => 'fa-user'
            ),

            // 'assignleadusers' => array(
            //     'category' => 'CompanyAdmin',
            //     'tab' => 1,
            //     'name' => "Manage Lead Users ",
            //     'url' => '/local/custompage/assign_lu.php',
            //     'cap' => 'block/iomad_company_admin:company_edit',
                // 'icondefault' => 'userenrolements',
                // 'style' => 'user',
                // 'icon' => 'fa-user',
                // 'iconsmall' => 'fa-user'
            // ),

            // 'assignleaduser' => array(
            //     'category' => 'CompanyAdmin',
            //     'tab' => 1,
            //     'name' => "Assign Lead Student Batch ",
            //     'url' => '/local/custompage/assign_lead_batch_user_form.php',
            //     'cap' => 'block/iomad_company_admin:company_edit',
            //     'icondefault' => 'useredit',
            //     'style' => 'user',
            //     'icon' => 'fa-user',
            //     'iconsmall' => 'fa-gear'
            // ),'assignleadcourse' => array(
            //     'category' => 'CompanyAdmin',
            //     'tab' => 1,
            //     'name' => "Assign Lead Student Batch ",
            //     'url' => '/local/custompage/assign_lead_batch_course_form.php',
            //     'cap' => 'block/iomad_company_admin:company_edit',
            //     'icondefault' => 'useredit',
            //     'style' => 'user',
            //     'icon' => 'fa-user',
            //     'iconsmall' => 'fa-gear'
            // ),

            // 'assingbatchusers' => array(
            //     'category' => 'CompanyAdmin',
            //     'tab' => 1,
            //     'name' => "Assign User to Batch ",
            //     'url' => '/local/custompage/assign_lead_batch_user_form.php',
            //     'cap' => 'block/iomad_company_admin:company_edit',
            //     'icondefault' => 'useredit',
            //     'style' => 'user',
            //     'icon' => 'fa-user',
            //     'iconsmall' => 'fa-gear'
            // ),

            'createleadbatch' => array(
                'category' => 'CompanyAdmin',
                'tab' => 1,
                'name' => "Create Lead Batch",
                'url' => '/local/custompage/create_lead_batch_form.php',
                'cap' => 'block/iomad_company_admin:company_edit',
                'icondefault' => 'newcompany',
                'style' => 'user',
                'icon' => 'fa-user',
                'iconsmall' => 'fa-plus-square'
            ),
            // 'assingbatchcourses' => array(
            //     'category' => 'CompanyAdmin',
            //     'tab' => 1,
            //     'name' => "Assing Course to Batch",
            //     'url' => '/local/custompage/assign_lead_batch_course_form.php',
            //     'cap' => 'block/iomad_company_admin:company_edit',
            //     'icondefault' => 'useredit',
            //     'style' => 'user',
            //     'icon' => 'fa-user',
            //     'iconsmall' => 'fa-gear'
            // ),
            
            'createleaduser' => array(
                'category' => 'CompanyAdmin',
                'tab' => 1,
                'name' => "Create Lead User",
                'url' => '/local/custompage/company_user_create_form.php?createlu='. 1,
                'cap' => 'block/iomad_company_admin:company_edit',
                'icondefault' => 'usernew',
                'style' => 'user',
                'icon' => 'fa-user',
                'iconsmall' => 'fa-plus-square'
            ),

            'uploadleadusers' => array(
                'category' => 'CompanyAdmin',
                'tab' => 1,
                'name' => "Upload Lead Users",
                'url' => '/local/custompage/uploaduser.php',
                'cap' => 'block/iomad_company_admin:company_edit',
                'icondefault' => 'up',
                'style' => 'user',
                'icon' => 'fa-user',
                'iconsmall' => 'fa-upload'
            ),

            'createleadtest' => array(
                'category' => 'CompanyAdmin',
                'tab' => 1,
                'name' => "Create Lead Test",
                'url' => '/local/custompage/create_lead_test_form.php',
                'cap' => 'block/iomad_company_admin:company_edit',
                'icondefault' => 'newcourse',
                'style' => 'user',
                'icon' => 'fa-user',
                'iconsmall' => 'fa-plus-square'
            ),

            'manageleadtest' => array(
                'category' => 'CompanyAdmin',
                'tab' => 1,
                'name' => "Manage Lead Tests",
                'url' => '/local/custompage/test.php',
                'cap' => 'block/iomad_company_admin:company_edit',
                'icondefault' => 'newcourse',
                'style' => 'user',
                'icon' => 'fa-user',
                'iconsmall' => 'fa-edit'
            ),

            'createuser' => array(
                'category' => 'UserAdmin',
                'tab' => 2,
                'name' => get_string('createuser', 'block_iomad_company_admin'),
                'url' => 'company_user_create_form.php',
                'cap' => 'block/iomad_company_admin:user_create',
                'icondefault' => 'usernew',
                'style' => 'user',
                'icon' => 'fa-user',
                'iconsmall' => 'fa-plus-square',
            ),

            // 'leadusermanagement' => array(
            //     'category' => 'CompanyAdmin',
            //     'tab' => 1,
            //     'name' => "LEAD User Management",
            //     'url' => '/local/custompage/usermanagement.php',
            //     'cap' => 'block/iomad_company_admin:company_edit',
            //     'icondefault' => 'userenrolements',
            //     'style' => 'user',
            //     'icon' => 'fa-user',
            //     'iconsmall' => 'fa-user'
            // ),

            'edituser' => array(
                'category' => 'UserAdmin',
                'tab' => 2,
                'name' => get_string('edituser', 'block_iomad_company_admin'),
                'url' => 'editusers.php',
                'cap' => 'block/iomad_company_admin:user_create',
                'icondefault' => 'useredit',
                'style' => 'user',
                'icon' => 'fa-user',
                'iconsmall' => 'fa-gear',
            ),
            'assignmanagers' => array(
                'category' => 'CompanyAdmin',
                'tab' => 2,
                'name' => get_string('assignmanagers', 'block_iomad_company_admin'),
                'url' => 'company_managers_form.php',
                'cap' => 'block/iomad_company_admin:company_manager',
                'icondefault' => 'assigndepartmentusers',
                'style' => 'department',
                'icon' => 'fa-group',
                'iconsmall' => 'fa-chevron-circle-right'
            ),
            'assignusertocompany' => array(
                'category' => 'UserAdmin',
                'tab' => 2,
                'name' => get_string('assigntocompany', 'block_iomad_company_admin'),
                'url' => 'company_users_form.php',
                'cap' => 'block/iomad_company_admin:company_user',
                'icondefault' => '',
                'style' => 'user',
                'icon' => 'fa-building',
                'iconsmall' => 'fa-chevron-circle-left',
            ),
            'uploadfromfile' => array(
                'category' => 'UserAdmin',
                'tab' => 2,
                'name' => get_string('user_upload_title', 'block_iomad_company_admin'),
                'url' => 'uploaduser.php',
                'cap' => 'block/iomad_company_admin:user_upload',
                'icondefault' => 'up',
                'style' => 'user',
                'icon' => 'fa-file',
                'iconsmall' => 'fa-upload',

            ),
            'downloadusers' => array(
                'category' => 'UserAdmin',
                'tab' => 2,
                'name' => get_string('users_download', 'block_iomad_company_admin'),
                'url' => 'user_bulk_download.php',
                'cap' => 'block/iomad_company_admin:user_upload',
                'icondefault' => 'down',
                'style' => 'user',
                'icon' => 'fa-group',
                'iconsmall' => 'fa-download',
            ),
            'createcourse' => array(
                'category' => 'CourseAdmin',
                'tab' => 3,
                'name' => get_string('createcourse', 'block_iomad_company_admin'),
                'url' => 'company_course_create_form.php',
                'cap' => 'block/iomad_company_admin:company_edit',
                'icondefault' => 'createcourse',
                'style' => 'course',
                'icon' => 'fa-file-text',
                'iconsmall' => 'fa-plus-square',
            ),
            'enroluser' => array(
                'category' => 'UserAdmin',
                'tab' => 3,
                'name' => get_string('enroluser', 'block_iomad_company_admin'),
                'url' => 'company_course_users_form.php',
                'cap' => 'block/iomad_company_admin:company_course_users',
                'icondefault' => 'userenrolements',
                'style' => 'course',
                'icon' => 'fa-file-text',
                'iconsmall' => 'fa-user',
            ),
            'managecourses' => array(
                'category' => 'CourseAdmin',
                'tab' => 3,
                'name' => get_string('iomad_courses_title', 'block_iomad_company_admin'),
                'url' => 'iomad_courses_form.php',
                'cap' => 'block/iomad_company_admin:viewcourses',
                'icondefault' => 'managecoursesettings',
                'style' => 'course',
                'icon' => 'fa-file-text',
                'iconsmall' => 'fa-gear',
            ),
            'assigntocompany' => array(
                'category' => 'CourseAdmin',
                'tab' => 3,
                'name' => get_string('assigntocompany', 'block_iomad_company_admin'),
                'url' => 'company_courses_form.php',
                'cap' => 'block/iomad_company_admin:company_course',
                'icondefault' => 'assigntocompany',
                'style' => 'course',
                'icon' => 'fa-building',
                'iconsmall' => 'fa-chevron-circle-left'
            ),
            'managegroups' => array(
                'category' => 'CourseAdmin',
                'tab' => 3,
                'name' => get_string('managegroups', 'block_iomad_company_admin'),
                'url' => 'company_groups_create_form.php',
                'cap' => 'block/iomad_company_admin:edit_groups',
                'icondefault' => 'groupsedit',
                'style' => 'group',
                'icon' => 'fa-group',
                'iconsmall' => 'fa-gear',
            ),
            'assigngroups' => array(
                'category' => 'CourseAdmin',
                'tab' => 3,
                'name' => get_string('assigncoursegroups', 'block_iomad_company_admin'),
                'url' => 'company_groups_users_form.php',
                'cap' => 'block/iomad_company_admin:assign_groups',
                'icondefault' => 'groupsassign',
                'style' => 'group',
                'icon' => 'fa-group',
                'iconsmall' => 'fa-plus-square',
            ),
            'classrooms' => array(
                'category' => 'CourseAdmin',
                'tab' => 3,
                'name' => get_string('classrooms', 'block_iomad_company_admin'),
                'url' => 'classroom_list.php',
                'cap' => 'block/iomad_company_admin:classrooms',
                'icondefault' => 'teachinglocations',
                'style' => 'company',
                'icon' => 'fa-map-marker',
                'iconsmall' => 'fa-gear',
            ),
            'manageiomadlicenses' => array(
                'category' => 'LicenseAdmin',
                'tab' => 4,
                'name' => get_string('managelicenses', 'block_iomad_company_admin'),
                'url' => 'company_license_list.php',
                'cap' => 'block/iomad_company_admin:edit_my_licenses',
                'icondefault' => 'licensemanagement',
                'style' => 'license',
                'icon' => 'fa-legal',
                'iconsmall' => 'fa-gear',
            ),
            'licenseusers' => array(
                'category' => 'LicenseAdmin',
                'tab' => 4,
                'name' => get_string('licenseusers', 'block_iomad_company_admin'),
                'url' => 'company_license_users_form.php',
                'cap' => 'block/iomad_company_admin:allocate_licenses',
                'icondefault' => 'userlicenseallocations',
                'style' => 'license',
                'icon' => 'fa-legal',
                'iconsmall' => 'fa-user'
            ),
            'iomadframeworksettings' => array(
                'category' => 'CompetencyAdmin',
                'tab' => 5,
                'name' => get_string('frameworksettings', 'block_iomad_company_admin'),
                'url' => 'iomad_frameworks_form.php',
                'cap' => 'block/iomad_company_admin:manageframeworks',
                'icondefault' => 'managecoursesettings',
                'style' => 'competency',
                'icon' => 'fa-list',
                'iconsmall' => 'fa-cog'
            ),
            'companytemplates' => array(
                'category' => 'CompetencyAdmin',
                'tab' => 5,
                'name' => get_string('companytemplates', 'block_iomad_company_admin'),
                'url' => 'company_competency_templates_form.php',
                'cap' => 'block/iomad_company_admin:company_template',
                'icondefault' => 'assigntocompany',
                'style' => 'competency',
                'icon' => 'fa-cubes',
                'iconsmall' => 'fa-chevron-circle-right'
            ),
            'iomadtemplatesettings' => array(
                'category' => 'CompetencyAdmin',
                'tab' => 5,
                'name' => get_string('templatesettings', 'block_iomad_company_admin'),
                'url' => 'iomad_templates_form.php',
                'cap' => 'block/iomad_company_admin:managetemplates',
                'icondefault' => 'managecoursesettings',
                'style' => 'competency',
                'icon' => 'fa-cubes',
                'iconsmall' => 'fa-cog'
            ),
            'edittemplates' => array(
                'category' => 'CompetencyAdmin',
                'tab' => 5,
                'name' => get_string('templates', 'tool_lp'),
                'url' => '/admin/tool/lp/learningplans.php?pagecontextid=1',
                'cap' => 'block/iomad_company_admin:templateview',
                'icondefault' => 'userenrolements',
                'style' => 'competency',
                'icon' => 'fa-cubes',
                'iconsmall' => 'fa-eye'
            ),
            
        );
    }else if($companyid == 4){
        return array(
            'addcompany' => array(
                'category' => 'CompanyAdmin',
                'tab' => 1,
                'name' => get_string('createcompany', 'block_iomad_company_admin'),
                'url' => 'company_edit_form.php?createnew=1',
                'cap' => 'block/iomad_company_admin:company_add',
                'icondefault' => 'newcompany',
                'style' => 'company',
                'icon' => 'fa-building',
                'iconsmall' => 'fa-plus-square'
            ),
            'editcompany' => array(
                'category' => 'CompanyAdmin',
                'tab' => 1,
                'name' => get_string('editcompany', 'block_iomad_company_admin'),
                'url' => 'company_edit_form.php',
                'cap' => 'block/iomad_company_admin:company_edit',
                'icondefault' => 'editcompany',
                'style' => 'company',
                'icon' => 'fa-building',
                'iconsmall' => 'fa-edit'
            ),
            'managecompanies' => array(
                'category' => 'CompanyAdmin',
                'tab' => 1,
                'name' => get_string('managecompanies', 'block_iomad_company_admin'),
                'url' => 'editcompanies.php',
                'cap' => 'block/iomad_company_admin:company_add_child',
                'icondefault' => 'editcompany',
                'style' => 'company',
                'icon' => 'fa-building',
                'iconsmall' => 'fa-gear'
            ),
            'editdepartments' => array(
                'category' => 'CompanyAdmin',
                'tab' => 1,
                'name' => get_string('editdepartment', 'block_iomad_company_admin'),
                'url' => 'company_departments.php',
                'cap' => 'block/iomad_company_admin:edit_departments',
                'icondefault' => 'managedepartment',
                'style' => 'department',
                'icon' => 'fa-group',
                'iconsmall' => 'fa-gear'
            ),
            'userprofiles' => array(
                'category' => 'CompanyAdmin',
                'tab' => 1,
                'name' => get_string('userprofiles', 'block_iomad_company_admin'),
                'url' => 'company_user_profiles.php',
                'cap' => 'block/iomad_company_admin:company_user_profiles',
                'icondefault' => 'optionalprofiles',
                'style' => 'user',
                'icon' => 'fa-user',
                'iconsmall' => 'fa-info-circle'
            ),
            'restrictcapabilities' => array(
                'category' => 'CompanyAdmin',
                'tab' => 1,
                'name' => get_string('restrictcapabilities', 'block_iomad_company_admin'),
                'url' => 'company_capabilities.php',
                'cap' => 'block/iomad_company_admin:restrict_capabilities',
                'icondefault' => 'users',
                'style' => 'user',
                'icon' => 'fa-user',
                'iconsmall' => 'fa-info-circle'
            ),

            'btuserscore' => array(
                'category' => 'CompanyAdmin',
                'tab' => 1,
                'name' => "BT Users Score ",
                'url' => '/local/custompage/bu_drive_list.php',
                'cap' => 'block/iomad_company_admin:company_edit',
                'icondefault' => 'useredit',
                'style' => 'user',
                'icon' => 'fa-user',
                'iconsmall' => 'fa-gear'
            ),

            'btuserreport' => array(
                'category' => 'CompanyAdmin',
                'tab' => 1,
                'name' => "BT Users Report ",
                'url' => '/local/custompage/btuserreport.php',
                'cap' => 'block/iomad_company_admin:company_edit',
                'icondefault' => 'report',
                'style' => 'user',
                'icon' => 'fa-user',
                'iconsmall' => 'fa-bar-chart-o'
            ),

            // 'createbrm' => array(
            //     'category' => 'CompanyAdmin',
            //     'tab' => 1,
            //     'name' => "Create BT Reporting Manager ",
            //     'url' => '/local/custompage/create_brm_form.php',
            //     'cap' => 'block/iomad_company_admin:restrict_capabilities',
            //     'icondefault' => 'useredit',
            //     'style' => 'user',
            //     'icon' => 'fa-user',
            //     'iconsmall' => 'fa-gear'
            // ),

            // 'assignbrm' => array(
            //     'category' => 'CompanyAdmin',
            //     'tab' => 1,
            //     'name' => "Assign to BT Reporting Manager ",
            //     'url' => '/local/custompage/assign_brm.php',
            //     'cap' => 'block/iomad_company_admin:restrict_capabilities',
            //     'icondefault' => 'useredit',
            //     'style' => 'user',
            //     'icon' => 'fa-user',
            //     'iconsmall' => 'fa-gear'
            // ),


            'managebtuser' => array(
                'category' => 'CompanyAdmin',
                'tab' => 1,
                'name' => "Manage BT User ",
                'url' => '/local/custompage/bu_list.php',
                'cap' => 'block/iomad_company_admin:company_edit',
                'icondefault' => 'userenrolements',
                'style' => 'user',
                'icon' => 'fa-user',
                'iconsmall' => 'fa-user'
            ),

            'btrecruitmentdrrive' => array(
                'category' => 'CompanyAdmin',
                'tab' => 1,
                'name' => "Create BT Recruitment Drive ",
                'url' => '/local/custompage/create_bt_recruitment_drive_form.php',
                'cap' => 'block/iomad_company_admin:company_edit',
                'icondefault' => 'createcourse',
                'style' => 'user',
                'icon' => 'fa-user',
                'iconsmall' => 'fa-plus-square'
            ),

            'bttest' => array(
                'category' => 'CompanyAdmin',
                'tab' => 1,
                'name' => "Create BT Test ",
                'url' => '/local/custompage/create_bt_test_form.php',
                'cap' => 'block/iomad_company_admin:company_edit',
                'icondefault' => 'newcourse',
                'style' => 'user',
                'icon' => 'fa-user',
                'iconsmall' => 'fa-plus-square'
            ),

            // 'btusermanagement' => array(
            //     'category' => 'CompanyAdmin',
            //     'tab' => 1,
            //     'name' => "BT User Management",
            //     'url' => '/local/custompage/usermanagement.php',
            //     'cap' => 'block/iomad_company_admin:company_edit',
            //     'icondefault' => 'userenrolements',
            //     'style' => 'user',
            //     'icon' => 'fa-user',
            //     'iconsmall' => 'fa-user'
            // ),

            'btcms' => array(
                'category' => 'CompanyAdmin',
                'tab' => 1,
                'name' => "BT CMS ",
                'url' => '/local/custompage/cms.php',
                'cap' => 'block/iomad_company_admin:company_edit',
                'icondefault' => 'managecoursesettings',
                'style' => 'user',
                'icon' => 'fa-user',
                'iconsmall' => 'fa-edit'
            ),

            'managebttest' => array(
                'category' => 'CompanyAdmin',
                'tab' => 1,
                'name' => "Manage BT Tests",
                'url' => '/local/custompage/test.php',
                'cap' => 'block/iomad_company_admin:company_edit',
                'icondefault' => 'newcourse',
                'style' => 'user',
                'icon' => 'fa-user',
                'iconsmall' => 'fa-edit'
            ),

            'managebtdrive' => array(
                'category' => 'CompanyAdmin',
                'tab' => 1,
                'name' => "Manage BT Recruitment Drives ",
                'url' => '/local/custompage/recruitment_drive.php',
                'cap' => 'block/iomad_company_admin:company_edit',
                'icondefault' => 'courses',
                'style' => 'user',
                'icon' => 'fa-user',
                'iconsmall' => 'fa-edit'
            ),

            'btquestionbank' => array(
                'category' => 'CompanyAdmin',
                'tab' => 1,
                'name' => "BT Question Bank ",
                'url' => '/course/admin.php?courseid='.$courseid,
                'cap' => 'block/iomad_company_admin:company_edit',
                'icondefault' => 'managecoursesettings',
                'style' => 'user',
                'icon' => 'fa-user',
                'iconsmall' => 'fa-edit'
            ),

            'createuser' => array(
                'category' => 'UserAdmin',
                'tab' => 2,
                'name' => get_string('createuser', 'block_iomad_company_admin'),
                'url' => 'company_user_create_form.php',
                'cap' => 'block/iomad_company_admin:user_create',
                'icondefault' => 'usernew',
                'style' => 'user',
                'icon' => 'fa-user',
                'iconsmall' => 'fa-plus-square',
            ),
            'edituser' => array(
                'category' => 'UserAdmin',
                'tab' => 2,
                'name' => get_string('edituser', 'block_iomad_company_admin'),
                'url' => 'editusers.php',
                'cap' => 'block/iomad_company_admin:user_create',
                'icondefault' => 'useredit',
                'style' => 'user',
                'icon' => 'fa-user',
                'iconsmall' => 'fa-gear',
            ),
            'assignmanagers' => array(
                'category' => 'CompanyAdmin',
                'tab' => 2,
                'name' => get_string('assignmanagers', 'block_iomad_company_admin'),
                'url' => 'company_managers_form.php',
                'cap' => 'block/iomad_company_admin:company_manager',
                'icondefault' => 'assigndepartmentusers',
                'style' => 'department',
                'icon' => 'fa-group',
                'iconsmall' => 'fa-chevron-circle-right'
            ),
            'assignusertocompany' => array(
                'category' => 'UserAdmin',
                'tab' => 2,
                'name' => get_string('assigntocompany', 'block_iomad_company_admin'),
                'url' => 'company_users_form.php',
                'cap' => 'block/iomad_company_admin:company_user',
                'icondefault' => '',
                'style' => 'user',
                'icon' => 'fa-building',
                'iconsmall' => 'fa-chevron-circle-left',
            ),
            'uploadfromfile' => array(
                'category' => 'UserAdmin',
                'tab' => 2,
                'name' => get_string('user_upload_title', 'block_iomad_company_admin'),
                'url' => 'uploaduser.php',
                'cap' => 'block/iomad_company_admin:user_upload',
                'icondefault' => 'up',
                'style' => 'user',
                'icon' => 'fa-file',
                'iconsmall' => 'fa-upload',

            ),
            'downloadusers' => array(
                'category' => 'UserAdmin',
                'tab' => 2,
                'name' => get_string('users_download', 'block_iomad_company_admin'),
                'url' => 'user_bulk_download.php',
                'cap' => 'block/iomad_company_admin:user_upload',
                'icondefault' => 'down',
                'style' => 'user',
                'icon' => 'fa-group',
                'iconsmall' => 'fa-download',
            ),
            'createcourse' => array(
                'category' => 'CourseAdmin',
                'tab' => 3,
                'name' => get_string('createcourse', 'block_iomad_company_admin'),
                'url' => 'company_course_create_form.php',
                'cap' => 'block/iomad_company_admin:createcourse',
                'icondefault' => 'createcourse',
                'style' => 'course',
                'icon' => 'fa-file-text',
                'iconsmall' => 'fa-plus-square',
            ),
            'enroluser' => array(
                'category' => 'UserAdmin',
                'tab' => 3,
                'name' => get_string('enroluser', 'block_iomad_company_admin'),
                'url' => 'company_course_users_form.php',
                'cap' => 'block/iomad_company_admin:company_course_users',
                'icondefault' => 'userenrolements',
                'style' => 'course',
                'icon' => 'fa-file-text',
                'iconsmall' => 'fa-user',
            ),
            'managecourses' => array(
                'category' => 'CourseAdmin',
                'tab' => 3,
                'name' => get_string('iomad_courses_title', 'block_iomad_company_admin'),
                'url' => 'iomad_courses_form.php',
                'cap' => 'block/iomad_company_admin:viewcourses',
                'icondefault' => 'managecoursesettings',
                'style' => 'course',
                'icon' => 'fa-file-text',
                'iconsmall' => 'fa-gear',
            ),
            'assigntocompany' => array(
                'category' => 'CourseAdmin',
                'tab' => 3,
                'name' => get_string('assigntocompany', 'block_iomad_company_admin'),
                'url' => 'company_courses_form.php',
                'cap' => 'block/iomad_company_admin:company_course',
                'icondefault' => 'assigntocompany',
                'style' => 'course',
                'icon' => 'fa-building',
                'iconsmall' => 'fa-chevron-circle-left'
            ),
            'managegroups' => array(
                'category' => 'CourseAdmin',
                'tab' => 3,
                'name' => get_string('managegroups', 'block_iomad_company_admin'),
                'url' => 'company_groups_create_form.php',
                'cap' => 'block/iomad_company_admin:edit_groups',
                'icondefault' => 'groupsedit',
                'style' => 'group',
                'icon' => 'fa-group',
                'iconsmall' => 'fa-gear',
            ),
            'assigngroups' => array(
                'category' => 'CourseAdmin',
                'tab' => 3,
                'name' => get_string('assigncoursegroups', 'block_iomad_company_admin'),
                'url' => 'company_groups_users_form.php',
                'cap' => 'block/iomad_company_admin:assign_groups',
                'icondefault' => 'groupsassign',
                'style' => 'group',
                'icon' => 'fa-group',
                'iconsmall' => 'fa-plus-square',
            ),
            'classrooms' => array(
                'category' => 'CourseAdmin',
                'tab' => 3,
                'name' => get_string('classrooms', 'block_iomad_company_admin'),
                'url' => 'classroom_list.php',
                'cap' => 'block/iomad_company_admin:classrooms',
                'icondefault' => 'teachinglocations',
                'style' => 'company',
                'icon' => 'fa-map-marker',
                'iconsmall' => 'fa-gear',
            ),
            'manageiomadlicenses' => array(
                'category' => 'LicenseAdmin',
                'tab' => 4,
                'name' => get_string('managelicenses', 'block_iomad_company_admin'),
                'url' => 'company_license_list.php',
                'cap' => 'block/iomad_company_admin:edit_my_licenses',
                'icondefault' => 'licensemanagement',
                'style' => 'license',
                'icon' => 'fa-legal',
                'iconsmall' => 'fa-gear',
            ),
            'licenseusers' => array(
                'category' => 'LicenseAdmin',
                'tab' => 4,
                'name' => get_string('licenseusers', 'block_iomad_company_admin'),
                'url' => 'company_license_users_form.php',
                'cap' => 'block/iomad_company_admin:allocate_licenses',
                'icondefault' => 'userlicenseallocations',
                'style' => 'license',
                'icon' => 'fa-legal',
                'iconsmall' => 'fa-user'
            ),
            'iomadframeworksettings' => array(
                'category' => 'CompetencyAdmin',
                'tab' => 5,
                'name' => get_string('frameworksettings', 'block_iomad_company_admin'),
                'url' => 'iomad_frameworks_form.php',
                'cap' => 'block/iomad_company_admin:manageframeworks',
                'icondefault' => 'managecoursesettings',
                'style' => 'competency',
                'icon' => 'fa-list',
                'iconsmall' => 'fa-cog'
            ),
            'companytemplates' => array(
                'category' => 'CompetencyAdmin',
                'tab' => 5,
                'name' => get_string('companytemplates', 'block_iomad_company_admin'),
                'url' => 'company_competency_templates_form.php',
                'cap' => 'block/iomad_company_admin:company_template',
                'icondefault' => 'assigntocompany',
                'style' => 'competency',
                'icon' => 'fa-cubes',
                'iconsmall' => 'fa-chevron-circle-right'
            ),
            'iomadtemplatesettings' => array(
                'category' => 'CompetencyAdmin',
                'tab' => 5,
                'name' => get_string('templatesettings', 'block_iomad_company_admin'),
                'url' => 'iomad_templates_form.php',
                'cap' => 'block/iomad_company_admin:managetemplates',
                'icondefault' => 'managecoursesettings',
                'style' => 'competency',
                'icon' => 'fa-cubes',
                'iconsmall' => 'fa-cog'
            ),
            'edittemplates' => array(
                'category' => 'CompetencyAdmin',
                'tab' => 5,
                'name' => get_string('templates', 'tool_lp'),
                'url' => '/admin/tool/lp/learningplans.php?pagecontextid=1',
                'cap' => 'block/iomad_company_admin:templateview',
                'icondefault' => 'userenrolements',
                'style' => 'competency',
                'icon' => 'fa-cubes',
                'iconsmall' => 'fa-eye'
            ),
            
        );
    }else{
        return array(
            'addcompany' => array(
                'category' => 'CompanyAdmin',
                'tab' => 1,
                'name' => get_string('createcompany', 'block_iomad_company_admin'),
                'url' => 'company_edit_form.php?createnew=1',
                'cap' => 'block/iomad_company_admin:company_add',
                'icondefault' => 'newcompany',
                'style' => 'company',
                'icon' => 'fa-building',
                'iconsmall' => 'fa-plus-square'
            ),
            'editcompany' => array(
                'category' => 'CompanyAdmin',
                'tab' => 1,
                'name' => get_string('editcompany', 'block_iomad_company_admin'),
                'url' => 'company_edit_form.php',
                'cap' => 'block/iomad_company_admin:company_edit',
                'icondefault' => 'editcompany',
                'style' => 'company',
                'icon' => 'fa-building',
                'iconsmall' => 'fa-edit'
            ),
            'managecompanies' => array(
                'category' => 'CompanyAdmin',
                'tab' => 1,
                'name' => get_string('managecompanies', 'block_iomad_company_admin'),
                'url' => 'editcompanies.php',
                'cap' => 'block/iomad_company_admin:company_add_child',
                'icondefault' => 'editcompany',
                'style' => 'company',
                'icon' => 'fa-building',
                'iconsmall' => 'fa-gear'
            ),
            'editdepartments' => array(
                'category' => 'CompanyAdmin',
                'tab' => 1,
                'name' => get_string('editdepartment', 'block_iomad_company_admin'),
                'url' => 'company_departments.php',
                'cap' => 'block/iomad_company_admin:edit_departments',
                'icondefault' => 'managedepartment',
                'style' => 'department',
                'icon' => 'fa-group',
                'iconsmall' => 'fa-gear'
            ),
            'userprofiles' => array(
                'category' => 'CompanyAdmin',
                'tab' => 1,
                'name' => get_string('userprofiles', 'block_iomad_company_admin'),
                'url' => 'company_user_profiles.php',
                'cap' => 'block/iomad_company_admin:company_user_profiles',
                'icondefault' => 'optionalprofiles',
                'style' => 'user',
                'icon' => 'fa-user',
                'iconsmall' => 'fa-info-circle'
            ),
            'restrictcapabilities' => array(
                'category' => 'CompanyAdmin',
                'tab' => 1,
                'name' => get_string('restrictcapabilities', 'block_iomad_company_admin'),
                'url' => 'company_capabilities.php',
                'cap' => 'block/iomad_company_admin:restrict_capabilities',
                'icondefault' => 'users',
                'style' => 'user',
                'icon' => 'fa-user',
                'iconsmall' => 'fa-info-circle'
            ),

            // 'genaricuserscore' => array(
            //     'category' => 'CompanyAdmin',
            //     'tab' => 1,
            //     'name' => $company." Users Score ",
            //     'url' => '/local/custompage/bu_drive_list.php',
            //     'cap' => 'block/iomad_company_admin:company_edit',
            //     'icondefault' => 'useredit',
            //     'style' => 'user',
            //     'icon' => 'fa-user',
            //     'iconsmall' => 'fa-gear'
            // ),

            'genaricuserreport' => array(
                'category' => 'CompanyAdmin',
                'tab' => 1,
                'name' => $company." Users Report ",
                'url' => '/local/custompage/usersreport.php',
                'cap' => 'block/iomad_company_admin:company_edit',
                'icondefault' => 'report',
                'style' => 'user',
                'icon' => 'fa-user',
                'iconsmall' => 'fa-bar-chart-o'
            ),

            // 'createbrm' => array(
            //     'category' => 'CompanyAdmin',
            //     'tab' => 1,
            //     'name' => "Create BT Reporting Manager ",
            //     'url' => '/local/custompage/create_brm_form.php',
            //     'cap' => 'block/iomad_company_admin:restrict_capabilities',
            //     'icondefault' => 'useredit',
            //     'style' => 'user',
            //     'icon' => 'fa-user',
            //     'iconsmall' => 'fa-gear'
            // ),

            // 'assignbrm' => array(
            //     'category' => 'CompanyAdmin',
            //     'tab' => 1,
            //     'name' => "Assign to BT Reporting Manager ",
            //     'url' => '/local/custompage/assign_brm.php',
            //     'cap' => 'block/iomad_company_admin:restrict_capabilities',
            //     'icondefault' => 'useredit',
            //     'style' => 'user',
            //     'icon' => 'fa-user',
            //     'iconsmall' => 'fa-gear'
            // ),


            'managegenaricuser' => array(
                'category' => 'CompanyAdmin',
                'tab' => 1,
                'name' => "Manage ".$company." User ",
                'url' => '/local/custompage/users_list.php',
                'cap' => 'block/iomad_company_admin:company_edit',
                'icondefault' => 'userenrolements',
                'style' => 'user',
                'icon' => 'fa-user',
                'iconsmall' => 'fa-user'
            ),

            'genaricrecruitmentdrrive' => array(
                'category' => 'CompanyAdmin',
                'tab' => 1,
                'name' => "Create ".$company." Recruitment Drive ",
                'url' => '/local/custompage/create_new_recruitment_drive.php',
                'cap' => 'block/iomad_company_admin:company_edit',
                'icondefault' => 'createcourse',
                'style' => 'user',
                'icon' => 'fa-user',
                'iconsmall' => 'fa-plus-square'
            ),

            'genarictest' => array(
                'category' => 'CompanyAdmin',
                'tab' => 1,
                'name' => "Create ".$company." Test ",
                'url' => '/local/custompage/create_new_test.php',
                'cap' => 'block/iomad_company_admin:company_edit',
                'icondefault' => 'newcourse',
                'style' => 'user',
                'icon' => 'fa-user',
                'iconsmall' => 'fa-plus-square'
            ),

            // 'btusermanagement' => array(
            //     'category' => 'CompanyAdmin',
            //     'tab' => 1,
            //     'name' => "BT User Management",
            //     'url' => '/local/custompage/usermanagement.php',
            //     'cap' => 'block/iomad_company_admin:company_edit',
            //     'icondefault' => 'userenrolements',
            //     'style' => 'user',
            //     'icon' => 'fa-user',
            //     'iconsmall' => 'fa-user'
            // ),

            'genariccms' => array(
                'category' => 'CompanyAdmin',
                'tab' => 1,
                'name' => $company." CMS ",
                'url' => '/local/custompage/cms.php',
                'cap' => 'block/iomad_company_admin:company_edit',
                'icondefault' => 'managecoursesettings',
                'style' => 'user',
                'icon' => 'fa-user',
                'iconsmall' => 'fa-edit'
            ),
            'managegenarictest' => array(
                'category' => 'CompanyAdmin',
                'tab' => 1,
                'name' => "Manage $company Tests",
                'url' => '/local/custompage/test.php',
                'cap' => 'block/iomad_company_admin:company_edit',
                'icondefault' => 'newcourse',
                'style' => 'user',
                'icon' => 'fa-user',
                'iconsmall' => 'fa-edit'
            ),

            'managegenaricdrive' => array(
                'category' => 'CompanyAdmin',
                'tab' => 1,
                'name' => "Manage $company Recruitment Drives ",
                'url' => '/local/custompage/recruitment_drive.php',
                'cap' => 'block/iomad_company_admin:company_edit',
                'icondefault' => 'courses',
                'style' => 'user',
                'icon' => 'fa-user',
                'iconsmall' => 'fa-edit'
            ),

            'genaricquestionbank' => array(
                'category' => 'CompanyAdmin',
                'tab' => 1,
                'name' => $company." Question Bank ",
                'url' => '/course/admin.php?courseid='.$courseid,
                'cap' => 'block/iomad_company_admin:company_edit',
                'icondefault' => 'managecoursesettings',
                'style' => 'user',
                'icon' => 'fa-user',
                'iconsmall' => 'fa-edit'
            ),

            'createuser' => array(
                'category' => 'UserAdmin',
                'tab' => 2,
                'name' => get_string('createuser', 'block_iomad_company_admin'),
                'url' => 'company_user_create_form.php',
                'cap' => 'block/iomad_company_admin:user_create',
                'icondefault' => 'usernew',
                'style' => 'user',
                'icon' => 'fa-user',
                'iconsmall' => 'fa-plus-square',
            ),
            'edituser' => array(
                'category' => 'UserAdmin',
                'tab' => 2,
                'name' => get_string('edituser', 'block_iomad_company_admin'),
                'url' => 'editusers.php',
                'cap' => 'block/iomad_company_admin:user_create',
                'icondefault' => 'useredit',
                'style' => 'user',
                'icon' => 'fa-user',
                'iconsmall' => 'fa-gear',
            ),
            'assignmanagers' => array(
                'category' => 'CompanyAdmin',
                'tab' => 2,
                'name' => get_string('assignmanagers', 'block_iomad_company_admin'),
                'url' => 'company_managers_form.php',
                'cap' => 'block/iomad_company_admin:company_manager',
                'icondefault' => 'assigndepartmentusers',
                'style' => 'department',
                'icon' => 'fa-group',
                'iconsmall' => 'fa-chevron-circle-right'
            ),
            'assignusertocompany' => array(
                'category' => 'UserAdmin',
                'tab' => 2,
                'name' => get_string('assigntocompany', 'block_iomad_company_admin'),
                'url' => 'company_users_form.php',
                'cap' => 'block/iomad_company_admin:company_user',
                'icondefault' => '',
                'style' => 'user',
                'icon' => 'fa-building',
                'iconsmall' => 'fa-chevron-circle-left',
            ),
            'uploadfromfile' => array(
                'category' => 'UserAdmin',
                'tab' => 2,
                'name' => get_string('user_upload_title', 'block_iomad_company_admin'),
                'url' => 'uploaduser.php',
                'cap' => 'block/iomad_company_admin:user_upload',
                'icondefault' => 'up',
                'style' => 'user',
                'icon' => 'fa-file',
                'iconsmall' => 'fa-upload',

            ),
            'downloadusers' => array(
                'category' => 'UserAdmin',
                'tab' => 2,
                'name' => get_string('users_download', 'block_iomad_company_admin'),
                'url' => 'user_bulk_download.php',
                'cap' => 'block/iomad_company_admin:user_upload',
                'icondefault' => 'down',
                'style' => 'user',
                'icon' => 'fa-group',
                'iconsmall' => 'fa-download',
            ),
            'createcourse' => array(
                'category' => 'CourseAdmin',
                'tab' => 3,
                'name' => get_string('createcourse', 'block_iomad_company_admin'),
                'url' => 'company_course_create_form.php',
                'cap' => 'block/iomad_company_admin:createcourse',
                'icondefault' => 'createcourse',
                'style' => 'course',
                'icon' => 'fa-file-text',
                'iconsmall' => 'fa-plus-square',
            ),
            'enroluser' => array(
                'category' => 'UserAdmin',
                'tab' => 3,
                'name' => get_string('enroluser', 'block_iomad_company_admin'),
                'url' => 'company_course_users_form.php',
                'cap' => 'block/iomad_company_admin:company_course_users',
                'icondefault' => 'userenrolements',
                'style' => 'course',
                'icon' => 'fa-file-text',
                'iconsmall' => 'fa-user',
            ),
            'managecourses' => array(
                'category' => 'CourseAdmin',
                'tab' => 3,
                'name' => get_string('iomad_courses_title', 'block_iomad_company_admin'),
                'url' => 'iomad_courses_form.php',
                'cap' => 'block/iomad_company_admin:viewcourses',
                'icondefault' => 'managecoursesettings',
                'style' => 'course',
                'icon' => 'fa-file-text',
                'iconsmall' => 'fa-gear',
            ),
            'assigntocompany' => array(
                'category' => 'CourseAdmin',
                'tab' => 3,
                'name' => get_string('assigntocompany', 'block_iomad_company_admin'),
                'url' => 'company_courses_form.php',
                'cap' => 'block/iomad_company_admin:company_course',
                'icondefault' => 'assigntocompany',
                'style' => 'course',
                'icon' => 'fa-building',
                'iconsmall' => 'fa-chevron-circle-left'
            ),
            'managegroups' => array(
                'category' => 'CourseAdmin',
                'tab' => 3,
                'name' => get_string('managegroups', 'block_iomad_company_admin'),
                'url' => 'company_groups_create_form.php',
                'cap' => 'block/iomad_company_admin:edit_groups',
                'icondefault' => 'groupsedit',
                'style' => 'group',
                'icon' => 'fa-group',
                'iconsmall' => 'fa-gear',
            ),
            'assigngroups' => array(
                'category' => 'CourseAdmin',
                'tab' => 3,
                'name' => get_string('assigncoursegroups', 'block_iomad_company_admin'),
                'url' => 'company_groups_users_form.php',
                'cap' => 'block/iomad_company_admin:assign_groups',
                'icondefault' => 'groupsassign',
                'style' => 'group',
                'icon' => 'fa-group',
                'iconsmall' => 'fa-plus-square',
            ),
            'classrooms' => array(
                'category' => 'CourseAdmin',
                'tab' => 3,
                'name' => get_string('classrooms', 'block_iomad_company_admin'),
                'url' => 'classroom_list.php',
                'cap' => 'block/iomad_company_admin:classrooms',
                'icondefault' => 'teachinglocations',
                'style' => 'company',
                'icon' => 'fa-map-marker',
                'iconsmall' => 'fa-gear',
            ),
            'manageiomadlicenses' => array(
                'category' => 'LicenseAdmin',
                'tab' => 4,
                'name' => get_string('managelicenses', 'block_iomad_company_admin'),
                'url' => 'company_license_list.php',
                'cap' => 'block/iomad_company_admin:edit_my_licenses',
                'icondefault' => 'licensemanagement',
                'style' => 'license',
                'icon' => 'fa-legal',
                'iconsmall' => 'fa-gear',
            ),
            'licenseusers' => array(
                'category' => 'LicenseAdmin',
                'tab' => 4,
                'name' => get_string('licenseusers', 'block_iomad_company_admin'),
                'url' => 'company_license_users_form.php',
                'cap' => 'block/iomad_company_admin:allocate_licenses',
                'icondefault' => 'userlicenseallocations',
                'style' => 'license',
                'icon' => 'fa-legal',
                'iconsmall' => 'fa-user'
            ),
            'iomadframeworksettings' => array(
                'category' => 'CompetencyAdmin',
                'tab' => 5,
                'name' => get_string('frameworksettings', 'block_iomad_company_admin'),
                'url' => 'iomad_frameworks_form.php',
                'cap' => 'block/iomad_company_admin:manageframeworks',
                'icondefault' => 'managecoursesettings',
                'style' => 'competency',
                'icon' => 'fa-list',
                'iconsmall' => 'fa-cog'
            ),
            'companytemplates' => array(
                'category' => 'CompetencyAdmin',
                'tab' => 5,
                'name' => get_string('companytemplates', 'block_iomad_company_admin'),
                'url' => 'company_competency_templates_form.php',
                'cap' => 'block/iomad_company_admin:company_template',
                'icondefault' => 'assigntocompany',
                'style' => 'competency',
                'icon' => 'fa-cubes',
                'iconsmall' => 'fa-chevron-circle-right'
            ),
            'iomadtemplatesettings' => array(
                'category' => 'CompetencyAdmin',
                'tab' => 5,
                'name' => get_string('templatesettings', 'block_iomad_company_admin'),
                'url' => 'iomad_templates_form.php',
                'cap' => 'block/iomad_company_admin:managetemplates',
                'icondefault' => 'managecoursesettings',
                'style' => 'competency',
                'icon' => 'fa-cubes',
                'iconsmall' => 'fa-cog'
            ),
            'edittemplates' => array(
                'category' => 'CompetencyAdmin',
                'tab' => 5,
                'name' => get_string('templates', 'tool_lp'),
                'url' => '/admin/tool/lp/learningplans.php?pagecontextid=1',
                'cap' => 'block/iomad_company_admin:templateview',
                'icondefault' => 'userenrolements',
                'style' => 'competency',
                'icon' => 'fa-cubes',
                'iconsmall' => 'fa-eye'
            ),
            
        );
    }

}
