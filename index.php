<?php
echo "<h2>Hello World - PHP is Working</h2>";
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Hello World</title>
</head>
<body>

<?php
$conn = mysqli_connect("localhost", "root", "", "shopdb");

if ($conn) {
    echo 'Database Connected Successfully';
} else {
    echo ' Connection Failed: ';
}
?>

</body>
</html>
