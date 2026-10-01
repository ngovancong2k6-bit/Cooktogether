<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Recipe App</title>
  <link rel="stylesheet" href="index3.css">
</head>
<body>
    <div class="header-container">
    <p id="user-greeting"></p>
      <h2 class="header">Welcome to Recipe Sharing Platform</h2>
      <div class="log">
        <button class="logout_button" type="button" onclick="logout()">Logout</button>
      </div>
    </div>
  <div class="container">
    
    <div class="user-section">
      <p id="user-greeting"></p>
      <button class="view" type="button" onclick="viewUploadedRecipes()">View your Recipes</button>
      <button class="upload" type="button" onclick="uploadRecipe()">Upload Recipe</button>
    </div>
    <div class="buttons">
      <p>Don't know how to cook? No worries!! Select the category of food you want to cook from this list:</p>
      <label for="category">Select Category:</label>
        <select id="category" name="category">
        <option value="NorthIndian">North Indian</option>
        <option value="SouthIndian">South Indian</option>
        <option value="Chinese">Chinese</option>
        <option value="Dessert">Dessert</option>
        <option value="Drinks">Drinks</option>
      </select>
      <button type="button" onclick="viewRecipes()">View Recipes</button>
    </div>
  </div>


  <script>
    // Simulate user authentication, replace with actual authentication logic
    var authenticatedUser = {
      name: "Prathvish", // Replace with the authenticated user's name
      id: 1
    };

    // Fetch user name from the server when the page loads
    window.onload = function () {
      fetchUserName();
    };

    // Function to fetch user name from the server
    function fetchUserName() {
      // Assuming you have a PHP script to fetch the user name by user ID
      var userId = authenticatedUser.id; // Assuming you have the user ID in authenticatedUser
      var xhr = new XMLHttpRequest();
      
      xhr.onreadystatechange = function () {
        if (xhr.readyState === 4 && xhr.status === 200) {
          var response = JSON.parse(xhr.responseText);
          var userName = response.name;
          displayUserName(userName);
        }
      };

      xhr.open("GET", "getUserById.php?user_id=" + userId, true);
      xhr.send();
    }

    // Function to display user name on the page
    function displayUserName(userName) {
      var userGreeting = document.getElementById("user-greeting");
      userGreeting.innerText = "Hello, " + userName;
      userGreeting.classList.add("highlight");
    }



    function uploadRecipe() {
      window.location.href = "index4.php";
    }

    function viewRecipes() {
      var category = document.getElementById("category").value;
      
      if (category) {
        window.location.href = "viewRecipes.php?category=" + category;
      }
    }

    function viewUploadedRecipes() {
      // Redirect to a page that shows recipes uploaded by the authenticated user
      window.location.href = "viewUploadedRecipes.php";
    }

    function logout() {
      // Implement logout logic, for example, redirect to a logout page
      window.location.href = "index.php";
    }
  </script>
</body>
</html>