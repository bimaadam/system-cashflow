<?php
require 'function.php';
require 'cek.php';

// If already logged in, redirect to main dashboard
if (is_logged_in()) {
    header('Location: index.php');
    exit();
}

// Handle login submission
if (isset($_POST['login'])) {
    $identity = trim($_POST['email'] ?? '');
    $password = $_POST['password'] ?? '';

    // Allow login with email or username; verify hashed password
    $stmt = $conn->prepare("SELECT id, email, username, password, role, is_active FROM users WHERE email = ? OR username = ? LIMIT 1");
    $stmt->bind_param('ss', $identity, $identity);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result && $result->num_rows === 1) {
        $row = $result->fetch_assoc();
        if ((int)$row['is_active'] === 1 && password_verify($password, $row['password'])) {
            $_SESSION['user_logged_in'] = true;
            $_SESSION['user_id'] = (int)$row['id'];
            $_SESSION['username'] = $row['username'] ?: $row['email'];
            $_SESSION['user_role'] = $row['role'] ?: 'user';

            header('Location: index.php');
            exit();
        }
    }

    $error = 'Email/Username atau password salah, atau akun nonaktif';
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8" />
    <meta http-equiv="X-UA-Compatible" content="IE=edge" />
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no" />
    <title>Login - Graceful Dekorasi</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.2.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="css/styles.css" rel="stylesheet" />
    <style>
        body {
            background-image: url('assets/img/Dekor7.jpg'); /* Ganti path dengan lokasi gambar Anda */
            background-size: cover;
            background-position: center;
            background-repeat: no-repeat;
            height: 100vh;
        }

        .login-card {
            background: rgba(255, 255, 255, 0.85); /* Semi-transparent white */
            border: 1px solid #ccc;
            border-radius: 15px;
            box-shadow: 0px 4px 20px rgba(0, 0, 0, 0.2);
            padding: 30px;
        }

        .login-title {
            font-weight: 700;
            color: #555;
        }

        .btn-primary {
            background-color: #6c757d; /* abu-abu */
            border: none;
        }

        .btn-primary:hover {
            background-color: #5a6268;
        }


.login-card {
    position: relative;
    z-index: 1;
    border-radius: 15px;
    background: rgba(255,255,255,0.1);
    backdrop-filter: blur(15px);
    -webkit-backdrop-filter: blur(15px);
    padding: 30px;
    box-shadow: 0 0 20px rgba(255,255,255,0.3);
    overflow: hidden;
}

/* border neon animasi */
.login-card::before {
    content: "";
    position: absolute;
    top: -3px; left: -3px; right: -3px; bottom: -3px;
    border-radius: 18px;
    background: linear-gradient(270deg, 
        #ffffff, #cccccc, #666666, #000000, #666666, #cccccc, #ffffff);
    background-size: 400% 400%;
    animation: borderGlow 8s linear infinite;
    z-index: -1;
    filter: blur(8px);
}

@keyframes borderGlow {
    0%   { background-position: 0% 50%; }
    50%  { background-position: 100% 50%; }
    100% { background-position: 0% 50%; }
}
    </style>
</head>
<body>
    <div class="d-flex justify-content-center align-items-center" style="height:100vh;">
        <div class="col-md-4 login-card">
            <h3 class="text-center login-title mb-4">Graceful Dekorasi</h3>
            <?php if (!empty($error)): ?>
                <div class="alert alert-danger py-2 mb-3" role="alert">
                    <?= htmlspecialchars($error) ?>
                </div>
            <?php endif; ?>
    <form method="post" autocomplete="off">
            <div class="form-group mb-3">
            <label class="small mb-1" for="inputEmailAddress">Email</label>
            <input class="form-control" name="email" id="inputEmailAddress" type="email" placeholder="Enter email address" value="" autocomplete="off" />
         </div>
         <div class="form-group mb-3">
        <label class="small mb-1" for="inputPassword">Password</label>
        <input class="form-control" name="password" id="inputPassword" type="password" placeholder="Enter password" value="" autocomplete="new-password" />
        </div>
            <div class="d-flex justify-content-between mt-4 mb-0">
        <button class="btn btn-primary w-100" name="login">Login</button>
        </div>
    </form>
  
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.2.3/dist/js/bootstrap.bundle.min.js" crossorigin="anonymous"></script>
    
    <script src="https://cdnjs.cloudflare.com/ajax/libs/gsap/3.12.5/gsap.min.js"></script>
<script>
document.addEventListener("DOMContentLoaded", () => {
    gsap.from(".login-card", {
        duration: 1,
        y: 80,
        opacity: 0,
        ease: "power4.out"
    });

    gsap.from(".login-card input", {
        duration: 0.8,
        opacity: 0,
        y: 30,
        stagger: 0.2,
        ease: "power2.out",
        delay: 0.5
    });

    gsap.from(".login-card button", {
        duration: 0.8,
        scale: 0.8,
        opacity: 0,
        ease: "back.out(1.7)",
        delay: 1
    });
});
</script>

</body>
</html>