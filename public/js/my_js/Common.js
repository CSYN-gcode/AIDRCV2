function SubmitDccEditDocument(){
	$.ajax({
		url: "submit_dcc_edit_document",
		method: "post",
		data: $('#formDccEditDocument').serialize(),
		dataType: "json",
		beforeSend: function(){
		},
		success: function(JsonObject){
			if(JsonObject['result'] == 1){
    			toastr.success('Document Edit Success!');
  				dt_applications_master_list.draw();
  				$('#view_doc_no').removeClass('is-invalid');
				$('#view_doc_title').removeClass('is-invalid');
    		}else{
    			toastr.error('Document Edit Failed!');

    			if(JsonObject['error']['view_doc_no'] === undefined){
		          $('#view_doc_no').removeClass('is-invalid');
		        }else{
		          $('#view_doc_no').addClass('is-invalid');
		        }

		        if(JsonObject['error']['view_doc_title'] === undefined){
		          $('#view_doc_title').removeClass('is-invalid');
		        }else{
		          $('#view_doc_title').addClass('is-invalid');
		        }
    		}
		},
		error: function(data, xhr, status){
            toastr.error('An error occured!\n' + 'Data: ' + data + "\n" + "XHR: " + xhr + "\n" + "Status: " + status);
        }
	});
}

function LinkCheckpointsToApplication(array_documents, application_id){
	$.ajax({
		url: "link_checkpoints_to_application",
		method: "get",
		data:{
			application_id: application_id,
			array_documents: array_documents,
		},
		dataType: "json",
		beforeSend: function(){

		},
		success: function(JsonObject){
			if(JsonObject['result'] == 1){
				toastr.success('Successfully Saved Affected Documents!');
				array_documents = [];
			}else{
				toastr.error('Saving Affected Documents Failed!');
			}
		},
		error: function(data, xhr, status){
            toastr.error('An error occured!\n' + 'Data: ' + data + "\n" + "XHR: " + xhr + "\n" + "Status: " + status);
        }
	});
}

function NewCancelApplication(application_id){
	$.ajax({
		url: "new_cancel_application",
		method: "get",
		data:{
			application_id: application_id,
		},
		dataType: "json",
		beforeSend: function(){

		},
		success: function(JsonObject){
			if(JsonObject['result'] == 1){
				$('#modalViewApplication').modal('hide');
				toastr.warning('Cancelled Application!');
				dt_applications.draw();

				SendNewMailer(application_id); //clark comment for now 07/05/2025 (TESTING)
			}else{
				toastr.error('Saving Affected Documents Failed!');
			}
		},
		error: function(data, xhr, status){
            toastr.error('An error occured!\n' + 'Data: ' + data + "\n" + "XHR: " + xhr + "\n" + "Status: " + status);
        }
	});
}

function ChangeApplicationStatus(application_id, param_status){
    let csrf = $('#ChangeApplicationStatus').find('input[name="_token"]').val();
    $.ajax({
        url: "change_application_status",
        method: "post",
        data: {
            _token: csrf,
            application_id: application_id,
            status: param_status
        },
        dataType: "json",
        success: function(JsonObject){
            if(param_status == 1){
                if(JsonObject['result'] == 1){
                    toastr.success('Application Submitted!');
                    $('#modalSubmitApplication').modal('hide');

                    dt_applications.draw();
                    SendNewMailer(application_id);//clark comment for now 07/05/2025 (TESTING)
                }else{
                    toastr.error('Submission of Application Failed!');
                }
            }else{
                if(JsonObject['result'] == 1){
                    toastr.success('Application Cancelled!');
                    $('#modalConfirmToProceed').modal('hide');

                    dt_applications.draw();
                    SendNewMailer(application_id);//clark comment for now 07/05/2025 (TESTING)
                }else{
                    toastr.error('Cancellation of Application Failed!');
                }
            }
		},
		error: function(data, xhr, status){
            toastr.error('An error occured!\n' + 'Data: ' + data + "\n" + "XHR: " + xhr + "\n" + "Status: " + status);
        }
    });
}

function RetrieveDocumentsForApproval1(application_id, approving_as, array_documents){
	let temp_array_documents;
	$.ajax({
		url: "retrieve_documents_for_approval",
		method: "get",
		data:
		{
			application_id: application_id,
			approving_as: approving_as,
		},
		dataType: "json",
		beforeSend: function(){

		},
		success: function(JsonObject){
			if(JsonObject['result'] == 1){
				array_documents = JsonObject['array_documents'];
				temp_array_documents = JsonObject['array_documents'];
			}

			dt_head_approval_documents.draw();
		},
		error: function(data, xhr, status){
            toastr.error('An error occured!\n' + 'Data: ' + data + "\n" + "XHR: " + xhr + "\n" + "Status: " + status);
        }
	});
}

function SubmitAffectedDocument(array_documents){
	$.ajax({
		url: "submit_affected_document",
		method: "post",
		data: $('#formAffectedDocument').serialize(),
		dataType: "json",
		beforeSend: function(){

		},
		success: function(JsonObject){
			if(JsonObject['result'] == 1){
    			toastr.success('Application Validated!');
    			$('#modalAddaffectedDocumentDetails').modal('hide');
    			$('#formAffectedDocument')[0].reset();

    			array_documents.push(JsonObject['affected_document_id']);
    			console.log(array_documents);

  				dt_affected_documents.draw();
  				dt_qs_validations.draw();
  				dt_head_approval_documents.draw();
    			//dt_applications.draw();
    		}else{
    			toastr.error('Applicaiton Validation Failed!');

    			if(JsonObject['error']['affected_doc_no'] === undefined){
		          $('#affected_doc_no').removeClass('is-invalid');
		        }else{
		          $('#affected_doc_no').addClass('is-invalid');
		        }

		        if(JsonObject['error']['affected_doc_title'] === undefined){
		          $('#affected_doc_title').removeClass('is-invalid');
		        }else{
		          $('#affected_doc_title').addClass('is-invalid');
		        }

		        if(JsonObject['error']['affected_doc_rev_no'] === undefined){
		          $('#affected_doc_rev_no').removeClass('is-invalid');
		        }else{
		          $('#affected_doc_rev_no').addClass('is-invalid');
		        }

		        if(JsonObject['error']['affected_checkpoint_type'] === undefined){
		          $('#affected_checkpoint_type').removeClass('is-invalid');
		        }else{
		          $('#affected_checkpoint_type').addClass('is-invalid');
		        }

		        if(JsonObject['error']['affected_person_in_charge'] === undefined){
		          $('#affected_person_in_charge').removeClass('is-invalid');
		        }else{
		          $('#affected_person_in_charge').addClass('is-invalid');
		        }

		        if(JsonObject['error']['affected_rev_due_date'] === undefined){
		          $('#affected_rev_due_date').removeClass('is-invalid');
		        }else{
		          $('#affected_rev_due_date').addClass('is-invalid');
		        }
    		}
		},
		error: function(data, xhr, status){
            toastr.error('An error occured!\n' + 'Data: ' + data + "\n" + "XHR: " + xhr + "\n" + "Status: " + status);
        }
	});
}

