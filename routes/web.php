<?php

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\PdfController;

// Route::get('/test_mailer/{id}', function($id) {
//     $request = new \Illuminate\Http\Request(['application_id' => $id]);
//     return app(\App\Http\Controllers\ApplicationController::class)->send_mailer($request);
// });

Route::get('/', function () {
    return view('test_dashboard');
})->name('test_dashboard');

// Route::get('/', function () { //orig code
//     return view('dashboard');
// })->name('dashboard');

Route::get('/dashboard_test', function(){
    return view('dashboard_test');
})->name('dashboard_test');

Route::get('/dashboard', function(){
    return view('dashboard');
})->name('dashboard');

Route::get('/dashboard_new', function(){
    return view('dashboard_new');
})->name('dashboard_new');

Route::get('/master_list', function () {
    return view('master_list');
})->name('master_list');


Route::get('/affected_documents', function () {
    return view('affected_documents');
})->name('affected_documents');


Route::get('/blank', function () {
    return view('blank');
})->name('blank');

Route::get('/user', function () {
    return view('user');
})->name('user');

Route::get('/auto_mailer', function () {
    return view('auto_mailer');
})->name('auto_mailer');

// USER CONTROLLER
Route::post('/sign_in', 'UserController@sign_in')->name('sign_in');
Route::post('/sign_out', 'UserController@sign_out')->name('sign_out');
Route::post('/change_pass', 'UserController@change_pass')->name('change_pass');
Route::post('/change_user_stat', 'UserController@change_user_stat')->name('change_user_stat');
Route::get('/view_users', 'UserController@view_users');
Route::post('/add_user', 'UserController@add_user');
Route::get('/get_user_by_id', 'UserController@get_user_by_id');
Route::get('/get_user_list', 'UserController@get_user_list');
Route::get('/get_user_by_batch', 'UserController@get_user_by_batch');
Route::get('/get_user_by_stat', 'UserController@get_user_by_stat');
Route::post('/edit_user', 'UserController@edit_user');
Route::post('/reset_password', 'UserController@reset_password');
Route::get('/generate_user_qrcode', 'UserController@generate_user_qrcode');
Route::post('/import_user', 'UserController@import_user');

//ACCESS LEVEL CONTROLLER
Route::get('/load_rapidx_users_with_esign', 'AccessLevelController@loadRapidxUsersWithEsign');
Route::get('/aidrc_v2/check-image-exists/{emp_id}', 'AccessLevelController@checkImageExists');
Route::get('/validate_emp_no_signature','AccessLevelController@validate_emp_no_signature');
Route::get('/load_rapidx_user_list','AccessLevelController@load_rapidx_user_list');
Route::get('/load_originator_list','AccessLevelController@load_originator_list');
Route::get('/load_approver_list','AccessLevelController@load_approver_list');
Route::get('/load_affected_approver_list','AccessLevelController@load_affected_approver_list');
Route::get('/load_pic_list','AccessLevelController@load_pic_list');
Route::get('/load_qs_inspector_list','AccessLevelController@load_qs_inspector_list');
Route::get('/load_rapidx_user_list_sectionhead','AccessLevelController@load_rapidx_user_list_sectionhead');
Route::get('/load_rapidx_user_list_qs_inspector','AccessLevelController@load_rapidx_user_list_qs_inspector');
Route::get('/load_accesslevel_table','AccessLevelController@load_accesslevel_table');
Route::post('/submit_add_user','AccessLevelController@submit_add_user');
Route::get('/load_rapidx_department_list','AccessLevelController@load_rapidx_department_list');

//APPLICATION CONTROLLER
Route::get('/load_acdcs_table','ApplicationController@load_acdcs_table');
Route::get('/load_acdcs_active_documents','ApplicationController@load_acdcs_active_documents');
Route::get('/load_acdcs_document_details','ApplicationController@load_acdcs_document_details');
Route::get('/load_array_affected_documents','ApplicationController@load_array_affected_documents');
Route::post('/submit_add_application','ApplicationController@submit_add_application');
Route::get('/submit_application_affected_documents','ApplicationController@submit_application_affected_documents');

