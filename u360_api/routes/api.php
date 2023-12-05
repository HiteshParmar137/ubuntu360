<?php

use App\Http\Controllers\Admin\AdminDashboardController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Admin\AuthController as AdminAuthController;
use App\Http\Controllers\Admin\UserGroupController;
use App\Http\Controllers\Admin\AdminUsersController;
use App\Http\Controllers\Admin\UsersController;
use App\Http\Controllers\Admin\UtilityController;
use App\Http\Controllers\Admin\DashboardController;
use App\Http\Controllers\Admin\ProfileController;
use App\Http\Controllers\Admin\SiteSettingsController;
use App\Http\Controllers\Admin\TemplatesManagementController;
use App\Http\Controllers\Admin\CMSManagementController;
use App\Http\Controllers\Admin\FeedbackController;
use App\Http\Controllers\Admin\ProjectController as AdminProjectController;
use App\Http\Controllers\Admin\TransactionsController;
use App\Http\Controllers\Admin\EsgReportsController as AdminEsgReportsController;
use App\Http\Controllers\Front\AuthController as FrontAuthController;
use App\Http\Controllers\Front\EsgReportsController;
use App\Http\Controllers\Front\ProjectController;
use App\Http\Controllers\Front\TransactionController as FrontTransactionController;
use App\Http\Controllers\Front\UserProfileController;
use App\Http\Controllers\Front\UserRegistrationController;
use App\Http\Controllers\Front\UserDashboardController;
use App\Http\Controllers\Front\ProjectCommunityController;
use App\Http\Controllers\GeneralController;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
*/

/**
 * Admin Routes
 */