function LoadAddDocumentDetails(active_doc_id, addition_type){
	$.ajax({
		url: "load_acdcs_document_details",
		method: "get",
		data:
		{
			active_doc_id: active_doc_id
		},
		dataType: "json",
		beforeSend: function(){

		},
		success: function(JsonObject){
			if(JsonObject['result'] == 1){
				let document_number = JsonObject['document_details'][0].doc_no;
				let document_title = JsonObject['document_details'][0].doc_title;
				let revision_number = JsonObject['document_details'][0].rev_no;

                if($.isNumeric(revision_number)){
                    revision_number = parseInt(revision_number) + 1;
                }else{
                    revision_number = 0;
                }

				CheckExistingAidrcApplicationRevised(document_number, revision_number, document_title);
			}else{
				toastr.error('Error Loading Details');
			}
		},
		error: function(data, xhr, status){
            toastr.error('An error occured!\n' + 'Data: ' + data + "\n" + "XHR: " + xhr + "\n" + "Status: " + status);
        }
	});
}

function LoadAidrcv2AddDocumentDetails(active_doc_id, addition_type){
	$.ajax({
		url: "load_acdcs_document_details",
		method: "get",
		data:
		{
			active_doc_id: active_doc_id
		},
		dataType: "json",
		beforeSend: function(){

		},
		success: function(JsonObject){
			if(JsonObject['result'] == 1){
				let document_number = JsonObject['document_details'][0].doc_no;
				let document_title = JsonObject['document_details'][0].doc_title;
				let revision_number = JsonObject['document_details'][0].rev_no;

                if($.isNumeric(revision_number)){
                    revision_number = parseInt(revision_number) + 1;
                }else{
                    revision_number = 0;
                }

				CheckExistingAidrcApplicationRevised(document_number, revision_number, document_title);
			}else{
				toastr.error('Error Loading Details');
			}
		},
		error: function(data, xhr, status){
            toastr.error('An error occured!\n' + 'Data: ' + data + "\n" + "XHR: " + xhr + "\n" + "Status: " + status);
        }
	});
}

function LoadDccDocumentDetails(active_doc_id){
	$.ajax({
		url: "load_acdcs_document_details",
		method: "get",
		data:
		{
			active_doc_id: active_doc_id
		},
		dataType: "json",
		beforeSend: function(){

		},
		success: function(JsonObject){
			if(JsonObject['result'] == 1){
				let document_number = JsonObject['document_details'][0].doc_no;
				let document_title = JsonObject['document_details'][0].doc_title;
				let revision_number = JsonObject['document_details'][0].rev_no;

				$('#view_doc_no').val(document_number);
				$('#view_doc_title').val(document_title);
				$('#view_doc_rev_no').val(revision_number);

				$('#modalSearchDocumentDetails').modal('hide');
				toastr.success('Document Details Added!');
			}else{
				toastr.error('Error Loading Details');
			}
		},
		error: function(data, xhr, status){
            toastr.error('An error occured!\n' + 'Data: ' + data + "\n" + "XHR: " + xhr + "\n" + "Status: " + status);
        }
	});
}

function LoadAffectedDocumentDetails(active_doc_id, addition_type){
	$.ajax({
		url: "load_acdcs_document_details",
		method: "get",
		data:
		{
			active_doc_id: active_doc_id
		},
		dataType: "json",
		beforeSend: function(){

		},
		success: function(JsonObject){
			if(JsonObject['result'] == 1){

				toastr.success('Successfully Loaded Document Details!');

				let document_number = JsonObject['document_details'][0].doc_no;
				let document_title = JsonObject['document_details'][0].doc_title;
				let revision_number = JsonObject['document_details'][0].rev_no;

				revision_number = parseInt(revision_number) + 1;

				$('#affected_doc_no').val(document_number);
				$('#affected_doc_title').val(document_title);
				$('#affected_doc_rev_no').val(revision_number);

				if(addition_type == 4){
					$('#affected_checkpoint_type').val(1).trigger('change');
					$('.class-affected-doc').removeAttr('hidden');
					$('.class-checkpoint').prop('hidden', 'hidden');
					$('.class-checkpoint-preprod').prop('hidden', 'hidden');
				}else if(addition_type == 2){
					$('#affected_checkpoint_type').prop('selectedIndex',0);
					$('.class-affected-doc').prop('hidden', 'hidden');
					$('.class-checkpoint').removeAttr('hidden');
					$('.class-checkpoint-preprod').prop('hidden', 'hidden');
				}else{	$('#affected_checkpoint_type').prop('selectedIndex',0);
					$('.class-affected-doc').removeAttr('hidden');
					$('.class-checkpoint').removeAttr('hidden');
					$('.class-checkpoint-preprod').prop('hidden', 'hidden');
				}

			}else{
				toastr.error('Error Loading Details');
			}
		},
		error: function(data, xhr, status){
            toastr.error('An error occured!\n' + 'Data: ' + data + "\n" + "XHR: " + xhr + "\n" + "Status: " + status);
        }
	});
}

function CheckExistingAidrcApplicationRevised(document_number, revision_number, document_title){
	$.ajax({
		url: "check_existing_aidrc_application",
		method: "get",
		data:
		{
			document_number: document_number,
			revision_number: revision_number
		},
		dataType: "json",
		beforeSend: function(){

		},
		success: function(JsonObject){
			if(JsonObject['result'] == 1){
				toastr.error("There's already an existing application for the document!");
			}else{
				toastr.success('Successfully Loaded Document Details!');

				$('#add_doc_no').val(document_number);
				$('#add_doc_title').val(document_title);
				$('#add_doc_rev_no').val(revision_number);

				$('#modalSearchDocumentDetails').modal('hide');
				//console.log("no existing document");
			}
		},
		error: function(data, xhr, status){
            toastr.error('An error occured!\n' + 'Data: ' + data + "\n" + "XHR: " + xhr + "\n" + "Status: " + status);
        }
	});
}

