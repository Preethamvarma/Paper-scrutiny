<html>
    <head>
<style>
.head1 {
            color: goldenrod;
            text-decoration: none;
            font-size: 30px;
            padding-right: 5%;
        }
.head {
    color: cornsilk;
    text-decoration: none;
    font-size: 30px;
    text-align: center;
    border: 3px #6c757d dotted;
    background-color: #183321;
        }
    
.zoom { 
top:-50px; 
left:-35px; 
display:block; 
z-index:999; 
cursor: pointer; 
-webkit-transition-property: all; 
-webkit-transition-duration: 0.3s; 
-webkit-transition-timing-function: ease; 
} 

/*change the number below to scale to the appropriate size*/ 
.zoom:hover { 
transform: scale(3); 

}

 /* Style for buttons container */
 .buttons-container {
    text-align: center;
    margin-top: 10px;
  }


/* Center and add spacing around the table */
.centered-table {
    margin: 5px auto; /* Adjust the margin as needed */
    padding: 5px; 
    display: block;
}

/* Adjust the width of specific columns */
.date-column {
    width: 150px; /* Adjust the width as needed */
}

td.gist-column {
    width: fit-content;
}

textarea{
    width: 500px;
    height: 150px;
}
/* Style the edit-container */
.edit-container {
    position: relative;
}

/* Style the edit-button within the container */
.edit-button {
    position: absolute;
    top: 120px; /* Adjust the top position as needed */
    right: 5px; /* Adjust the right position as needed */
}

/* Style the save-button within the container */
.save-button {
    position: absolute;
    top: 5px; /* Adjust the top position as needed */
    right: 5px; /* Adjust the right position as needed */
}
tr {
    background-color: ghostwhite;
}

    </style>
    <script src="https://kit.fontawesome.com/727dd170d4.js" crossorigin="anonymous"></script>
    </head>
<body> 
<div class="header">
    <nav class="navbar navbar-expand-lg navbar-dark bg-dark">
        <div class="head1">Paper Scrutiny</div>
        <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarNav"
                aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
            <span class="navbar-toggler-icon"></span>
        </button>
        <div class="collapse navbar-collapse" id="navbarNav">
            <ul class="navbar-nav">
                <li class="nav-item active">
                    <a class="nav-link" href="#">Display Images <span class="sr-only">(current)</span></a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="search.html">Search</a>
                </li><li class="nav-item">
                    <a class="nav-link" href="index.html">Upload</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="#">Log Out</a>
                </li>
            </ul>
        </div>
    </nav>
</div>
<br>
<div class = "container">          
   <form method="post" action="download.php">         
                           <table class="table table-hover centered-table">

                    
                                    <thead class="thead-dark">
                                            <tr>
                                                                            
                                                <th style="text-align:center">Checkbox</th>  
                                                <th style="text-align:center"> S.No</th>                             
                                                <th style="text-align:center">Image</th> 
                                                <th style="text-align:center">Date</th>
                                                <th style="text-align:center">GIST</th>                               
                                            </tr>
                                    </thead>
<?php

// Start a PHP session (if not already started)
session_start();
require_once('vendor/autoload.php');

ini_set('max_execution_time', 300); // Set maximum execution time to 300 seconds (5 minutes)


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

if (isset($_GET['submit'])) {
    $keywords = array();
    $params = array();
    $types = "";

    // Initialize the $searchedkeyword variable
    $searchedkeyword = '';

    /// Check if keyword1 was entered and add it to the array
if (!empty($_GET['keyword1'])) {
    $keyword1 = mysqli_real_escape_string($conn, $_GET['keyword1']);
    $keywords[] = 'englishText LIKE ?';
    $params[] = "%" . $keyword1 . "%";
    $types .= "s";
    // Add keyword1 to the $searchedkeyword variable
    $searchedkeyword .= $keyword1 . ' ';
}

// Check if keyword2 was entered and add it to the array
if (!empty($_GET['keyword2'])) {
    $keyword2 = mysqli_real_escape_string($conn, $_GET['keyword2']);
    $keywords[] = 'englishText LIKE ?';
    $params[] = "%" . $keyword2 . "%";
    $types .= "s";
    // Add keyword2 to the $searchedkeyword variable
    $searchedkeyword .= $keyword2 . ' ';
}
    // Check if keyword3 was entered and add it to the array
    if (!empty($_GET['keyword3'])) {
        $keyword3 = mysqli_real_escape_string($conn, $_GET['keyword3']);
        $keywords[] = 'Paper LIKE ?';
        $params[] = "%" . $keyword3 . "%";
        $types .= "s";

        $searchedkeyword .= $keyword3 . ' ';
    }

    // Check if keyword4 was entered and add it to the array
    if (!empty($_GET['keyword4'])) {
        $keyword4 = mysqli_real_escape_string($conn, $_GET['keyword4']);
        $keywords[] = '(District LIKE ? OR Constituency LIKE ?)';
        $params[] = "%" . $keyword4 . "%";
        $params[] = "%" . $keyword4 . "%";
        $types .= "ss";

        $searchedkeyword .= $keyword4 . ' ';
    }

    // Store the searched keyword in a session variable
    $_SESSION['searchedKeyword'] = $searchedkeyword;
    
    // Construct the WHERE clause for the search query
    if (!empty($keywords)) {
        $where_clause = "WHERE " . implode(" AND ", $keywords);
    } else {
        $where_clause = ""; // No keywords provided, search all records
    }

    // Check if a date range was entered
    if (!empty($_GET['fdate']) && !empty($_GET['tdate'])) {
        $fdate = mysqli_real_escape_string($conn, $_GET['fdate']);
        $tdate = mysqli_real_escape_string($conn, $_GET['tdate']);
        $params[] = $fdate;
        $params[] = $tdate;
        $types .= "ss";
        $where_clause .= " AND date BETWEEN ? AND ?";
    }

    // Construct the SQL query
    $sql_query = "SELECT * FROM newspapers $where_clause ORDER BY date ASC";

    // Prepare and execute the query
    $stmt = $conn->prepare($sql_query);
    if ($stmt) {
        if (!empty($params)) {
            $stmt->bind_param($types, ...$params);
        }
        $stmt->execute();
        $result = $stmt->get_result();

        if (mysqli_num_rows($result) == 0) {
            echo "No images found.";
        } else {
            // Display the searched keyword in the table header
        if (!empty($searchedkeyword)) {
            echo '
            <div class="head">
            <h4>Searched Keyword: ' . $searchedkeyword . '</h4>
            </div>';
        }
  
            while ($row = mysqli_fetch_array($result)) {
            // Process the retrieved images
            // ...
        

                        

                

                echo '<tr>
        <td style="text-align:center">
            <div class="custom-control custom-checkbox mb-3">
                <input type="checkbox" name="fileId[]" class="custom-control-input" id="checkbox'.$row['id'].'" value="'.$row['id'].'">
                <label class="custom-control-label" for="checkbox'.$row['id'].'">'.$row['filename'].'</label>
            </div>
        </td>
        <td style="text-align:center">'.$row["id"].'</td>
        <td style="text-align:center">
            <div class="zoom">
                <img src="uploads/'.$row['filename'].'" height="250" width="250" class="img-thumbnail" />
            </div>
        </td>
        <td style="text-align:center" class="date-column">'.$row["date"].'</td>
        <td class="gist-column">
            <div class="edit-container">
                <button class="edit-button btn btn-outline-success" data-id="' . $row['id'] . '"><i class="fa-solid fa-pen-to-square"></i></button>
                <textarea class="edit-textarea" data-id="' . $row['id'] . '">' . $row['englishText'] . '</textarea>
                <button class="save-button btn btn-outline-warning" data-id="' . $row['id'] . '" style="display: none;"><i class="fa-solid fa-floppy-disk"></i></button>
            </div>
        </td>


    

 
    </tr>';
            
            }   
        }
    }
}    
            
