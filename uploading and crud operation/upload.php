<?php

// Start session
session_start();

// Get the user ID from the logged-in user
$userId = $_SESSION['user_id'];


// Connect to the database
$dsn = 'mysql:host=localhost;port=3306;dbname=recipe_sharing_platform';

$pdo = new PDO($dsn, 'root', '');


// Validate the form data
if (empty($_POST['title']) || empty($_POST['cooking_time']) || empty($_POST['recipe']) || empty($_POST['type'])) {
  // Display an error message
  echo "Please fill in all required fields.";
  return;
}

// Get the category ID from the database
$stmt = $pdo->prepare('SELECT id FROM categories WHERE category_name = :category_name');
$stmt->bindParam(':category_name', $_POST['type']);
$stmt->execute();
$categoryId = $stmt->fetchColumn();

// Upload the image file
if (isset($_FILES['image']) && $_FILES['image']['size'] > 0) {
  $fileName = time() . '_' . basename($_FILES['image']['name']);
  $filePath = __DIR__ . '/' . $fileName;

  move_uploaded_file($_FILES['image']['tmp_name'], $filePath);
} else {
  $fileName = '';
}

// Upload the video link
$videoLink = isset($_POST['video_link']) ? $_POST['video_link'] : '';
if (!empty($videoLink)) {
  // Validate the video link
  if (!filter_var($videoLink, FILTER_VALIDATE_URL)) {
    // Display an error message
    return;
  }
} else {
  $videoLink = '';
}

$description = isset($_POST['description']) ? $_POST['description'] : '';
$instructions = isset($_POST['instructions']) ? $_POST['instructions'] : '';

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
$stmt->bindParam(':description', $description);
$stmt->bindParam(':instructions', $instructions);
$stmt->bindParam(':user_id', $userId); // Get the user ID from the logged-in user

$stmt->execute();

// Get the recipe ID of the new recipe
$recipeId = $pdo->lastInsertId();

// Insert the ingredient data into the database
if (!empty($_POST['ingredients']) && is_array($_POST['ingredients'])) {
  foreach ($_POST['ingredients'] as $key => $ingredient) {
    if (empty($ingredient)) continue;
    $stmt = $pdo->prepare('INSERT INTO ingredients (recipe_id, ingredient_name, quantity, unit)
  VALUES (:recipe_id, :ingredient_name, :quantity, :unit)');

    $stmt->bindParam(':recipe_id', $recipeId);
    $stmt->bindParam(':ingredient_name', $ingredient);
    $qty = isset($_POST['quantities'][$key]) ? $_POST['quantities'][$key] : 0;
    $unit = isset($_POST['units'][$key]) ? $_POST['units'][$key] : '';
    $stmt->bindParam(':quantity', $qty);
    $stmt->bindParam(':unit', $unit);

    $stmt->execute();
  }
}
header('Location: index3.php');
exit();
