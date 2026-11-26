// public function submit_external_application_old(Request $request){
    //     session_start();
    //     date_default_timezone_set('Asia/Manila');
    //     $originator_id = $_SESSION['rapidx_user_id'];
    //     $status = 0;
    //     if($request->submit_mode == 'Final'){
    //         $status = 1;
    //     }

    //     $orig_document_info = Applications::select('aidrc_filename','original_filename','aidrc_excel_filename','excel_filename')->where('id', $request->application_id_external_app)->first();
    //     // return $orig_document_info;

    //     //UPDATE APPLICATIONS USING THE FILE FROM EXTERNAL APPLICATION
    //     if($status == 1){
    //         $control_number = $request->application_control_number;

    //         // ORIGINAL PDF FILENAME
    //         $generated_filename = $control_number."_aidrc_attachment_" . date('YmdHis');
    //         // $generated_filename_excel = "excel_aidrc_attachment_" . date('YmdHis');

    //         if($request->hasFile('reupload_attachment')){
    //             $uploadedPdfFile = $request->file('reupload_attachment');

    //             // Get the original filename parts
    //             $filename_pdf = pathinfo($uploadedPdfFile->getClientOriginalName(), PATHINFO_FILENAME);
    //             $file_extension_pdf = $uploadedPdfFile->getClientOriginalExtension();

    //             // 🔹 Remove special characters (keep only letters, numbers, spaces, dash, underscore)
    //             $cleanName = preg_replace('/[^A-Za-z0-9 _-]/', '', $filename_pdf);
    //             $cleanName = str_replace(' ', '_', $cleanName);
    //             $cleanedFilenamePdf = $cleanName . '.' . $file_extension_pdf;
    //             $aidrc_filename = $generated_filename . "." . $file_extension_pdf;

    //             Storage::putFileAs('public/file_attachments', $request->reupload_attachment, $aidrc_filename);
    //         }else{
    //             $original_filename = $request->input('reupload_attachment_pdf_name');
    //             $file_extension = pathinfo($original_filename, PATHINFO_EXTENSION);
    //             $file_extension = strtolower($file_extension);
    //             $aidrc_filename = $generated_filename . "." . $file_extension;

    //             $external_app_file = ExternalApplication::where('id', $request->external_application_id)->first();

    //             if ($external_app_file && Storage::exists("public/file_attachments/{$external_app_file->external_aidrc_filename}")){
    //                 // Storage::delete("public/file_attachments/{$aidrc_filename}");
    //                 Storage::copy(
    //                             "public/file_attachments/{$external_app_file->external_aidrc_filename}",
    //                             "public/file_attachments/{$aidrc_filename}"
    //                         );
    //             }
    //         }

    //         // // ORIGINAL EXCEL FILENAME
    //         // if($request->hasFile('reupload_attachment_raw')){
    //         //     // $original_filename_excel = $request->file('reupload_attachment_raw')->getClientOriginalName();
    //         //     // $file_extension_excel = $request->file('reupload_attachment_raw')->getClientOriginalExtension();

    //         //     $uploadedRawFile = $request->file('reupload_attachment_raw');

    //         //     // Get the original filename parts
    //         //     $filename_raw = pathinfo($uploadedRawFile->getClientOriginalName(), PATHINFO_FILENAME);
    //         //     $file_extension_raw = $uploadedRawFile->getClientOriginalExtension();

    //         //     // 🔹 Remove special characters (keep only letters, numbers, spaces, dash, underscore)
    //         //     $cleanName = preg_replace('/[^A-Za-z0-9 _-]/', '', $filename_raw);
    //         //     // 🔹 Replace spaces with underscores for safety
    //         //     $cleanName = str_replace(' ', '_', $cleanName);
    //         //     // 🔹 Add timestamp or unique ID if needed
    //         //     $cleanedFilenameRaw = $cleanName . '.' . $file_extension_raw;

    //         //     $aidrc_filename_excel = $generated_filename_excel . "." . $file_extension_raw;

    //         //     Storage::putFileAs('public/file_attachments', $request->reupload_attachment_raw, $aidrc_filename_excel);
    //         // }else{
    //         //     $original_filename_excel = $request->input('reupload_attachment_raw_name');
    //         //     $file_extension_excel = pathinfo($original_filename_excel, PATHINFO_EXTENSION);
    //         //     $file_extension_excel = strtolower($file_extension_excel);
    //         //     $aidrc_filename_excel = $generated_filename_excel . "." . $file_extension_excel;

    //         //     $external_app_file = ExternalApplication::where('id', $request->external_application_id)->first();

    //         //     if ($external_app_file && Storage::exists("public/file_attachments/{$external_app_file->external_aidrc_excel_filename}")) {
    //         //         // Storage::delete("public/file_attachments/{$aidrc_filename_excel}");
    //         //         Storage::copy(
    //         //                 "public/file_attachments/{$external_app_file->external_aidrc_excel_filename}",
    //         //                 "public/file_attachments/{$aidrc_filename_excel}"
    //         //             );
    //         //     }
    //         // }

    //         $app_data_arr = [
    //             'aidrc_filename' => "modified_{$aidrc_filename}",
    //             'original_filename' => $cleanedFilenamePdf,
    //             // 'aidrc_excel_filename' => $aidrc_filename_excel,
    //             // 'excel_filename' => $cleanedFilenameRaw,
    //             'updated_at' => date('Y-m-d H:i:s')
    //         ];

    //         Applications::where('id', $request->application_id_external_app)->update($app_data_arr);
    //     }

    //     if(!isset($request->external_application_id)){ //INSERT
    //         $validator = Validator::make($request->all(), [
    //                     'reupload_attachment' => 'required',
    //         ]);

    //         if($validator->passes()){
    //                 try{
    //                     // ORIGINAL PDF FILENAME
    //                     // $original_filename = $request->file('reupload_attachment')->getClientOriginalName();
    //                     // $file_extension = $request->file('reupload_attachment')->getClientOriginalExtension();
    //                     $uploadedPdfFile = $request->file('reupload_attachment');

    //                     // Get the original filename parts
    //                     $filename_pdf = pathinfo($uploadedPdfFile->getClientOriginalName(), PATHINFO_FILENAME);
    //                     $file_extension_pdf = $uploadedPdfFile->getClientOriginalExtension();

    //                     // 🔹 Remove special characters (keep only letters, numbers, spaces, dash, underscore)
    //                     $cleanName = preg_replace('/[^A-Za-z0-9 _-]/', '', $filename_pdf);
    //                     // 🔹 Replace spaces with underscores for safety
    //                     $cleanName = str_replace(' ', '_', $cleanName);
    //                     // 🔹 Add timestamp or unique ID if needed
    //                     $cleanedFilenamePdf = $cleanName . '.' . $file_extension_pdf;

    //                     $generated_filename = "pdf_ex_aidrc_attachment_" . date('YmdHis');
    //                     $aidrc_filename = $generated_filename . "." . $file_extension_pdf;

    //                     // ORIGINAL EXCEL FILENAME
    //                     // $original_filename_excel = $request->file('reupload_attachment_raw')->getClientOriginalName();
    //                     // $file_extension_excel = $request->file('reupload_attachment_raw')->getClientOriginalExtension();

    //                     // $uploadedRawFile = $request->file('reupload_attachment_raw');

    //                     // // Get the original filename parts
    //                     // $filename_raw = pathinfo($uploadedRawFile->getClientOriginalName(), PATHINFO_FILENAME);
    //                     // $file_extension_raw = $uploadedRawFile->getClientOriginalExtension();

    //                     // // 🔹 Remove special characters (keep only letters, numbers, spaces, dash, underscore)
    //                     // $cleanName = preg_replace('/[^A-Za-z0-9 _-]/', '', $filename_raw);
    //                     // // 🔹 Replace spaces with underscores for safety
    //                     // $cleanName = str_replace(' ', '_', $cleanName);
    //                     // // 🔹 Add timestamp or unique ID if needed
    //                     // $cleanedFilenameRaw = $cleanName . '.' . $file_extension_raw;

    //                     // $generated_filename_excel = "raw_ex_aidrc_attachment_" . date('YmdHis');
    //                     // $aidrc_filename_excel = $generated_filename_excel . "." . $file_extension_raw;

    //                     // Storage::putFileAs('public/file_attachments', $request->reupload_attachment_raw, $aidrc_filename_excel);
    //                     Storage::putFileAs('public/file_attachments', $request->reupload_attachment, $aidrc_filename);

    //                     ExternalApplication::insert([
    //                         'status' => $status,
    //                         'application_id' => $request->application_id_external_app,

    //                         'orig_aidrc_filename' => $orig_document_info->aidrc_filename,
    //                         'orig_original_filename' => $orig_document_info->original_filename,
    //                         // 'orig_excel_filename' => $orig_document_info->excel_filename,
    //                         // 'orig_aidrc_excel_filename' => $orig_document_info->aidrc_excel_filename,

    //                         'external_aidrc_filename' => $aidrc_filename,
    //                         'external_original_filename' => $cleanedFilenamePdf,
    //                         // 'external_aidrc_excel_filename' => $aidrc_filename_excel,
    //                         // 'external_excel_filename' => $cleanedFilenameRaw,
    //                         'remarks' => $request->reupload_remarks,
    //                         'created_at' => date('Y-m-d H:i:s'),
    //                         'updated_at' => date('Y-m-d H:i:s'),
    //                     ]);

    //                     return response()->json(['result' => 1]);
    //                 }catch (\Exception $e){
    //                     DB::rollback();
    //                     throw $e;
    //                     return response()->json(['result1' => $e]);
    //                 }
    //         }else{
    //             return response()->json(['result' => 0, 'error' => $validator->messages()]);
    //         }
    //     }else{ //UPDATE
    //         try{
    //             $data_arr = [
    //                 'status' => $status,
    //                 'application_id' => $request->application_id_external_app,

    //                 'orig_aidrc_filename' => $orig_document_info->aidrc_filename,
    //                 'orig_original_filename' => $orig_document_info->original_filename,
    //                 // 'orig_excel_filename' => $orig_document_info->excel_filename,
    //                 // 'orig_aidrc_excel_filename' => $orig_document_info->aidrc_excel_filename,

    //                 'remarks' => $request->reupload_remarks,
    //                 'created_at' => date('Y-m-d H:i:s'),
    //                 'updated_at' => date('Y-m-d H:i:s'),
    //             ];

    //             if(isset($request->reupload_attachment)){
    //                 // ORIGINAL PDF FILENAME
    //                 // $filename = $request->file('reupload_attachment')->getClientOriginalName();
    //                 // $file_extension = $request->file('reupload_attachment')->getClientOriginalExtension();
    //                 $uploadedFile = $request->file('reupload_attachment');

    //                 // Get the original filename parts
    //                 $filename_pdf = pathinfo($uploadedFile->getClientOriginalName(), PATHINFO_FILENAME);
    //                 $file_extension_pdf = $uploadedFile->getClientOriginalExtension();

    //                 // 🔹 Remove special characters (keep only letters, numbers, spaces, dash, underscore)
    //                 $cleanName = preg_replace('/[^A-Za-z0-9 _-]/', '', $filename_pdf);
    //                 // 🔹 Replace spaces with underscores for safety
    //                 $cleanName = str_replace(' ', '_', $cleanName);
    //                 // 🔹 Add timestamp or unique ID if needed
    //                 $cleanedFilenamePdf = $cleanName . '.' . $file_extension_pdf;

    //                 $generated_filename = "pdf_ex_aidrc_attachment_" . date('YmdHis');
    //                 $aidrc_filename = $generated_filename . "." . $file_extension_pdf;

    //                 Storage::putFileAs('public/file_attachments', $request->reupload_attachment, $aidrc_filename);

    //                 $data_arr['external_aidrc_filename'] = $aidrc_filename;
    //                 $data_arr['external_original_filename'] = $cleanedFilenamePdf;
    //             }

    //             // if(isset($request->reupload_attachment_raw)){
    //             //     // ORIGINAL EXCEL FILENAME
    //             //     $uploadedFile = $request->file('reupload_attachment_raw');

    //             //     // Get the original filename parts
    //             //     $filename_excel = pathinfo($uploadedFile->getClientOriginalName(), PATHINFO_FILENAME);
    //             //     $file_extension_excel = $uploadedFile->getClientOriginalExtension();

    //             //     // 🔹 Remove special characters (keep only letters, numbers, spaces, dash, underscore)
    //             //     $cleanName = preg_replace('/[^A-Za-z0-9 _-]/', '', $filename_excel);
    //             //     // 🔹 Replace spaces with underscores for safety
    //             //     $cleanName = str_replace(' ', '_', $cleanName);
    //             //     // 🔹 Add timestamp or unique ID if needed
    //             //     $cleanedFilenameExcel = $cleanName . '.' . $file_extension_excel;

    //             //     // $filename_excel = $request->file('reupload_attachment_raw')->getClientOriginalName();
    //             //     // $file_extension_excel = $request->file('reupload_attachment_raw')->getClientOriginalExtension();

    //             //     $generated_filename_excel = "raw_ex_aidrc_attachment_" . date('YmdHis');
    //             //     $aidrc_filename_excel = $generated_filename_excel . "." . $file_extension_excel;

    //             //     Storage::putFileAs('public/file_attachments', $request->reupload_attachment_raw, $aidrc_filename_excel);

    //             //     $data_arr['external_aidrc_excel_filename'] = $aidrc_filename_excel;
    //             //     $data_arr['external_excel_filename'] = $cleanedFilenameExcel;
    //             // }

    //             ExternalApplication::where('id', $request->external_application_id)->update($data_arr);

    //             return response()->json(['result' => 1]);
    //         }catch (\Exception $e){
    //             DB::rollback();
    //             throw $e;
    //             return response()->json(['result1' => $e]);
    //         }
    //     }
    // }