?>
                                


 
<td colspan="2"><input type="submit" name="createzip" class="btn btn-primary"  value="Download All" />&nbsp; </td>

<td colspan="2"><input type="submit" name="generatepdf" class="btn btn-primary"  value="Generate PDF" />&nbsp; </td>
                        
</table>
          
   </form>

   <div class="row ml-1 mb-3">
        <div class="col-sm-auto">
            <input type="button" class="btn btn-outline-info" onclick='selects()' value="Select All" />
        </div>
        <div class="col-sm-auto">
            <input type="button" class="btn btn-outline-info" onclick='deSelect()' value="Deselect All" />
        </div>
        <div class="col-sm-auto">
            <a href="ocr_search.php"><button class="btn btn-success">Search Images</button></a>
        </div>
        <div class="col-sm-auto">
        <input type="submit" name="generatepdf" class="btn btn-success"  value="Generate PDF" />
        </div>
    </div>
    </div>
</div>

    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css" >
    <!-- /#wrapper -->
    <!-- jQuery Version 1.11.0 -->
    <script src="js/jquery-1.11.0.js"></script>

    <!-- Bootstrap Core JavaScript -->
    <script src="js/bootstrap.min.js"></script>

    <!-- Metis Menu Plugin JavaScript -->
    <script src="js/plugins/metisMenu/metisMenu.min.js"></script>

    <!-- DataTables JavaScript -->
    <script src="js/plugins/dataTables/jquery.dataTables.js"></script>
    <script src="js/plugins/dataTables/dataTables.bootstrap.js"></script>

    <!-- Custom Theme JavaScript -->
    <script src="js/sb-admin-2.js"></script>
    <script src="https://code.jquery.com/jquery-3.7.1.js" integrity="sha256-eKhayi8LEQwp4NKxN+CfCh+3qOVUtJn3QNZ0TciWLP4=" crossorigin="anonymous"></script>
    <!-- Page-Level Demo Scripts - Tables - Use for reference -->
    <script>

        // Script for editing englishText
        $(document).ready(function () {
        // Toggle between displaying text and textarea
        $(".edit-button").on("click", function (e) {
            e.preventDefault(); // Prevent form submission
            var id = $(this).data("id");
            $(".edit-textarea[data-id='" + id + "']").toggle();
            $(".save-button[data-id='" + id + "']").toggle();
        });

        // Save changes to the database
        $(".save-button").on("click", function (e) {
            e.preventDefault(); // Prevent form submission
            var id = $(this).data("id");
            var editedText = $(".edit-textarea[data-id='" + id + "']").val();

            // Send the edited text to a PHP script for database update
            $.ajax({
                url: "update.php",
                method: "POST",
                data: { id: id, editedText: editedText },
                success: function (response) {
                    alert(response); // Display success or error message
                },
                error: function () {
                    alert("An error occurred during the update.");
                }
            });
        });
    });


        // This script for selecting and Deselecting checkboxes
    $(document).ready(function() {
        $('#dataTables-example').dataTable();
    });
    </script>
   
   <script type="text/javascript">  
            function selects(){  
                var ele=document.getElementsByName("fileId[]");  
                for(var i=0; i<ele.length; i++){  
                    if(ele[i].type=='checkbox')  
                        ele[i].checked=true;  
                }  
            }  
            function deSelect(){  
                var ele=document.getElementsByName("fileId[]");  
                for(var i=0; i<ele.length; i++){  
                    if(ele[i].type=='checkbox')  
                        ele[i].checked=false;  
                      
                }  
            } 
            
            
        
        </script> 
        


</body>

</html>