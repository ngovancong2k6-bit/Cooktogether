<!DOCTYPE html>
<html>
<head>
  <title>Recipe Uploader</title>
  <link rel="stylesheet" href="index4.css">

  <style>
    hr {
      border: 1px solid black; 
      margin: 10px 0; 
      font-weight: bold; 
    }
  </style>
</head>
<body>
<div class="box">
  <h1>Recipe Uploader</h1>
    
      <div class="container">

        <form action="upload.php" method="post" enctype="multipart/form-data">
          <input type="text" name="title" placeholder="Recipe Title">
          <input type="number" name="cooking_time" placeholder="Cooking Time (in minutes)">
          <textarea name="recipe" placeholder="Recipe Instructions"></textarea>
          <select name="type">
            <option value="NorthIndian"> North Indian</option>
            <option value="SouthIndian"> South Indian</option>

            <option value="chinese">Chinese</option>
            <option value="dessert">Dessert</option>
            <option value="drinks">Drinks</option>
          </select>
          <h3>Upload image</h3> 
          <input type="file" name="image" accept="image/png,image/jpeg">
          <input type="text" name="video_link" placeholder="Video Link (optional)">
          <input type="hidden" name="user_id" value="<?php echo $userId; ?>">
          <h3>Ingredients</h4>
          <button type="button" onclick="addIngredient()">Add Ingredient</button>
          <ul id="ingredient-list">
          </ul>

          <input type="submit" value="Upload Recipe">
          <button type="button" onclick="goBack()">Back</button>
        </form>
      </div>
    </div>
  <script>
    function addIngredient() {
      var ingredientList = document.querySelector('#ingredient-list');

      var ingredientInput = document.createElement('input');
      ingredientInput.type = 'text';
      ingredientInput.name = 'ingredients[]';
      ingredientInput.placeholder = 'Ingredient Name';

      var quantityInput = document.createElement('input');
      quantityInput.type = 'number';
      quantityInput.name = 'quantities[]';
      quantityInput.placeholder = 'Quantity';

      var unitInput = document.createElement('input');
      unitInput.type = 'text';
      unitInput.name = 'units[]';
      unitInput.placeholder = 'Unit';
      // var hr = document.createElement('hr');


      ingredientList.appendChild(ingredientInput);
      ingredientList.appendChild(quantityInput);
      ingredientList.appendChild(unitInput);
      ingredientList.appendChild(hr);
    }
    function goBack() {
      window.history.back();
    }
  </script>
</body>
</html>
