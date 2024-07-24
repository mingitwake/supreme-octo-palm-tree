<!DOCTYPE html>
<html>
<head>
    <title>Upload Document</title>
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <script src="https://cdn.jsdelivr.net/npm/axios/dist/axios.min.js"></script>
</head>
<body>
<div class="container mt-5">
    <div class="row">
        <div class="col-md-12">
            <h2>Upload Document</h2>
            <div class="form-group">
                <label for="documentUrl">Enter Document URL:</label>
                <input type="url" id="documentUrl" class="form-control">
            </div>
            <button id="submit" class="btn btn-primary">Submit</button>
        </div>
    </div>
    <div class="row mt-3">
        <div class="col-md-12">
            <h4>API Response:</h4>
            <div id="response" class="alert alert-info" role="alert"></div>
        </div>
    </div>
</div>

<script type="text/javascript">
    document.getElementById('submit').addEventListener('click', function() {
        var documentUrl = document.getElementById('documentUrl').value;
        if (documentUrl === "") {
            alert("Please enter a document URL.");
            return;
        }

        axios.post('/upload', {
            'document': documentUrl
        }, {
            headers: {
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                'Content-Type': 'application/json'
            }
        })
        .then(function(response) {
            document.getElementById('response').textContent = response.data.message;
        })
        .catch(function(error) {
            document.getElementById('response').textContent = 'An error occurred.';
        });
    });
</script>
</body>
</html>
