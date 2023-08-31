<html>

<head>
	<style>
		
	</style>
</head>

<body> -->
	<table>
		<thead>

			<tr>
			<th style="text-align: center; font-weight: bold; font-family:'Arial'; font-size:16pt; padding: 10px 10px; margin: 10px 10px;" colspan="12">AIDRC - Affected Documents Report</th>
			</tr>

			<tr>
				<th style="text-align: center; font-weight: bold; font-family:'Arial'; font-size:11pt; padding: 10px 10px; margin: 10px 10px;">AIDRC Control Number</th>
				<th style="text-align: center; font-weight: bold; font-family:'Arial'; font-size:11pt; padding: 10px 10px; margin: 10px 10px;">Affected Document Type</th>
				<th style="text-align: center; font-weight: bold; font-family:'Arial'; font-size:11pt; padding: 10px 10px; margin: 10px 10px;">Document Number</th>
				<th style="text-align: center; font-weight: bold; font-family:'Arial'; font-size:11pt; padding: 10px 10px; margin: 10px 10px;">Document Name</th>
				<th style="text-align: center; font-weight: bold; font-family:'Arial'; font-size:11pt; padding: 10px 10px; margin: 10px 10px;">Revision Number</th>
				<th style="text-align: center; font-weight: bold; font-family:'Arial'; font-size:11pt; padding: 10px 10px; margin: 10px 10px;">Revision Due Date</th>
				<th style="text-align: center; font-weight: bold; font-family:'Arial'; font-size:11pt; padding: 10px 10px; margin: 10px 10px;">Document Remarks</th>
				<th style="text-align: center; font-weight: bold; font-family:'Arial'; font-size:11pt; padding: 10px 10px; margin: 10px 10px;">Person In Charge</th>
				<!-- <th style="text-align: center; font-weight: bold; font-family:'Arial'; font-size:11pt; padding: 10px 10px; margin: 10px 10px;">Document Approver</th> -->
				<th style="text-align: center; font-weight: bold; font-family:'Arial'; font-size:11pt; padding: 10px 10px; margin: 10px 10px;">DCC In-Charge</th>
				<th style="text-align: center; font-weight: bold; font-family:'Arial'; font-size:11pt; padding: 10px 10px; margin: 10px 10px;">Document Status</th>
				<th style="text-align: center; font-weight: bold; font-family:'Arial'; font-size:11pt; padding: 10px 10px; margin: 10px 10px;">Document Control Date</th>
			</tr>
			
		</thead>

		<tbody>
			
			@for($i = 0; $i < count($affected_documents); $i++)
			<tr>

				<!--AIDRC CONTROL NUMBER-->
				<td style="text-align: left; font-family:'Arial';"> {{ $affected_documents[$i]->application_details->aidrc_control_number }} </td>

				<!--AFFECTED DOCUMENT TYPE-->
				@if($affected_documents[$i]->approver_type == 1)
					<td style="text-align: left; font-family:'Arial';">Affected Document</td>
				@elseif($affected_documents[$i]->approver_type == 2)
					<td style="text-align: left; font-family:'Arial';">FMEA</td>
				@elseif($affected_documents[$i]->approver_type == 3)
					<td style="text-align: left; font-family:'Arial';">Control Plan</td>
				@elseif($affected_documents[$i]->approver_type == 4)
					<td style="text-align: left; font-family:'Arial';">Pre-Production Checksheet</td>
				@else
					<td style="text-align: left; font-family:'Arial';">---</td>	
				@endif


				<!--DOCUMENT NUMBER-->
				<td style="text-align: left; font-family:'Arial';"> {{ $affected_documents[$i]->document_number }} </td>

				<!--DOCUMENT NAME-->
				<td style="text-align: left; font-family:'Arial';"> {{ $affected_documents[$i]->document_name }} </td>

				<!--DOCUMENT DOCUMENT REVISION NUMBER-->
				<td style="text-align: left; font-family:'Arial';"> {{ $affected_documents[$i]->document_revision_number }} </td>

				<!--DOCUMENT DOCUMENT REVISION DUE DATE-->
				<td style="text-align: left; font-family:'Arial';"> {{ $affected_documents[$i]->document_revision_due_date }} </td>

				<!--DOCUMENT REMARKS-->
				<td style="text-align: left; font-family:'Arial';"> {{ $affected_documents[$i]->document_remarks }} </td>

				<!--PERSON IN CHARGE-->
				<td style="text-align: left; font-family:'Arial';"> {{ $affected_documents[$i]->pic_details->name }} </td>

				<!--DCC IN CHARGE-->
				@if($affected_documents[$i]->control_details != null)
					@if($affected_documents[$i]->control_details->rev_no == $affected_documents[$i]->document_revision_number)
					<td style="text-align: center; font-family:'Calibri'; font-size:11pt; padding: 10px 10px; margin: 10px 10px;">{{ $affected_documents[$i]->control_details->controller_details->name }}</td>
					@else
					<td style="text-align: center; font-family:'Calibri'; font-size:11pt; padding: 10px 10px; margin: 10px 10px;"></td>
					@endif

					@else
					<td style="text-align: center; font-family:'Calibri'; font-size:11pt; padding: 10px 10px; margin: 10px 10px;"></td>

				@endif

				<!--DOCUMENT STATUS-->
				@if($affected_documents[$i]->control_details != null)
					@if($affected_documents[$i]->control_details->rev_no == $affected_documents[$i]->document_revision_number)
					<td style="text-align: center; font-family:'Calibri'; font-size:11pt; padding: 10px 10px; margin: 10px 10px;">{{ $affected_documents[$i]->control_details->controller_details->name }}</td>
					@else
					<td style="text-align: center; font-family:'Calibri'; font-size:11pt; padding: 10px 10px; margin: 10px 10px;">FOR CONTROL</td>
					@endif

					@else
					<td style="text-align: center; font-family:'Calibri'; font-size:11pt; padding: 10px 10px; margin: 10px 10px;">FOR CONTROL</td>

				@endif

				<!--DOCUMENT CONTROL DATE-->
				@if($affected_documents[$i]->control_details != null)
					@if($affected_documents[$i]->control_details->rev_no == $affected_documents[$i]->document_revision_number)
					<td style="text-align: center; font-family:'Calibri'; font-size:11pt; padding: 10px 10px; margin: 10px 10px;">{{ $affected_documents[$i]->control_details->date_time_created }}</td>
					@else
					<td style="text-align: center; font-family:'Calibri'; font-size:11pt; padding: 10px 10px; margin: 10px 10px;">---</td>
					@endif

					@else
					<td style="text-align: center; font-family:'Calibri'; font-size:11pt; padding: 10px 10px; margin: 10px 10px;">---</td>

					@endif


			</tr>
			@endfor
		</tbody>
	</table>
 </body>

</html>