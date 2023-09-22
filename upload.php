<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

require_once('vendor/autoload.php');

use thiagoalessio\TesseractOCR\TesseractOCR;


// Connect to the database
$db_host = 'localhost';
$db_name = 'files';
$db_user = 'root';
$db_pass = '';
$conn = new mysqli($db_host, $db_user, $db_pass, $db_name);
$conn->set_charset('utf8');

// Check for errors
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

$paperNames = array("Disha", "Velugu", "Jyothi");
// Define an array of district names
$districtNames = array(
  "ADILABAD",
  "BHADRADRI KOTHAGUDEM",
  "HANUMAKONDA",
  "HYDERABAD",
  "JAGTIAL",
  "JANGOAN",
  "JAYASHANKAR BHUPALPALLY",
  "JOGULAMBA GADWAL",
  "KAMAREDDY",
  "KARIMNAGAR",
  "KHAMMAM",
  "KOMARAM BHEEM ASIFABAD",
  "MAHABUBABAD",
  "MAHABUBNAGAR",
  "MANCHERIAL",
  "MEDAK",
  "MEDCHAL-MALKAJGIRI",
  "MULUG",
  "NAGARKURNOOL",
  "NALGONDA",
  "NARAYANPET",
  "NIRMAL",
  "NIZAMABAD",
  "PEDDAPALLI",
  "RAJANNA SIRCILLA",
  "RANGAREDDY",
  "SANGAREDDY",
  "SIDDIPET",
  "SURYAPET",
  "VIKARABAD",
  "WANAPARTHY",
  "WARANGAL",
  "YADADRI BHUVANAGIRI"
);

$constituencyNames = array(
  "Sirpur",
  "Asifabad",
  "Chennur",
  "Bellampalli",
  "Mancherial",
  "Adilabad",
  "Boath",
  "Nirmal",
  "Mudhole",
  "Khanapur",
  "Banswada",
  "Armur",
  "Bodhan",
  "Nizamabad (Urban)",
  "Nizamabad (Rural)",
  "Balkonda",
  "Jukkal",
  "Yellareddy",
  "Kamareddy",
  "Koratla",
  "Jagtial",
  "Dharmapuri",
  "Ramagundam",
  "Manthani",
  "Peddapalle",
  "Karimnagar",
  "Choppadandi",
  "Manakondur",
  "Huzurabad",
  "Vemulawada",
  "Sircilla",
  "Zaheerabad",
  "Patancheru",
  "Narayankhed",
  "Andole",
  "Narsapur",
  "Medak",
  "Dubbak",
  "Gajwel",
  "Husnabad",
  "Siddipet",
  "Kalwakurthy",
  "Shadnagar",
  "Ibrahimpatnam",
  "Lal Bahadur Nagar",
  "Maheswaram",
  "Rajendranagar",
  "Serilingampally",
  "Chevella",
  "Pargi",
  "Vicarabad",
  "Tandur",
  "Kodangal",
  "Medchal",
  "Malkajgiri",
  "Quthbullapur",
  "Kukatpally",
  "Uppal",
  "Musheerabad",
  "Malakpet",
  "Amberpet",
  "Khairatabad",
  "Jubilee Hills",
  "Sanathnagar",
  "Nampally",
  "Karwan",
  "Goshamahal",
  "Charminar",
  "Chandrayangutta",
  "Yakutpura",
  "Bahadurpura",
  "Secunderabad",
  "Secunderabad Cantt",
  "Mahbubnagar",
  "Jadcherla",
  "Devarkadra",
  "Kollapur",
  "Nagarkurnool",
  "Achampet",
  "Wanaparthy",
  "Gadwal",
  "Alampur",
  "Nakrekal",
  "Nalgonda",
  "Munugode",
  "Devarakonda",
  "Nagarjuna Sagar",
  "Miryalaguda",
  "Huzurnagar",
  "Kodad",
  "Thungathurthi",
  "Suryapet",
  "Alair",
  "Bhongir",
  "Jangaon",
  "Ghanpur",
  "Palakurthi",
  "Dornakal",
  "Mahabubabad",
  "Narsampet",
  "Parkal",
  "Warangal West",
  "Warangal East",
  "Waradhanapet",
  "Bhupalpalle",
  "Pinapaka",
  "Yellandu",
  "Kothagudem",
  "Aswaraopeta",
  "Bhadrachalam",
  "Khammam",
  "Palair",
  "Madhira",
  "Wyra",
  "Sathupalle",
  "Mulugu",
  "Makthal",
  "Narayanpet",
  "Andole"
);





