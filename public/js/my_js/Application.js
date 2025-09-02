

function CheckExistingAidrcApplicationNew(document_title)
{
	$.ajax({

		url: "check_existing_aidrc_application_new",
		method: "get",
		data:
		{
			document_title: document_title,
		},
		dataType: "json",
		beforeSend: function(){

		},
		success: function(JsonObject){
			if(JsonObject['result'] == 1){
				toastr.error("There's already an existing application for the document!");
			}else{
				$('#formAddApplication').submit();
				console.log('no same title');
			}
		},
		error: function(data, xhr, status){
            toastr.error('An error occured!\n' + 'Data: ' + data + "\n" + "XHR: " + xhr + "\n" + "Status: " + status);
        }
	});
}

function LoadAcdcsLayout()
{
	$.ajax({

		url: "load_acdcs_layout",
		method: "get",
		data:
		{

		},
		dataType: "json",
		beforeSend: function()
		{

		},
		success: function(JsonObject)
		{
			if(JsonObject['result'] == 1)
			{
				$('.acdcs_layout').removeClass('d-none');
			}
			else
			{
				$('.acdcs_layout').addClass('d-none');
			}
		},
		error: function(data, xhr, status){
            toastr.error('An error occured!\n' + 'Data: ' + data + "\n" + "XHR: " + xhr + "\n" + "Status: " + status);
        }
	});
}


function LoadMasterListDetails(active_doc_id)
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
				$('#modalSearchDocumentDetails').modal('hide');

				let document_number = JsonObject['document_details'][0].doc_no;
				let document_title = JsonObject['document_details'][0].doc_title;

				$('#ml_doc_no').val(document_number);
				$('#ml_doc_title').val(document_title);
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

function LoadEditDocumentDetails(active_doc_id)
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
				$('#modalEditDocumentDetails').modal('hide');

				let document_number = JsonObject['document_details'][0].doc_no;
				let document_title = JsonObject['document_details'][0].doc_title;
				let revision_number = JsonObject['document_details'][0].rev_no;

				revision_number = parseInt(revision_number) + 1;

				$('#edit_doc_no').val(document_number);
				$('#edit_doc_title').val(document_title);
				$('#edit_doc_rev_no').val(revision_number);
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



function PushToAffectedDocumentsArray(array_object, array_docs)
{
	if(array_docs.some(docs => docs.document_pkid === array_object.document_pkid))
	{
		toastr.error('Affected Documents Already Added');
	}
	else
	{
		array_docs.push(array_object);
		dt_affected_documents.draw();

		dt_sec_additional_affected_documents.draw();
		dt_documents_from_affected_approver.draw();

		$('#modalSecAddaffectedDocumentDetails').modal('hide');
		$('#modalSecAddAffectedDocuments').modal('hide');

		$('#modalAddDocumentDetailsAffectedApprover').modal('hide');
		$('#modalApproverAffectedDocumentDetails').modal('hide');

		$('#modalAddaffectedDocumentDetails').modal('hide');
		$('#modalAddAffectedDocuments').modal('hide');

		toastr.success('Affected Document Added!');

		console.log(array_docs);
	}
}

