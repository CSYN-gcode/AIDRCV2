function SubmitDccEditDocument()
{
	$.ajax({

		url: "submit_dcc_edit_document",
		method: "post",
		data: $('#formDccEditDocument').serialize(),
		dataType: "json",
		beforeSend: function()
		{

		},
		success: function(JsonObject)
		{
			if(JsonObject['result'] == 1)
    		{
    			toastr.success('Document Edit Success!');
  				dt_applications_master_list.draw();
  				$('#view_doc_no').removeClass('is-invalid');
				$('#view_doc_title').removeClass('is-invalid');
    		}
    		else
    		{
    			toastr.error('Document Edit Failed!');

    			if(JsonObject['error']['view_doc_no'] === undefined)
		        {
		          $('#view_doc_no').removeClass('is-invalid');
		        }
		        else
		        {
		          $('#view_doc_no').addClass('is-invalid');
		        }

		        if(JsonObject['error']['view_doc_title'] === undefined)
		        {
		          $('#view_doc_title').removeClass('is-invalid');
		        }
		        else
		        {
		          $('#view_doc_title').addClass('is-invalid');
		        }
    		}
		},
		error: function(data, xhr, status){
            toastr.error('An error occured!\n' + 'Data: ' + data + "\n" + "XHR: " + xhr + "\n" + "Status: " + status);
        }
	});
}

function LinkCheckpointsToApplication(array_documents, application_id)
{
	$.ajax({

		url: "link_checkpoints_to_application",
		method: "get",
		data:
		{
			application_id: application_id,
			array_documents: array_documents,
		},
		dataType: "json",
		beforeSend: function()
		{

		},
		success: function(JsonObject)
		{
			if(JsonObject['result'] == 1)
			{
				toastr.success('Successfully Saved Affected Documents!');
				array_documents = [];
			}
			else
			{
				toastr.error('Saving Affected Documents Failed!');
			}
		},
		error: function(data, xhr, status){
            toastr.error('An error occured!\n' + 'Data: ' + data + "\n" + "XHR: " + xhr + "\n" + "Status: " + status);
        }

	});
}

function NewCancelApplication(application_id)
{
	$.ajax({

		url: "new_cancel_application",
		method: "get",
		data:
		{
			application_id: application_id,
		},
		dataType: "json",
		beforeSend: function()
		{

		},
		success: function(JsonObject)
		{
			if(JsonObject['result'] == 1)
			{
				$('#modalViewApplication').modal('hide');
				toastr.warning('Cancelled Application!');
				dt_applications.draw();

				SendNewMailer(application_id);
			}
			else
			{
				toastr.error('Saving Affected Documents Failed!');
			}
		},
		error: function(data, xhr, status){
            toastr.error('An error occured!\n' + 'Data: ' + data + "\n" + "XHR: " + xhr + "\n" + "Status: " + status);
        }

	});
}

function RetrieveDocumentsForApproval1(application_id, approving_as, array_documents)
{
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
		beforeSend: function()
		{

		},
		success: function(JsonObject)
		{
			if(JsonObject['result'] == 1)
			{
				array_documents = JsonObject['array_documents'];
				temp_array_documents = JsonObject['array_documents'];
			}

			dt_head_approval_documents.draw();
		},
		error: function(data, xhr, status){
            toastr.error('An error occured!\n' + 'Data: ' + data + "\n" + "XHR: " + xhr + "\n" + "Status: " + status);
        }

	});


   return temp_array_documents;
}