// Check if the form was submitted
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
  if (isset($_POST['date'])) {
    $date = $_POST['date'];
} else {
    // handle the case where 'date' field is not defined
}

if (isset($_POST['englishText'])) {
   $englishText = $_POST['englishText'];
} else {
  // handle the case where 'englishText' field is not defined
}

 // Initialize a variable to store the matching paper name
 $matchedPaper = array();

 // Iterate through the array of paper names
        foreach ($paperNames as $paperName) {
            // Check if the paper name is present in the englishText
            if (stripos($englishText, $paperName) !== false) {
                $matchedPaper[] = $paperName;
          
            }
        }
      
        // Convert the array of matched keywords to a comma-separated string
        $paper = implode(', ', $matchedPaper);

 // Initialize a variable to store the matching dist name
 $matchedDist = array();

 // Iterate through the array of paper names
        foreach ($districtNames as $districtName) {
            // Check if the paper name is present in the englishText
            if (stripos($englishText, $districtName) !== false) {
                $matchedDist[] = $districtName;
          
            }
        }
      
        // Convert the array of matched keywords to a comma-separated string
        $district = implode(', ', $matchedDist); 

       
          // Initialize a variable to store the matching constituency names
          $matchedConstituency = array();

          // Iterate through the array of constituency names
          foreach ($constituencyNames as $constituencyName) {
              // Check if the constituency name is present in the englishText
              if (stripos($englishText, $constituencyName) !== false) {
                  $matchedConstituency[] = $constituencyName;
              }
          }

          // Check if any partial matches were found
          if (!empty($matchedConstituency)) {
              // Convert the array of matched partial keywords to a comma-separated string
              $Constituency = implode(', ', $matchedConstituency);
          } else {
              // If no matches were found, leave the $Constituency variable empty or handle it as needed
              $Constituency = "";
          }


    // Check if any file was uploaded
    if (!empty($_FILES['images']['name'][0])) {
        // Loop through each uploaded file
        for ($i = 0; $i < count($_FILES['images']['name']); $i++) {
            // Check if the file is an image
            // Check if the file is an image and if it's a PNG image
            if (exif_imagetype($_FILES['images']['tmp_name'][$i])) {

  
                // Upload the image file to the server
                $filename = $_FILES['images']['name'][$i];
                $target_dir = 'uploads/';

                // Check if the file already exists in the uploads directory
                $file_parts = pathinfo($filename);
                $file_extension = $file_parts['extension'];
                $filename_without_ext = $file_parts['filename'];
                $counter = 1;
                while (file_exists($target_dir . $filename)) {
                    $filename = $filename_without_ext . '(' . $counter . ').' . $file_extension;
                    $counter++;
                }

                $target_file = $target_dir . basename($filename);
                move_uploaded_file($_FILES['images']['tmp_name'][$i], $target_file);

                // Extract text from the image using Tesseract OCR
                $ocr_text = (new TesseractOCR($target_file))
                ->lang('tel+eng')
                ->tessdataDir('C:/xampp/htdocs/ocr/tessdata')
                ->run();
              
                
                // Save the image file, image filename, OCR text, and date to the database
                $image_data = file_get_contents($target_file);
                $image_filename = mysqli_real_escape_string($conn, $filename);
                $stmt = $conn->prepare("INSERT INTO newspapers (image, filename, ocr_text, date, Paper, District, Constituency, englishText) VALUES (?, ?, ?, ?, ?, ?, ?, ?)");
                $null = NULL;
                $stmt->bind_param("bsssssss", $null, $image_filename, $ocr_text, $date, $paper, $district, $Constituency, $englishText);
                $stmt->send_long_data(0, $image_data);
                if ($stmt->execute()) {
                    header('Location: index.html');
                } else {
                    echo "Error uploading file: " . $filename . "<br>";
                    echo $stmt->error;
                }
            } else {
                echo "Error: " . $_FILES['images']['name'][$i] . " is not an image file.<br>";
            }
        }
    }
}


// Close the database connection
$conn->close();
?>