function SubmitNewApplication(array_documents){

	let formData = new FormData($('#formAddApplication')[0]);
    let signatureData = [];

    $("#dynamicTable tbody tr").each(function () {
        let row = $(this);
        let rowIndex = row.find(".previewPdfButton").data("row"); // Get row number

        let approvalOrder = row.find(`#approvalOrder-${rowIndex}`).text().trim();
        let pageNumber = row.find(`#pageNumber-${rowIndex}`).text().trim();
        let coordinatesText = row.find(`#coordinates-${rowIndex}`).text().trim();
        let signaturePath = row.find(`#esignature-${rowIndex}`).val();
        let selectedApprover = row.find(`#approver-${rowIndex}`).val();
        // // ✅ Get the stored zoomed-in dimensions
        let pdfwidth = $('#pdfPreview').data('pdfWidth');
        let pdfheight = $('#pdfPreview').data('pdfHeight');

        // Get actual dimensions in points (used in backend scaling)
        // let pdfWidth = viewport.width;
        // let pdfHeight = viewport.height;

        let canvas = $('#pdfPreview').find('canvas')[0];
        let CanvasWidth = canvas.width;
        let CanvasHeight = canvas.height;

        if (coordinatesText !== "No coordinates selected" && signaturePath){
            let matches = coordinatesText.match(/X:\s*([\d.]+),\s*Y:\s*([\d.]+)/);
            if (matches) {
                let x = parseFloat(matches[1]);
                let y = parseFloat(matches[2]);

                signatureData.push({
                    x: x,
                    y: y,
                    approval_order: parseInt(approvalOrder),
                    page: parseInt(pageNumber),
                    path: signaturePath,
                    approver: selectedApprover,
                    canvasWidth: CanvasWidth,
                    canvasHeight: CanvasHeight,
                    pdfWidth: pdfwidth,
                    pdfHeight: pdfheight,
                });
            }
        }else{
            signatureData.push({
                x: '',
                y: '',
                approval_order: parseInt(approvalOrder),
                page: parseInt(pageNumber),
                path: '',
                approver: selectedApprover,
                canvasWidth: CanvasWidth,
                canvasHeight: CanvasHeight,
                pdfWidth: pdfwidth,
                pdfHeight: pdfheight,
            });
        }
    });

    // After building signatureData
    let hasMissingCoordinates = false;

    signatureData.forEach(sig => {
        if (sig.approver && (sig.x === '' || sig.y === '' || sig.x === undefined || sig.y === undefined)) {
            hasMissingCoordinates = true;
        }
    });

    if (hasMissingCoordinates) {
        toastr.error("One or more selected approvers have no signature coordinates. Please click inside the PDF to place the signature.");
        return;
    }

    formData.append("signatureData", JSON.stringify(signatureData)); // Append to formData

    // const triggerCategories = ['1','2','3','10','11','12'];
    // const selectedCategory = $('#add_doc_category').val();

    // if (triggerCategories.includes(selectedCategory)) {
    //     const approvers = esignApprovers; // Replace with your actual array or source

    //     const isRequiredApproverPresent = approvers.some(appr =>
    //         appr.name === requiredApprover.name &&
    //         appr.coordinates != null && appr.coordinates !== ''
    //     );

    //     if (!isRequiredApproverPresent) {
    //         e.preventDefault();
    //         toastr.error(`The required approver "${requiredApprover.name}" is missing or has no coordinates.`);
    //         return false;
    //     }
    // }

    // ✅ VALIDATE if required approver ID is present
    const triggerCategories = ['1','2','3','10','11','12'];
    const selectedCategory = $('#add_doc_category').val();

    if (triggerCategories.includes(selectedCategory)) {
        const requiredApproverId = "564"; //NVL EmpNo for Section Head Approver
        const approverFound = signatureData.some(sig => sig.approver === requiredApproverId && sig.x !== '' && sig.y !== '');

        if (!approverFound) {
            toastr.error("Required approver Nian V. Lim is missing or has no signature placement.");
            return; // stop submission
        }
    }

	$.ajax({
		url: "submit_new_application",
		method: "post",
		processData: false,
		contentType: false,
    	data: formData,
    	dataType: "json",
    	beforeSend: function(){
    		//$('#btnSubmitApplication').prop('disabled','disabled');
    	},
    	success: function(JsonObject){
    		if(JsonObject['result'] == 1){
    			toastr.success('Application Saved Successfully!');
    			$('#modalAddApplication').modal('hide');//clark comment

    			if(array_documents.length > 0){
    				LinkCheckpointsToApplication(array_documents, JsonObject['application_id']);
    			}else{
    				array_documents = [];
    			}

    			dt_applications.draw();
    			// dt_affected_documents.draw();
    			// SendNewMailer(JsonObject['application_id']);//clark comment for now 07/05/2025 (TESTING)
    		}else{
       			toastr.error('Application Submission Failed!');

    			if(JsonObject['error']['add_attachment'] === undefined){
		          $('#add_attachment').removeClass('is-invalid');
		        }else{
		          $('#add_attachment').addClass('is-invalid');
		        }

		        if(JsonObject['error']['add_doc_category'] === undefined){
		          $('#add_doc_category').removeClass('is-invalid');
		        }else{
		          $('#add_doc_category').addClass('is-invalid');
		        }

		        if(JsonObject['error']['add_doc_type'] === undefined){
		          $('#add_doc_type').removeClass('is-invalid');
		        }else{
		          $('#add_doc_type').addClass('is-invalid');
		        }

		        if(JsonObject['error']['add_for_group'] === undefined){
		          $('#add_for_group').removeClass('is-invalid');
		        }else{
		          $('#add_for_group').addClass('is-invalid');
		        }

		        if(JsonObject['error']['add_department'] === undefined){
		          $('#add_department').removeClass('is-invalid');
		        }else{
		          $('#add_department').addClass('is-invalid');
		        }

		        if(JsonObject['error']['add_doc_no'] === undefined){
		          $('#add_doc_no').removeClass('is-invalid');
		        }else{
		          $('#add_doc_no').addClass('is-invalid');
		        }

		        if(JsonObject['error']['add_doc_title'] === undefined){
		          $('#add_doc_title').removeClass('is-invalid');
		        }else{
		          $('#add_doc_title').addClass('is-invalid');
		        }

		        if(JsonObject['error']['add_doc_rev_no'] === undefined){
		          $('#add_doc_rev_no').removeClass('is-invalid');
                }else{
		          $('#add_doc_rev_no').addClass('is-invalid');
		        }

		        if(JsonObject['error']['add_section_head_approver'] === undefined){
		          $('#add_section_head_approver').removeClass('is-invalid');
		        }else{
		          $('#add_section_head_approver').addClass('is-invalid');
		        }

		        if(JsonObject['error']['add_production_head'] === undefined){
		          $('#add_production_head').removeClass('is-invalid');
		        }else{
		          $('#add_production_head').addClass('is-invalid');
		        }

		        if(JsonObject['error']['add_qc_head'] === undefined){
		          $('#add_qc_head').removeClass('is-invalid');
		        }else{
		          $('#add_qc_head').addClass('is-invalid');
		        }

		        if(JsonObject['error']['add_eng_head'] === undefined){
		          $('#add_eng_head').removeClass('is-invalid');
		        }else{
		          $('#add_eng_head').addClass('is-invalid');
		        }

		        if(JsonObject['error']['add_approver_priority'] === undefined){

		        }else{
		          toastr.error('Please select your Initial Approver!');
		        }

		        if(JsonObject['error']['add_qs_inspector'] === undefined){
		          $('#add_qs_inspector').removeClass('is-invalid');
		        }else{
		          $('#add_qs_inspector').addClass('is-invalid');
		        }
    		}
    	},
    	error: function(data, xhr, status){
    		//$('#btnSubmitApplication').removeAttr('disabled');
            toastr.error('An error occured!\n' + 'Data: ' + data + "\n" + "XHR: " + xhr + "\n" + "Status: " + status);
        }

	});
}

