<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>PHP|Laravel SDK Implementation</title>
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-EVSTQN3/azprG1Anm3QDgpJLIm9Nao0Yz1ztcQTwFspd3yD65VohhpuuCOmLASjC" crossorigin="anonymous">
    <style>
        /* Center the form */
        body {
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
        }

        form {
            width: 100%;
            max-width: 400px;
            padding: 20px;
            border: 1px solid #ccc;
            border-radius: 5px;
        }
    </style>
</head>

<body>
    <div class="">
        <h3 class="mt-5">PHP|Laravel SDK Implementation</h3>
        <form action="processTransaction.php" method="POST" class="mt-3">
            <div class="mb-3">
                <label for="customerName" class="form-label">Customer Name</label>
                <input type="text" class="form-control" id="customerName" name="customerName">
            </div>
            <div class="mb-3">
                <label for="email" class="form-label">Customer Email</label>
                <input type="email" class="form-control" id="email" name="email">
            </div>
            <div class="mb-3">
                <label for="amount" class="form-label">Amount</label>
                <input type="number" class="form-control" id="amount" name="amount">
            </div>
            <div class="mb-3">
                <label for="description" class="form-label">Payment Description</label>
                <input type="text" class="form-control" id="description" name="description">
            </div>
            <div class="mb-3">
                <label for="meta" class="form-label">Other Information</label>
                <input type="text" class="form-control" id="meta" name="meta">
            </div>

            <!-- Enter the URL where you want your customers to be redirected after completing the payment process. 
            Ensure that this URL routes to your 'processTransaction.php' file. -->
            <input type="hidden" name="callback" value="https://yourwebsite.com/processTransaction.php">

            <!-- Enter the URL where you want your customers to be redirected after a successful transaction. -->
            <input type="hidden" name="success_url" value="https://yourwebsite.com/?status=success">

            <!-- Enter the URL where you want your customers to be redirected if the transaction fails. -->
            <input type="hidden" name="failure_url" value="https://yourwebsite.com/?status=failed">

            <button type="submit" class="btn btn-primary" name="processPayment">Send Payment</button>
        </form>
    </div>

    <!-- Bootstrap Bundle with Popper -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/js/bootstrap.bundle.min.js" integrity="sha384-MrcW6ZMFYlzcLA8Nl+NtUVF0sA7MsXsP1UyJoMp4YLEuNSfAP+JcXn/tWtIaxVXM" crossorigin="anonymous"></script>
</body>

</html>