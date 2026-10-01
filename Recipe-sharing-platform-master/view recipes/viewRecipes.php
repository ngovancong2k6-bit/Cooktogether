<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>View Recipes</title>
    <link rel="stylesheet" href="index51.css"> 
</head>
<body>
    <div class="container">
        <h1>Recipes</h1>
        <button onclick="goBack()">Back</button>
        <div id="recipe-list"></div>
    </div>
    <script>
        window.onload = function () {
            fetchRecipes();
        };

        function fetchRecipes() {
            var category = new URLSearchParams(window.location.search).get('category');
            var xhr = new XMLHttpRequest();
            xhr.onreadystatechange = function () {
                if (xhr.readyState === 4 && xhr.status === 200) {
                    var recipes = JSON.parse(xhr.responseText);
                    displayRecipes(recipes);
                }
            };
            xhr.open("GET", "getRecipes.php?category=" + category, true);
            xhr.send();
        }
        


        /*
        Function to display the recipe on the screen along with the avg rating
        */



        function displayRecipes(recipes) {
            var recipeListDiv = document.getElementById("recipe-list");
            recipeListDiv.innerHTML = "";
            if(recipes.length>0){
                recipes.forEach(function (recipe) {
                console.log("Recipe:", recipe);

                    var recipeDiv = document.createElement("div");
                    recipeDiv.classList.add("recipe-item");

                    var title = document.createElement("h2");
                    title.innerText = recipe.title;
                    recipeDiv.appendChild(title);

                    var avgRating = document.createElement("p");

                    // Convert avg_rating to a number
                    var avgRatingNumber = parseFloat(recipe.avg_rating);

                    avgRating.innerText = "Average Rating: " + (!isNaN(avgRatingNumber) ? avgRatingNumber.toFixed(1) : "N/A");
                    recipeDiv.appendChild(avgRating);

                    var viewButton = document.createElement("button");
                    viewButton.innerText = "View Recipe";
                    viewButton.addEventListener("click", function () {
                        viewRecipeDetails(recipe.id);
                    });
                    recipeDiv.appendChild(viewButton);

                    recipeListDiv.appendChild(recipeDiv);
                });
            }
}




/*
*/

        function viewRecipeDetails(recipeId) {
            window.location.href = "viewRecipeDetails.php?recipe_id=" + recipeId;
        }
        function goBack() {
            window.history.back();
        }
    </script>

</body>
</html>