function LoadQsApplicationDetails(application_id)
{
	$.ajax({

		url: "load_application_details",
		method: "get",
		data:
		{
			application_id: application_id
		},
		dataType: "json",
		beforeSend: function()
		{

		},
		success: function(JsonObject)
		{
			if(JsonObject['result'] == 1)
			{
				let application_id = JsonObject['application_details'][0].id;
				let aidrc_control_number = JsonObject['application_details'][0].aidrc_control_number;
				let document_number = JsonObject['application_details'][0].document_number;
				let document_name = JsonObject['application_details'][0].document_name;
				let document_revision_number = JsonObject['application_details'][0].document_revision_number;
				let document_category = JsonObject['application_details'][0].document_category;
				let document_type = JsonObject['application_details'][0].document_type;
				let for_group = JsonObject['application_details'][0].for_group;
				let department = JsonObject['application_details'][0].department;
				let application_originator = JsonObject['application_details'][0].application_originator;
				let application_section_head = JsonObject['application_details'][0].application_section_head;
				let application_prod_head = JsonObject['application_details'][0].application_prod_head;
				let application_qc_head = JsonObject['application_details'][0].application_qc_head;
				let application_eng_head = JsonObject['application_details'][0].application_eng_head;
				let qs_inspector = JsonObject['application_details'][0].qs_inspector;
				let approver_priority = JsonObject['application_details'][0].approver_priority;
				let originator = JsonObject['application_details'][0].application_originator;
				let originator_remarks = JsonObject['application_details'][0].originator_remarks;
				let created_at = JsonObject['application_details'][0].created_at;

				if(document_number != null)
				{
					$('#qs_doc_no').val(document_number);
				}
				else
				{
					$('#qs_doc_no').val('---');
				}


				$('#qs_doc_title').val(document_name);
				$('#qs_doc_rev_no').val(document_revision_number);

				$('#qs_aidrc_control_number').val(aidrc_control_number);
				$('#qs_doc_category').val(document_category);
				$('#qs_doc_type').val(document_type);
				$('#qs_for_group').val(for_group);
				$('#qs_department').val(department);
				$('#qs_originator').val(originator);
				$('#qs_created_at').val(created_at);
				$('#qs_remarks').val(originator_remarks);

				$('#qs_qc_head').val(application_qc_head);
				$('#qs_eng_head').val(application_eng_head);

				$('#qs_application_id').val(application_id);
			}
			else
			{
				toastr.error('Error Loading Details');
			}
		},
		error: function(data, xhr, status){
            toastr.error('An error occured!\n' + 'Data: ' + data + "\n" + "XHR: " + xhr + "\n" + "Status: " + status);
        }
	});
}

// function SubmitQsValidation(array_documents){
// 	$.ajax({
// 		url: "submit_new_qs_validation",
// 		method: "post",
// 		data: $('#formQsValidation').serialize(),
// 		dataType: "json",
// 		beforeSend: function(){

// 		},
// 		success: function(JsonObject){
// 			if(JsonObject['result'] == 1){
//     			toastr.success('QS Validation Submitted!');
//     			$('#modalQSValidation').modal('hide');
//     			$('#formQsValidation')[0].reset();

//     			if(array_documents.length > 0){
//     				LinkCheckpointsToApplication(array_documents, JsonObject['application_id']);
//     			}else{
//     				array_documents = [];
//     			}

//     			dt_applications.draw();
//     			dt_qs_validations.draw();

//     			SendNewMailer(JsonObject['application_id']); //clark comment for now 07/05/2025 (TESTING)
//     		}else{
//     			toastr.error('Saving QS Validation Failed!');

//     			if(JsonObject['error']['qs_validation_remarks'] === undefined)
// 		        {
// 		          $('#qs_validation_remarks').removeClass('is-invalid');
// 		        }
// 		        else
// 		        {
// 		          $('#qs_validation_remarks').addClass('is-invalid');
// 		        }
//     		}
// 		},
// 		error: function(data, xhr, status){
//             toastr.error('An error occured!\n' + 'Data: ' + data + "\n" + "XHR: " + xhr + "\n" + "Status: " + status);
//         }
// 	});
// }

function GetEsignApprover(cboElement1, userId){

    let result = '<option value="" disabled selected> Select Approver Name </option>';
    $.ajax({
        type: "get",
        url: "load_rapidx_users_with_esign",
        dataType: "json",
        beforeSend: function(){
            result = '<option value="0" disabled selected>--Loading--</option>';
        },
        success: function (response) {
            let user_details = response['users'];
            console.log('user_details', user_details);
            if(user_details.length > 0){
                    result = '<option value="" disabled selected> Select Approver Name </option>';
                for(let index = 0; index < user_details.length; index++){
                    result += '<option emp_id="'+user_details[index].emp_no +'" value="'+user_details[index].id+'">'+user_details[index].name+'</option>';
                }
            }else{
                result = '<option value="0" selected disabled> -- No record found -- </option>';
            }
            cboElement1.html(result);
            if(userId){
                cboElement1.val(userId).trigger('change');
            }
        },
        error: function(data, xhr, status) {
            result = '<option value="0" selected disabled> -- Reload Again -- </option>';
            cboElement1.html(result);
            console.log('Data: ' + data + "\n" + "XHR: " + xhr + "\n" + "Status: " + status);
        }
    });
}

