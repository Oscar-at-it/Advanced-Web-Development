<?php
// Week 3 Task: PHP Basics + Database Connection
// BIT3208 - Advanced Web Design and Development
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Week 3 – PHP & DB Connection | BIT3208</title>
    <style>
        body { font-family: 'Segoe UI', sans-serif; background: #f0f4f8; margin: 0; }
        header { background: #1a3c5e; color: white; padding: 15px 30px; display: flex; justify-content: space-between; align-items: center; }
        .logo { font-size: 20px; font-weight: bold; }
        .badge { background: #e74c3c; padding: 4px 14px; border-radius: 20px; font-size: 12px; }
        .container { max-width: 800px; margin: 30px auto; padding: 0 20px; }
        .card { background: white; border-radius: 10px; padding: 25px; box-shadow: 0 3px 12px rgba(0,0,0,0.1); margin-bottom: 20px; }
        .section-title { font-size: 1rem; font-weight: bold; color: #1a3c5e; border-left: 4px solid #e74c3c; padding-left: 12px; margin-bottom: 15px; }
        code { background: #f4f4f4; padding: 2px 6px; border-radius: 3px; font-family: monospace; font-size: 13px; }
        pre { background: #1e1e1e; color: #f8f8f2; padding: 15px; border-radius: 8px; overflow-x: auto; font-size: 13px; line-height: 1.6; }
        .success { color: #27ae60; font-weight: bold; }
        .error { color: #e74c3c; font-weight: bold; }
        table { width: 100%; border-collapse: collapse; margin-top: 10px; }
        th { background: #1a3c5e; color: white; padding: 10px; text-align: left; font-size: 13px; }
        td { padding: 10px; border-bottom: 1px solid #ecf0f1; font-size: 13px; }
        tr:hover { background: #f9f9f9; }
        footer { background: #1a3c5e; color: rgba(255,255,255,0.8); text-align: center; padding: 15px; font-size: 13px; margin-top: 40px; }
    </style>
</head>
<body>

<header>
    <div class="logo">🛒 ShopSmart</div>
    <div class="badge">WEEK 3 — PHP Basics</div>
</header>

<div class="container">

    <!-- PHP BASICS -->
    <div class="card">
        <div class="section-title">Fig 3: PHP Syntax Practice</div>
        <h3 style="margin-bottom:15px; color:#1a3c5e;">PHP Variables & Output</h3>
        <?php
            // Variables
            $projectName = "ShopSmart E-Commerce";
            $studentName = "Oscar";           // Change to your name
            $year        = 2025;
            $courseUnit  = "BIT3208";
            $pi          = 3.14159;
            $isActive    = true;

            echo "<p><strong>Project:</strong> $projectName</p>";
            echo "<p><strong>Student:</strong> $studentName</p>";
            echo "<p><strong>Year:</strong> $year</p>";
            echo "<p><strong>Unit:</strong> $courseUnit</p>";
            echo "<p><strong>Pi Value:</strong> $pi</p>";
            echo "<p><strong>Active:</strong> " . ($isActive ? "Yes" : "No") . "</p>";
        ?>
        <pre>
&lt;?php
$projectName = "ShopSmart E-Commerce";
$studentName = "Oscar";
$year        = 2025;

echo $projectName;     // Output a string
echo $year;            // Output a number
?&gt;
        </pre>
    </div>

    <!-- PHP FORM HANDLING -->
    <div class="card">
        <div class="section-title">Fig 5: Dynamic User Input Handling (PHP Form)</div>

        <?php
        // Check if form was submitted
        if ($_SERVER["REQUEST_METHOD"] === "POST") {
            $name    = htmlspecialchars(trim($_POST["name"]));
            $message = htmlspecialchars(trim($_POST["message"]));

            if (!empty($name) && !empty($message)) {
                echo "<div style='background:#d4edda;color:#155724;padding:15px;border-radius:8px;margin-bottom:15px;'>
                    ✅ <strong>Form received!</strong><br>
                    Hello, <strong>$name</strong>! Your message: \"$message\"
                </div>";
            } else {
                echo "<div style='background:#f8d7da;color:#721c24;padding:15px;border-radius:8px;margin-bottom:15px;'>
                    ❌ Please fill in all fields.
                </div>";
            }
        }
        ?>

        <form method="POST" action="">
            <div style="margin-bottom:12px;">
                <label style="display:block;font-size:13px;font-weight:bold;margin-bottom:5px;">Your Name:</label>
                <input type="text" name="name" placeholder="Enter your name"
                    style="width:100%;padding:10px;border:2px solid #bdc3c7;border-radius:6px;font-size:14px;">
            </div>
            <div style="margin-bottom:15px;">
                <label style="display:block;font-size:13px;font-weight:bold;margin-bottom:5px;">Message:</label>
                <textarea name="message" placeholder="Type a message..." rows="3"
                    style="width:100%;padding:10px;border:2px solid #bdc3c7;border-radius:6px;font-size:14px;resize:vertical;"></textarea>
            </div>
            <button type="submit"
                style="background:#1a3c5e;color:white;padding:10px 25px;border:none;border-radius:6px;font-size:14px;font-weight:bold;cursor:pointer;">
                Send Message
            </button>
        </form>
    </div>

    <!-- DATABASE CONNECTION -->
    <div class="card">
        <div class="section-title">Fig 4: Database Connection Test (PHP + MySQL)</div>

        <?php
        // Database connection string
        $host   = "localhost";
        $user   = "root";
        $pass   = "";
        $dbname = "shopdb";

        $conn = mysqli_connect($host, $user, $pass, $dbname);

        if ($conn) {
            echo '<p class="success">✅ mysqli_connect() — Connected to MySQL Successfully!</p>';
            echo '<p style="font-size:13px;color:#555;margin-top:5px;">Database: <code>' . $dbname . '</code> | Host: <code>' . $host . '</code></p>';

            // Fetch and display products from DB
            $result = mysqli_query($conn, "SELECT * FROM products LIMIT 5");
            if ($result && mysqli_num_rows($result) > 0) {
                echo "<h4 style='margin:15px 0 10px;color:#1a3c5e;'>Products from Database:</h4>";
                echo "<table>
                    <tr>
                        <th>ID</th>
                        <th>Name</th>
                        <th>Category</th>
                        <th>Price (KES)</th>
                        <th>Stock</th>
                    </tr>";
                while ($row = mysqli_fetch_assoc($result)) {
                    echo "<tr>
                        <td>{$row['id']}</td>
                        <td>{$row['name']}</td>
                        <td>{$row['category']}</td>
                        <td>" . number_format($row['price'], 2) . "</td>
                        <td>{$row['stock']}</td>
                    </tr>";
                }
                echo "</table>";
            } else {
                echo "<p style='color:#f39c12;'>⚠️ Products table is empty. Run week1/database.sql first.</p>";
            }

            mysqli_close($conn);
        } else {
            echo '<p class="error">❌ Connection Failed: ' . mysqli_connect_error() . '</p>';
            echo '<p style="font-size:13px;color:#555;">Make sure XAMPP is running and the database <code>shopdb</code> exists.</p>';
        }
        ?>

        <pre style="margin-top:15px;">
&lt;?php
$conn = mysqli_connect("localhost", "root", "", "shopdb");

if($conn){
    echo "Connected Successfully";
} else {
    echo "Connection Failed: " . mysqli_connect_error();
}
?&gt;
        </pre>
    </div>

</div>

<footer>BIT3208 — Week 3: PHP Basics & Database Connection | ShopSmart Project</footer>
</body>
</html>