function SubmitAddApplication(array_affected_documents){
	let form_data = new FormData($('#formAddApplication')[0]);

	$.ajax({
		url: "submit_add_application",
		method: "post",
		processData: false,
        
		contentType: false,
    	data: form_data,
    	dataType: "json",
    	beforeSend: function()
    	{
    		$('#btnSubmitApplication').prop('disabled','disabled');
    	},
    	success: function(JsonObject)
    	{
    		$('#btnSubmitApplication').removeAttr('disabled');

    		if(JsonObject['result'] == 1){
                // document.activeElement.blur();
                $('#pdfPreviewModal').modal('hide');
    			$('#modalAddApplication').modal('hide');
    			$('#formAddApplication')[0].reset();

    			dt_applications.draw();

    			SendMailer(JsonObject['application_id']);

    			SubmitApplicationAffectedDocuments(array_affected_documents, JsonObject['application_id'], JsonObject['approver']);

    			toastr.success('Application Saved!');
    		}
    		else
    		{
    			toastr.error('Saving Application Error!');

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

		        if(JsonObject['error']['add_application_approver'] === undefined)
		        {
		          $('#add_application_approver').removeClass('is-invalid');
		        }
		        else
		        {
		          $('#add_application_approver').addClass('is-invalid');
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
    		$('#btnSubmitApplication').removeAttr('disabled');
            toastr.error('An error occured!\n' + 'Data: ' + data + "\n" + "XHR: " + xhr + "\n" + "Status: " + status);
        }

    });
}

function SubmitApplicationAffectedDocuments(array_affected_document, application_id, approver)
{
	$.ajax({

		url: "submit_application_affected_documents",
		method: "get",
		data:
		{
			array_affected_document: array_affected_document,
			application_id: application_id,
			approver: approver,
		},
		dataType: "json",
		beforeSend: function()
		{

		},
		success: function(JsonObject)
		{
			if(JsonObject['result'] == 1)
			{

			}
			else
			{
				toastr.error('Error Saving Affected Documents!');
			}
		},
		error: function(data, xhr, status){
    		$('#btnSubmitApplication').removeAttr('disabled');
            toastr.error('An error occured!\n' + 'Data: ' + data + "\n" + "XHR: " + xhr + "\n" + "Status: " + status);
        }

	});
}

function LoadSectionHeadApplicationDetails(application_id)
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
				let aidrc_application_id = JsonObject['application_details'][0].id;
				let document_number = JsonObject['application_details'][0].document_number;
				let document_title = JsonObject['application_details'][0].document_name;
				let revision_number = JsonObject['application_details'][0].document_revision_number;
				let document_category = JsonObject['application_details'][0].document_category;
				let document_type = JsonObject['application_details'][0].document_type;
				let for_group = JsonObject['application_details'][0].for_group;
				let department = JsonObject['application_details'][0].department;
				let section_head = JsonObject['application_details'][0].application_section_head;

				$('#sec_doc_category').val(document_category).trigger('change');
				$('#sec_doc_type').val(document_type).trigger('change');
				$('#sec_for_group').val(for_group).trigger('change');
				$('#sec_department').val(department).trigger('change');

				$('#sec_doc_no').val(document_number);
				$('#sec_doc_title').val(document_title);
				$('#sec_doc_rev_no').val(revision_number);

				$('#sec_hidden_application_id').val(aidrc_application_id);
				$('#sec_hidden_approver_id').val(section_head);

				//draw table
				dt_sec_affected_documents.draw();
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

function SubmitSectionHeadApproval(array_edit_docs, array_add_docs){
	$.ajax({
		url: "submit_section_head_approval",
		method: "post",
		data: $('#formSectionHeadApproval').serialize(),
		dataType: "json",
		beforeSend: function()
		{
			$('#btnApproveApplication').prop('disabled','disabled');
			$('#btnDispproveApplication').prop('disabled','disabled');
		},
		success: function(JsonObject)
		{
			$('#btnApproveApplication').removeAttr('disabled');
			$('#btnDispproveApplication').removeAttr('disabled');

			array_add_docs = [];

			if(JsonObject['result'] == 1)
			{
				let application_id = $('#sec_hidden_application_id').val();

				$('#modalApproveApplication').modal('hide');
				$('#formSectionHeadApproval')[0].reset();


				SendMailer(application_id);

				SubmitSectionHeadAffectedDocuments(application_id, array_edit_docs)

				//draw table
				dt_applications.draw();
			}
			else
			{
				toastr.error('Error Saving Details');
			}
		},
		error: function(data, xhr, status){
			$('#btnApproveApplication').removeAttr('disabled');
			$('#btnDispproveApplication').removeAttr('disabled');
            toastr.error('An error occured!\n' + 'Data: ' + data + "\n" + "XHR: " + xhr + "\n" + "Status: " + status);
        }



	});
}

function SubmitSectionHeadAffectedDocuments(application_id, array_edit_docs)
{
	$.ajax({

		url: "submit_section_head_affected_documents",
		method: "get",
		data: {

			application_id: application_id,
			array_edit_docs: array_edit_docs,
		},
		dataType: "json",
		beforeSend: function()
		{

		},
		success: function(JsonObject)
		{
			if(JsonObject['result'] == 1)
			{
				toastr.success('Saved Affected Document Changes!');
			}
			else
			{
				toastr.error('Error Saving Affected Documents. Contact local 205/208!');
			}
		},
		error: function(data, xhr, status){
            toastr.error('An error occured!\n' + 'Data: ' + data + "\n" + "XHR: " + xhr + "\n" + "Status: " + status);
        }
	});
}

function LoadQsValidationDetails(application_id)
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
				let aidrc_application_id = JsonObject['application_details'][0].id;
				let document_number = JsonObject['application_details'][0].document_number;
				let document_title = JsonObject['application_details'][0].document_name;
				let revision_number = JsonObject['application_details'][0].document_revision_number;
				let document_category = JsonObject['application_details'][0].document_category;
				let document_type = JsonObject['application_details'][0].document_type;
				let for_group = JsonObject['application_details'][0].for_group;
				let department = JsonObject['application_details'][0].department;

				$('#qs_doc_category').val(document_category).trigger('change');
				$('#qs_doc_type').val(document_type).trigger('change');
				$('#qs_for_group').val(for_group).trigger('change');
				$('#qs_department').val(department).trigger('change');

				$('#qs_doc_no').val(document_number);
				$('#qs_doc_title').val(document_title);
				$('#qs_doc_rev_no').val(revision_number);

				$('#qs_hidden_application_id').val(aidrc_application_id);

				//draw table
				dt_qs_inspection_checkpoints.draw();
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

function LoadQsDocumentDetails(active_doc_id)
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
				$('#modalAddDocumentDetailsQS').modal('hide');

				let document_pkid = JsonObject['document_details'][0].pkid;
				let document_number = JsonObject['document_details'][0].doc_no;
				let document_title = JsonObject['document_details'][0].doc_title;
				let revision_number = JsonObject['document_details'][0].rev_no;

				revision_number = parseInt(revision_number) + 1;

				$('#checkpoint_hidden_doc_id').val(document_pkid);
				$('#checkpoint_doc_no').val(document_number);
				$('#checkpoint_doc_title').val(document_title);
				$('#checkpoint_doc_rev_no').val(revision_number);
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

function SubmitQsValidation(array_affected_documents){
	$.ajax({
		url: "submit_qs_validations",
		method: "post",
		data: $('#formQSValidation').serialize(),
		dataType: "json",
		beforeSend: function(){
			$('#btnSubmitQSValidation').prop('disabled','disabled');
		},
		success: function(JsonObject){
			$('#btnSubmitQSValidation').removeAttr('disabled');

			if(JsonObject['result'] == 1){
				let application_id = $('#qs_hidden_application_id').val();

				array_affected_documents = [];
				$('#modalQSValidation').modal('hide');
				$('#formQSValidation')[0].reset();

				toastr.success('Saving Successful!');

				CheckApplicationAffectedDocumentStatus(application_id);
				SendMailer(application_id);

				//draw table
				dt_applications.draw();
			}else{
				toastr.error('Error Saving Details');

				if(JsonObject['error']['qs_remarks'] === undefined)
		        {
		          $('#qs_remarks').removeClass('is-invalid');
		        }
		        else
		        {
		          $('#qs_remarks').addClass('is-invalid');
		        }
			}
		},
		error: function(data, xhr, status){
			$('#btnSubmitQSValidation').removeAttr('disabled');
            toastr.error('An error occured!\n' + 'Data: ' + data + "\n" + "XHR: " + xhr + "\n" + "Status: " + status);
        }
	});
}

function SubmitQsValidationCheckpoints(array_affected_documents, application_id){
	$.ajax({

		url: "submit_qs_validations_checkpoints",
		method: "get",
		data:
	    {
			array_affected_documents: array_affected_documents,
			application_id: application_id
		},
		dataType: "json",
		beforeSend: function(){

		},
		success: function(JsonObject){
			if(JsonObject['result'] == 1){
				toastr.success('Should be sending e-mail!');
			}else{
				toastr.error('Error Saving Details');
			}
		},
		error: function(data, xhr, status){

            toastr.error('An error occured!\n' + 'Data: ' + data + "\n" + "XHR: " + xhr + "\n" + "Status: " + status);
        }
	});
}

function LoadAffectedDocumentApproval(affected_doc_id){
	$.ajax({

		url: "load_affected_document_details",
		method: "get",
		data:
		{
			affected_doc_id: affected_doc_id,
		},
		dataType: "json",
		beforeSend: function(){

		},
		success: function(JsonObject){
			if(JsonObject['result'] == 1){
				let document_category = JsonObject['document_details'][0].application_details.document_category;
				let document_type = JsonObject['document_details'][0].application_details.document_type;
				let for_group = JsonObject['document_details'][0].application_details.for_group;
				let department = JsonObject['document_details'][0].application_details.department;

				let originator = JsonObject['document_details'][0].application_details.application_originator;
				let application_section_head = JsonObject['document_details'][0].application_details.application_section_head;
				let section_head_approval_remarks = JsonObject['document_details'][0].application_details.section_head_approval_remarks;
				let application_qs_inspector = JsonObject['document_details'][0].application_details.application_qs_inspector;
				let qs_inspector_remarks = JsonObject['document_details'][0].application_details.qs_inspector_remarks;

				let applied_document_number = JsonObject['document_details'][0].application_details.document_number;
				let applied_document_name = JsonObject['document_details'][0].application_details.document_name;
				let applied_document_revision_number = JsonObject['document_details'][0].application_details.document_revision_number;

				let affected_document_number = JsonObject['document_details'][0].document_number;
				let affected_document_name = JsonObject['document_details'][0].document_name;
				let affected_document_revision_number = JsonObject['document_details'][0].document_revision_number;

				let affected_document_remarks = JsonObject['document_details'][0].document_remarks;

				let affected_document_id = JsonObject['document_details'][0].id;

				let document_acdcs_pkid = null;

				let person_in_charge = null;

				let revision_due_date = null;

				if(JsonObject['document_details'][0].document_acdcs_pkid != null)
				{
					document_acdcs_pkid = JsonObject['document_details'][0].control_details.fkid_document;
				}

				if(JsonObject['document_details'][0].person_in_charge != null)
				{
					person_in_charge = JsonObject['document_details'][0].person_in_charge;
				}

				if(JsonObject['document_details'][0].document_revision_due_date != null)
				{
					revision_due_date = JsonObject['document_details'][0].document_revision_due_date;
				}

				$('#app_originator').val(originator).trigger('change');

				$('#app_doc_category').val(document_category).trigger('change');
				$('#app_doc_type').val(document_type).trigger('change');
				$('#app_for_group').val(for_group).trigger('change');
				$('#app_department').val(department).trigger('change');

				$('#app_aff_pic').val(originator).trigger('change');
				$('#app_section_head').val(application_section_head).trigger('change');
				$('#app_section_head_remarks').val(section_head_approval_remarks);
				$('#app_qs_inspector').val(application_qs_inspector).trigger('change');

				let total_remarks = 'FOR AFFECTED DOCUMENT: ' + affected_document_remarks + "\n" + 'FOR APPLICATION: ' +  qs_inspector_remarks;

				$("#app_qs_inspector_remarks").val(total_remarks);

				$('#app_doc_no').val(applied_document_number);
				$('#app_doc_title').val(applied_document_name);
				$('#app_doc_rev_no').val(applied_document_revision_number);

				$('#app_aff_doc_no').val(affected_document_number);
				$('#app_aff_doc_title').val(affected_document_name);
				$('#app_aff_doc_rev_no').val(affected_document_revision_number);

				$('#app_aff_doc_id').val(affected_document_id);

				if(document_acdcs_pkid == null){
					$('#btnViewAcdcsPkid').prop('disabled','disabled');
				}else{
					$('#btnViewAcdcsPkid').removeAttr('disabled');
					$('#app_aff_hidden_pkid').val(document_acdcs_pkid);
				}

				$('#app_aff_pic').val(person_in_charge).trigger('change');
				$('#app_aff_revision_due_date').val(revision_due_date);
			}else{
				toastr.error('Error Saving Details');
			}
		},
		error: function(data, xhr, status){
            toastr.error('An error occured!\n' + 'Data: ' + data + "\n" + "XHR: " + xhr + "\n" + "Status: " + status);
        }
	});
}

function SubmitApproveAffectedDocument(array_affected_documents)
{
	$.ajax({

	url: "approve_affected_document",
	method: "post",
	data: $('#formApproveAffectedDocument').serialize(),
	dataType: "json",
	beforeSend: function()
	{
		$('#btnApproveAffectedDocument').prop('disabled','disabled');
	},
	success: function(JsonObject)
	{
		$('#btnApproveAffectedDocument').removeAttr('disabled');

		if(JsonObject['result'] == 1)
		{
			let application_id = JsonObject['affected_document_details'][0].application_id;

			if(array_affected_documents.length > 0)
			{
				SubmitApplicationAffectedDocumentsFromApprover(array_affected_documents, application_id);
			}

			CheckApplicationAffectedDocumentStatus(application_id);

			$('#modalApproveAffectedDocument').modal('hide');
			$('#formApproveAffectedDocument')[0].reset();

			toastr.success('Affected Document Conformed!');

			//draw table
			dt_applications.draw();
		}
		else
		{
			toastr.error('Error Saving Details');

			if(JsonObject['error']['app_aff_pic'] === undefined)
	        {
	          $('#app_aff_pic').removeClass('is-invalid');
	        }
	        else
	        {
	          $('#app_aff_pic').addClass('is-invalid');
	        }

	        if(JsonObject['error']['app_aff_revision_due_date'] === undefined)
	        {
	          $('#app_aff_revision_due_date').removeClass('is-invalid');
	        }
	        else
	        {
	          $('#app_aff_revision_due_date').addClass('is-invalid');
	        }
		}
	},
	error: function(data, xhr, status){
		$('#btnApproveAffectedDocument').removeAttr('disabled');
        toastr.error('An error occured!\n' + 'Data: ' + data + "\n" + "XHR: " + xhr + "\n" + "Status: " + status);
    }
	});
}

function CheckApplicationAffectedDocumentStatus(application_id)
{
	$.ajax({

	url: "check_application_affected_document_status",
	method: "get",
	data: {
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
			SendMailer(application_id);
			dt_applications.draw();
		}
		else
		{
			dt_applications.draw();
		}
	},
	error: function(data, xhr, status){
        toastr.error('An error occured!\n' + 'Data: ' + data + "\n" + "XHR: " + xhr + "\n" + "Status: " + status);
    }
	});
}

function ReturnDccValidationDetails(application_id)
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
				let aidrc_application_id = JsonObject['application_details'][0].id;
				let document_number = JsonObject['application_details'][0].document_number;
				let document_title = JsonObject['application_details'][0].document_name;
				let revision_number = JsonObject['application_details'][0].document_revision_number;
				let document_category = JsonObject['application_details'][0].document_category;
				let document_type = JsonObject['application_details'][0].document_type;
				let for_group = JsonObject['application_details'][0].for_group;
				let department = JsonObject['application_details'][0].department;

				$('#dcc_doc_category').val(document_category).trigger('change');
				$('#dcc_doc_type').val(document_type).trigger('change');
				$('#dcc_for_group').val(for_group).trigger('change');
				$('#dcc_department').val(department).trigger('change');

				$('#dcc_doc_no').val(document_number);
				$('#dcc_doc_title').val(document_title);
				$('#dcc_doc_rev_no').val(revision_number);

				$('#dcc_hidden_application_id').val(aidrc_application_id);

				dt_dcc_affected_documents.draw();
				dt_previous_dcc_validations.draw();
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

function SubmitOldDccValidation(){
	$.ajax({
        url: "submit_dcc_validation",
        method: "post",
        data: $('#formDccValidation').serialize(),
        dataType: "json",
        beforeSend: function(){
            $('#btnSubmitDccValidation').prop('disabled','disabled');
        },
        success: function(JsonObject)
        {
            $('#btnSubmitDccValidation').removeAttr('disabled');

            if(JsonObject['result'] == 1)
            {
                let application_id = $('#dcc_hidden_application_id').val();

                SendMailer(application_id);

                $('#modalDccValidation').modal('hide');
                $('#formDccValidation')[0].reset();

                toastr.success('Affected Document Validated!');

                //draw table
                dt_applications.draw();
            }
            else
            {
                toastr.error('Error Saving Details');
            }
        },
        error: function(data, xhr, status){
            $('#btnSubmitDccValidation').removeAttr('disabled');
            toastr.error('An error occured!\n' + 'Data: ' + data + "\n" + "XHR: " + xhr + "\n" + "Status: " + status);
        }
	});
}


function LoadSecAffectedDocumentDetails(active_doc_id)
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

				let document_pkid = JsonObject['document_details'][0].pkid;
				let document_number = JsonObject['document_details'][0].doc_no;
				let document_title = JsonObject['document_details'][0].doc_title;
				let revision_number = JsonObject['document_details'][0].rev_no;

				revision_number = parseInt(revision_number) + 1;

				$('#add_sec_affected_doc_pkid').val(document_pkid);
				$('#add_sec_affected_doc_no').val(document_number);
				$('#add_sec_affected_doc_title').val(document_title);
				$('#add_sec_affected_doc_rev_no').val(revision_number);
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

function LoadSectionEditAffectedDocument(affected_doc_id)
{
	$.ajax({

		url: "load_affected_document_details",
		method: "get",
		data:
		{
			affected_doc_id: affected_doc_id,
		},
		dataType: "json",
		beforeSend: function()
		{

		},
		success: function(JsonObject)
		{
			if(JsonObject['result'] == 1)
			{
				let affected_document_number = JsonObject['document_details'][0].document_number;
				let affected_document_name = JsonObject['document_details'][0].document_name;
				let affected_document_revision_number = JsonObject['document_details'][0].document_revision_number;
				let affected_document_id = JsonObject['document_details'][0].id;

				let revision_date = JsonObject['document_details'][0].document_revision_due_date;
				let person_in_charge = JsonObject['document_details'][0].person_in_charge;

				$('#edit_sec_affected_doc_no').val(affected_document_number);
				$('#edit_sec_affected_doc_title').val(affected_document_name);
				$('#edit_sec_affected_doc_rev_no').val(affected_document_revision_number);
				$('#edit_sec_affected_doc_pkid').val(affected_document_id);

				$('#edit_sec_affected_pic').val(person_in_charge).trigger('change');
				$('#edit_sec_affected_doc_revision_datetime').val(revision_date);

			}
			else
			{
				toastr.error('Error Saving Details');
			}
		},
		error: function(data, xhr, status){

            toastr.error('An error occured!\n' + 'Data: ' + data + "\n" + "XHR: " + xhr + "\n" + "Status: " + status);
        }
	});
}

function LoadMinorRevisionDetails(application_id)
{
	$.ajax({

		url: "load_application_details",
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
				let aidrc_application_id = JsonObject['application_details'][0].id;
				let document_number = JsonObject['application_details'][0].document_number;
				let document_title = JsonObject['application_details'][0].document_name;
				let revision_number = JsonObject['application_details'][0].document_revision_number;
				let document_category = JsonObject['application_details'][0].document_category;
				let document_type = JsonObject['application_details'][0].document_type;
				let for_group = JsonObject['application_details'][0].for_group;
				let department = JsonObject['application_details'][0].department;

				let original_filename = JsonObject['application_details'][0].original_filename;

				let dcc_in_charge = JsonObject['application_details'][0].dcc_validation_details[0].dcc_validator_details.name;
				let dcc_validation_datetime = JsonObject['application_details'][0].dcc_validation_details[0].dcc_validation_date;
				let dcc_remarks = JsonObject['application_details'][0].dcc_validation_details[0].dcc_remarks;

				$('#minor_dcc_in_charge').val(dcc_in_charge);
				$('#minor_dcc_validation_datetime').val(dcc_validation_datetime);
				$('#minor_dcc_remarks').val(dcc_remarks);

				$('#minor_doc_category').val(document_category).trigger('change');
				$('#minor_doc_type').val(document_type).trigger('change');
				$('#minor_for_group').val(for_group).trigger('change');
				$('#minor_department').val(department).trigger('change');

				$('#minor_doc_no').val(document_number);
				$('#minor_doc_title').val(document_title);
				$('#minor_doc_rev_no').val(revision_number);

				$('#minor_hidden_application_id').val(aidrc_application_id);
				$('#minor_current_attachment').val(original_filename);
			}
			else
			{
				toastr.error('Error Saving Details');
			}
		},
		error: function(data, xhr, status){

            toastr.error('An error occured!\n' + 'Data: ' + data + "\n" + "XHR: " + xhr + "\n" + "Status: " + status);
        }

	});
}