function LoadHeadApplicationDetails(application_id, approving_as, approval_order){
	$.ajax({
		url: "load_application_details",
		method: "get",
		data:
		{
			application_id: application_id
		},
		dataType: "json",
		beforeSend: function(){
            $('#viewEsignApproverTable tbody').empty();
		},
		success: function(JsonObject){
			if(JsonObject['result'] == 1){
				let application_id = JsonObject['application_details'][0].id;
				let aidrc_control_number = JsonObject['application_details'][0].aidrc_control_number;
				let document_number = JsonObject['application_details'][0].document_number;
				let document_name = JsonObject['application_details'][0].document_name;
				let document_revision_number = JsonObject['application_details'][0].document_revision_number;
				let document_category = JsonObject['application_details'][0].document_category;
				let document_type = JsonObject['application_details'][0].document_type;
				let for_group = JsonObject['application_details'][0].for_group;
				let department = JsonObject['application_details'][0].department;
				// let application_originator = JsonObject['application_details'][0].application_originator;
				// let application_section_head = JsonObject['application_details'][0].application_section_head;
				// let application_prod_head = JsonObject['application_details'][0].application_prod_head;
				// let application_qc_head = JsonObject['application_details'][0].application_qc_head;
				// let application_eng_head = JsonObject['application_details'][0].application_eng_head;
				// let qs_inspector = JsonObject['application_details'][0].qs_inspector;
				// let approver_priority = JsonObject['application_details'][0].approver_priority;
				let originator = JsonObject['application_details'][0].application_originator;
				let originator_remarks = JsonObject['application_details'][0].originator_remarks;
				let created_at = JsonObject['application_details'][0].created_at;
                let esign_details = JsonObject['application_details'][0].esign_approver_details;

                let orig_filename = JsonObject['application_details'][0].original_filename;
                let raw_filename = JsonObject['application_details'][0].excel_filename;

				if(document_number != null){
					$('#head_doc_no').val(document_number);
				}else{
					$('#head_doc_no').val('---');
				}

				$('#head_doc_title').val(document_name);
				$('#head_doc_rev_no').val(document_revision_number);
				$('#head_aidrc_control_number').val(aidrc_control_number);
				$('#head_doc_category').val(document_category);
				$('#head_doc_type').val(document_type);
				$('#head_for_group').val(for_group);
				$('#head_department').val(department);
				$('#head_originator').val(originator);
				$('#head_created_at').val(created_at);
				$('#head_remarks').val(originator_remarks);

				/*$('#head_qc_head').val(application_qc_head);
				$('#head_eng_head').val(application_eng_head);*/
				// $('#head_approving_as').val(approving_as);

                $('#head_application_id').val(application_id);
				$('#approvalOrder').val(approval_order);

                if(orig_filename){
                    $('#txtViewUploadedPdfFile').val(orig_filename);
                    $('#downloadPdfLink').attr('href', 'http://rapidx/aidrc_v2/download_attached_document_new/'+application_id);
                }else{
                    $('#downloadPdfLink').prop('hidden', true);
                }

                if(raw_filename){
                    $('#txtViewUploadedRawFile').val(raw_filename);
                    $('#downloadRawLink').attr('href', 'http://rapidx/aidrc_v2/download_attached_doc_excel/'+application_id);
                }else{
                    $('#downloadRawLink').prop('hidden', true);
                }

                esign_details.forEach(row => {
                    var rowHtml = `
                        <tr>
                            <td id="approvalOrder-${row.approval_order}">${row.approval_order}</td>
                            <td>
                                <select disabled id="approver-${row.approval_order}" class="form-control form-control-sm SelectEsignApprover"></select>
                            </td>
                            <td hidden>
                                <input class="form-control form-control-sm" id="esignature-${row.approval_order}" data-signature value="">
                            </td>
                            <td id="pageNumber-${row.approval_order}">${row.page_no}</td>
                            <td id="coordinates-${row.approval_order}" value="">${row.coordinates}</td>
                            <td>
                                <button disabled type="button" class="btn btn-primary btn-sm previewPdfButton" data-row="${row.approval_order}">Preview PDF</button>
                                <button disabled type="button" class="btn btn-danger btn-sm deleteRow">Delete</button>
                            </td>
                        </tr>
                    `;
                    $('#viewEsignApproverTable tbody').append(rowHtml);

                    console.log('name', row.approver_id);

                    // Populate SelectApprover dropdown
                    GetEsignApprover($('.SelectEsignApprover').last(), row.approver_id);
                });
			}else{
				toastr.error('Error Loading Details');
			}
		},
		error: function(data, xhr, status){
            toastr.error('An error occured!\n' + 'Data: ' + data + "\n" + "XHR: " + xhr + "\n" + "Status: " + status);
        }
	});
}

function SubmitHeadApproval(array_documents){
	$.ajax({
		url: "submit_new_head_approval",
		method: "post",
		data: $('#formHeadApproval').serialize(),
		dataType: "json",
		beforeSend: function(){
		},
		success: function(JsonObject){
			if(JsonObject['result'] == 1){
    			toastr.success('Application Review Submitted!');
    			$('#modalHeadApprover').modal('hide');
    			$('#formHeadApproval')[0].reset();

    			if(array_documents.length > 0){
    				LinkCheckpointsToApplication(array_documents, JsonObject['application_id']);
    			}else{
    				array_documents = [];
    			}

    			dt_applications.draw();
    			dt_head_approval_documents.draw();

   				SendNewMailer(JsonObject['application_id']);//clark comment for now 07/05/2025 (TESTING)
    		}
    		else
    		{
    			toastr.error('Application Review Failed!');

    			if(JsonObject['error']['head_approval_remarks'] === undefined)
		        {
		          $('#head_approval_remarks').removeClass('is-invalid');
		        }
		        else
		        {
		          $('#head_approval_remarks').addClass('is-invalid');
		        }
    		}
		},
		error: function(data, xhr, status){
            toastr.error('An error occured!\n' + 'Data: ' + data + "\n" + "XHR: " + xhr + "\n" + "Status: " + status);
        }
	});
}

function LoadDccApplicationDetails(application_id)
{
	$.ajax({

		url: "load_application_details",
		method: "get",
		data:
		{
			application_id: application_id
		},
		dataType: "json",
		beforeSend: function()
		{

		},
		success: function(JsonObject)
		{
			if(JsonObject['result'] == 1)
			{
				let application_id = JsonObject['application_details'][0].id;
				let aidrc_control_number = JsonObject['application_details'][0].aidrc_control_number;
				let document_number = JsonObject['application_details'][0].document_number;
				let document_name = JsonObject['application_details'][0].document_name;
				let document_revision_number = JsonObject['application_details'][0].document_revision_number;
				let document_category = JsonObject['application_details'][0].document_category;
				let document_type = JsonObject['application_details'][0].document_type;
				let for_group = JsonObject['application_details'][0].for_group;
				let department = JsonObject['application_details'][0].department;
				let application_originator = JsonObject['application_details'][0].application_originator;
				let application_section_head = JsonObject['application_details'][0].application_section_head;
				let application_prod_head = JsonObject['application_details'][0].application_prod_head;
				let application_qc_head = JsonObject['application_details'][0].application_qc_head;
				let application_eng_head = JsonObject['application_details'][0].application_eng_head;
				let qs_inspector = JsonObject['application_details'][0].qs_inspector;
				let approver_priority = JsonObject['application_details'][0].approver_priority;
				let originator = JsonObject['application_details'][0].application_originator;
				let originator_remarks = JsonObject['application_details'][0].originator_remarks;
				let created_at = JsonObject['application_details'][0].created_at;

				if(document_number != null)
				{
					$('#dcc_doc_no').val(document_number);
				}
				else
				{
					$('#dcc_doc_no').val('---');
				}


				$('#dcc_doc_title').val(document_name);
				$('#dcc_doc_rev_no').val(document_revision_number);

				$('#dcc_aidrc_control_number').val(aidrc_control_number);
				$('#dcc_doc_category').val(document_category);
				$('#dcc_doc_type').val(document_type);
				$('#dcc_for_group').val(for_group);
				$('#dcc_department').val(department);
				$('#dcc_originator').val(originator);
				$('#dcc_created_at').val(created_at);
				$('#dcc_remarks').val(originator_remarks);

				/*$('#dcc_qc_head').val(application_qc_head);
				$('#dcc_eng_head').val(application_eng_head);*/

				$('#dcc_application_id').val(application_id);

				dt_dcc_affected_documents.draw();
			}
			else
			{
				toastr.error('Error Loading Details');
			}
		},
		error: function(data, xhr, status){
            toastr.error('An error occured!\n' + 'Data: ' + data + "\n" + "XHR: " + xhr + "\n" + "Status: " + status);
        }
	});
}

