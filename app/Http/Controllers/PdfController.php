<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Response;
use Illuminate\Support\Str;

use App\Model\AcdcsDocs;
use App\Model\Applications;
use App\Model\EsignApprover;
use App\Model\PatchDataPdf;

use setasign\Fpdi\Tcpdf\Fpdi;
use Illuminate\Support\Facades\Log;
use Carbon\Carbon;

class PdfController extends Controller
{
        private function attachPatchDataToPdf($pdf, $patchData, $filePath) {
        $pageCount = $pdf->setSourceFile($filePath);

        for ($i = 1; $i <= $pageCount; $i++) {
            $templateId = $pdf->importPage($i);
            $size = $pdf->getTemplateSize($templateId);

            $pdf->AddPage($size['orientation'], [$size['width'], $size['height']]);
            $pdf->useTemplate($templateId);

            foreach ($patchData as $item) {
                if ($item->page == $i) {
                    $x = (float) $item->x * $size['width'];
                    $y = (float) $item->y * $size['height'];
                    $pdf->SetFont('helvetica', '', $item->patchFontSize);
                    $pdf->Text($x - 17, $y - 3, $item->patchData);
                }
            }
        }
        return $pdf;
    }

    private function convertPdfToCompatible($inputPath){
        $outputPath = storage_path('app/temp/converted_' . time() . '.pdf');

        // Ensure input and output paths are properly escaped
        $escapedInput = escapeshellarg($inputPath);
        $escapedOutput = escapeshellarg($outputPath);

        $command = "gs -sDEVICE=pdfwrite -dCompatibilityLevel=1.4 -dPDFSETTINGS=/default " .
               "-dNOPAUSE -dQUIET -dBATCH -sOutputFile=$escapedOutput $escapedInput 2>&1";

        // $command = "gs -sDEVICE=pdfwrite -dCompatibilityLevel=1.4 -dPDFSETTINGS=/default " .
        //         "-dNOPAUSE -dQUIET -dBATCH -sOutputFile=" . escapeshellarg($outputPath) . " " . escapeshellarg($inputPath);

        exec($command, $outputLines, $exitCode);

        // Log::error("Ghostscript Command:\n $command");
        // Log::error("Ghostscript output:\n" . implode("\n", $outputLines));
        // Log::error("Ghostscript return code: $exitCode");

        if ($exitCode  !== 0) {
            throw new \Exception("PDF conversion failed: " . implode("\n", $outputLines));
        }

        return $outputPath;
    }

