<!DOCTYPE html>
<html lang="en">

<head>

    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <title>PHP SDK Implementation </title>
    <style>
        #btn-of-destiny {
            margin-top: 2em;
        }

        #explain1 {
            padding: 20px;
            margin: 2em auto auto;
        }
    </style>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.4.1/css/bootstrap.min.css" integrity="sha384-Vkoo8x4CGsO3+Hhxv8T/Q5PaXtkKtu6ug5TOeNV6gBiFeWPGFN9MuhOf23Q9Ifjh" crossorigin="anonymous">

</head>

<body>

    <form action="processTransaction.php" method="POST" id="paymentForm">

        <label>Email</label>
        <input type="email" name="email">
        <br>

        <label>Amount</label>
        <input type="number" name="amount">
        <br>

        <label>Currency</label>
        <input type="text" name="currency" value="NGN" />
        <br>

        <label>Customer Name</label>
        <input type="text" name="customerName" />
        <br>

        <label>First Name</label>
        <input type="text" name="description" />
        <br>
 
        <label>Other Info</label>
        <input type="Text" name="meta" />
        <br>

        <input type="hidden" name="callback" value="http://localhost:4000/processTransaction.php">

        <input type="hidden" name="success_url" value="http://localhost:4000/processTransaction.php?status=success">
        <!-- Put your success url here -->
        <input type="hidden" name="failure_url" value="http://localhost:4000/processTransaction.php?status=failed">

        <center><input id="btn-of-destiny" class="btn btn-warning" type="submit" value="Pay Now" /></center>

    </form>

    <script src="https://code.jquery.com/jquery-3.4.1.slim.min.js" integrity="sha384-J6qa4849blE2+poT4WnyKhv5vZF5SrPo0iEjwBvKU7imGFAV0wwj1yYfoRSJoZ+n" crossorigin="anonymous"></script>
    <script src="https://cdn.jsdelivr.net/npm/popper.js@1.16.0/dist/umd/popper.min.js" integrity="sha384-Q6E9RHvbIyZFJoft+2mJbHaEWldlvI9IOYy5n3zV9zzTtmI3UksdQRVvoxMfooAo" crossorigin="anonymous"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.4.1/js/bootstrap.min.js" integrity="sha384-wfSDF2E50Y2D1uUdj0O3uMBJnjuUD4Ih7YwaYd1iqfktj0Uod8GCExl3Og8ifwB6" crossorigin="anonymous"></script>
</body>

</html>