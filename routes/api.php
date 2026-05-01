<?php

use App\Modules\Auth\AuthController;
use Illuminate\Support\Facades\Route;

// Auth module - public routes (đăng nhập, quên mật khẩu, đặt lại mật khẩu)
Route::prefix('auth')->middleware('log.activity')->group(function () {
    require base_path('app/Modules/Auth/Routes/auth.php');
});

// Cấu hình công khai - không cần xác thực
Route::get('/settings/public', [\App\Modules\Core\SettingController::class, 'public'])->middleware('log.activity');
Route::get('/document-signers/public', [\App\Modules\Document\DocumentSignerController::class, 'public'])->middleware('log.activity');
Route::get('/document-signers/public-options', [\App\Modules\Document\DocumentSignerController::class, 'publicOptions'])->middleware('log.activity');
Route::get('/document-fields/public', [\App\Modules\Document\DocumentFieldController::class, 'public'])->middleware('log.activity');
Route::get('/document-fields/public-options', [\App\Modules\Document\DocumentFieldController::class, 'publicOptions'])->middleware('log.activity');
Route::get('/document-types/public', [\App\Modules\Document\DocumentTypeController::class, 'public'])->middleware('log.activity');
Route::get('/document-types/public-options', [\App\Modules\Document\DocumentTypeController::class, 'publicOptions'])->middleware('log.activity');
Route::get('/meeting-types/public', [\App\Modules\Meeting\MeetingTypeController::class, 'public'])->middleware('log.activity');
Route::get('/meeting-types/public-options', [\App\Modules\Meeting\MeetingTypeController::class, 'publicOptions'])->middleware('log.activity');
Route::get('/meeting-locations/public', [\App\Modules\Meeting\MeetingLocationController::class, 'public'])->middleware('log.activity');
Route::get('/meeting-locations/public-options', [\App\Modules\Meeting\MeetingLocationController::class, 'publicOptions'])->middleware('log.activity');
Route::get('/meeting-document-types/public', [\App\Modules\Meeting\MeetingDocumentTypeController::class, 'public'])->middleware('log.activity');
Route::get('/meeting-document-types/public-options', [\App\Modules\Meeting\MeetingDocumentTypeController::class, 'publicOptions'])->middleware('log.activity');
Route::get('/meetings/public', [\App\Modules\Meeting\MeetingController::class, 'public'])->middleware('log.activity');
Route::get('/meetings/public/{meeting}', [\App\Modules\Meeting\MeetingController::class, 'publicShow'])->middleware('log.activity');
Route::get('/meeting-documents/public', [\App\Modules\Meeting\MeetingDocumentController::class, 'public'])->middleware('log.activity');
Route::get('/meeting-documents/public/{meetingDocument}', [\App\Modules\Meeting\MeetingDocumentController::class, 'publicShow'])->middleware('log.activity');
Route::get('/issuing-levels/public', [\App\Modules\Document\IssuingLevelController::class, 'public'])->middleware('log.activity');
Route::get('/issuing-levels/public-options', [\App\Modules\Document\IssuingLevelController::class, 'publicOptions'])->middleware('log.activity');
Route::get('/issuing-agencies/public', [\App\Modules\Document\IssuingAgencyController::class, 'public'])->middleware('log.activity');
Route::get('/issuing-agencies/public-options', [\App\Modules\Document\IssuingAgencyController::class, 'publicOptions'])->middleware('log.activity');
Route::get('/post-categories/public', [\App\Modules\Post\PostCategoryController::class, 'public'])->middleware('log.activity');
Route::get('/post-categories/public-options', [\App\Modules\Post\PostCategoryController::class, 'publicOptions'])->middleware('log.activity');
Route::get('/organizations/public', [\App\Modules\Core\OrganizationController::class, 'public'])->middleware('log.activity');
Route::get('/organizations/public-options', [\App\Modules\Core\OrganizationController::class, 'publicOptions'])->middleware('log.activity');

