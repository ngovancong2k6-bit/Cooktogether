<?php

// Start session
session_start();

// Get the user ID from the logged-in user
$userId = $_SESSION['user_id'];


// Connect to the database
$dsn = 'mysql:host=localhost;port=3307;dbname=recipe_sharing_platform';

$pdo = new PDO($dsn, 'root', '');


// Validate the form data
if (empty($_POST['title']) || empty($_POST['cooking_time']) || empty($_POST['recipe']) || empty($_POST['type'])) {
  // Display an error message
  return;
}

// Get the category ID from the database
$stmt = $pdo->prepare('SELECT id FROM categories WHERE category_name = :category_name');
$stmt->bindParam(':category_name', $_POST['type']);
$stmt->execute();
$categoryId = $stmt->fetchColumn();

// Upload the image file
if ($_FILES['image']['size'] > 0) {
  $fileName = $_FILES['image']['name'];
  $filePath = 'C:\xampp\htdocs\dbmsproject' . $fileName;

  move_uploaded_file($_FILES['image']['tmp_name'], $filePath);
} else {
  $fileName = '';
}

// Upload the video link
$videoLink = $_POST['video_link'];
if (!empty($videoLink)) {
  // Validate the video link
  if (!filter_var($videoLink, FILTER_VALIDATE_URL)) {
    // Display an error message
    return;
  }
} else {
  $videoLink = '';
}

// Insert the recipe data into the database
$stmt = $pdo->prepare('INSERT INTO recipes (title, cooking_time, recipe, type, category_id, photo, video_link, description, instructions, user_id, uploaded_at)
VALUES (:title, :cooking_time, :recipe, :type, :category_id, :photo, :video_link, :description, :instructions, :user_id, NOW())');

$stmt->bindParam(':title', $_POST['title']);
$stmt->bindParam(':cooking_time', $_POST['cooking_time']);
$stmt->bindParam(':recipe', $_POST['recipe']);
$stmt->bindParam(':type', $_POST['type']);
$stmt->bindParam(':category_id', $categoryId);
$stmt->bindParam(':photo', $fileName);
$stmt->bindParam(':video_link', $videoLink);
$stmt->bindParam(':description', $_POST['description']);
$stmt->bindParam(':instructions', $_POST['instructions']);
$stmt->bindParam(':user_id', $userId); // Get the user ID from the logged-in user

$stmt->execute();

// Get the recipe ID of the new recipe
$recipeId = $pdo->lastInsertId();

// Insert the ingredient data into the database
foreach ($_POST['ingredients'] as $key => $ingredient) {
  $stmt = $pdo->prepare('INSERT INTO ingredients (recipe_id, ingredient_name, quantity, unit)
VALUES (:recipe_id, :ingredient_name, :quantity, :unit)');

  $stmt->bindParam(':recipe_id', $recipeId);
  $stmt->bindParam(':ingredient_name', $ingredient);
  $stmt->bindParam(':quantity', $_POST['quantities'][$key]);
  $stmt->bindParam(':unit', $_POST['units'][$key]);

  $stmt->execute();
  

}
echo "Your recipe has been added successfully!!!";

// Redirect the user to the recipe page
header('Location: index3.php');
