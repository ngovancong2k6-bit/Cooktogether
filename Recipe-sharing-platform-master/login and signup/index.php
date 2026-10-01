<!DOCTYPE html>
<html>
    <head>
        <title>Recipe Sharing Platform - Sign In</title>
        <link rel="stylesheet" href="index1.css">
        <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css" />
    </head>
    <body>
        <div class="container">
            <div class="login_form">
                <h1>Login</h1>
                <form action="login.php" method="post">
                    <div class="input_group">
                        <i class="fa fa-user"></i>
                        <input class="input_text" type="email" name="email" placeholder="Email">
                    </div>

                    <div class="input_group">
                        <i class="fa fa-unlock-alt"></i>
                        <input class="input_text" type="password" name="password" placeholder="Password" >
                    </div>

                    <div class="button_group" id="login_button">
                        <button type="submit">Sign In</button>
                    </div>    
                </form>

                <p>Don't have an account? <a href="index2.php"> Sign Up </a>.</p>

            </div>
        </div>
    </body>
</html>