function LoadMasterListApplicationDetails(application_id)
{
	$.ajax({

		url: "load_application_details",
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
				let aidrc_application_id = JsonObject['application_details'][0].id;
				let document_number = JsonObject['application_details'][0].document_number;
				let document_title = JsonObject['application_details'][0].document_name;
				let revision_number = JsonObject['application_details'][0].document_revision_number;
				let document_category = JsonObject['application_details'][0].document_category;
				let document_type = JsonObject['application_details'][0].document_type;
				let for_group = JsonObject['application_details'][0].for_group;
				let department = JsonObject['application_details'][0].department;

				let originator = JsonObject['application_details'][0].application_originator;
				let section_head = JsonObject['application_details'][0].application_section_head;
				let qs_inspector = JsonObject['application_details'][0].application_qs_inspector;

				let application_datetime = JsonObject['application_details'][0].created_at;
				let section_head_approval_datetime = JsonObject['application_details'][0].section_head_approval_datetime;
				let qs_inspector_datetime = JsonObject['application_details'][0].qs_inspector_datetime;

				let qs_inspector_remarks = JsonObject['application_details'][0].qs_inspector_remarks;
				let section_head_approval_remarks = JsonObject['application_details'][0].section_head_approval_remarks;

				$('#ml_doc_category').val(document_category).trigger('change');
				$('#ml_doc_type').val(document_type).trigger('change');
				$('#ml_for_group').val(for_group).trigger('change');
				$('#ml_department').val(department).trigger('change');

				$('#ml_application_originator').val(originator).trigger('change');
				$('#ml_application_approver').val(section_head).trigger('change');
				$('#ml_qs_inspector').val(qs_inspector).trigger('change');

				$('#ml_doc_no').val(document_number);
				$('#ml_doc_title').val(document_title);
				$('#ml_doc_rev_no').val(revision_number);

				$('#ml_hidden_id').val(aidrc_application_id);

				$('#ml_application_created_at').val(application_datetime);
				$('#ml_application_approved_at').val(section_head_approval_datetime);
				$("#ml_application_validated_at").val(qs_inspector_datetime);

				$('#ml_qs_validation_remarks').val(qs_inspector_remarks);
				$('#ml_sec_approval_remarks').val(section_head_approval_remarks);

				dt_affected_documents.draw();
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


function SubmitMinorRevisions()
{
	let form_data = new FormData($('#formMinorRevisions')[0]);

	$.ajax({

		url: "submit_minor_revisions",
		method: "post",
		processData: false,
		contentType: false,
    	data: form_data,
    	dataType: "json",
		beforeSend: function()
		{
			$('#btnSubmitRevisions').prop('disabled','disabled');
		},
		success: function(JsonObject)
		{
			$('#btnSubmitRevisions').removeAttr('disabled');

			if(JsonObject['result'] == 1)
			{

				let application_id = $('#minor_hidden_application_id').val();

				SendMailer(application_id);

				$('#modalMinorRevisions').modal('hide');
				$('#formMinorRevisions')[0].reset();

				toastr.success('Submitted Minor Revisions!');

				//draw table
				dt_applications.draw();
			}
			else
			{
				toastr.error('Error Saving Details');
			}
		},
		error: function(data, xhr, status){
			$('#btnSubmitRevisions').removeAttr('disabled');
	        toastr.error('An error occured!\n' + 'Data: ' + data + "\n" + "XHR: " + xhr + "\n" + "Status: " + status);
	    }

	});
}

function SendMailer(application_id){
	$.ajax({
		url: "send_mailer",
		method: "get",
		data:
		{
			application_id: application_id,
		},
		dataType: "json",
		beforeSend: function(){
		},
		success: function(JsonObject){
			if(JsonObject['result'] == 1){
				toastr.success('E-Mail Sent to Recipients!');
			}else{
				toastr.error('Error Saving Details');
			}
		},
		error: function(data, xhr, status){
            toastr.error('An error occured!\n' + 'Data: ' + data + "\n" + "XHR: " + xhr + "\n" + "Status: " + status);
        }

	});
}

function CancelApplication(application_id){
	$.ajax({
		url: "cancel_application",
		method: "get",
		data:
		{
			application_id: application_id,
		},
		dataType: "json",
		beforeSend: function(){

		},
		success: function(JsonObject){
			if(JsonObject['result'] == 1){
				SendMailer(application_id);
				toastr.success('Cancelled Application');
				dt_applications.draw();
			}else{
				toastr.error('Error Saving Details');
			}
		},
		error: function(data, xhr, status){
            toastr.error('An error occured!\n' + 'Data: ' + data + "\n" + "XHR: " + xhr + "\n" + "Status: " + status);
        }

	});
}

function LoadActionApproverAffectedDocuments(active_doc_id)
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

				let document_pkid = JsonObject['document_details'][0].pkid;
				let document_number = JsonObject['document_details'][0].doc_no;
				let document_title = JsonObject['document_details'][0].doc_title;
				let revision_number = JsonObject['document_details'][0].rev_no;

				revision_number = parseInt(revision_number) + 1;

				$('#approver_affected_doc_pkid').val(document_pkid);
				$('#approver_affected_doc_no').val(document_number);
				$('#approver_affected_doc_title').val(document_title);
				$('#approver_affected_doc_rev_no').val(revision_number);
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

function SubmitApplicationAffectedDocumentsFromApprover(array_affected_documents, application_id)
{
	$.ajax({

		url: "submit_application_affected_documents_from_approver",
		method: "get",
		data:
		{
			array_affected_documents: array_affected_documents,
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

			}
			else
			{
				toastr.error('Error Saving Affected Documents!');
			}
		},
		error: function(data, xhr, status){
    		$('#btnSubmitApplication').removeAttr('disabled');
            toastr.error('An error occured!\n' + 'Data: ' + data + "\n" + "XHR: " + xhr + "\n" + "Status: " + status);
        }

	});
}

function LoadViewEditApplicationDetails(application_id, view_edit, edit_status)
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
				let aidrc_application_id = JsonObject['application_details'][0].id;
				let document_number = JsonObject['application_details'][0].document_number;
				let document_title = JsonObject['application_details'][0].document_name;
				let revision_number = JsonObject['application_details'][0].document_revision_number;
				let document_category = JsonObject['application_details'][0].document_category;
				let document_type = JsonObject['application_details'][0].document_type;
				let for_group = JsonObject['application_details'][0].for_group;
				let department = JsonObject['application_details'][0].department;

				let original_filename = JsonObject['application_details'][0].original_filename;

				let section_head = JsonObject['application_details'][0].application_section_head;
				let qs_inspector = JsonObject['application_details'][0].application_qs_inspector;

				let section_head_remarks = JsonObject['application_details'][0].section_head_approval_remarks;
				let qs_inspector_remarks = JsonObject['application_details'][0].qs_inspector_remarks;

				$('#edit_doc_category').val(document_category).trigger('change');
				$('#edit_doc_type').val(document_type).trigger('change');
				$('#edit_for_group').val(for_group).trigger('change');
				$('#edit_department').val(department).trigger('change');

				$('#edit_current_attachment').val(original_filename);

				$('#edit_doc_no').val(document_number);
				$('#edit_doc_title').val(document_title);
				$('#edit_doc_rev_no').val(revision_number);

				$('#edit_application_approver').val(section_head).trigger('change');
				$('#edit_qs_inspector').val(qs_inspector).trigger('change');

				$('#edit_application_approver_remarks').val(section_head_remarks);
				$('#edit_qs_inspector_remarks').val(qs_inspector_remarks);

				$('#edit_hidden_application_approver').val(section_head);
				$('#edit_hidden_application_id').val(aidrc_application_id);
				$('#edit_hidden_view_edit').val(view_edit);
				$('#edit_hidden_status').val(edit_status);

				if(view_edit == 1)
				{
					$('.edit_attachment_row').addClass('d-none');
					$('#btnEditDocumentDetails').addClass('d-none');
					$('#ViewEditApplicationDetails').addClass('d-none');
					$('#footerViewEdit').addClass('d-none');

					$('#edit_doc_category').prop('disabled','disabled');
					$('#edit_doc_type').prop('disabled','disabled');
					$('#edit_for_group').prop('disabled','disabled');
					$('#edit_department').prop('disabled','disabled');
					$('#edit_doc_no').prop('disabled','disabled');
					$('#edit_doc_title').prop('disabled','disabled');
					$('#edit_doc_rev_no').prop('disabled','disabled');


					$('#edit_application_approver').prop('disabled','disabled');
					$('#edit_qs_inspector').prop('disabled','disabled');
				}
				else
				{
					$('.edit_attachment_row').removeClass('d-none');
					$('#btnEditDocumentDetails').removeClass('d-none');
					$('#ViewEditApplicationDetails').removeClass('d-none');
					$('#footerViewEdit').removeClass('d-none');

					$('#edit_doc_category').removeAttr('disabled');
					$('#edit_doc_type').removeAttr('disabled');
					$('#edit_for_group').removeAttr('disabled');
					$('#edit_department').removeAttr('disabled');
					$('#edit_doc_no').removeAttr('disabled');
					$('#edit_doc_title').removeAttr('disabled');
					$('#edit_doc_rev_no').removeAttr('disabled');

					$('#edit_application_approver').removeAttr('disabled');
				}

				//draw table
				dt_edit_affected_documents.draw();
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

// CLARK COMMENT 07/23/2025
// function SubmitEditApplication(array_documents){
//         // console.log('test1');
// 		let form_data = new FormData($('#formEditApplication')[0]);
// 		$.ajax({
//             url: "submit_edit_application",
//             method: "post",
//             processData: false,
//             contentType: false,
//             data: form_data,
//             dataType: "json",
//             beforeSend: function(){
//     		$('#btnSubmitEditApplication').prop('disabled','disabled');
//             },
//             success: function(JsonObject)
//             {

//                 $('#btnSubmitEditApplication').removeAttr('disabled');

//                 if(JsonObject['result'] == 1)
//                 {
//                     array_documents = [];

//                     $('#modalViewEditApplication').modal('hide');
//                     $('#formEditApplication')[0].reset();

//                     dt_applications.draw();

//                     SendMailer(JsonObject['application_id']);

//                     toastr.success('Application Edited!');
//                 }
//                 else
//                 {
//                     toastr.error('Saving Application Error!');
//                 }
//             },
//             error: function(data, xhr, status){
//                 $('#btnSubmitEditApplication').removeAttr('disabled');
//                 toastr.error('An error occured!\n' + 'Data: ' + data + "\n" + "XHR: " + xhr + "\n" + "Status: " + status);
//             }
//     });
// }

function LoadGlobalAffectedDocuments(active_doc_id, application_id, approver_id)
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
				$('#modalAddDocumentDetails').modal('hide');

				let document_pkid = JsonObject['document_details'][0].pkid;
				let document_number = JsonObject['document_details'][0].doc_no;
				let document_title = JsonObject['document_details'][0].doc_title;
				let revision_number = JsonObject['document_details'][0].rev_no;

				revision_number = parseInt(revision_number) + 1;

				$('#global_affected_application_id').val(application_id);
				$('#global_affected_doc_pkid').val(document_pkid);
				$('#global_affected_doc_no').val(document_number);
				$('#global_affected_doc_title').val(document_title);
				$('#global_affected_doc_rev_no').val(revision_number);

				$('#global_affected_approver_id').val(approver_id)
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

function LoadGlobalApproverAffectedDocuments(active_doc_id, application_id, approver_id)
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
				$('#modalAddDocumentDetails').modal('hide');

				let document_pkid = JsonObject['document_details'][0].pkid;
				let document_number = JsonObject['document_details'][0].doc_no;
				let document_title = JsonObject['document_details'][0].doc_title;
				let revision_number = JsonObject['document_details'][0].rev_no;

				revision_number = parseInt(revision_number) + 1;

				$('#global_approver_affected_application_id').val(application_id);
				$('#global_approver_affected_doc_pkid').val(document_pkid);
				$('#global_approver_affected_doc_no').val(document_number);
				$('#global_approver_affected_doc_title').val(document_title);
				$('#global_approver_affected_doc_rev_no').val(revision_number);

				$('#global_approver_affected_approver_id').val(approver_id)
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

function LoadQsInspectorDetails(application_id)
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
				let aidrc_application_id = JsonObject['application_details'][0].id;
				let qs_inspector = JsonObject['application_details'][0].application_qs_inspector;


				$('#change_qs_hidden_id').val(aidrc_application_id);
				$('#change_qs_hidden_inspector').val(qs_inspector);
				$('#change_qs_current_inspector').val(qs_inspector).trigger('change');
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

function SubmitChangeQsInspector()
{
	$.ajax({

		url: "submit_change_qs_inspector",
		method: "post",
		data: $('#formChangeQsInspector').serialize(),
		dataType: "json",
		beforeSend: function()
		{
			$('#btnSubmitChangeQsInspector').prop('disabled','disabled');
		},
		success: function(JsonObject)
		{
			$('#btnSubmitChangeQsInspector').removeAttr('disabled');

			if(JsonObject['result'] == 1)
			{
				let application_id = JsonObject['application_id'];

				$('#modalChangeQsInspector').modal('hide');
				toastr.success('QS Inspector Changed!');
				SendMailer(application_id);
				dt_applications.draw();
			}
			else if(JsonObject['result'] == 2)
			{
				toastr.error('QS Inspector same as current');
			}
			else if(JsonObject['result'] == 3)
			{
				toastr.error('QS Validation already completed by previous inspector');
			}
			else
			{
				toastr.error('Error Submitting!');
			}
		},
		error: function(data, xhr, status){
			$('#btnSubmitChangeQsInspector').removeAttr('disabled');
            toastr.error('An error occured!\n' + 'Data: ' + data + "\n" + "XHR: " + xhr + "\n" + "Status: " + status);
        }
	});
}

