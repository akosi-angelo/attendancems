<?php
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    echo "Form submitted successfully!";
    echo "<pre>";
    print_r($_POST);
    echo "</pre>";
} else {
    echo "This script only handles POST requests.";
}
?>