function SubmitDccValidation(){
	$.ajax({
		url: "submit_new_dcc_validation",
		method: "post",
		data: $('#formDccValidation').serialize(),
		dataType: "json",
		beforeSend: function(){

		},
		success: function(JsonObject){
			if(JsonObject['result'] == 1){
    			toastr.success('Application Validated!');
    			$('#modalDccValidations').modal('hide');
    			$('#formDccValidation')[0].reset();

    			dt_applications.draw();
    			SendNewMailer(JsonObject['application_id']);//clark comment 04/30/2025
    		}else{
    			toastr.error('Applicaiton Validation Failed!');

    			if(JsonObject['error']['dcc_checkpoint_similar'] === undefined){
		          $('#dcc_checkpoint_similar').removeClass('is-invalid');
		        }else{
		          $('#dcc_checkpoint_similar').addClass('is-invalid');
		        }

		        if(JsonObject['error']['dcc_checkpoint_alignment'] === undefined){
		          $('#dcc_checkpoint_alignment').removeClass('is-invalid');
		        }else{
		          $('#dcc_checkpoint_alignment').addClass('is-invalid');
		        }

		        if(JsonObject['error']['dcc_checkpoint_standard'] === undefined){
		          $('#dcc_checkpoint_standard').removeClass('is-invalid');
		        }else{
		          $('#dcc_checkpoint_standard').addClass('is-invalid');
		        }

		        if(JsonObject['error']['dcc_validation_judgement'] === undefined){
		          $('#dcc_validation_judgement').removeClass('is-invalid');
		        }else{
		          $('#dcc_validation_judgement').addClass('is-invalid');
		        }
    		}
		},
		error: function(data, xhr, status){
            toastr.error('An error occured!\n' + 'Data: ' + data + "\n" + "XHR: " + xhr + "\n" + "Status: " + status);
        }
	});
}

function LoadViewApplicationDetails(application_id, view_edit){
	$.ajax({
		url: "load_application_details",
		method: "get",
		data:
		{
			application_id: application_id
		},
		dataType: "json",
        beforeSend: function(){
            $('#editApproverTable tbody').empty();
		},
		success: function(JsonObject){
			if(JsonObject['result'] == 1){
                let esign_details = JsonObject['application_details'][0].esign_approver_details;
                let aidrc_filename = JsonObject['application_details'][0].aidrc_filename;
				let application_id = JsonObject['application_details'][0].id;
				let application_status = JsonObject['application_details'][0].status;
				let aidrc_control_number = JsonObject['application_details'][0].aidrc_control_number;
				let document_number = JsonObject['application_details'][0].document_number;
				let document_name = JsonObject['application_details'][0].document_name;
				let document_revision_number = JsonObject['application_details'][0].document_revision_number;
				let document_category = JsonObject['application_details'][0].document_category;
				let document_type = JsonObject['application_details'][0].document_type;
				let for_group = JsonObject['application_details'][0].for_group;
				let department = JsonObject['application_details'][0].department;
				let originator = JsonObject['application_details'][0].application_originator;
				let created_at = JsonObject['application_details'][0].created_at;

				if(document_number != null){
					$('#view_doc_no').val(document_number);
				}else{
					$('#view_doc_no').val('');
				}

				$('#view_doc_title').val(document_name);
				$('#view_doc_rev_no').val(document_revision_number);
				$('#view_aidrc_control_number').val(aidrc_control_number);
				$('#view_doc_category').val(document_category);
				$('#view_doc_type').val(document_type);
				$('#view_for_group').val(for_group);
				$('#view_department').val(department);
				$('#view_originator').val(originator);
				$('#view_created_at').val(created_at);
				$('#view_application_id').val(application_id);

				if(view_edit == 1){
					if(application_status == 6 || application_status == 7 || application_status == 8){
						$('.view-edit').addClass('d-none');
					}else{
						$('.view-edit').removeClass('d-none');
					}
				}else{
					$('.view-edit').addClass('d-none');
				}

                $('#editApproverStatus').removeClass('d-none');
                $('#editApproverRemarks').removeClass('d-none');

                $('#edit_attachment').data('application-id', application_id);
                console.log('testval', $('#edit_attachment').data('application-id'));

                if($('#edit_attachment')[0].files.length > 0){
                    // Detect uploaded file (if any)
                    let uploadedFile = $('#edit_attachment')[0].files[0];
                    let filePath;

                    // Use Object URL for preview
                    filePath = URL.createObjectURL(uploadedFile);

                    console.log('Uploaded file detected:', uploadedFile);
                    $('#edit_attachment_excel').prop('required', true);

                    $('#editApproverButton').attr('data-filepath', filePath);
                    // Since new file is uploaded, clear esign details
                    $('#editApproverTable tbody').empty();
                    esign_details = []; // reset
                }else{
                    console.log('Existing file:');

                    filename = aidrc_filename.replace('modified_', '');
                    filePath = "http://rapidx/aidrc_v2/storage/app/public/file_attachments/"+filename;

                    $('#editApproverButton').attr('data-filepath', filePath);
                    esign_details.forEach(row => {
                        let status;

                        if(row.status == 1){
                            status = 'Approved';
                        }else if(row.status == 2){
                            status = 'Disapproved';
                        }else{
                            status = 'N/A';
                        }

                        if(row.remarks == null){
                            remarks = 'No Record';
                        }else{
                            remarks = row.remarks;
                        }

                        let coords = row.coordinates.split('|'); // "0.1885|0.1728" → [0.1885, 0.1728]
                        let coordinateText = `X: ${coords[0]}, Y: ${coords[1]}`;

                        var rowHtml = `
                            <tr>
                                <td id="approvalOrder-${row.approval_order}">${row.approval_order}</td>
                                <td>
                                    <select id="approver-${row.approval_order}" class="form-control form-control-sm select2bs5 SelectEditApprover"></select>
                                </td>
                                <td hidden>
                                    <input class="form-control form-control-sm" id="esignature-${row.approval_order}" data-signature value="">
                                </td>
                                <td id="pageNumber-${row.approval_order}">${row.page_no}</td>
                                <td id="coordinates-${row.approval_order}">${coordinateText}</td>
                                <td id="status-${row.approval_order}">${status}</td>
                                <td id="remarks-${row.approval_order}">${remarks}</td>
                                <td>
                                    <button type="button" class="btn btn-primary btn-sm previewPdfButton" data-row="${row.approval_order}" data-file-url="${filePath}">Preview PDF</button>
                                    <button type="button" class="btn btn-danger btn-sm editApproverDeleteRow">Delete</button>
                                </td>
                            </tr>
                        `;

                        $('#editApproverTable tbody').append(rowHtml);
                        // Populate SelectApprover dropdown
                        GetEsignApprover($('.SelectEditApprover').last(), row.approver_id);

                        $('.select2bs5').select2({
                            width: '100%',
                            theme: 'bootstrap-4'
                        });
                    });
                }

                // // Detect uploaded file (if any)
                // let uploadedFile = $('#edit_attachment')[0].files[0];
                // let filePath;

                // if (uploadedFile) {
                //     console.log('Uploaded file detected:', uploadedFile);

                //     // Use Object URL for preview
                //     filePath = URL.createObjectURL(uploadedFile);

                //     // Since new file is uploaded, clear esign details
                //     $('#editApproverTable tbody').empty();
                //     esign_details = []; // reset
                // }else{
                    // console.log('Existing file:');

                    // filename = aidrc_filename.replace('modified_', '');
                    // filePath = "http://rapidx/aidrc_v2/storage/app/public/file_attachments/"+filename;

                    // $('#editApproverButton').attr('data-filepath', filePath);
                    // esign_details.forEach(row => {
                    //     let status;

                    //     if(row.status == 1){
                    //         status = 'Approved';
                    //     }else if(row.status == 2){
                    //         status = 'Disapproved';
                    //     }else{
                    //         status = 'N/A';
                    //     }

                    //     if(row.remarks == null){
                    //         remarks = 'No Record';
                    //     }else{
                    //         remarks = row.remarks;
                    //     }

                    //     let coords = row.coordinates.split('|'); // "0.1885|0.1728" → [0.1885, 0.1728]
                    //     let coordinateText = `X: ${coords[0]}, Y: ${coords[1]}`;

                    //     var rowHtml = `
                    //         <tr>
                    //             <td id="approvalOrder-${row.approval_order}">${row.approval_order}</td>
                    //             <td>
                    //                 <select id="approver-${row.approval_order}" class="form-control form-control-sm select2bs5 SelectEditApprover"></select>
                    //             </td>
                    //             <td hidden>
                    //                 <input class="form-control form-control-sm" id="esignature-${row.approval_order}" data-signature value="">
                    //             </td>
                    //             <td id="pageNumber-${row.approval_order}">${row.page_no}</td>
                    //             <td id="coordinates-${row.approval_order}">${coordinateText}</td>
                    //             <td id="status-${row.approval_order}">${status}</td>
                    //             <td id="remarks-${row.approval_order}">${remarks}</td>
                    //             <td>
                    //                 <button type="button" class="btn btn-primary btn-sm previewPdfButton" data-row="${row.approval_order}" data-file-url="${filePath}">Preview PDF</button>
                    //                 <button type="button" class="btn btn-danger btn-sm editApproverDeleteRow">Delete</button>
                    //             </td>
                    //         </tr>
                    //     `;

                    //     $('#editApproverTable tbody').append(rowHtml);
                    //     // Populate SelectApprover dropdown
                    //     GetEsignApprover($('.SelectEditApprover').last(), row.approver_id);

                    //     $('.select2bs5').select2({
                    //         width: '100%',
                    //         theme: 'bootstrap-4'
                    //     });
                    // });
                // }

				//draw tables
				dt_view_affected_documents.draw();
				dt_view_dcc_validations.draw();
				dt_view_approvals.draw();
			}else{
				toastr.error('Error Loading Details');
			}
		},
		error: function(data, xhr, status){
            toastr.error('An error occured!\n' + 'Data: ' + data + "\n" + "XHR: " + xhr + "\n" + "Status: " + status);
        }
	});
}