function SubmitAffectedDocument(array_documents)
{
	$.ajax({

		url: "submit_affected_document",
		method: "post",
		data: $('#formAffectedDocument').serialize(),
		dataType: "json",
		beforeSend: function()
		{

		},
		success: function(JsonObject)
		{
			if(JsonObject['result'] == 1)
    		{
    			toastr.success('Application Validated!');
    			$('#modalAddaffectedDocumentDetails').modal('hide');
    			$('#formAffectedDocument')[0].reset();

    			array_documents.push(JsonObject['affected_document_id']);
    			console.log(array_documents);

  				dt_affected_documents.draw();
  				dt_qs_validations.draw();
  				dt_head_approval_documents.draw();
    			//dt_applications.draw();
    		}
    		else
    		{
    			toastr.error('Applicaiton Validation Failed!');

    			if(JsonObject['error']['affected_doc_no'] === undefined)
		        {
		          $('#affected_doc_no').removeClass('is-invalid');
		        }
		        else
		        {
		          $('#affected_doc_no').addClass('is-invalid');
		        }

		        if(JsonObject['error']['affected_doc_title'] === undefined)
		        {
		          $('#affected_doc_title').removeClass('is-invalid');
		        }
		        else
		        {
		          $('#affected_doc_title').addClass('is-invalid');
		        }

		        if(JsonObject['error']['affected_doc_rev_no'] === undefined)
		        {
		          $('#affected_doc_rev_no').removeClass('is-invalid');
		        }
		        else
		        {
		          $('#affected_doc_rev_no').addClass('is-invalid');
		        }

		        if(JsonObject['error']['affected_checkpoint_type'] === undefined)
		        {
		          $('#affected_checkpoint_type').removeClass('is-invalid');
		        }
		        else
		        {
		          $('#affected_checkpoint_type').addClass('is-invalid');
		        }

		        if(JsonObject['error']['affected_person_in_charge'] === undefined)
		        {
		          $('#affected_person_in_charge').removeClass('is-invalid');
		        }
		        else
		        {
		          $('#affected_person_in_charge').addClass('is-invalid');
		        }

		        if(JsonObject['error']['affected_rev_due_date'] === undefined)
		        {
		          $('#affected_rev_due_date').removeClass('is-invalid');
		        }
		        else
		        {
		          $('#affected_rev_due_date').addClass('is-invalid');
		        }
    		}
		},
		error: function(data, xhr, status){
            toastr.error('An error occured!\n' + 'Data: ' + data + "\n" + "XHR: " + xhr + "\n" + "Status: " + status);
        }
	});
}

