<?php
require_once __DIR__ . '/../../controllers/AdminController.php';
if (!isset($_SESSION['admin'])) {
    header('Location: ../../login.php');
    exit;
}

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Manage Promo Codes</title>
    <link href="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css" rel="stylesheet">
    <style>
        .table-wrapper {
            max-height: 400px;
            overflow-y: auto;
        }
    </style>
</head>
<body>
    <div class="container">
        <h1 class="mt-4">Manage Promo Codes</h1>
        <form class="form-inline mb-4">
            <input type="text" id="searchInput" class="form-control mr-2" placeholder="Search by code or date">
            <button type="button" class="btn btn-primary" onclick="filterTable()">Search</button>
        </form>
        <?php if (isset($error)): ?>
            <div class="alert alert-danger" role="alert">
                Error: <?php echo $error; ?>
            </div>
        <?php else: ?>
            <div class="table-wrapper">
                <table class="table table-bordered mt-4" id="promoCodesTable">
                    <thead class="thead-dark">
                        <tr>
                            <th>ID</th>
                            <th>Code</th>
                            <th>Discount Percentage</th>
                            <th>Valid From</th>
                            <th>Valid To</th>
                            <th>Created At</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($promoCodes as $promoCode): ?>
                            <tr>
                                <td><?php echo $promoCode['id']; ?></td>
                                <td><?php echo $promoCode['code']; ?></td>
                                <td><?php echo $promoCode['discount_percentage']; ?></td>
                                <td><?php echo $promoCode['valid_from']; ?></td>
                                <td><?php echo $promoCode['valid_to']; ?></td>
                                <td><?php echo $promoCode['created_at']; ?></td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        <?php endif; ?>
    </div>
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>
    <script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
    <script>
        function filterTable() {
            var input, filter, table, tr, td, i, j, txtValue;
            input = document.getElementById("searchInput");
            filter = input.value.toLowerCase();
            table = document.getElementById("promoCodesTable");
            tr = table.getElementsByTagName("tr");

            for (i = 1; i < tr.length; i++) {
                tr[i].style.display = "none";
                td = tr[i].getElementsByTagName("td");
                for (j = 0; j < td.length; j++) {
                    if (td[j]) {
                        txtValue = td[j].textContent || td[j].innerText;
                        if (txtValue.toLowerCase().indexOf(filter) > -1) {
                            tr[i].style.display = "";
                            break;
                        }
                    }
                }
            }
        }
    </script>
</body>
</html>