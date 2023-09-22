<?php
session_start();

// Connect to the database
$db_host = 'localhost';
$db_name = 'files';
$db_user = 'root';
$db_pass = '';
$conn = new mysqli($db_host, $db_user, $db_pass, $db_name);
$conn->set_charset('utf8');


 
if (isset($_REQUEST['createzip']) && $_REQUEST['createzip'] != "") {
    extract($_REQUEST);

    // Generate and download the ZIP file
    $filename = "Download" . time() . ".zip";

    $fileQry = mysqli_query($conn, 'SELECT * FROM newspapers WHERE id IN (' . implode(",", $fileId) . ')');

    $zip = new ZipArchive();

    if ($zip->open($filename, ZipArchive::CREATE)) {
        while ($row = $fileQry->fetch_assoc()) {
            $zip->addFile(getcwd() . '/' . 'uploads/' . $row['filename'], $row['filename']);
        }
        $zip->close();

        header("Content-type: application/zip");
        header("Content-Disposition: attachment; filename=$filename");
        header("Content-length: " . filesize($filename));
        header("Pragma: no-cache");
        header("Expires: 0");
        readfile("$filename");
        unlink($filename);
    } else {
        echo 'Failed!';
    }
} elseif (isset($_REQUEST['generatepdf']) && $_REQUEST['generatepdf'] != "") {
    // Retrieve the selected file IDs from the form (make sure to use the correct input name)
    $fileId = isset($_POST['fileId']) ? $_POST['fileId'] : array();
    
    // Generate and download the PDF
    require('fpdf.php'); // Include the FPDF library
    
    // Create a PDF object
    $pdf = new FPDF();
    $pdf->AddPage();
    $pdf->SetFont('Arial', '', 16);

    // Check if the session variable is set
if (isset($_GET['searchedKeyword'])) {
    $searchedkeyword = urldecode($_GET['searchedkeyword']);
}    
    // Retrieve the searched keyword from the session
    $searchedkeyword = isset($_SESSION['searchedKeyword']) ? $_SESSION['searchedKeyword'] : '';
    
    // Add the header with the searched keyword in the center and in bold
    $pdf->Cell(0, 10, $searchedkeyword, 0, 1, 'C', true);
    
    // Initialize serial number
    $serialNo = 1;
    
    // Loop through the selected images and add content to the PDF
    foreach ($fileId as $id) {
        $fileQry = mysqli_query($conn, "SELECT * FROM newspapers WHERE id = $id");
        if ($row = $fileQry->fetch_assoc()) {
            // Add serial number, date, paper, and English text to the PDF
           
            $pdf->Cell(10, 10, $serialNo++, 0, 0, 'L'); // Increment serial number

            // Concatenate the English text and date
            $englishTextWithDate = $row['englishText'] . '/' . $row['date'];

            $pdf->MultiCell(0, 10, $englishTextWithDate, 0, 'L'); // Display English text with date

        }
    }
    
    // Calculate the total number of pages
    $totalPages = $pdf->PageNo();
    
    // Create a new page for the images (last page)
    $pdf->AddPage();
    
    // Loop through the selected images and add them to the last page
    foreach ($fileId as $id) {
        $fileQry = mysqli_query($conn, "SELECT * FROM newspapers WHERE id = $id");
        if ($row = $fileQry->fetch_assoc()) {
            $imagePath = 'uploads/' . $row['filename']; // Assuming the path to your images
            $pdf->Image($imagePath, 30, 30, 150); // Adjust the X, Y, and width as needed
            $pdf->AddPage();
        }
    }
    
    // Output the PDF to the browser for download
    $pdf->Output('generated_pdf.pdf', 'D'); // 'D' forces download
}

?>