Route::get('recurring-paymentCron', [FrontTransactionController::class, 'recurringPaymentCron']);
Route::post('/refresh-token', [AdminAuthController::class, 'refreshToken']);
Route::post('get-cms-page', [CMSManagementController::class, 'getCmsPage']);
Route::post('/stripe/webhook', [FrontTransactionController::class, 'handleWebhook']);
Route::get('/get-site-settings', [SiteSettingsController::class, 'getSiteSettings']);
Route::group(['prefix' => 'admin', 'middleware' => ['assign.guard:admins']], function () {
    Route::post('/login', [AdminAuthController::class, 'adminLogin']);

    Route::post(
        '/forgot-password',
        [
            AdminAuthController::class,
            'adminSendResetLinkResponse'
        ]
    )->name('admin_password.sent');
    Route::post(
        '/reset-password',
        [
            AdminAuthController::class,
            'adminSendResetResponse'
        ]
    )->name('admin_password.reset');
    Route::post('/admin_verified_email', [AdminAuthController::class, 'AdminVerifiedEmail']);

    Route::group(
        ['prefix' => '/', 'middleware' => ['jwt.verify', 'assign.guard:admins']],
        function () {
            Route::get('dashboard', [AdminDashboardController::class, 'index']);
            Route::get('/get_menus', [UtilityController::class, 'getMenus']);
            Route::get('/get_system_modules', [UtilityController::class, 'getSystemModules']);
            Route::get('/get_sidebar', [UtilityController::class, 'getSidebar']);
            Route::get('/check_permissions', [UtilityController::class, 'checkPermissions']);
            // Get Stats
            Route::get('/getStats', [DashboardController::class, 'index']);

            //admin dashboard APIs
            // Route::get('/user/dashboard', [UserDashboardController::class, 'index']);
            Route::get('/dashboard-info', [AdminDashboardController::class, 'userProjectDashboard']);
            Route::get('/dashboard-cards', [AdminDashboardController::class, 'countsForDashboardCards']);
            Route::post('/sponser-chart-details', [AdminDashboardController::class, 'sponserChart']);
            Route::post('/user-details-chart', [AdminDashboardController::class, 'userChart']);
            Route::post('/country-map', [AdminDashboardController::class, 'dashboardMap']);
        }
    );

    Route::group(
        ['prefix' => '/', 'middleware' => ['jwt.verify', 'acl_checker', 'assign.guard:admins']],
        function () {
            //Profile management
            Route::get('profile', [ProfileController::class, 'adminProfile']);
            Route::post('profile-change', [ProfileController::class, 'profileChange']);
            //Template management
            Route::group(
                ['prefix' => '/templates-management'],
                function () {
                    Route::post('create', [TemplatesManagementController::class, 'create']);
                    Route::post('update', [TemplatesManagementController::class, 'update']);
                    Route::get('details/{id}', [TemplatesManagementController::class, 'edit']);
                    Route::delete('delete/{id}', [TemplatesManagementController::class, 'delete']);
                    Route::post('list', [TemplatesManagementController::class, 'index']);
                }
            );
            //CMS management
            Route::group(
                ['prefix' => '/cms-management'],
                function () {
                    Route::post('create', [CMSManagementController::class, 'create']);
                    Route::post('update', [CMSManagementController::class, 'update']);
                    Route::get('details/{id}', [CMSManagementController::class, 'edit']);
                    Route::delete('delete/{id}', [CMSManagementController::class, 'delete']);
                    Route::post('list', [CMSManagementController::class, 'index']);
                }
            );
            //Site Settings
            Route::post('site-settings/store', [SiteSettingsController::class, 'store']);
            Route::get('site-settings/get-site-settings', [SiteSettingsController::class, 'getSiteSettings']);

            //users management
            Route::post('users/list', [UsersController::class, 'index']);
            Route::get('users/get-user/{id}', [UsersController::class, 'getUserDetails']);
            Route::post('users/user-report-list/', [UsersController::class, 'reportUserList']);
            Route::post('users/export-user-report-list/', [UsersController::class, 'exportUserReportList']);

            //Feedback
            Route::post('feedback/list', [FeedbackController::class, 'index']);
            Route::delete('feedback/delete/{id}', [FeedbackController::class, 'delete']);

            //userGroup API
            Route::post('/user_groups/list', [UserGroupController::class, 'index']);
            Route::post('/user_groups/create', [UserGroupController::class, 'store']);
            Route::get('/user_groups/get-all-modules', [UserGroupController::class, 'userGroupList']);
            Route::get('/user_groups/{id}', [UserGroupController::class, 'getUserGroup']);
            Route::post('/user_groups/edit', [UserGroupController::class, 'update']);
            Route::post('/user_groups/delete/{id}', [UserGroupController::class, 'destroy']);

            //Users API
            Route::post('/admin_users/list', [AdminUsersController::class, 'index']);
            Route::post('/admin_users/create', [AdminUsersController::class, 'create']);
            Route::get('/admin_users/{id}', [AdminUsersController::class, 'getUsers']);
            Route::post('/admin_users/update', [AdminUsersController::class, 'update']);
            Route::delete('/admin_users/delete/{id}', [AdminUsersController::class, 'destroy']);
            Route::get('/get-user-groups', [AdminUsersController::class, 'getUserGroups']);
            Route::get('/logout', [AdminAuthController::class, 'adminLogout']);
            Route::post('/password-change', [AdminAuthController::class, 'adminPasswordChange']);

            //ESGReport
            Route::post('esg-report/list', [EsgReportsController::class, 'getEsgReport']);
            Route::delete('esg-report/delete/{id}', [EsgReportsController::class, 'delete']);
            Route::post('esg-report/export', [AdminEsgReportsController::class, 'exportEsgReport']);

            //Subscription
            Route::post('subscription/list', [EsgReportsController::class, 'getSubscription']);
            Route::delete('subscription/delete/{id}', [EsgReportsController::class, 'deleteSubscriptionEmail']);
            Route::post('subscriptions/export', [AdminEsgReportsController::class, 'exportSubscriptionReport']);

            //project
            Route::group(['prefix' => 'project/'], function () {
                Route::post('list', [AdminProjectController::class, 'getProjectsList']);
                Route::delete('delete/{id}', [AdminProjectController::class, 'deleteProject']);
                Route::get('get-users', [AdminProjectController::class, 'getProjectUsers']);
                Route::get('details/{id}', [AdminProjectController::class, 'getProjectDetails']);
                Route::post('get-docs', [AdminProjectController::class, 'getProjectDocuments']);
                Route::post('save/{stepType}/{stepNo}', [AdminProjectController::class, 'save']);
                Route::post('save-docs', [AdminProjectController::class, 'saveProjectDocument']);
                Route::delete('delete-docs/{id}', [AdminProjectController::class, 'deleteProjectDocument']);
                Route::post('follow', [AdminProjectController::class, 'getFollowedProjectsList']);
                Route::post('donation/list', [AdminProjectController::class, 'getProjectDonationList']);
                Route::post('volunteer/list', [AdminProjectController::class, 'getProjectVolunteerList']);
                Route::post('followers/list', [AdminProjectController::class, 'getProjectFollowersList']);
                Route::post('approve', [AdminProjectController::class, 'approveProject']);
                Route::post('reject', [AdminProjectController::class, 'rejectProject']);
                Route::post('report-list', [AdminProjectController::class, 'reportProjectList']);
                Route::post('export-report-list', [AdminProjectController::class, 'exportProjectList']);
                Route::post('reach-goal', [AdminProjectController::class, 'checkReachGoal']);
            });

            //Transactions
            Route::post('transactions/list', [TransactionsController::class, 'index']);
            Route::get('transactions/get-users', [TransactionsController::class, 'getUsers']);
        }
    );
});

/* Admin User Completed */


//General controller
Route::get('project/get-categories', [GeneralController::class, 'getCategoriesList']);
Route::get('project/get-sdgs', [GeneralController::class, 'getSdgsList']);
Route::get('project/get-user-interest', [GeneralController::class, 'getUserInterestList']);
Route::get('project/get-countries', [GeneralController::class, 'getCountriesList']);
Route::get('project/get-industries', [GeneralController::class, 'getIndustryList']);