function LoadAddDocumentDetails(active_doc_id, addition_type)
{
	$.ajax({

		url: "load_acdcs_document_details",
		method: "get",
		data:
		{
			active_doc_id: active_doc_id
		},
		dataType: "json",
		beforeSend: function()
		{

		},
		success: function(JsonObject)
		{
			if(JsonObject['result'] == 1)
			{
				let document_number = JsonObject['document_details'][0].doc_no;
				let document_title = JsonObject['document_details'][0].doc_title;
                //clark modification 05282024
				let revision_number = JsonObject['document_details'][0].rev_no;
                // let rev_datatype = typeof revision_number;
                // console.log('revision_number_datatype', rev_datatype);
                // if(rev_datatype == 'number'){
                //     revision_number = parseInt(revision_number) + 1;
                // }else{
                if($.isNumeric(revision_number)){
                    revision_number = parseInt(revision_number) + 1;
                }else{
                    revision_number = 0;
                }
                //clark modification 06142024
                console.log('revision_number', revision_number);

				CheckExistingAidrcApplicationRevised(document_number, revision_number, document_title);
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

function LoadDccDocumentDetails(active_doc_id)
{
	$.ajax({

		url: "load_acdcs_document_details",
		method: "get",
		data:
		{
			active_doc_id: active_doc_id
		},
		dataType: "json",
		beforeSend: function()
		{

		},
		success: function(JsonObject)
		{
			if(JsonObject['result'] == 1)
			{
				let document_number = JsonObject['document_details'][0].doc_no;
				let document_title = JsonObject['document_details'][0].doc_title;
				let revision_number = JsonObject['document_details'][0].rev_no;

				$('#view_doc_no').val(document_number);
				$('#view_doc_title').val(document_title);
				$('#view_doc_rev_no').val(revision_number);

				$('#modalSearchDocumentDetails').modal('hide');
				toastr.success('Document Details Added!');
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

function LoadAffectedDocumentDetails(active_doc_id, addition_type)
{
	$.ajax({

		url: "load_acdcs_document_details",
		method: "get",
		data:
		{
			active_doc_id: active_doc_id
		},
		dataType: "json",
		beforeSend: function()
		{

		},
		success: function(JsonObject)
		{
			if(JsonObject['result'] == 1)
			{
				toastr.success('Successfully Loaded Document Details!');
				//$('#modalAddaffectedDocumentDetails').modal('hide');

				let document_number = JsonObject['document_details'][0].doc_no;
				let document_title = JsonObject['document_details'][0].doc_title;
				let revision_number = JsonObject['document_details'][0].rev_no;

				revision_number = parseInt(revision_number) + 1;

				$('#affected_doc_no').val(document_number);
				$('#affected_doc_title').val(document_title);
				$('#affected_doc_rev_no').val(revision_number);

				if(addition_type == 4)
				{
					$('#affected_checkpoint_type').val(1).trigger('change');
					$('.class-affected-doc').removeAttr('hidden');
					$('.class-checkpoint').prop('hidden', 'hidden');
					$('.class-checkpoint-preprod').prop('hidden', 'hidden');
				}
				else if(addition_type == 2)
				{
					$('#affected_checkpoint_type').prop('selectedIndex',0);
					$('.class-affected-doc').prop('hidden', 'hidden');
					$('.class-checkpoint').removeAttr('hidden');
					$('.class-checkpoint-preprod').prop('hidden', 'hidden');
				}
				else
				{	$('#affected_checkpoint_type').prop('selectedIndex',0);
					$('.class-affected-doc').removeAttr('hidden');
					$('.class-checkpoint').removeAttr('hidden');
					$('.class-checkpoint-preprod').prop('hidden', 'hidden');
				}
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

function CheckExistingAidrcApplicationRevised(document_number, revision_number, document_title)
{
	$.ajax({

		url: "check_existing_aidrc_application",
		method: "get",
		data:
		{
			document_number: document_number,
			revision_number: revision_number
		},
		dataType: "json",
		beforeSend: function()
		{

		},
		success: function(JsonObject)
		{
			if(JsonObject['result'] == 1)
			{
				toastr.error("There's already an existing application for the document!");
			}
			else
			{
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

function SubmitNewApplication(array_documents)
{
	let form_data = new FormData($('#formAddApplication')[0]);

	$.ajax({

		url: "submit_new_application",
		method: "post",
		processData: false,
		contentType: false,
    	data: form_data,
    	dataType: "json",
    	beforeSend: function()
    	{
    		//$('#btnSubmitApplication').prop('disabled','disabled');
    	},
    	success: function(JsonObject)
    	{
    		if(JsonObject['result'] == 1)
    		{
    			toastr.success('Application Submitted!');
    			$('#modalAddApplication').modal('hide');

    			if(array_documents.length > 0)
    			{
    				LinkCheckpointsToApplication(array_documents, JsonObject['application_id']);
    			}
    			else
    			{
    				array_documents = [];
    			}

    			dt_applications.draw();
    			dt_affected_documents.draw();

    			SendNewMailer(JsonObject['application_id']);
    		}
    		else
    		{
       			toastr.success('Application Submission Failed!');

    			if(JsonObject['error']['add_attachment'] === undefined)
		        {
		          $('#add_attachment').removeClass('is-invalid');
		        }
		        else
		        {
		          $('#add_attachment').addClass('is-invalid');
		        }

		        if(JsonObject['error']['add_doc_category'] === undefined)
		        {
		          $('#add_doc_category').removeClass('is-invalid');
		        }
		        else
		        {
		          $('#add_doc_category').addClass('is-invalid');
		        }

		        if(JsonObject['error']['add_doc_type'] === undefined)
		        {
		          $('#add_doc_type').removeClass('is-invalid');
		        }
		        else
		        {
		          $('#add_doc_type').addClass('is-invalid');
		        }

		        if(JsonObject['error']['add_for_group'] === undefined)
		        {
		          $('#add_for_group').removeClass('is-invalid');
		        }
		        else
		        {
		          $('#add_for_group').addClass('is-invalid');
		        }

		        if(JsonObject['error']['add_department'] === undefined)
		        {
		          $('#add_department').removeClass('is-invalid');
		        }
		        else
		        {
		          $('#add_department').addClass('is-invalid');
		        }

		        if(JsonObject['error']['add_doc_no'] === undefined)
		        {
		          $('#add_doc_no').removeClass('is-invalid');
		        }
		        else
		        {
		          $('#add_doc_no').addClass('is-invalid');
		        }

		        if(JsonObject['error']['add_doc_title'] === undefined)
		        {
		          $('#add_doc_title').removeClass('is-invalid');
		        }
		        else
		        {
		          $('#add_doc_title').addClass('is-invalid');
		        }

		        if(JsonObject['error']['add_doc_rev_no'] === undefined)
		        {
		          $('#add_doc_rev_no').removeClass('is-invalid');
		        }
		        else
		        {
		          $('#add_doc_rev_no').addClass('is-invalid');
		        }

		        if(JsonObject['error']['add_section_head_approver'] === undefined)
		        {
		          $('#add_section_head_approver').removeClass('is-invalid');
		        }
		        else
		        {
		          $('#add_section_head_approver').addClass('is-invalid');
		        }

		        if(JsonObject['error']['add_production_head'] === undefined)
		        {
		          $('#add_production_head').removeClass('is-invalid');
		        }
		        else
		        {
		          $('#add_production_head').addClass('is-invalid');
		        }

		        if(JsonObject['error']['add_qc_head'] === undefined)
		        {
		          $('#add_qc_head').removeClass('is-invalid');
		        }
		        else
		        {
		          $('#add_qc_head').addClass('is-invalid');
		        }

		        if(JsonObject['error']['add_eng_head'] === undefined)
		        {
		          $('#add_eng_head').removeClass('is-invalid');
		        }
		        else
		        {
		          $('#add_eng_head').addClass('is-invalid');
		        }

		        if(JsonObject['error']['add_approver_priority'] === undefined)
		        {

		        }
		        else
		        {
		          toastr.error('Please select your Initial Approver!');
		        }


		        if(JsonObject['error']['add_qs_inspector'] === undefined)
		        {
		          $('#add_qs_inspector').removeClass('is-invalid');
		        }
		        else
		        {
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

function SubmitQsValidation(array_documents)
{
	$.ajax({

		url: "submit_new_qs_validation",
		method: "post",
		data: $('#formQsValidation').serialize(),
		dataType: "json",
		beforeSend: function()
		{

		},
		success: function(JsonObject)
		{
			if(JsonObject['result'] == 1)
    		{
    			toastr.success('QS Validation Submitted!');
    			$('#modalQSValidation').modal('hide');
    			$('#formQsValidation')[0].reset();

    			if(array_documents.length > 0)
    			{
    				LinkCheckpointsToApplication(array_documents, JsonObject['application_id']);
    			}
    			else
    			{
    				array_documents = [];
    			}

    			dt_applications.draw();
    			dt_qs_validations.draw();

    			SendNewMailer(JsonObject['application_id']);
    		}
    		else
    		{
    			toastr.error('Saving QS Validation Failed!');

    			if(JsonObject['error']['qs_validation_remarks'] === undefined)
		        {
		          $('#qs_validation_remarks').removeClass('is-invalid');
		        }
		        else
		        {
		          $('#qs_validation_remarks').addClass('is-invalid');
		        }
    		}
		},
		error: function(data, xhr, status){
            toastr.error('An error occured!\n' + 'Data: ' + data + "\n" + "XHR: " + xhr + "\n" + "Status: " + status);
        }
	});
}

function LoadHeadApplicationDetails(application_id, approving_as)
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
					$('#head_doc_no').val(document_number);
				}
				else
				{
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

				$('#head_application_id').val(application_id);
				$('#head_approving_as').val(approving_as);
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

function SubmitHeadApproval(array_documents)
{
	$.ajax({

		url: "submit_new_head_approval",
		method: "post",
		data: $('#formHeadApproval').serialize(),
		dataType: "json",
		beforeSend: function()
		{

		},
		success: function(JsonObject)
		{
			if(JsonObject['result'] == 1)
    		{
    			toastr.success('Application Review Submitted!');
    			$('#modalHeadApprover').modal('hide');
    			$('#formHeadApproval')[0].reset();

    			if(array_documents.length > 0)
    			{
    				LinkCheckpointsToApplication(array_documents, JsonObject['application_id']);
    			}
    			else
    			{
    				array_documents = [];
    			}

    			dt_applications.draw();
    			dt_head_approval_documents.draw();

   				SendNewMailer(JsonObject['application_id']);
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

function SubmitDccValidation()
{
	$.ajax({

		url: "submit_new_dcc_validation",
		method: "post",
		data: $('#formDccValidation').serialize(),
		dataType: "json",
		beforeSend: function()
		{

		},
		success: function(JsonObject)
		{
			if(JsonObject['result'] == 1)
    		{
    			toastr.success('Application Validated!');
    			$('#modalDccValidations').modal('hide');
    			$('#formDccValidation')[0].reset();

    			dt_applications.draw();

    			SendNewMailer(JsonObject['application_id']);
    		}
    		else
    		{
    			toastr.error('Applicaiton Validation Failed!');

    			if(JsonObject['error']['dcc_checkpoint_similar'] === undefined)
		        {
		          $('#dcc_checkpoint_similar').removeClass('is-invalid');
		        }
		        else
		        {
		          $('#dcc_checkpoint_similar').addClass('is-invalid');
		        }

		        if(JsonObject['error']['dcc_checkpoint_alignment'] === undefined)
		        {
		          $('#dcc_checkpoint_alignment').removeClass('is-invalid');
		        }
		        else
		        {
		          $('#dcc_checkpoint_alignment').addClass('is-invalid');
		        }

		        if(JsonObject['error']['dcc_checkpoint_standard'] === undefined)
		        {
		          $('#dcc_checkpoint_standard').removeClass('is-invalid');
		        }
		        else
		        {
		          $('#dcc_checkpoint_standard').addClass('is-invalid');
		        }

		        if(JsonObject['error']['dcc_validation_judgement'] === undefined)
		        {
		          $('#dcc_validation_judgement').removeClass('is-invalid');
		        }
		        else
		        {
		          $('#dcc_validation_judgement').addClass('is-invalid');
		        }
    		}
		},
		error: function(data, xhr, status){
            toastr.error('An error occured!\n' + 'Data: ' + data + "\n" + "XHR: " + xhr + "\n" + "Status: " + status);
        }
	});
}

function LoadViewApplicationDetails(application_id, view_edit)
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
					$('#view_doc_no').val(document_number);
				}
				else
				{
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
				$('#view_remarks').val(originator_remarks);

				$('#view_qc_head').val(application_qc_head);
				$('#view_eng_head').val(application_eng_head);
				$('#view_production_head').val(application_prod_head);
				$('#view_qs_inspector').val(qs_inspector);

				$('#view_section_head_approver').val(application_section_head);

				$('#view_application_id').val(application_id);

				$("input[name=view_approver_priority][value=" + approver_priority + "]").prop('checked',true);

				if(view_edit == 1)
				{
					if(JsonObject['application_details'][0].status == 10 || JsonObject['application_details'][0].status == 9)
					{
						$('.view-edit').addClass('d-none');
					}
					else
					{
						$('.view-edit').removeClass('d-none');

						if(for_group == 1)
						{
							$('.view-operations-approver').removeClass('d-none');
							$('.view-sg-approver').addClass('d-none');
						}
						else
						{
							$('.view-operations-approver').addClass('d-none');
							$('.view-sg-approver').removeClass('d-none');
						}
					}
				}
				else
				{
					$('.view-edit').addClass('d-none');
				}

				//draw tables
				dt_view_affected_documents.draw();
				dt_view_dcc_validations.draw();
				dt_view_approvals.draw();
				dt_view_qs_validations.draw();
				//dt_view_revisions.draw();
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

function SubmitEditApplication()
{
	let form_data = new FormData($('#formEditApplication')[0]);

	$.ajax({

		url: "submit_new_edit_application",
		method: "post",
		processData: false,
		contentType: false,
    	data: form_data,
    	dataType: "json",
    	beforeSend: function()
    	{
    		//$('#btnSubmitApplication').prop('disabled','disabled');
    	},
    	success: function(JsonObject)
    	{
    		if(JsonObject['result'] == 1)
    		{
    			toastr.success('Application Submitted!');
    			//$('#modalViewApplication').modal('hide');

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
				dt_view_qs_validations.draw();
				//dt_view_revisions.draw();

				SendNewMailer(JsonObject['application_id']);
    		}
    		else
    		{
       			toastr.error('Application Submission Failed!');

    			if(JsonObject['error']['edit_attachment'] === undefined)
		        {
		          $('#edit_attachment').removeClass('is-invalid');
		        }
		        else
		        {
		          $('#edit_attachment').addClass('is-invalid');
		        }

		        if(JsonObject['error']['view_doc_title'] === undefined)
		        {
		          $('#view_doc_title').removeClass('is-invalid');
		        }
		        else
		        {
		          $('#view_doc_title').addClass('is-invalid');
		        }

		        if(JsonObject['error']['view_doc_category'] === undefined)
		        {
		          $('#view_doc_category').removeClass('is-invalid');
		        }
		        else
		        {
		          $('#view_doc_category').addClass('is-invalid');
		        }

		        if(JsonObject['error']['view_remarks'] === undefined)
		        {
		          $('#view_remarks').removeClass('is-invalid');
		        }
		        else
		        {
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


function SubmitEditAffectedDocument()
{
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
			if(JsonObject['result'] == 1)
    		{
    			toastr.success('Affected Document Revised!');
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

function SendNewMailer(application_id)
{
	$.ajax({

		url: "send_new_mailer",
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
				toastr.success('Mail Sent to Recipients!');
			}
			else
			{
				toastr.error('Error Sending Mail!');
			}
		},
		error: function(data, xhr, status){
            toastr.error('An error occured!\n' + 'Data: ' + data + "\n" + "XHR: " + xhr + "\n" + "Status: " + status);
        }
	});
}