    function attachSignature($attachment, $approvers){
        // if($approvers->page_no != null){
            // Convert the PDF to compatible format
            $compatibleFile = $this->convertPdfToCompatible($attachment);

            $pdf = new Fpdi('P', 'mm', 'A4'); // mm unit
            $pdf->SetAutoPageBreak(false, 0);

            $pageCount = $pdf->setSourceFile($compatibleFile);
            for ($i = 1; $i <= $pageCount; $i++) {
                $templateId = $pdf->importPage($i);
                $size = $pdf->getTemplateSize($templateId);

                // Add a new page and use the imported PDF as template
                $pdf->AddPage($size['orientation'], [$size['width'], $size['height']]);
                $pdf->useTemplate($templateId);

                foreach($approvers as $approver) {
                    if($approver->page_no == $i) {
                        // Set coordinates where the signature will be placed (e.g. x=120, y=250)
                        $exploded_ordinates = explode('|', $approver->coordinates);
                        $x = (float) $exploded_ordinates[0]  * $size['width']; // Convert to mm;
                        $y = (float) $exploded_ordinates[1] * $size['height']; // Convert to mm;

                        // if ($x + $signatureWidth > $size['width']) {
                        //     $x = $size['width'] - $signatureWidth;
                        // }
                        // if ($y + $signatureHeight > $size['height']) {
                        //     $y = $size['height'] - $signatureHeight;
                        // }

                        // Optional: resize signaturesignaturePath
                        $signatureWidth = 22;
                        $signatureHeight = 11;

                        // Manual offsets for fine-tuning
                        $xOffset = -22;
                        $yOffset = -12;

                        // Apply offset
                        $xWithOffset = $x + $xOffset;
                        $yWithOffset = $y + $yOffset;

                        // Clamp coordinates so signature stays within page
                        $maxX = $size['width'] - $signatureWidth;
                        $maxY = $size['height'] - $signatureHeight;

                        $safeX = min(max(0, $xWithOffset), $maxX);
                        $safeY = min(max(0, $yWithOffset), $maxY);

                        // Path to your signature image (JPG or PNG)
                        $imagePath = '../RapidX_E-Signature/'.$approver->user_details->employee_number.'.png';

                        if ($approver->status == 1 && file_exists($imagePath)) {
                            $pdf->Image($imagePath, $safeX, $safeY, $signatureWidth, $signatureHeight, 'PNG');
                        } else {
                            $pdf->SetFont('Helvetica', 'B', 8);
                            $pdf->SetTextColor(255, 0, 0);
                            $pdf->SetDrawColor(255, 0, 0);             // Red border
                            $pdf->SetLineWidth(0.3);                   // Optional: thinner border
                            // $pdf->SetXY($safeX, $safeY + ($signatureHeight / 2));
                            // Position exactly where signature would be placed
                            $pdf->SetXY($safeX, $safeY);

                            // Draw a rectangle with text vertically/horizontally centered
                            $pdf->Cell(
                                $signatureWidth,                      // Width matches signature
                                $signatureHeight,                     // Height matches signature
                                'Signature Here',                     // Text
                                1,                                    // Draw border
                                0,                                    // No line break
                                'C',                                  // Center align
                                false                                 // No fill
                            );
                            // $pdf->Cell($signatureWidth, 5, 'Signature Here', 0, 0, 'C');
                            // $pdf->Cell($signatureWidth, $signatureHeight, 'Signature Here', 1, 0, 'C');
                        }

                        // if (file_exists($imagePath)) {
                        //     // Insert the image
                        //     // $pdf->Image($imagePath, $x-10, $y+5, $signatureWidth, $signatureHeight, 'PNG');
                        //     // $pdf->Image($imagePath, $x-22, $y-12, $signatureWidth, $signatureHeight, 'PNG');//working

                        //     // Place the image
                        //     $pdf->Image($imagePath, $safeX, $safeY, $signatureWidth, $signatureHeight, 'PNG');

                        //     Log::info("PDF Page Size (mm): Width = {$size['width']}, Height = {$size['height']}");
                        //     Log::info("Placing signature at: X = $x mm, Y = $y mm");
                        // } else {
                        //     Log::warning("Signature image not found: $imagePath");
                        // }
                    }
                }
            }
            return $pdf;
            // $pdf->Output($attachment, 'F'); // Stream file //CLARK COMMENT 07/10/2025
            // $pdf->Output($attachment, 'I'); // Stream file
            // @unlink($compatibleFile);
        // }
    }

    public function getPatchData(Request $request){
        $data = PatchDataPdf::where('application_id', $request->application_id)
            ->whereNull('deleted_at')
            ->get();

        return response()->json($data);
    }

    public function savePdfPatchData(Request $request) {
        session_start();
        date_default_timezone_set('Asia/Manila');

        $applicationId = $request->application_id;
        // return $applicationId;
        $rawData = $request->input('additionalData');
        $textDataArray = json_decode($rawData, true);

        if (!is_array($textDataArray)){
            return response()->json(['error' => 'Invalid format for additionalData'], 400);
        }

        //🔴 Delete existing data
        PatchDataPdf::where('application_id', $applicationId)->delete();

        if($request->patchActionType == 'save_to_draft'){
            $status = 0;
        }elseif($request->patchActionType == 'final_submit'){
            $status = 1;
        }

        foreach ($textDataArray as $item) {
            PatchDataPdf::insert([
                'status'         => $status,
                'application_id' => $applicationId,
                'patch_data'     => $item['patchData'],
                'font_size'      => $item['patchFontSize'],
                'page_no'        => $item['page'],
                // 'remarks'        => $item['remarks'],
                'coordinates'    => $item['x'].'|'.$item['y'],
                'created_at'     => now(),
                'updated_at'     => now()
            ]);
        }

        if($status == 1){
            Applications::where('id', $applicationId)->update([
                                'status' => 8, // 8 - Patch Data Submitted
                                'updated_at' => NOW(),
                            ]);

            return redirect()->route('download_attached_document_new', ['application_id' => $applicationId, 'save_to_storage' => 'true']);
        }else{
            return response()->json(['result' => 1]);
        }
    }

