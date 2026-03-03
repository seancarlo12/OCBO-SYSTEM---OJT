<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Portal - Login</title>

    <!-- Boxicons CSS (same as sidebar) -->
    <link href="https://unpkg.com/boxicons@2.1.4/css/boxicons.min.css" rel="stylesheet" />
    <!-- Bootstrap CSS (same as sidebar) -->
    <link
        href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css"
        rel="stylesheet"
        integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH"
        crossorigin="anonymous"
    />
    <!-- Global shared styles -->
    <link rel="stylesheet" href="../assets/style/globalStyle.css" />
    <!-- Shared sidebar styles (colors, fonts, variables) -->
    <link rel="stylesheet" href="../assets/style/login.css" />

    
</head>
<body>
    <div class="login-wrapper">
        <div class="login-card">
            <h1>Admin Login</h1>
            <form action="#" method="post">
                <div class="mb-3">
                    <label for="username" class="form-label">Username</label>
                    <input type="text" id="username" name="username" class="form-control" placeholder="Enter your username">
                </div>

                <div class="mb-3">
                    <label for="password" class="form-label">Password</label>
                    <input type="password" id="password" name="password" class="form-control" placeholder="Enter your password">
                </div>

                <button type="submit" class="btn btn-login">
                    <i class='bx bx-log-in login-icon'></i>
                    <span>Login</span>
                </button>
            </form>
        </div>
    </div>
</body>
</html>