// Route yêu cầu đăng nhập (Bearer token) và đặt ngữ cảnh team cho Spatie Permission
Route::middleware(['auth:sanctum', 'set.permissions.team', 'log.activity'])->group(function () {
    Route::get('/user', [AuthController::class, 'me']);

    Route::prefix('users')->group(function () {
        require base_path('app/Modules/Core/Routes/user.php');
    });
    Route::prefix('posts')->middleware('ensure.route.org')->group(function () {
        require base_path('app/Modules/Post/Routes/post.php');
    });
    Route::prefix('post-categories')->group(function () {
        require base_path('app/Modules/Post/Routes/post_category.php');
    });
    Route::prefix('permissions')->group(function () {
        require base_path('app/Modules/Core/Routes/permission.php');
    });
    Route::prefix('roles')->group(function () {
        require base_path('app/Modules/Core/Routes/role.php');
    });
    Route::prefix('organizations')->group(function () {
        require base_path('app/Modules/Core/Routes/organization.php');
    });
    Route::prefix('log-activities')->group(function () {
        require base_path('app/Modules/Core/Routes/log_activity.php');
    });
    Route::prefix('documents')->middleware('ensure.route.org')->group(function () {
        require base_path('app/Modules/Document/Routes/document.php');
    });
    Route::prefix('document-types')->group(function () {
        require base_path('app/Modules/Document/Routes/document_type.php');
    });
    Route::prefix('issuing-agencies')->group(function () {
        require base_path('app/Modules/Document/Routes/issuing_agency.php');
    });
    Route::prefix('issuing-levels')->group(function () {
        require base_path('app/Modules/Document/Routes/issuing_level.php');
    });
    Route::prefix('document-signers')->group(function () {
        require base_path('app/Modules/Document/Routes/document_signer.php');
    });
    Route::prefix('document-fields')->group(function () {
        require base_path('app/Modules/Document/Routes/document_field.php');
    });
    Route::prefix('settings')->group(function () {
        require base_path('app/Modules/Core/Routes/setting.php');
    });
    Route::prefix('meetings')->middleware('ensure.route.org')->group(function () {
        require base_path('app/Modules/Meeting/Routes/meeting.php');
    });
    Route::prefix('meeting-types')->group(function () {
        require base_path('app/Modules/Meeting/Routes/meeting_type.php');
    });
    Route::prefix('meeting-locations')->group(function () {
        require base_path('app/Modules/Meeting/Routes/meeting_location.php');
    });
    Route::prefix('meeting-document-types')->group(function () {
        require base_path('app/Modules/Meeting/Routes/meeting_document_type.php');
    });
    Route::prefix('meeting-attendee-groups')->middleware('ensure.route.org')->group(function () {
        require base_path('app/Modules/Meeting/Routes/meeting_attendee_group.php');
    });
    Route::prefix('meeting-attendees')->middleware('ensure.route.org')->group(function () {
        require base_path('app/Modules/Meeting/Routes/meeting_attendee.php');
    });
    Route::prefix('meeting-agendas')->middleware('ensure.route.org')->group(function () {
        require base_path('app/Modules/Meeting/Routes/meeting_agenda.php');
    });
    Route::prefix('meeting-documents')->middleware('ensure.route.org')->group(function () {
        require base_path('app/Modules/Meeting/Routes/meeting_document.php');
    });
    Route::prefix('meeting-participants')->middleware('ensure.route.org')->group(function () {
        require base_path('app/Modules/Meeting/Routes/meeting_participant.php');
    });
    Route::prefix('meeting-attendances')->middleware('ensure.route.org')->group(function () {
        require base_path('app/Modules/Meeting/Routes/meeting_attendance.php');
    });
    Route::prefix('meeting-vote-topics')->middleware('ensure.route.org')->group(function () {
        require base_path('app/Modules/Meeting/Routes/meeting_vote_topic.php');
    });
    Route::prefix('meeting-vote-responses')->middleware('ensure.route.org')->group(function () {
        require base_path('app/Modules/Meeting/Routes/meeting_vote_response.php');
    });
    Route::prefix('meeting-conclusions')->middleware('ensure.route.org')->group(function () {
        require base_path('app/Modules/Meeting/Routes/meeting_conclusion.php');
    });
    Route::prefix('meeting-discussion-registrations')->middleware('ensure.route.org')->group(function () {
        require base_path('app/Modules/Meeting/Routes/meeting_discussion_registration.php');
    });
    Route::prefix('meeting-personal-notes')->middleware('ensure.route.org')->group(function () {
        require base_path('app/Modules/Meeting/Routes/meeting_personal_note.php');
    });
    Route::prefix('meeting-personal-note-attachments')->middleware('ensure.route.org')->group(function () {
        require base_path('app/Modules/Meeting/Routes/meeting_personal_note_attachment.php');
    });
});