    public function download_attached_document_new(Request $request, $category = null) {
        $application = Applications::with([
            'esign_approver_details.user_details',
            'external_app_details'
        ])
        ->where('id', $request->application_id)
        ->where('logdel', 0)
        ->first();

        if (!$application) {
            return response()->json(['error' => 'Application not found'], 404);
        }

        $approvers = $application->esign_approver_details;
        $patchData = PatchDataPdf::where('application_id', $request->application_id)->whereNull('deleted_at')->get();

        if($category == 'orig_pdf' && $application->external_app_details){
            $documentName = $application->external_app_details->aidrc_filename;
        }else{
            $documentName = $application->aidrc_filename;
        }

        // $filename = str_replace('modified_', '', $application->aidrc_filename); clark comment 10/24/2025
        $filename = str_replace('modified_', '', $documentName);
        $filePath = storage_path("app/public/file_attachments/{$filename}");
        // Convert PDF to compatible format
        $compatibleFile = $this->convertPdfToCompatible($filePath);

        // $pdf = new \setasign\Fpdi\Fpdi('P', 'mm', 'A4');
        $pdf = new Fpdi('P', 'mm', 'A4'); // mm unit

        $pdf->setPrintHeader(false);
        $pdf->setPrintFooter(false);

        $pdf->SetAutoPageBreak(false, 0);
        $pageCount = $pdf->setSourceFile($compatibleFile);

        for ($i = 1; $i <= $pageCount; $i++) {
            $templateId = $pdf->importPage($i);
            $size = $pdf->getTemplateSize($templateId);

            $pdf->AddPage($size['orientation'], [$size['width'], $size['height']]);
            $pdf->useTemplate($templateId);

            // Add e-signatures
            foreach ($approvers as $approver) {
                if ($approver->page_no == $i && $approver->coordinates) {
                    $coords = explode('|', $approver->coordinates);
                    $x = (float) $coords[0] * $size['width'];
                    $y = (float) $coords[1] * $size['height'];

                    // Convert frontend px to mm
                    $sigWidth  = 100 * 0.2646; // ~26.46 mm
                    $sigHeight = 50  * 0.2646; // ~13.23 mm

                    // $sigWidth = 30;
                    // $sigHeight = 20;
                    // $safeX = max(0, min($x - 22, $size['width'] - $sigWidth));
                    // $safeY = max(0, min($y - 12, $size['height'] - $sigHeight));

                    //old width & height
                    // $sigWidth = 30;
                    // $sigHeight = 20;

                    // old offset
                    // offset x - 22
                    // offset y - 12

                    $safeX = max(0, min($x - 18, $size['width'] - $sigWidth));
                    $safeY = max(0, min($y - 5, $size['height'] - $sigHeight));

                    $imagePath = '../RapidX_E-Signature/' . $approver->user_details->employee_number . '.png';

                    if ($approver->status == 1 && file_exists($imagePath)) {
                        $pdf->Image($imagePath, $safeX, $safeY, $sigWidth, $sigHeight, 'PNG');
                    } else {
                        $pdf->SetFont('Helvetica', 'B', 8);
                        $pdf->SetTextColor(255, 0, 0);
                        $pdf->SetDrawColor(255, 0, 0);
                        $pdf->SetLineWidth(0.3);
                        $pdf->SetXY($safeX, $safeY);
                        $pdf->Cell($sigWidth, $sigHeight, 'Signature Here', 1, 0, 'C', false);
                    }
                }
            }

            // Add patch data
            foreach ($patchData as $patch) {
                if ($patch->page_no == $i && !empty($patch->coordinates)){
                    [$xPercent, $yPercent] = explode('|', $patch->coordinates);
                    $x = (float) $xPercent * $size['width'];
                    $y = (float) $yPercent * $size['height'];

                    $fontSize = max(6, (int) $patch->font_size); // Ensure readable
                    $pdf->SetFont('helvetica', '', $fontSize);
                    // $pdf->Text($x - 17, $y - 3, $patch->patch_data);

                    if ($patch->status == 1) {
                        // ✅ Approved: show actual patch
                        $pdf->SetTextColor(0, 0, 0); // Black
                        $pdf->Text($x - 17, $y - 3, $patch->patch_data);
                    }else {
                        // ❌ Not approved: draw "Data Here" inside a red border box
                        $pdf->SetTextColor(255, 0, 0);  // Red text
                        $pdf->SetDrawColor(255, 0, 0);  // Red border
                        $pdf->SetLineWidth(0.3);

                        // Estimate text width (rough estimation: 0.35 mm per pt per character)
                        $text = 'Data Here';
                        $textWidth = $pdf->GetStringWidth($text) + 2; // Add padding
                        $textHeight = $fontSize * 0.35 + 1;           // Approximate height

                        $pdf->SetXY($x - 17, $y - 3);
                        $pdf->Cell($textWidth, $textHeight, $text, 1, 0, 'C', false);
                        // $pdf->SetXY($x, $y);
                        // $pdf->Cell($textWidth, $textHeight, $text, 1, 0, 'C', false);

                        // // ❌ Not approved: show "Data Here" in red at same position, size, and font
                        // $pdf->SetTextColor(255, 0, 0); // Red
                        // $pdf->SetDrawColor(255, 0, 0); // Red border
                        // $pdf->SetLineWidth(0.3);
                        // $pdf->SetXY($x - 17, $y - 3, 'Data Here'); // Adjust position

                        // $labelText = 'Data Here';
                        // $labelWidth = 35;
                        // $labelHeight = 10;

                        // $pdf->Cell(
                        //     $labelWidth,
                        //     $labelHeight,
                        //     $labelText,
                        //     1,      // border
                        //     0,      // no line break
                        //     'C'     // center alignment
                        // );
                    }
                }
            }
        }

        if($request->query('save_to_storage') == 'true') {
            $date_now = Carbon::now()->toDateString();
            $generated_filename = $application->aidrc_control_number. "_aidrc_attachment_". date('YmdHis') . ".pdf";
            $savePath = storage_path('app/public/file_attachments/' . $generated_filename);

            $pdf->Output($savePath, 'F');

            Applications::where('id', $application->id)->update([
                'aidrc_filename' => $generated_filename,
                'updated_at' => $date_now,
            ]);

            return response()->json([
                'result' => 1,
                'message' => 'PDF saved successfully.',
                'saved_filename' => $generated_filename,
            ]);
        }

        // 📄 Default behavior: Stream to browser
        return response()->stream(function () use ($pdf) {
            $pdf->Output('', 'I');
        }, 200, [
            'Content-Type' => 'application/pdf',
            'Content-Disposition' => 'inline; filename="previewed_document.pdf"',
            'Cache-Control' => 'no-store, no-cache, must-revalidate, max-age=0',
            'Pragma'        => 'no-cache',
            'Expires'       => '0',
        ]);
    }