// Route::get('/download_attached_document_new/{application_id}','NewApplicationController@download_attached_document_new')->name('download_attached_document_new');
// Route::get('/download_attached_document123/{application_id}','ApplicationController@download_attached_document123')->name('download_attached_document123');
Route::get('/download_attached_doc_excel/{application_id}','ApplicationController@download_attached_doc_excel')->name('download_attached_doc_excel');
Route::get('/get_application_attachment','ApplicationController@get_application_attachment');

Route::get('/load_affected_documents_table','ApplicationController@load_affected_documents_table');
Route::post('/submit_section_head_approval','ApplicationController@submit_section_head_approval');
Route::get('/load_qs_validation_checkpoints','ApplicationController@load_qs_validation_checkpoints');
Route::post('/submit_qs_validations','ApplicationController@submit_qs_validations');
Route::get('/submit_qs_validations_checkpoints','ApplicationController@submit_qs_validations_checkpoints');
Route::get('/load_affected_document_details','ApplicationController@load_affected_document_details');

Route::post('/approve_affected_document','ApplicationController@approve_affected_document');
Route::get('/check_application_affected_document_status','ApplicationController@check_application_affected_document_status');
Route::post('/submit_dcc_validation','ApplicationController@submit_dcc_validation');

//ADDITIONAL FUNCTIONS
Route::get('/submit_section_head_affected_documents','ApplicationController@submit_section_head_affected_documents');

Route::post('/submit_minor_revisions','ApplicationController@submit_minor_revisions');

Route::get('/cancel_application','ApplicationController@cancel_application');

Route::get('/submit_application_affected_documents_from_approver','ApplicationController@submit_application_affected_documents_from_approver');

Route::post('/submit_change_qs_inspector','ApplicationController@submit_change_qs_inspector');

//MASTER LIST CONTROLLER
Route::get('/load_acdcs_master_list','MasterListController@load_acdcs_master_list');
Route::get('/load_affected_documents_master_list','MasterListController@load_affected_documents_master_list');
Route::get('/load_acdcs_pending_documents','MasterListController@load_acdcs_pending_documents');

//mailer
Route::get('/send_mailer','ApplicationController@send_mailer');
Route::get('/send_manual_mailer/{aidrc_control_number}','ApplicationController@send_manual_mailer');

//exports
Route::get('/export_applications_report','MasterListController@export_applications_report');
Route::get('/export_affected_documents_report','MasterListController@export_affected_documents_report');


//ADDITIONAL FUNCTIONALITIES FOR SYSTEM TO WORK!
Route::get('/insert_qs_checkpoint', 'ApplicationController@insert_qs_checkpoint');
Route::get('/load_qs_validation_checkpoints_by_id','ApplicationController@load_qs_validation_checkpoints_by_id');
Route::get('/remove_qs_checkpoint', 'ApplicationController@remove_qs_checkpoint');
Route::get('/remove_qs_checkpoint_by_array','ApplicationController@remove_qs_checkpoint_by_array');

Route::post('/submit_global_affected_document','ApplicationController@submit_global_affected_document');

Route::post('/submit_edit_application','ApplicationController@submit_edit_application');

Route::post('/submit_global_affected_document_from_head','ApplicationController@submit_global_affected_document_from_head');

Route::get('/load_acdcs_layout', 'ApplicationController@load_acdcs_layout');

Route::post('/submit_edit_documents_dcc','MasterListController@submit_edit_documents_dcc');

Route::get('/load_previous_dcc_validations','ApplicationController@load_previous_dcc_validations');

Route::get('/check_existing_aidrc_application','ApplicationController@check_existing_aidrc_application');
Route::get('/check_existing_aidrc_application_new','ApplicationController@check_existing_aidrc_application_new');

//From New Application Controller
Route::get('/load_acdcs_documents_table', 'NewApplicationController@load_acdcs_documents_table');

