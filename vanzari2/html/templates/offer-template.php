<!DOCTYPE html>

<html>

<head>

<meta charset="utf-8">

<style>

body{
    font-family:Arial;
    font-size:12px;
}

table{
    width:100%;
    border-collapse:collapse;
}

th,
td{
    border:1px solid #ccc;
    padding:5px;
}

</style>

</head>

<body>

<h1>
Offer
<?= $offer['numaroferta'] ?>
</h1>

<p>

Company:
<?= htmlspecialchars(
    $offer['firma']
) ?>

</p>

<p>

Date:
<?= htmlspecialchars(
    $offer['data']
) ?>

</p>

<table>

<thead>

<tr>

<th>Code</th>
<th>Description</th>
<th>Qty</th>
<th>Price</th>
<th>Total</th>

</tr>

</thead>

<tbody>

<?php
foreach(
    $items
    as $item
):
?>

<tr>

<td>
<?= $item['cod'] ?>
</td>

<td>
<?= $item['descriere'] ?>
</td>

<td>
<?= $item['buc'] ?>
</td>

<td>
<?= $item['pret'] ?>
</td>

<td>
<?= $item['total'] ?>
</td>

</tr>

<?php
endforeach;
?>

</tbody>

</table>

</body>

</html>