function SubmitEditApplication(){
    let $form = $("#formEditApplication")[0]; // get raw form element

    // ✅ run native HTML5 validation before proceeding
    if (!$form.checkValidity()){
        $form.reportValidity(); // show the browser's validation popup
        toastr.error('Please upload an excel file before submitting.');
        return; // stop here if invalid
    }

	let form_data = new FormData($('#formEditApplication')[0]);
    let signatureData = [];

    $("#editApproverTable tbody tr").each(function(){
        let row = $(this);
        let rowIndex = row.find(".previewPdfButton").data("row"); // Get row number

        let approvalOrder = row.find(`#approvalOrder-${rowIndex}`).text().trim();
        let pageNumber = row.find(`#pageNumber-${rowIndex}`).text().trim();
        let coordinatesText = row.find(`#coordinates-${rowIndex}`).text().trim();
        let signaturePath = row.find(`#esignature-${rowIndex}`).val();
        let selectedApprover = row.find(`#approver-${rowIndex}`).val();
        let pdfwidth = $('#pdfPreview').data('pdfWidth');
        let pdfheight = $('#pdfPreview').data('pdfHeight');

        let canvas = $('#pdfPreview').find('canvas')[0];
        let CanvasWidth = 0;
        let CanvasHeight = 0;

        if (canvas) {
            CanvasWidth = canvas.width;
            CanvasHeight = canvas.height;
        }

        // let CanvasWidth = canvas.width;
        // let CanvasHeight = canvas.height;

        if(coordinatesText !== "No coordinates selected"){
            let matches = coordinatesText.match(/X:\s*([\d.]+),\s*Y:\s*([\d.]+)/);
            if (matches) {
                let x = parseFloat(matches[1]);
                let y = parseFloat(matches[2]);

                signatureData.push({
                    x: x,
                    y: y,
                    approval_order: parseInt(approvalOrder),
                    page: parseInt(pageNumber),
                    path: signaturePath,
                    approver: selectedApprover,
                    canvasWidth: CanvasWidth,
                    canvasHeight: CanvasHeight,
                    pdfWidth: pdfwidth,
                    pdfHeight: pdfheight,
                });
            }
        }else{
            signatureData.push({
                x: '',
                y: '',
                approval_order: parseInt(approvalOrder),
                page: parseInt(pageNumber),
                path: '',
                approver: selectedApprover,
                canvasWidth: CanvasWidth,
                canvasHeight: CanvasHeight,
                pdfWidth: pdfwidth,
                pdfHeight: pdfheight,
            });
        }
    });

    // After building signatureData
    let hasMissingCoordinates = false;

    signatureData.forEach(sig => {
        if (sig.approver && (sig.x === '' || sig.y === '' || sig.x === undefined || sig.y === undefined)) {
            hasMissingCoordinates = true;
        }
    });

    if (hasMissingCoordinates) {
        toastr.error("One or more selected approvers have no signature coordinates. Please click inside the PDF to place the signature.");
        return;
    }

    form_data.append("signatureData", JSON.stringify(signatureData)); // Append to formData

	$.ajax({
		url: "submit_new_edit_application",
		method: "post",
		processData: false,
		contentType: false,
    	data: form_data,
    	dataType: "json",
    	beforeSend: function(){
    	},
    	success: function(JsonObject){
    		if(JsonObject['result'] == 1){
    			toastr.success('Application Submitted!');
    			$('#edit_attachment').prop('disabled','disabled');
				$('#view_doc_category').prop('disabled','disabled');
				$('#view_doc_title').prop('readonly','readonly');
				$('#view_remarks').prop('readonly','readonly');

				$('#view_qc_head').prop('disabled','disabled');
				$('#view_eng_head').prop('disabled','disabled');
				$('#view_production_head').prop('disabled','disabled');
				$('#view_qs_inspector').prop('disabled','disabled');
				$('#view_section_head_approver').prop('disabled','disabled');
				$('.view_approver_priority').prop('disabled','disabled');

				$('#btnCancelEditApplication').prop('disabled','disabled');
				$('#btnSubmitEditApplication').prop('disabled','disabled');
				$('#btnCancelApplication').prop('disabled','disabled');

    			dt_applications.draw();
    			dt_view_dcc_validations.draw();
				dt_view_approvals.draw();

                $('#editApproverTable tbody').empty(); //CLARK Added

                $('#modalViewApplication').modal('hide');
				// dt_view_qs_validations.draw(); //CLARK COMMENT
				SendNewMailer(JsonObject['application_id']); //clark comment for now 07/05/2025 (TESTING)
    		}else{
       			toastr.error('Application Submission Failed!');

                // if (JsonObject['error']['edit_excel_file'] === undefined) {
                //     $("#edit_attachment_excel").removeClass('is-invalid');
                //     $("#edit_attachment_excel").attr('title', '');
                // } else {
                //     $("#edit_attachment_excel").addClass('is-invalid');
                //     $("#edit_attachment_excel").attr('title', response['error']['edit_excel_file']);
                // }

		        if(JsonObject['error']['view_doc_title'] === undefined){
		          $('#view_doc_title').removeClass('is-invalid');
		        }else{
		          $('#view_doc_title').addClass('is-invalid');
		        }

		        if(JsonObject['error']['view_doc_category'] === undefined){
		          $('#view_doc_category').removeClass('is-invalid');
		        }else{
		          $('#view_doc_category').addClass('is-invalid');
		        }

		        if(JsonObject['error']['view_remarks'] === undefined){
		          $('#view_remarks').removeClass('is-invalid');
		        }else{
		          $('#view_remarks').addClass('is-invalid');
		        }
    		}
    	},
    	error: function(data, xhr, status){
    		//$('#btnSubmitApplication').removeAttr('disabled');
            toastr.error('An error occured!\n' + 'Data: ' + data + "\n" + "XHR: " + xhr + "\n" + "Status: " + status);
        }

	});
}