    public function generateFullPdfWithSignatureAndPatch(Request $request) {
        $application = Applications::with([
            'esign_approver_details.user_details'
        ])->where('id', $request->application_id)
        ->where('logdel', 0)
        ->first();

        if (!$application) {
            return response()->json(['error' => 'Application not found'], 404);
        }

        $approvers = $application->esign_approver_details;
        $patchData = PatchDataPdf::where('application_id', $application->id)
                                ->where('status', 1)
                                ->get();

        return $patchData;
        $filePath = storage_path("app/public/file_attachments/" . str_replace('modified_', '', $application->aidrc_filename));

        $compatibleFile = $this->convertPdfToCompatible($filePath);

        $pdf = new Fpdi('P', 'mm', 'A4');
        $pdf->SetAutoPageBreak(false, 0);
        $pageCount = $pdf->setSourceFile($compatibleFile);

        for ($i = 1; $i <= $pageCount; $i++) {
            $templateId = $pdf->importPage($i);
            $size = $pdf->getTemplateSize($templateId);
            $pdf->AddPage($size['orientation'], [$size['width'], $size['height']]);
            $pdf->useTemplate($templateId);

            // 🔹 Apply E-Signatures
            foreach ($approvers as $approver) {
                if ($approver->page_no == $i) {
                    $exploded = explode('|', $approver->coordinates);
                    $x = (float) $exploded[0] * $size['width'];
                    $y = (float) $exploded[1] * $size['height'];

                    $signatureWidth = 30;
                    $signatureHeight = 20;
                    $safeX = min(max(0, $x - 22), $size['width'] - $signatureWidth);
                    $safeY = min(max(0, $y - 12), $size['height'] - $signatureHeight);

                    $imagePath = '../RapidX_E-Signature/' . $approver->user_details->employee_number . '.png';
                    if ($approver->status == 1 && file_exists($imagePath)) {
                        $pdf->Image($imagePath, $safeX, $safeY, $signatureWidth, $signatureHeight, 'PNG');
                    } else {
                        $pdf->SetFont('Helvetica', 'B', 8);
                        $pdf->SetTextColor(255, 0, 0);
                        $pdf->SetDrawColor(255, 0, 0);
                        $pdf->SetLineWidth(0.3);
                        $pdf->SetXY($safeX, $safeY);
                        $pdf->Cell($signatureWidth, $signatureHeight, 'Signature Here', 1, 0, 'C');
                    }
                }
            }

            // 🔹 Apply Patch Data
            foreach ($patchData as $patch) {
                if ($patch->page == $i) {
                    $x = (float) $patch->x * $size['width'];
                    $y = (float) $patch->y * $size['height'];
                    $pdf->SetFont('Helvetica', '', $patch->patchFontSize);
                    $pdf->Text($x - 17, $y - 3, $patch->patchData);
                }
            }
        }

        // 🔚 Final Stream
        return response()->stream(function () use ($pdf) {
            $pdf->Output('', 'I'); // Inline view
        }, 200, [
            'Content-Type' => 'application/pdf',
            'Content-Disposition' => 'inline; filename="previewed_document.pdf"',
        ]);
    }
}
