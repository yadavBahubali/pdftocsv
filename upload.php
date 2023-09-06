<?php
include_once 'database.php';
include 'vendor/autoload.php';


if (isset($_POST['submit'])) {
    $upload = $_FILES['upload']['tmp_name'];

    if (empty($upload)) {
        echo "Please select a file to upload.";
    } else {
        // Check if the uploaded file is a valid PDF
        $file_info = pathinfo($_FILES['upload']['name']);
        if (strtolower($file_info['extension']) !== 'pdf') {
            echo "Invalid file format. Please upload a PDF file.";
        } else {
            // Parse the PDF
            $parser = new \Smalot\PdfParser\Parser();
            $pdf = $parser->parseFile($upload);
            $extractedText = $pdf->getText();

            //print($extractedText);die;
            // Assuming $extractedText contains the CSV data, you may need to process it
            $csvData = explode("\n", $extractedText);
            $i = 0;
            foreach ($csvData as $row) {
                $i++;
                if ($i == 1) {
                    continue;
                }
                $data = explode("\t", $row);
                // $id=$data[0];
                // $fname=$data[1];
                // $lname=$data[2];
                // $email=$data[3];
                // $phone=$data[4];
                // Add the updated code here to check and insert data into the database

                $sql = "INSERT INTO csv_data (id_no, first_name, last_name, email, phone_no) VALUES (?, ?, ?, ?, ?)";
                $stmt = mysqli_prepare($conn, $sql);

                if (!$stmt) {
                    echo "Error in SQL statement: " . mysqli_error($conn);
                } else {
                    // Bind parameters and execute the statement
                    mysqli_stmt_bind_param($stmt, 'isssi', $data[0], $data[1], $data[2], $data[3], $data[4]);

                    if (mysqli_stmt_execute($stmt)) {
                        // Insertion was successful
                    } else {
                        echo "Error inserting data: " . mysqli_error($conn);

                        mysqli_stmt_close($stmt);
                    }
                }
            }
            echo "recorded";
        }
    }
}
?>