function LoadAffectedDocDetails(affected_doc_id)
{
	$.ajax({

		url: "load_affected_documents_details",
		method: "get",
		data:
		{
			affected_doc_id: affected_doc_id
		},
		dataType: "json",
		beforeSend: function()
		{

		},
		success: function(JsonObject)
		{
			if(JsonObject['result'] == 1)
			{
				let affected_document_id = JsonObject['affected_document_details'][0].id;

				let document_number = JsonObject['affected_document_details'][0].document_number;
				let document_name = JsonObject['affected_document_details'][0].document_name;
				let document_revision_number = JsonObject['affected_document_details'][0].document_revision_number;

				let document_revision_due_date = JsonObject['affected_document_details'][0].document_revision_due_date;
				let person_in_charge = JsonObject['affected_document_details'][0].person_in_charge;
				let approver_type = JsonObject['affected_document_details'][0].approver_type;

				let document_remarks = JsonObject['affected_document_details'][0].document_remarks;
				let approver_remarks = JsonObject['affected_document_details'][0].approver_remarks;

				$('#edit_affected_doc_id').val(affected_document_id);
				$('#edit_affected_doc_no').val(document_number);
				$('#edit_affected_doc_title').val(document_name);
				$('#edit_affected_doc_rev_no').val(document_revision_number);
				$('#edit_affected_checkpoint_type').val(approver_type).trigger('change');
				$('#edit_affected_person_in_charge').val(person_in_charge).trigger('change');
				$('#edit_affected_rev_due_date').val(document_revision_due_date);
				$('#edit_affected_doc_remarks').val(document_remarks);
				$('#edit_affected_approver_remarks').val(approver_remarks);
			}
			else
			{
				toastr.error('Error Loading Details');
			}
		},
		error: function(data, xhr, status){
            toastr.error('An error occured!\n' + 'Data: ' + data + "\n" + "XHR: " + xhr + "\n" + "Status: " + status);
        }
	});
}

function DisapproveAffectedDocument(affected_doc_id, approver_remarks)
{
	$.ajax({

		url: "submit_disapprove_affected_document",
		method: "get",
		data:
		{
			affected_doc_id: affected_doc_id,
			approver_remarks: approver_remarks,
		},
		dataType: "json",
		beforeSend: function()
		{

		},
		success: function(JsonObject)
		{
			if(JsonObject['result'] == 1)
    		{
    			toastr.warning('Affected Document Disapproved!');
    			$('#modalEditaffectedDocumentDetails').modal('hide');
    			$('#formEditAffectedDocument')[0].reset();

  				dt_head_approval_documents.draw();
    		}
    		else
    		{
    			toastr.error('Affected Document Revision Failed!');
    		}
		},
		error: function(data, xhr, status){
            toastr.error('An error occured!\n' + 'Data: ' + data + "\n" + "XHR: " + xhr + "\n" + "Status: " + status);
        }
	});
}


function SubmitEditAffectedDocument(){
	$.ajax({
		url: "submit_edit_affected_document",
		method: "post",
		data: $('#formEditAffectedDocument').serialize(),
		dataType: "json",
		beforeSend: function()
		{

		},
		success: function(JsonObject)
		{
			if(JsonObject['result'] == 1){
    			toastr.success('Affected Document Revised!');
    			$('#modalEditaffectedDocumentDetails').modal('hide');
    			$('#formEditAffectedDocument')[0].reset();

  				dt_head_approval_documents.draw();
    		}else{
    			toastr.error('Affected Document Revision Failed!');
    		}
		},
		error: function(data, xhr, status){
            toastr.error('An error occured!\n' + 'Data: ' + data + "\n" + "XHR: " + xhr + "\n" + "Status: " + status);
        }
	});
}

function SendNewMailer(application_id){
	$.ajax({
		url: "send_new_mailer",
		method: "get",
		data:
		{
			application_id: application_id
		},
		dataType: "json",
		beforeSend: function(){

		},
		success: function(JsonObject){
			if(JsonObject['result'] == 1){
				toastr.success('Mail Sent to Recipients!');
			}else{
				toastr.error('Error Sending Mail!');
			}
		},
		error: function(data, xhr, status){
            toastr.error('An error occured!\n' + 'Data: ' + data + "\n" + "XHR: " + xhr + "\n" + "Status: " + status);
        }
	});
}
