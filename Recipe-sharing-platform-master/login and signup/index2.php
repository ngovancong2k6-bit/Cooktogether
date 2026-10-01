<!DOCTYPE html>
<html>
    <head>
        <title>Recipe Sharing Platform - Create an Account</title>
        <link rel="stylesheet" href="index2.css">
        <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css" />
    </head>
    <body>
        <div class="container">
            <div class="login_form">
                <h1>Create an Account</h1>
                <form action="register.php" method="post">
                    

                    <div class="input_group">
                        <i class="fa fa-user"></i>
                        <input class="input_text" type="text" name="name" placeholder="Name">
                    </div>

                    <div class="input_group">
                        <i class="fa fa-unlock-alt"></i>
                        <input class="input_text" type="password" name="password" placeholder="Password (Between 4-10 characters)">
                    </div>
                    
                    <div class="input_group">
                        <i class="fa fa-envelope"></i>
                        <input class="input_text" type="email" name="email" placeholder="Email">
                     </div>
                    
                     <div class="button_group" id="login_button">
                        <button type="submit">Create Account</button>
                     </div>
                    
                </form>
                <p>Already have an account? <a href="index.php">Sign in</a>.</p>
            </div>

        </div>
    </body>
</html>
