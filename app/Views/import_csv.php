<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Import CSV</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
</head>
<body class="bg-light py-5">

    <!-- Container for Centered Content -->
    <div class="container">

        <!-- Header Section -->
        <div class="row justify-content-center mb-4">
            <div class="col-lg-8 col-md-10 text-center">
                <h2 class="display-4">Import CSV File</h2>
                <p class="lead text-muted">Upload your CSV file to import data into the system.</p>
            </div>
        </div>

        <!-- Form Section -->
        <div class="row justify-content-center">
            <div class="col-lg-6 col-md-10">
                <div class="card shadow border-0">
                    <div class="card-body">
                        <!-- Form starts here -->
                        <form id="csvForm" action="<?= base_url('csv-import/upload') ?>" method="post" enctype="multipart/form-data" class="">
                            <div class="mb-4">
                                <label for="csvFile" class="form-label fs-5">Select CSV File</label>
                                <input type="file" class="form-control form-control-lg" name="csv_file" id="csvFile" accept=".csv">
                            </div>

                            <button type="submit" class="btn btn-primary btn-lg float-end w-25">Upload</button>
                        </form>
                    </div>
                </div>
            </div>
        </div>

        
        <div id="message-container" class="mt-4"></div>

    </div>

    
    <script>
        $(document).ready(function() {
            $("#csvForm").submit(function(e) {
                e.preventDefault(); 

                var formData = new FormData(this);

                $.ajax({
                    url: '<?= base_url('csv-import/upload') ?>', 
                    type: 'POST',
                    data: formData,
                    dataType: 'json',
                    processData: false, 
                    contentType: false, 
                    success: function(response) {
                        if (response.status === 'error') {
                            var errorMessages = '';
                            $.each(response.validation_errors, function(index, error) {
                                errorMessages += '<li>' + error + '</li>';
                            });
                            $('#message-container').html('<div class="text-danger"><ul>' + errorMessages + '</ul></div>');
                        } else if (response.status === 'success') {
                            $('#message-container').html('<div class="text-success">' + response.message + '</div>');
                        }
                    },
                    error: function() {
                        $('#message-container').html('<div class="text-danger">An error occurred while uploading the file.</div>');
                    }
                });
            });
        });
    </script>

</body>
</html>
