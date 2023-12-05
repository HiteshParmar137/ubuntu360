<?php

namespace Database\Seeders;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class TemplatesManagementTableSeeder extends Seeder
{

    /**
     * Auto generated seed file
     *
     * @return void
     */
    public function run()
    {
        

        DB::table('templates_management')->delete();
        
        DB::table('templates_management')->insert(array (
            0 => 
            array (
                'id' => 17,
                'name' => 'Verify Email',
                'slug' => 'verify_email',
                'template_type' => 'email',
                'subject' => 'Verify email',
                'template' => '<tr>
<td style="padding: 30px;">
<table width="100%" cellspacing="0" cellpadding="0" bgcolor="#fff">
<tr>
<td style="font-size: 18px;font-weight: bold;color: #3D4852;padding-bottom: 12px;">Hello ##NAME##, </td>
</tr>
<tr>
<td style="font-size: 16px;color: #718096;">Click the button below to verify your email address.</td>
</tr>
<tr>
<td height="30">&nbsp;</td>
</tr>
<tr>
<td align="center" ><a href="##URL##" style="background-color: #2d3748;color: #fff;padding: 8px 16px;display: inline-block;text-decoration: none;font-size: 15px;">Verify Email Address</a></td>
</tr>
<tr>
<td height="50">&nbsp;</td>
</tr>
<tr>
<td style="font-size: 16px;color: #718096;padding-bottom: 5px;">Regards,</td>
</tr>
<tr>
<td style="font-size: 16px;color: #718096;">Ubuntu360</td>
</tr>
<tr>
<td style="border-bottom: 1px solid #E8E5EF;" height="1">&nbsp;</td>
</tr>
</table>
</td>
</tr>',
                'status' => '1',
                'created_by' => NULL,
                'updated_by' => NULL,
                'created_at' => '2022-04-18 02:26:30',
                'updated_at' => '2022-05-24 05:16:12',
            ),
            1 => 
            array (
                'id' => 22,
                'name' => 'Welcome Email',
                'slug' => 'welcome_email',
                'template_type' => 'email',
                'subject' => NULL,
                'template' => '<tr>
<td style="padding: 30px;">
<table width="100%" cellspacing="0" cellpadding="0" bgcolor="#fff">
<tr>
<td style="font-size: 18px;font-weight: bold;color: #3D4852;padding-bottom: 12px;">Hello ##NAME##, </td>
</tr>
<tr>
<td style="font-size: 16px;color: #718096;">Click the button below to verify your email address.</td>
</tr>
<tr>
<td height="30">&nbsp;</td>
</tr>
<tr>
<td align="center" ><a href="##URL##" style="background-color: #2d3748;color: #fff;padding: 8px 16px;display: inline-block;text-decoration: none;font-size: 15px;">Verify Email Address</a></td>
</tr>
<tr>
<td height="50">&nbsp;</td>
</tr>
<tr>
<td style="font-size: 16px;color: #718096;padding-bottom: 5px;">Regards,</td>
</tr>
<tr>
<td style="font-size: 16px;color: #718096;">Ubuntu360</td>
</tr>
<tr>
<td style="border-bottom: 1px solid #E8E5EF;" height="1">&nbsp;</td>
</tr>
</table>
</td>
</tr>',
                'status' => '0',
                'created_by' => NULL,
                'updated_by' => NULL,
                'created_at' => '2022-04-22 01:47:41',
                'updated_at' => '2022-05-24 05:15:03',
            ),
            2 => 
            array (
                'id' => 32,
                'name' => 'New admin user create',
                'slug' => 'new_admin_user',
                'template_type' => 'email',
                'subject' => 'Admin user created',
                'template' => '<tr>
<td style="padding: 30px;">
<table width="100%" cellspacing="0" cellpadding="0" bgcolor="#fff">
<tr>
<td style="font-size: 18px;font-weight: bold;color: #3D4852;padding-bottom: 12px;">Hello ##NAME##,</td>
</tr>
<tr>
<td style="font-size: 16px;color: #718096;">Please get your credentials for login as below:</td>
</tr>
<tr>
<td style="font-size: 16px;color: #718096;">Email: ##EMAIL##</td>
</tr>
<tr>
<td style="font-size: 16px;color: #718096;">Password: ##PASSWORD##</td>
</tr>
</table>
</td>
</tr>',
                'status' => '1',
                'created_by' => NULL,
                'updated_by' => NULL,
                'created_at' => '2022-11-15 12:37:39',
                'updated_at' => '2022-11-15 12:37:39',
            ),
            3 => 
            array (
                'id' => 38,
                'name' => 'Reset password',
                'slug' => 'reset_password',
                'template_type' => 'email',
                'subject' => 'Reset password',
                'template' => '<tr>
<td style="padding: 30px;">
<table cellpadding="0" cellspacing="0" style="width:100%">
<tbody>
<tr>
<td>Hello ##NAME##,</td>
</tr>
<tr>
<td>Click the button below to reset your password.</td>
</tr>
<tr>
<td>&nbsp;</td>
</tr>
<tr>
<td><a href="##URL##">Reset Password</a></td>
</tr>
<tr>
<td>&nbsp;</td>
</tr>
<tr>
<td>Regards,</td>
</tr>
<tr>
<td>Ubuntu360</td>
</tr>
<tr>
<td style="border-bottom:1px solid #e8e5ef">&nbsp;</td>
</tr>
</tbody>
</table>
</td>
</tr>',
                'status' => '1',
                'created_by' => 1,
                'updated_by' => 1,
                'created_at' => '2022-11-17 00:19:35',
                'updated_at' => '2023-01-25 07:12:01',
            ),
            4 => 
            array (
                'id' => 39,
                'name' => 'Project closed',
                'slug' => 'project_closed',
                'template_type' => 'email',
                'subject' => 'Project closed',
                'template' => '<tr>
<td style="padding: 30px;">
<table width="100%" cellspacing="0" cellpadding="0" bgcolor="#fff">
<tr>
<td style="font-size: 18px;font-weight: bold;color: #3D4852;padding-bottom: 12px;">Hello ##NAME##,</td>
</tr>
<tr>
<td style="font-size: 16px;color: #718096;">Your project successfully closed.</td>
</tr>
</table>
</td>
</tr>',
                'status' => '1',
                'created_by' => 1,
                'updated_by' => 1,
                'created_at' => '2022-12-01 05:17:01',
                'updated_at' => '2023-01-25 07:04:14',
            ),
            5 => 
            array (
                'id' => 46,
                'name' => 'Signup Verify Email',
                'slug' => 'signup_email_verify',
                'template_type' => 'email',
                'subject' => 'Signup Verify email',
                'template' => '<tr>
<td style="padding: 30px;">
<table width="100%" cellspacing="0" cellpadding="0" bgcolor="#fff">
<tr>
<td style="font-size: 18px;font-weight: bold;color: #3D4852;padding-bottom: 12px;">Hello ##NAME##, </td>
</tr>
<tr>
<td style="font-size: 16px;color: #718096;">Click the button below to verify your email address.</td>
</tr>
<tr>
<td height="30">&nbsp;</td>
</tr>
<tr>
<td align="center" ><a href="##URL##" style="background-color: #2d3748;color: #fff;padding: 8px 16px;display: inline-block;text-decoration: none;font-size: 15px;">Verify Email Address</a></td>
</tr>
<tr>
<td height="50">&nbsp;</td>
</tr>
<tr>
<td style="font-size: 16px;color: #718096;padding-bottom: 5px;">Regards,</td>
</tr>
<tr>
<td style="font-size: 16px;color: #718096;">Ubuntu360</td>
</tr>
<tr>
<td style="border-bottom: 1px solid #E8E5EF;" height="1">&nbsp;</td>
</tr>
</table>
</td>
</tr>',
                'status' => '1',
                'created_by' => NULL,
                'updated_by' => NULL,
                'created_at' => '2022-04-18 02:26:30',
                'updated_at' => '2022-05-24 05:16:12',
            ),
            6 => 
            array (
                'id' => 47,
                'name' => 'New Project Create',
                'slug' => 'new_project_create',
                'template_type' => 'email',
                'subject' => 'New Project Arrived',
                'template' => '<tr>
<td style="padding: 30px;">
<table width="100%" cellspacing="0" cellpadding="0" bgcolor="#fff">
<tr>
<td style="font-size: 18px;font-weight: bold;color: #3D4852;padding-bottom: 12px;">Hello Admin, </td>
</tr>
<tr>
<td style="font-size: 16px;color: #718096;">New projects \'##TITLE##\' has been created by  ##NAME##.</td>
</tr>
<tr>
<td height="50">&nbsp;</td>
</tr>
<tr>
<td style="font-size: 16px;color: #718096;padding-bottom: 5px;">Regards,</td>
</tr>
<tr>
<td style="font-size: 16px;color: #718096;">Ubuntu360</td>
</tr>
<tr>
<td style="border-bottom: 1px solid #E8E5EF;" height="1">&nbsp;</td>
</tr>
</table>
</td>
</tr>',
                'status' => '1',
                'created_by' => NULL,
                'updated_by' => NULL,
                'created_at' => '2022-04-18 07:56:30',
                'updated_at' => '2022-05-24 10:46:12',
            ),
            7 => 
            array (
                'id' => 48,
                'name' => 'Project Approved',
                'slug' => 'project_approved',
                'template_type' => 'email',
                'subject' => 'Project Approved',
                'template' => '<tr>
<td style="padding: 30px;">
<table width="100%" cellspacing="0" cellpadding="0" bgcolor="#fff">
<tr>
<td style="font-size: 18px;font-weight: bold;color: #3D4852;padding-bottom: 12px;">Hello ##NAME##, </td>
</tr>
<tr>
<td style="font-size: 16px;color: #718096;">Your project \'##PROJECT_TITLE##\' has been approved by admin.</td>
</tr>
<tr>
<td height="30">&nbsp;</td>
</tr>
<tr>
<td style="font-size: 16px;color: #718096;padding-bottom: 5px;">Regards,</td>
</tr>
<tr>
<td style="font-size: 16px;color: #718096;">Ubuntu360</td>
</tr>
<tr>
<td style="border-bottom: 1px solid #E8E5EF;" height="1">&nbsp;</td>
</tr>
</table>
</td>
</tr>',
                'status' => '1',
                'created_by' => NULL,
                'updated_by' => NULL,
                'created_at' => '2022-04-18 07:56:30',
                'updated_at' => '2022-05-24 10:46:12',
            ),
            8 => 
            array (
                'id' => 49,
                'name' => 'Project Rejected',
                'slug' => 'project_rejected',
                'template_type' => 'email',
                'subject' => 'Project Rejected',
                'template' => '<tr>
<td style="padding: 30px;">
<table width="100%" cellspacing="0" cellpadding="0" bgcolor="#fff">
<tr>
<td style="font-size: 18px;font-weight: bold;color: #3D4852;padding-bottom: 12px;">Hello ##NAME##, </td>
</tr>
<tr>
<td style="font-size: 16px;color: #718096;">Your project \'##PROJECT_TITLE##\' has been rejected by admin. Please check below comment.</td>
</tr>
<tr>
<td style="font-size: 16px;color: #718096;">Comment : ##COMMENT##</td>
</tr>
<tr>
<td height="30">&nbsp;</td>
</tr>
<tr>
<td style="font-size: 16px;color: #718096;padding-bottom: 5px;">Regards,</td>
</tr>
<tr>
<td style="font-size: 16px;color: #718096;">Ubuntu360</td>
</tr>
<tr>
<td style="border-bottom: 1px solid #E8E5EF;" height="1">&nbsp;</td>
</tr>
</table>
</td>
</tr>',
                'status' => '1',
                'created_by' => NULL,
                'updated_by' => 2,
                'created_at' => '2022-04-18 07:56:30',
                'updated_at' => '2023-05-16 11:09:05',
            ),
            9 => 
            array (
                'id' => 50,
                'name' => 'Project Updated',
                'slug' => 'project_updated',
                'template_type' => 'email',
                'subject' => 'Project Updated',
                'template' => '<tr>
<td style="padding: 30px;">
<table width="100%" cellspacing="0" cellpadding="0" bgcolor="#fff">
<tr>
<td style="font-size: 18px;font-weight: bold;color: #3D4852;padding-bottom: 12px;">Hello Admin, </td>
</tr>
<tr>
<td style="font-size: 16px;color: #718096;">Projects \'##TITLE##\' has been updated by  ##NAME##. Please check below roject.</td>
</tr>
<tr>
<td style="font-size: 16px;color: #718096;padding-bottom: 5px;">Regards,</td>
</tr>
<tr>
<td style="font-size: 16px;color: #718096;">Ubuntu360</td>
</tr>
<tr>
<td style="border-bottom: 1px solid #E8E5EF;" height="1">&nbsp;</td>
</tr>
</table>
</td>
</tr>',
                'status' => '1',
                'created_by' => NULL,
                'updated_by' => NULL,
                'created_at' => '2022-04-18 07:56:30',
                'updated_at' => '2022-05-24 10:46:12',
            ),
            10 => 
            array (
                'id' => 51,
                'name' => 'Project Report',
                'slug' => 'project_report',
                'template_type' => 'email',
                'subject' => 'Project Report',
                'template' => '<table cellpadding="0" cellspacing="0" style="width:100%">
<tbody>
<tr>
<td>Hello Admin,</td>
</tr>
<tr>
<td>Please checked attached excel file for Project Report.</td>
</tr>
<tr>
<td>&nbsp;</td>
</tr>
<tr>
<td>Regards,</td>
</tr>
<tr>
<td>Ubuntu360</td>
</tr>
<tr>
<td style="border-bottom:1px solid #e8e5ef">&nbsp;</td>
</tr>
</tbody>
</table>',
                'status' => '1',
                'created_by' => NULL,
                'updated_by' => 1,
                'created_at' => '2023-05-22 02:26:30',
                'updated_at' => '2023-05-22 04:44:31',
            ),
            11 => 
            array (
                'id' => 52,
                'name' => 'User Report',
                'slug' => 'user_report',
                'template_type' => 'email',
                'subject' => 'User Report',
                'template' => '<table cellpadding="0" cellspacing="0" style="width:100%">
<tbody>
<tr>
<td>Hello Admin,</td>
</tr>
<tr>
<td>Please checked attached excel file for User Report.</td>
</tr>
<tr>
<td>&nbsp;</td>
</tr>
<tr>
<td>Regards,</td>
</tr>
<tr>
<td>Ubuntu360</td>
</tr>
<tr>
<td style="border-bottom:1px solid #e8e5ef">&nbsp;</td>
</tr>
</tbody>
</table>',
                'status' => '1',
                'created_by' => NULL,
                'updated_by' => 1,
                'created_at' => '2023-05-22 02:26:30',
                'updated_at' => '2023-05-22 04:44:31',
            ),
            12 => 
            array (
                'id' => 53,
                'name' => 'ESG Report',
                'slug' => 'esg_report',
                'template_type' => 'email',
                'subject' => 'ESG Report',
                'template' => '<table cellpadding="0" cellspacing="0" style="width:100%">
<tbody>
<tr>
<td>Hello Admin,</td>
</tr>
<tr>
<td>Please checked attached excel file for ESG Report.</td>
</tr>
<tr>
<td>&nbsp;</td>
</tr>
<tr>
<td>Regards,</td>
</tr>
<tr>
<td>Ubuntu360</td>
</tr>
<tr>
<td style="border-bottom:1px solid #e8e5ef">&nbsp;</td>
</tr>
</tbody>
</table>',
                'status' => '1',
                'created_by' => NULL,
                'updated_by' => 1,
                'created_at' => '2023-05-22 02:26:30',
                'updated_at' => '2023-05-22 04:44:31',
            ),
            13 => 
            array (
                'id' => 60,
                'name' => 'Donation Goal Reached',
                'slug' => 'thankyou',
                'template_type' => 'email',
                'subject' => 'Donation Goal Reached',
                'template' => '<table cellpadding="0" cellspacing="0" style="width:100%">
<tbody>
<tr>
<td>Hello ##USERNAME##,</td>
</tr>
<tr>
<td>Thank you for your donation. We have successfully achieved our goal for the project \'##PROJECT_TITLE##\'.</td>
</tr>
<tr>
<td>&nbsp;</td>
</tr>
<tr>
<td>Regards,</td>
</tr>
<tr>
<td>Ubuntu360</td>
</tr>
<tr>
<td style="border-bottom:1px solid #e8e5ef">&nbsp;</td>
</tr>
</tbody>
</table>',
                'status' => '1',
                'created_by' => NULL,
                'updated_by' => 1,
                'created_at' => '2023-05-21 20:56:30',
                'updated_at' => '2023-05-21 23:14:31',
            ),
            14 => 
            array (
                'id' => 61,
                'name' => 'Stop Recurring',
                'slug' => 'reccuring',
                'template_type' => 'email',
                'subject' => 'Stop Recurring',
                'template' => '<table cellpadding="0" cellspacing="0" style="width:100%">
<tbody>
<tr>
<td>Hello ##USERNAME##,</td>
</tr>
<tr>
<td>Thank you for your donation. We have successfully achieved our goal for the project \'##PROJECT_TITLE##\'. If you wish to discontinue your recurring payment, you can do so through the donation module.</td>
</tr>
<tr>
<td>&nbsp;</td>
</tr>
<tr>
<td>Regards,</td>
</tr>
<tr>
<td>Ubuntu360</td>
</tr>
<tr>
<td style="border-bottom:1px solid #e8e5ef">&nbsp;</td>
</tr>
</tbody>
</table>',
                'status' => '1',
                'created_by' => NULL,
                'updated_by' => 1,
                'created_at' => '2023-05-21 20:56:30',
                'updated_at' => '2023-05-21 23:14:31',
            ),
            15 => 
            array (
                'id' => 62,
                'name' => 'Subscription Report',
                'slug' => 'subscription_report',
                'template_type' => 'email',
                'subject' => 'Subscription Report',
                'template' => '<table cellpadding="0" cellspacing="0" style="width:100%">
<tbody>
<tr>
<td>Hello Admin,</td>
</tr>
<tr>
<td>Please checked attached excel file for Subscription Report.</td>
</tr>
<tr>
<td>&nbsp;</td>
</tr>
<tr>
<td>Regards,</td>
</tr>
<tr>
<td>Ubuntu360</td>
</tr>
<tr>
<td style="border-bottom:1px solid #e8e5ef">&nbsp;</td>
</tr>
</tbody>
</table>',
                'status' => '1',
                'created_by' => NULL,
                'updated_by' => 1,
                'created_at' => '2023-05-21 20:56:30',
                'updated_at' => '2023-05-21 23:14:31',
            ),
        ));
        
        
    }
}