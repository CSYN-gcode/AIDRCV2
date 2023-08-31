<!-- <html>

<head>
	<title>
		
	</title>
</head>
<body> -->

	<style type="text/css">
		
		table, td, th, tr {

			border: 1px solid black;

		}

	</style>

	<table>
		<thead>

		<tr>
		<th style="text-align: center; font-weight: bold; font-family:'Calibri'; font-size:11pt; padding: 10px 10px; margin: 10px 10px;" colspan="13">Application for Internal Document Revision Confirmation</th>
		</tr>

		<tr>
			<th style="text-align: center; font-weight: bold; font-family:'Calibri'; font-size:11pt; padding: 10px 10px; margin: 10px 10px; background-color: #47def5;">&nbsp;</th>
			<th style="text-align: center; font-weight: bold; font-family:'Calibri'; font-size:11pt; padding: 10px 10px; margin: 10px 10px;">Need Section Head Confirmation</th>
		</tr>

		<tr>
			<th style="text-align: center; font-weight: bold; font-family:'Calibri'; font-size:11pt; padding: 10px 10px; margin: 10px 10px; background-color: #69d100;">&nbsp;</th>
			<th style="text-align: center; font-weight: bold; font-family:'Calibri'; font-size:11pt; padding: 10px 10px; margin: 10px 10px;">Not Yet Endorsed to DCC for Control</th>
		</tr>

		<tr>
			<th style="text-align: center; font-weight: bold; font-family:'Calibri'; font-size:11pt; padding: 10px 10px; margin: 10px 10px; background-color: #fcf74e;">&nbsp;</th>
			<th style="text-align: center; font-weight: bold; font-family:'Calibri'; font-size:11pt; padding: 10px 10px; margin: 10px 10px;">Failed on Validation</th>
		</tr>

		<tr>
			<th style="text-align: center; font-weight: bold; font-family:'Calibri'; font-size:11pt; padding: 10px 10px; margin: 10px 10px; background-color: #fc9f26;">&nbsp;</th>
			<th style="text-align: center; font-weight: bold; font-family:'Calibri'; font-size:11pt; padding: 10px 10px; margin: 10px 10px;">Double Entry</th>
		</tr>

		<tr>
			<th style="text-align: center; font-weight: bold; font-family:'Calibri'; font-size:11pt; padding: 10px 10px; margin: 10px 10px; background-color: #7400c7;">&nbsp;</th>
			<th style="text-align: center; font-weight: bold; font-family:'Calibri'; font-size:11pt; padding: 10px 10px; margin: 10px 10px;">Cancelled</th>
		</tr>

		<tr>
		 <th style="text-align: center; font-weight: bold; font-family:'Calibri'; font-size:11pt; padding: 10px 10px; margin: 10px 10px; background-color: #c2c2c2;" colspan="7">QAD/DCC In-Charge (who assessed documents)</th>	
		 <th style="text-align: center; font-weight: bold; font-family:'Calibri'; font-size:11pt; padding: 10px 10px; margin: 10px 10px; background-color: #9b67bf;" colspan="6">DCC Staff (who controlled documents)</th>	
		</tr>

		<tr>
			<th style="text-align: center; font-weight: bold; font-family:'Calibri'; font-size:11pt; padding: 10px 10px; margin: 10px 10px;">Date of Application</th>
			<th style="text-align: center; font-weight: bold; font-family:'Calibri'; font-size:11pt; padding: 10px 10px; margin: 10px 10px;">Control Number</th>
			<th style="text-align: center; font-weight: bold; font-family:'Calibri'; font-size:11pt; padding: 10px 10px; margin: 10px 10px;">Document Number</th>
			<th style="text-align: center; font-weight: bold; font-family:'Calibri'; font-size:11pt; padding: 10px 10px; margin: 10px 10px;">Rev #</th>
			<th style="text-align: center; font-weight: bold; font-family:'Calibri'; font-size:11pt; padding: 10px 10px; margin: 10px 10px;">Document Title</th>
			<th style="text-align: center; font-weight: bold; font-family:'Calibri'; font-size:11pt; padding: 10px 10px; margin: 10px 10px;">Dept/Section</th>
			<th style="text-align: center; font-weight: bold; font-family:'Calibri'; font-size:11pt; padding: 10px 10px; margin: 10px 10px;">Originator</th>
			<th style="text-align: center; font-weight: bold; font-family:'Calibri'; font-size:11pt; padding: 10px 10px; margin: 10px 10px;">Document Control Date</th>
			<th style="text-align: center; font-weight: bold; font-family:'Calibri'; font-size:11pt; padding: 10px 10px; margin: 10px 10px;">Affected Documents for</th>
			<th style="text-align: center; font-weight: bold; font-family:'Calibri'; font-size:11pt; padding: 10px 10px; margin: 10px 10px;">QS-In-Charge</th>
			<th style="text-align: center; font-weight: bold; font-family:'Calibri'; font-size:11pt; padding: 10px 10px; margin: 10px 10px;">Revision Due Date</th>
			<th style="text-align: center; font-weight: bold; font-family:'Calibri'; font-size:11pt; padding: 10px 10px; margin: 10px 10px;">Controlled by DCC</th>
			<th style="text-align: center; font-weight: bold; font-family:'Calibri'; font-size:11pt; padding: 10px 10px; margin: 10px 10px;">Status</th>
		</tr>

		</thead>

		<tbody>

		@for($i = 0; $i < count($applications); $i++)

		<tr>

		<td style="text-align: center; font-family:'Calibri'; font-size:11pt; padding: 10px 10px; margin: 10px 10px;">{{ date_format($applications[$i]->created_at,"m/d/Y") }}</td>

		<td style="text-align: center; font-family:'Calibri'; font-size:11pt; padding: 10px 10px; margin: 10px 10px;">{{ $applications[$i]->aidrc_control_number }}</td>

		@if($applications[$i]->document_number != null)
		<td style="text-align: center; font-family:'Calibri'; font-size:11pt; padding: 10px 10px; margin: 10px 10px;">{{ $applications[$i]->document_number }}</td>
		@else
		<td style="text-align: center; font-family:'Calibri'; font-size:11pt; padding: 10px 10px; margin: 10px 10px;"></td>
		@endif

		<td style="text-align: center; font-family:'Calibri'; font-size:11pt; padding: 10px 10px; margin: 10px 10px;">{{ $applications[$i]->document_revision_number }}</td>

		<td style="text-align: center; font-family:'Calibri'; font-size:11pt; padding: 10px 10px; margin: 10px 10px;">{{ $applications[$i]->document_name }}</td>

		<td style="text-align: center; font-family:'Calibri'; font-size:11pt; padding: 10px 10px; margin: 10px 10px;">{{ $applications[$i]->department_details->department_name }}</td>

		<td style="text-align: center; font-family:'Calibri'; font-size:11pt; padding: 10px 10px; margin: 10px 10px;">{{ $applications[$i]->originator_details->name }}</td>

		@if($applications[$i]->control_details != null)

			@if($applications[$i]->control_details->rev_no == $applications[$i]->document_revision_number)
			<td style="text-align: center; font-family:'Calibri'; font-size:11pt; padding: 10px 10px; margin: 10px 10px;">{{ $applications[$i]->control_details->date_time_created  }}</td>
			@else
			<td style="text-align: center; font-family:'Calibri'; font-size:11pt; padding: 10px 10px; margin: 10px 10px;"></td>
			@endif

		@else
		<td style="text-align: center; font-family:'Calibri'; font-size:11pt; padding: 10px 10px; margin: 10px 10px;"></td>
		@endif

		<!--loop affected documents here-->
		@if($applications[$i]->affected_documents_details != null)
		<td style="text-align: center; font-family:'Calibri'; font-size:11pt; padding: 10px 10px; margin: 10px 10px;">
			
			@for($x = 0; $x < count($applications[$i]->affected_documents_details); $x++)

			<br> {{ $applications[$i]->affected_documents_details[$x]->document_number }} / Rev. {{ $applications[$i]->affected_documents_details[$x]->document_revision_number }}

			@endfor

		</td>
		@else
		<td style="text-align: center; font-family:'Calibri'; font-size:11pt; padding: 10px 10px; margin: 10px 10px;">---</td>
		@endif

		@if($applications[$i]->qs_inspector != null)
		<td style="text-align: center; font-family:'Calibri'; font-size:11pt; padding: 10px 10px; margin: 10px 10px;">{{ $applications[$i]->qs_inspector_details->name }}</td>
		@else
		<td style="text-align: center; font-family:'Calibri'; font-size:11pt; padding: 10px 10px; margin: 10px 10px;">---</td>
		@endif

		

			<!--loop affected documents here-->
		@if($applications[$i]->affected_documents_details != null)
		<td style="text-align: center; font-family:'Calibri'; font-size:11pt; padding: 10px 10px; margin: 10px 10px;">
			
			@for($x = 0; $x < count($applications[$i]->affected_documents_details); $x++)

			<br> {{ $applications[$i]->affected_documents_details[$x]->document_revision_due_date }}

			@endfor

		</td>
		@else
		<td style="text-align: center; font-family:'Calibri'; font-size:11pt; padding: 10px 10px; margin: 10px 10px;"></td>
		@endif

		@if($applications[$i]->control_details != null)

			@if($applications[$i]->control_details->rev_no == $applications[$i]->document_revision_number)
			<td style="text-align: center; font-family:'Calibri'; font-size:11pt; padding: 10px 10px; margin: 10px 10px;">{{ $applications[$i]->control_details->controller_details->name  }}</td>
			@else
			<td style="text-align: center; font-family:'Calibri'; font-size:11pt; padding: 10px 10px; margin: 10px 10px;"></td>
			@endif

		@else
		<td style="text-align: center; font-family:'Calibri'; font-size:11pt; padding: 10px 10px; margin: 10px 10px;"></td>
		@endif

		@switch($applications[$i]->status)

 		@case(1)

 			@if($applications[$i]->for_group)
 				<td style="text-align: center; font-family:'Calibri'; font-size:11pt; padding: 10px 10px; margin: 10px 10px;">FOR QS VALIDATION</td> 
 			@else
 				<td style="text-align: center; font-family:'Calibri'; font-size:11pt; padding: 10px 10px; margin: 10px 10px;">FOR APPLICATION REVIEW</td> 
 			@endif


	@break
		@case(2)
		<td style="text-align: center; font-family:'Calibri'; font-size:11pt; padding: 10px 10px; margin: 10px 10px;">FOR APPLICATION REVIEW</td> @break
		@case(3)
		<td style="text-align: center; font-family:'Calibri'; font-size:11pt; padding: 10px 10px; margin: 10px 10px;">DISAPPROVED BY QS STAFF</td> @break
		@case(4)
		<td style="text-align: center; font-family:'Calibri'; font-size:11pt; padding: 10px 10px; margin: 10px 10px;">FOR APPLICATION REVIEW</td> @break
		@case(5)
		<td style="text-align: center; font-family:'Calibri'; font-size:11pt; padding: 10px 10px; margin: 10px 10px;">DISAPPROVED BY APPROVER</td> @break
		@case(6)
		<td style="text-align: center; font-family:'Calibri'; font-size:11pt; padding: 10px 10px; margin: 10px 10px;">FOR DCC VALIDATION</td> @break
		@case(7)
		<td style="text-align: center; font-family:'Calibri'; font-size:11pt; padding: 10px 10px; margin: 10px 10px;">FOR MINOR REVISIONS</td> @break
		@case(8)
		<td style="text-align: center; font-family:'Calibri'; font-size:11pt; padding: 10px 10px; margin: 10px 10px;">FOR MAJOR REVISIONS</td> @break
		@case(9)
			
		@if($applications[$i]->control_details != null)
			@if($applications[$i]->control_details->rev_no == $applications[$i]->document_revision_number)
			<td style="text-align: center; font-family:'Calibri'; font-size:11pt; padding: 10px 10px; margin: 10px 10px;">DOCUMENT CONTROLLED</td>
			@else
			<td style="text-align: center; font-family:'Calibri'; font-size:11pt; padding: 10px 10px; margin: 10px 10px;">FOR CONTROL</td>
			@endif

			@else
			<td style="text-align: center; font-family:'Calibri'; font-size:11pt; padding: 10px 10px; margin: 10px 10px;">FOR CONTROL</td>

		@endif

		@break

		@case(10)
		<td style="text-align: center; font-family:'Calibri'; font-size:11pt; padding: 10px 10px; margin: 10px 10px;">CANCELLED</td> @break
		@case(11)
		<td style="text-align: center; font-family:'Calibri'; font-size:11pt; padding: 10px 10px; margin: 10px 10px;">DISAPPROVED BY APPROVER (SECONDARY)</td> @break
		@default
		<td style="text-align: center; font-family:'Calibri'; font-size:11pt; padding: 10px 10px; margin: 10px 10px;"></td> @break

		@endswitch

		

		</tr>
		@endfor

		</tbody>
	</table>

<!-- </body>
</html> -->