Route::get('/load_acdcs_applications_table_test', 'NewApplicationController@load_acdcs_applications_table_test');

Route::post('/submit_new_application', 'NewApplicationController@submit_new_application');

Route::get('/load_acdcs_applications_table','NewApplicationController@load_acdcs_applications_table');

Route::get('/load_application_details','NewApplicationController@load_application_details');

Route::post('/submit_new_qs_validation','NewApplicationController@submit_new_qs_validation');

Route::post('/submit_new_head_approval','NewApplicationController@submit_new_head_approval');

Route::post('/submit_new_dcc_validation','NewApplicationController@submit_new_dcc_validation');


Route::post('/submit_new_edit_application','NewApplicationController@submit_new_edit_application');

//Views
Route::get('/load_new_dcc_validations_table','NewApplicationController@load_new_dcc_validations_table');

Route::get('/load_new_head_approvals_table','NewApplicationController@load_new_head_approvals_table');

Route::get('/load_new_qs_validations_table','NewApplicationController@load_new_qs_validations_table');

Route::get('/load_new_document_revisions_table','NewApplicationController@load_new_document_revisions_table');

Route::get('/load_originator_affected_documents_table','NewApplicationController@load_originator_affected_documents_table');

Route::get('/load_originator_affected_documents_table','NewApplicationController@load_originator_affected_documents_table');

Route::get('/load_qs_validation_checkpoints_table','NewApplicationController@load_qs_validation_checkpoints_table');

Route::get('/link_checkpoints_to_application','NewApplicationController@link_checkpoints_to_application');

Route::post('/submit_affected_document','NewApplicationController@submit_affected_document');

Route::get('/retrieve_documents_for_approval','NewApplicationController@retrieve_documents_for_approval');

Route::get('load_new_affected_documents_table','NewApplicationController@load_new_affected_documents_table');

Route::get('/load_approver_checkpoints_table','NewApplicationController@load_approver_checkpoints_table');

Route::get('/load_affected_documents_details','NewApplicationController@load_affected_documents_details');

Route::post('/submit_edit_affected_document','NewApplicationController@submit_edit_affected_document');

Route::get('/submit_disapprove_affected_document','NewApplicationController@submit_disapprove_affected_document');

Route::get('/send_new_mailer', 'NewApplicationController@send_new_mailer');

Route::get('/retrive_documents_for_qs_inspection','NewApplicationController@retrive_documents_for_qs_inspection');

Route::get('/new_cancel_application','NewApplicationController@new_cancel_application');

Route::get('/remove_document','NewApplicationController@remove_document');

Route::post('/submit_approver_type', 'NewApplicationController@submit_approver_type');

Route::get('/load_section_head_list','NewApplicationController@load_section_head_list');

Route::get('/load_qc_head_list','NewApplicationController@load_qc_head_list');

Route::get('/load_eng_head_list','NewApplicationController@load_eng_head_list');

Route::get('/load_prod_head_list','NewApplicationController@load_prod_head_list');

Route::get('/load_acdcs_app_data','NewApplicationController@load_acdcs_app_data');

Route::get('/load_for_control_affected_documents_email_three_days','NewApplicationController@load_for_control_affected_documents_email_three_days');

Route::get('/load_for_control_status_email','NewApplicationController@load_for_control_status_email');

Route::get('/load_for_control_affected_documents_email_overdue','NewApplicationController@load_for_control_affected_documents_email_overdue');

Route::post('/submit_dcc_edit_document','MasterListController@submit_dcc_edit_document');

Route::get('/download_attached_document_new/{application_id}','PdfController@download_attached_document_new')->name('download_attached_document_new');
Route::post('/edit_pdf_attachment', 'PdfController@attachDataToPdf');
Route::post('/save_pdf_patch_data', 'PdfController@savePdfPatchData');
Route::get('/get_patch_data', 'PdfController@getPatchData');
Route::post('/change_application_status', 'NewApplicationController@changeApplicationStatus');
// Route::get('/get-patchdata/{application_id}', [PdfController::class, 'getPatchData']);