//Frontend Users
Route::post('/register-user', [UserRegistrationController::class, 'registerUser']);
Route::post('/verify-email', [UserRegistrationController::class, 'verifyEmail'])->name('user.verify');
Route::post('/login', [FrontAuthController::class, 'userLogin']);

Route::post('/save-subsciption-mails', [EsgReportsController::class, 'saveEmail']);
Route::post('/save-esg-report-mails', [EsgReportsController::class, 'saveESGEmail']);

Route::post('/forgot-password', [FrontAuthController::class, 'userSendResetLinkResponse'])->name('user_password.sent');
Route::post('/reset-password', [FrontAuthController::class, 'userSendResetResponse'])->name('user_password.reset');
Route::post('project/list', [ProjectController::class, 'getProjectsList']);
Route::get('project/details/{id}', [ProjectController::class, 'projectDetails']);
Route::post('project/reviews', [ProjectController::class, 'getAllReviews']);
Route::post('donation/list', [FrontTransactionController::class, 'getDonationDetails']);
Route::post('project/community/list', [ProjectCommunityController::class, 'getCommunityList']);
Route::post('community', [ProjectCommunityController::class, 'getAllCommunityList']);
Route::post('project/community', [ProjectCommunityController::class, 'getUserProjectCommunityList']);
Route::group(['prefix' => '', 'middleware' => [
    'assign.guard:users', 'jwt.verify', 'is_verify_email'
]], function () {
    Route::post('/logout', [FrontAuthController::class, 'logOut']);
    Route::post('/password-change', [FrontAuthController::class, 'userPasswordChange']);

    Route::get('/user/dashboard', [UserDashboardController::class, 'index']);
    Route::get('/user/dashboard-info', [UserDashboardController::class, 'userProjectDashboard']);
    Route::get('/user/dashboard-cards', [UserDashboardController::class, 'countsForDashboardCards']);
    Route::post('/user/sponser-chart-details', [UserDashboardController::class, 'sponserChart']);
    Route::post('/user/user-details-chart', [UserDashboardController::class, 'userChart']);
    Route::post('user/country-map', [UserDashboardController::class, 'dashboardMap']);
    Route::post('user/donation/list', [FrontTransactionController::class, 'getDonationDetails']);
    //User Project related
    Route::group(['prefix' => 'user/project/'], function () {
        Route::post('follow/create', [ProjectController::class, 'createFollow']);
        Route::post('save/{stepType}/{stepNo}', [ProjectController::class, 'save']);
        Route::get('details/{id}', [ProjectController::class, 'userProjectDetails']);
        Route::post('list', [ProjectController::class, 'getUserProjectsList']);
        Route::delete('delete/{id}', [ProjectController::class, 'deleteProject']);
        Route::post('save-docs', [ProjectController::class, 'saveProjectDocument']);
        Route::post('save-review', [ProjectController::class, 'saveReview']);
        Route::delete('delete-docs/{id}', [ProjectController::class, 'deleteProjectDocument']);
        Route::delete('delete-cover-image/{id}', [ProjectController::class, 'deleteProjectCoverImage']);
        Route::post('get-docs', [ProjectController::class, 'getProjectDocuments']);
        Route::post('get-feel-good-data', [ProjectController::class, 'getFeelGoodData']);
        Route::post('all-project-feel-good-data', [ProjectController::class, 'projectFeelGoodData']);
        Route::get('close/{id}', [ProjectController::class, 'closeProject']);
        Route::post('add-volunteer', [ProjectController::class, 'addVolunteer']);
        Route::post('followed-project-list', [ProjectController::class, 'getFollowedProjectsList']);
        Route::post('completed-project-list', [ProjectController::class, 'getCompletedProjectsList']);
        Route::post('add-donation', [FrontTransactionController::class, 'addDonation']);
        Route::post('stop-recurring-donation', [FrontTransactionController::class, 'stopRecurringDonation']);
        Route::post('community/add', [ProjectCommunityController::class, 'addCommunity']);
        Route::post('community/like', [ProjectCommunityController::class, 'addLike']);
        Route::post('community/comment/add', [ProjectCommunityController::class, 'addComment']);
    });
    
    Route::post('transactions/list', [FrontTransactionController::class, 'index']);
    //User
    Route::post('/save-user-details/{stepType}/{stepNo}', [UserRegistrationController::class, 'userDetailsSave']);
    Route::post('/update-user-details', [UserRegistrationController::class, 'userDetailsUpdate']);
    Route::get('get-user-details', [UserRegistrationController::class, 'userDetails']);

    //User profile management
    Route::get('profile', [UserProfileController::class, 'userProfile']);
    Route::post('profile-change', [UserProfileController::class, 'profileChange']);
    Route::post('profile-image-change', [UserProfileController::class, 'uploadProfileImage']);
});

/**
 * Fallback Route : if requested route not found
 */
Route::Fallback(function () {
    return response()->json(['message' => 'Route not found. Please check'], 404);
});

Route::any('{any}', function () {
    return response()->json(['status' => false, 'message' => 'Route not found. Please check'], 404);
})->where('any', '.*');
