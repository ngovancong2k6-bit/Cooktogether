<?php
session_start();

$servername = "localhost";
$username = "root";
$password = "";
$dbname = "recipe_sharing_Platform";

$conn = new mysqli($servername, $username, $password, $dbname,3306);

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

if (isset($_GET['recipe_id']) && isset($_GET['edit_option'])) {
    $recipeId = $_GET['recipe_id'];
    $editOption = $_GET['edit_option'];

    switch ($editOption) {
        case 'title':
            // Editing the recipe title
            if (isset($_POST['new_title'])) {
                $newTitle = $_POST['new_title'];
                $updateTitleSql = "UPDATE recipes SET title = '$newTitle' WHERE id = $recipeId";
                $conn->query($updateTitleSql);
                echo "Recipe Title updated successfully!";
                header("Location: viewUploadedRecipes.php");
                exit();
            } else {
                echo "Enter a new title for the recipe:";
                echo "<form method='post'>";
                echo "<input type='text' name='new_title'>";
                echo "<input type='submit' value='Update'>";
                echo "</form>";
            }
            break;

        case 'cooking_time':
            // Editing the cooking time
            if (isset($_POST['new_cooking_time'])) {
                $newCookingTime = $_POST['new_cooking_time'];
                $updateCookingTimeSql = "UPDATE recipes SET cooking_time = $newCookingTime WHERE id = $recipeId";
                $conn->query($updateCookingTimeSql);
                echo "Cooking Time updated successfully!";
                header("Location: viewUploadedRecipes.php");
                exit();
            } else {
                echo "Enter a new cooking time for the recipe (in minutes):";
                echo "<form method='post'>";
                echo "<input type='number' name='new_cooking_time'>";
                echo "<input type='submit' value='Update'>";
                echo "</form>";
            }
            break;

        case 'recipe':
            // Editing the entire recipe
            if (isset($_POST['new_recipe'])) {
                $newRecipe = $_POST['new_recipe'];
                $updateRecipeSql = "UPDATE recipes SET recipe = '$newRecipe' WHERE id = $recipeId";
                $conn->query($updateRecipeSql);
                echo "Recipe content updated successfully!";
                header("Location: viewUploadedRecipes.php");
                exit();
            } else {
                echo "Enter the updated recipe content:";
                echo "<form method='post'>";
                echo "<textarea name='new_recipe' rows='10' cols='50'></textarea><br>";
                echo "<input type='submit' value='Update'>";
                echo "</form>";
            }
            break;


        case 'category':
            // Editing the category
            $categoriesSql = "SELECT * FROM categories";
            $categoriesResult = $conn->query($categoriesSql);

            if ($categoriesResult->num_rows > 0) {
                if (isset($_POST['new_category'])) {
                    $newCategoryId = $_POST['new_category'];
        
                    // Fetch the category name based on the new category ID
                    $categoryNameSql = "SELECT category_name FROM categories WHERE id = $newCategoryId";
                    $categoryNameResult = $conn->query($categoryNameSql);
        
                    if ($categoryNameResult && $categoryNameResult->num_rows > 0) {
                        $category = $categoryNameResult->fetch_assoc();
                        $newCategoryName = $category['category_name'];
        
                        // Update both category_id and type in the recipes table
                        $updateCategorySql = "UPDATE recipes SET category_id = $newCategoryId, type = '$newCategoryName' WHERE id = $recipeId";
                        $conn->query($updateCategorySql);
                        header("Location: viewUploadedRecipes.php");
                        echo "Category updated successfully!";
                    }
                 } else {
                    echo "Select a new category for the recipe:";
                    echo "<form method='post'>";
                    echo "<select name='new_category'>";
                    while ($category = $categoriesResult->fetch_assoc()) {
                        echo "<option value='{$category['id']}'>{$category['category_name']}</option>";
                    }
                    echo "</select>";
                    echo "<input type='submit' value='Update'>";
                    echo "</form>";
                }
            } else {
                echo "No categories found.";
            }
            break;

       

        case 'ingredients':
            // Editing the ingredients
            if (isset($_POST['new_ingredient'])) {
                $newIngredientName = $_POST['new_ingredient'];
                $newIngredientQuantity = $_POST['new_quantity'];
                $newIngredientUnit = $_POST['new_unit'];
            
                // Insert the new ingredient into the ingredients table
                $insertIngredientSql = "INSERT INTO ingredients (recipe_id, ingredient_name, quantity, unit) VALUES ($recipeId, '$newIngredientName', $newIngredientQuantity, '$newIngredientUnit')";
                $conn->query($insertIngredientSql);
            
                echo "Ingredient added successfully!";
                header("Location: viewUploadedRecipes.php");
                exit();
            } else {
                echo "Enter details for the new ingredient:";
                echo "<form method='post'>";
                echo "<label for='new_ingredient'>Ingredient Name:</label>";
                echo "<input type='text' name='new_ingredient' required><br>";
                echo "<label for='new_quantity'>Quantity:</label>";
                echo "<input type='number' name='new_quantity' required><br>";
                echo "<label for='new_unit'>Unit:</label>";
                echo "<input type='text' name='new_unit' required><br>";
                echo "<input type='submit' value='Add Ingredient'>";
                echo "</form>";
                // Add additional logic for displaying existing ingredients and allowing updates/deletions
            }
            break;

// ...

        // Add more cases for other edit options as needed

        default:
            echo "Invalid edit option.";
    }
} else {
    echo "Recipe ID or edit option not provided.";
}

$conn->close();
?>
