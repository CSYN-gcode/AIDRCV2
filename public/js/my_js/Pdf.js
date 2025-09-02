$(document).ready(function(){
    let selectedCoordinates = null; // To store selected coordinates
    var currentRow = null; // To track which row will get the coordinates
    var currentPdf = null; // The loaded PDF document
    var currentPage = 1; // Current page of the PDF
    var totalPages = 0; // Total number of pages in the PDF
    let currentFile = null;

    // Preview the uploaded PDF file in the preview tab
    $('#add_attachment').on('change', function(event) {
        var file = event.target.files[0];
        console.log('Selected file:', file);

        currentFile = file; // Keep track of the current file

        if (file && file.type === 'application/pdf') {
            // Enable the Generate Row tab once a file is selected
            // $('#set-coordinates-tab').removeClass('disabled').removeAttr('aria-disabled');

            // Clear any existing preview
            $('#pdfPreview').html('');
        } else {
            alert('Please select a valid PDF file.');
        }

        // Enable checkbox when a PDF file is selected
        if($(this).val()){
            $('#withEsignature').prop('disabled', false);
        }else{
            $('#withEsignature').prop('disabled', true);
        }
    });

    // Enable the "Set Coordinates" tab when checkbox is checked in the "Upload" tab
    $('#withEsignature').on('change', function(){
        if ($(this).prop('checked')) {
            console.log('with e-signature');
            // Allow switching to the "Set Coordinates" tab
            $('#set-coordinates-tab').removeClass('disabled');
        }else{
            $('#set-coordinates-tab').addClass('disabled');
        }
    });

    $('#generateRowButton').on('click', function(){
        var rowId = Date.now(); // Unique ID
        var rowCount = $('#dynamicTable tbody tr').length + 1;
        var rowHtml = `
            <tr>
                <td id="approvalOrder-${rowId}">${rowCount}</td>
                <td>
                    <select id="approver-${rowId}" class="select2bs5 SelectApprover" style="width: 100%"></select>
                </td>
                <td hidden>
                    <input class="form-control form-control-sm" id="esignature-${rowId}" data-signature value="">
                </td>
                <td id="pageNumber-${rowId}">N/A</td>
                <td id="coordinates-${rowId}">No coordinates selected</td>
                <td>
                    <span hidden><input type="checkbox" class="disableButtonsCheckbox"> E-sign Not Required?</span>
                    <button type="button" class="btn btn-primary btn-sm previewPdfButton" data-row="${rowId}">Preview PDF</button>
                    <button type="button" class="btn btn-danger btn-sm deleteRow">Delete</button>
                </td>
            </tr>
        `;
        $('#dynamicTable tbody').append(rowHtml);

        // Populate SelectApprover dropdown
        GetEsignApprover($('.SelectApprover').last());

        $('.select2bs5').select2({
            width: '100%',
            theme: 'bootstrap-4'
        });
    });

    // Use jQuery to monitor checkbox changes and disable/enable buttons
    $(document).on('change', '.disableButtonsCheckbox', function () {
        var row = $(this).closest('tr');
        var isChecked = $(this).is(':checked');
        row.find('.previewPdfButton, .deleteRow').prop('disabled', isChecked);
    });

    // Handle Person selection (update e-signature image when selected)
    $(document).on('change', '.SelectApprover', function(){
        let select = $(this); // Store reference to the select element
        let selectedEmpId = select.find('option:selected').attr('emp_id');

        if (selectedEmpId) {
            checkImageExists(selectedEmpId, function(result){
                if (result) {
                    select.closest('tr').data('signature', '../RapidX_E-Signature/'+selectedEmpId+'.png');
                    select.closest('tr').find('input[data-signature]').val('../RapidX_E-Signature/'+selectedEmpId+'.png');
                } else {
                    toastr.error('E-Signature not found');
                }
            });
        }
    });

    function checkImageExists(empId, callback) {
        $.ajax({
            url: 'aidrc_v2/check-image-exists/' + empId,
            type: 'GET',
            success: function(response) {
                if (response.exists) {
                    console.log('Image exists:', empId + '.png');
                    callback(true);
                } else {
                    console.log('Image not found:', empId + '.png');
                    callback(false);
                }
            },
            error: function() {
                console.log('Error checking image.');
                callback(false);
            }
        });
    }

    //CLARK OLD CODE FOR PREVIEW PDF BUTTON
    // Open the preview modal and render the PDF when the button is clicked
    $(document).on('click', '.previewPdfButton', function(){
        console.log('clicked');

        var rowId = $(this).data('row');
        currentRow = rowId; // Store the current row for coordinates insertion
        // Get selected person e-signature from the row
        var personSignature = $(this).closest('tr').data('signature');

        if (!personSignature){
            alert('Please select a person first.');
            return;
        }

        // Display the e-signature in the modal
        $('#eSignature').attr('src', personSignature).show();
        $('#eSignature').css('display', 'none');

        // var file = currentFile; // Use the current file

        var file = typeof currentFile !== 'undefined' ? currentFile : null;
        var serverFileUrl = $(this).data('file-url'); // Get from button data attribute
        let pdfUrl;

        if (file && file.type === 'application/pdf') {
            pdfUrl = URL.createObjectURL(file);
        } else if (serverFileUrl) {
            pdfUrl = serverFileUrl;
        } else {
            alert('No PDF file available.');
            return;
        }

        // if (file && file.type === 'application/pdf') {
        //     var pdfUrl = URL.createObjectURL(file);

            // Clear previous preview if any
            $('#pdfPreview').html('');

            // Using pdf.js to load the PDF
            pdfjsLib.getDocument(pdfUrl).promise.then(function(pdf){
                currentPdf = pdf;
                totalPages = pdf.numPages;

                // Update page select dropdown
                $('#pageSelect').empty();
                for (var i = 1; i <= totalPages; i++) {
                    $('#pageSelect').append('<option value="' + i + '">Page ' + i + '</option>');
                }

                // Load the first page
                loadPage(currentPage);

                // Show the modal for previewing the PDF
                $('#pdfPreviewModal').modal('show');
            });
        // }
    });

    $('#pdfPreviewModal').on('show.bs.modal', function () {
        $(this).css('z-index', 1055);
    });
    $('#pdfPreviewModal').on('shown.bs.modal', function () {
        $('.modal-backdrop').last().css('z-index', 1054).addClass('stacked');
    });

    $('#pdfPreviewModal').on('hidden.bs.modal', function () {
        $('.modal-backdrop.stacked').remove();
    });

    // Handle click on the PDF preview to select coordinates
    $('#pdfPreview').on('click', function(event) {
        const canvas = $(this).find('canvas')[0];
        const rect = canvas.getBoundingClientRect();

        const clickedX = event.clientX - rect.left;
        const clickedY = event.clientY - rect.top;

        const signatureWidth = 100; //px
        const signatureHeight = 50; //px

        // Prevent placing near the edges
        const halfWidth = signatureWidth / 2;
        const halfHeight = signatureHeight / 2;

        const minX = halfWidth;
        const maxX = canvas.width - halfWidth;
        const minY = halfHeight;
        const maxY = canvas.height - halfHeight;

        if (clickedX < minX || clickedX > maxX || clickedY < minY || clickedY > maxY) {
            alert("Please place the signature away from the edge.");
            console.warn('Click is outside the PDF area. Ignoring.');
            return;
        }

        // ✅ Capture click position relative to the canvas
        // const clickX = event.clientX - rect.left;
        // // const clickY = event.clientY - rect.top; //old
        // const clickY = Math.min(event.clientY - rect.top, canvas.height);
        // const clickX = Math.min(Math.max(event.clientX - rect.left, 0), canvas.width);
        // const clickY = Math.min(Math.max(event.clientY - rect.top, 0), canvas.height);

        // const clickedX = event.offsetX;
        // const clickedY = event.offsetY;
        // const previewCanvas = document.querySelector("#pdfPreview canvas");

        // ✅ Get the stored zoomed-in dimensions
        var pdfwidth = $('#pdfPreview').data('pdfWidth');
        var pdfheight = $('#pdfPreview').data('pdfHeight');
        // ✅ Ensure we calculate based on the rendered PDF size
        // var scaleX = pdfWidth / rect.width;
        // var scaleY = pdfHeight / rect.height;

        // const scaleX = previewCanvas.width / originalPDFWidth;
        // const scaleY = previewCanvas.height / originalPDFHeight;

        // const renderedCanvasWidth = canvas.width;
        // const renderedCanvasHeight = canvas.height;
        // const pdfWidthMM = 210;   // example for A4
        // const pdfHeightMM = 297;  // example for A4

        // Seting the ordinates
        // changing format from pixels to mm

        // 🛑 Limit: Only allow clicks within canvas bounds
        // if (clickedX < 0 || clickedY < 0 || clickedX > rect.width || clickedY > rect.height) {
        //     console.warn('Click is outside the PDF area. Ignoring.');
        //     return;
        // }

        const xMM = (clickedX / canvas.width);
        const yMM = (clickedY / canvas.height);
        const formattedX = xMM.toFixed(4);
        const formattedY = yMM.toFixed(4);

        // console.log('Canvas Size (px):', canvas.width, canvas.height);
        // console.log('Clicked Position (px):', clickedX, clickedY);
        // console.log('Converted to mm:', formattedX, formattedY);
        selectedCoordinates = {
            // x: clickX,
            x: formattedX,
            y: formattedY,
            canvasWidth: canvas.width,
            canvasHeight: canvas.height,
            page: currentPage,
            pdfWidth: pdfwidth,
            pdfHeight: pdfheight
        };

        // OLD CODE
        // ✅ Store coordinates in preview (pixels)
        // selectedCoordinates = {
        //     x: clickX,
        //     y: clickY,
        //     canvasWidth: canvas.width,
        //     canvasHeight: canvas.height,
        //     page: currentPage
        // };

        // ✅ Show preview coordinates in input box
        // $('#coordinatesInput').val(`X: ${Math.round(clickX)}, Y: ${Math.round(clickY)}`);
        // $('#coordinatesInput').val(`X: ${clickX}, Y: ${clickY}`);
        $('#coordinatesInput').val(`X: ${formattedX}, Y: ${formattedY}`);

        // ✅ Clear old placed signature preview
        $('#pdfPreview img').remove();

        const signatureSrc = $('#eSignature').attr('src');
        if (signatureSrc) {
            const placedSignature = $('<img>')
                .attr('src', signatureSrc)
                .css({
                    position: 'absolute',
                    left: clickedX + 'px',
                    top: clickedY + 'px',
                    // width: '100px',
                    width: signatureWidth + 'px',
                    // height: 'auto', old
                    height: signatureHeight + 'px',
                    transform: 'translate(-50%, -50%)',
                    pointerEvents: 'none', // prevent future clicks from hitting the image
                    // border: '2px solid #333', // border
                    // padding: '3px',            // visual padding inside border
                    // backgroundColor: '#fff',   // white bg for contrast
                    // boxShadow: '0 0 5px rgba(0,0,0,0.3)'
                });

            $('#pdfPreview').append(placedSignature);
        }

        // ✅ Enable submission after placing signature
        $('#submitCoordinatesButton').prop('disabled', false);
    });

    // Clark working code, comment for now
    // $('#pdfPreview').on('click', function(event){
    //     var canvas = $(this).find('canvas')[0];
    //     var rect = canvas.getBoundingClientRect();

    //     // ✅ Capture click position relative to the zoomed-in PDF
    //     var clickX = event.clientX - rect.left;
    //     var clickY = event.clientY - rect.top;

    //     // ✅ Get the stored zoomed-in dimensions
    //     var pdfWidth = $('#pdfPreview').data('pdfWidth');
    //     var pdfHeight = $('#pdfPreview').data('pdfHeight');

    //     // ✅ Ensure we calculate based on the rendered PDF size
    //     var scaleX = pdfWidth / rect.width;
    //     var scaleY = pdfHeight / rect.height;

    //     var adjustedX = clickX * scaleX;
    //     var adjustedY = clickY * scaleY; // Orig Y-axis

    //     // Convert zoomed-in coordinates back to original PDF dimensions
    //     adjustedX = (clickX / pdfWidth) * rect.width;
    //     adjustedY = (clickY / pdfHeight) * rect.height;
    //     selectedCoordinates.x = clickX;
    //     selectedCoordinates.y = clickY;
    //     console.log("Final Selected Coordinates: X=" + adjustedX + ", Y=" + adjustedY);
    //     $('#coordinatesInput').val(`X: ${clickX}, Y: ${clickY}`);
    //     $('#pdfPreview img').remove();
    //     const signatureSrc = $('#eSignature').attr('src');
    //     if(signatureSrc) {
    //         const placedSignature = $('<img>')
    //             .attr('src', signatureSrc)
    //             .css({
    //                 position: 'absolute',
    //                 left: clickX + 'px',  // Position relative to preview
    //                 top: clickY + 'px',
    //                 width: '100px',
    //                 height: 'auto',
    //                 transform: 'translate(-50%, -50%)'
    //             });
    //         $('#pdfPreview').append(placedSignature);
    //     }
    //     $('#submitCoordinatesButton').prop('disabled', false);
    // });

    function loadPage(pageNumber) {
        currentPdf.getPage(pageNumber).then(function(page) {
            // Get the original viewport (scale = 1)
            const viewport = page.getViewport({ scale: 1 });

            // Create a canvas to draw the PDF page
            const canvas = document.createElement('canvas');
            const context = canvas.getContext('2d');

            // Set canvas dimensions to match original PDF size
            canvas.width = viewport.width;
            canvas.height = viewport.height;

            // Define render context
            const renderContext = {
                canvasContext: context,
                viewport: viewport
            };

            // Clear existing canvas and append new one
            $('#pdfPreview').html('').append(canvas);

            // Render the page
            page.render(renderContext).promise.then(() => {
                console.log('Page rendered at original dimensions:', viewport.width, viewport.height);
            }).catch(error => {
                console.error('Error rendering PDF page:', error);
            });

            // Store actual PDF render dimensions for later use (e.g. coordinate mapping)
            $('#pdfPreview').data('pdfWidth', viewport.width);
            $('#pdfPreview').data('pdfHeight', viewport.height);
        });
    }

    // WORKING CODE BY CHRIS
    // function loadPage(pageNumber) {
    //     currentPdf.getPage(pageNumber).then(function(page) {
    //         // Dynamic Scaling - Chris
    //         page.getViewport({ scale: 1 });

    //         const desiredWidth = 827; // A4 at 96 DPI or your target container
    //         const baseViewport = page.getViewport({ scale: 1 }); // no scaling yet
    //         const scale = desiredWidth / baseViewport.width;
    //         const viewport = page.getViewport({ scale }); // dynamically scaled!
    //         const canvas = document.createElement('canvas');
    //         const context = canvas.getContext('2d');

    //         // ✅ Update the canvas size to match the zoomed-in view
    //         canvas.width = viewport.width;
    //         canvas.height = viewport.height;

    //         const renderContext = {
    //             canvasContext: context,
    //             viewport: viewport
    //         };

    //         // Clear old canvas
    //         $('#pdfPreview').html('');
    //         $('#pdfPreview').append(canvas);

    //         page.render(renderContext).promise.then(() => {
    //             console.log('Page rendered successfully:', pageNumber);
    //         }).catch(error => {
    //             console.error('Error rendering page:', error);
    //         });

    //         // ✅ Store the zoomed-in dimensions
    //         $('#pdfPreview').data('pdfWidth', viewport.width);
    //         $('#pdfPreview').data('pdfHeight', viewport.height);
    //     });
    // }

    // Handle page selection change
    $('#pageSelect').on('change', function() {
         const selected = parseInt($(this).val(), 10);
        if (!isNaN(selected)) {
            currentPage = selected;
            loadPage(currentPage);
        } else {
            console.warn('Invalid page selection:', $(this).val());
        }
    });

    // Submit the coordinates to the selected row
    $('#submitCoordinatesButton').on('click', function() {
        if (selectedCoordinates.x !== null && selectedCoordinates.y !== null) {
            // Update the coordinates column of the selected row
            $('#coordinates-' + currentRow).text('X: ' + selectedCoordinates.x + ', Y: ' + selectedCoordinates.y);

            // Update the page number column of the selected row
            $('#pageNumber-' + currentRow).text(currentPage);  // Update the "Page #" column

            // Close the modal after submitting coordinates
            $('#pdfPreviewModal').modal('hide');

            // Reset the selected coordinates
            selectedCoordinates = null;
            $('#submitCoordinatesButton').prop('disabled', true); // Disable button again until new coordinates are selected
        } else {
            alert('Please select coordinates first!');
        }
    });

    // Delete a row
    $(document).on('click', '.deleteRow', function() {
        $(this).closest('tr').remove();
        // Update row numbers
        $('#dynamicTable tbody tr').each(function(index) {
            $(this).find('td:first').text(index + 1);
        });
    });

    $('#editApproverButton').on('click', function(){
        var rowId = Date.now(); // Unique ID
        let filepath = $(this).data('filepath');
        var rowCount = $('#editApproverTable tbody tr').length + 1;
        var rowHtml = `
            <tr>
                <td id="approvalOrder-${rowId}">${rowCount}</td>
                <td>
                    <select id="approver-${rowId}" class="select2bs5 SelectEditApprover" style="width: 100%"></select>
                </td>
                <td hidden>
                    <input class="form-control form-control-sm" id="esignature-${rowId}" data-signature value="">
                </td>
                <td id="pageNumber-${rowId}">N/A</td>
                <td id="coordinates-${rowId}">No coordinates selected</td>
                <td id="status-${rowId}" value="">N/A</td>
                <td id="remarks-${rowId}" value="">N/A</td>
                <td>
                    <span hidden><input type="checkbox" class="disableButtonsCheckbox"> E-sign Not Required?</span>
                    <button type="button" class="btn btn-primary btn-sm previewPdfButton" data-row="${rowId}" data-file-url="${filepath}">Preview PDF</button>
                    <button type="button" class="btn btn-danger btn-sm editApproverDeleteRow">Delete</button>
                </td>
            </tr>
        `;
        $('#editApproverTable tbody').append(rowHtml);

        // Populate SelectApprover dropdown
        GetEsignApprover($('.SelectEditApprover').last());

        $('.select2bs5').select2({
            width: '100%',
            theme: 'bootstrap-4'
        });
    });

    // Handle Person selection (update e-signature image when selected)
    $(document).on('change', '.SelectEditApprover', function(){
        let select = $(this); // Store reference to the select element
        let selectedEmpId = select.find('option:selected').attr('emp_id');

        if (selectedEmpId) {
            checkImageExists(selectedEmpId, function(result){
                if (result) {
                    select.closest('tr').data('signature', '../RapidX_E-Signature/'+selectedEmpId+'.png');
                    select.closest('tr').find('input[data-signature]').val('../RapidX_E-Signature/'+selectedEmpId+'.png');
                } else {
                    toastr.error('E-Signature not found');
                }
            });
        }
    });

    // Delete a row
    $(document).on('click', '.editApproverDeleteRow', function() {
        $(this).closest('tr').remove();
        // Update row numbers
        $('#editApproverTable tbody tr').each(function(index) {
            $(this).find('td:first').text(index + 1);
        });
    });

    // Enable the "Set Coordinates" tab when checkbox is checked in the "Upload" tab
    $('#editApproverWithEsignature').on('change', function(){
        if ($(this).prop('checked')) {
            console.log('with e-signature');
            // Allow switching to the "Set Coordinates" tab
            $('#editApprover-tab').removeClass('disabled');
        }else{
            $('#editApprover-tab').addClass('disabled');
        }
    });
});
