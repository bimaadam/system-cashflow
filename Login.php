<?php
require 'function.php';


//Cek login
if(isset($_POST['login'])){
    $email =$_POST['email'];
    $password =$_POST['password'];
//Cocokin dengan database
    $cekdatabase = mysqli_query($conn,"SELECT * FROM login where email='$email'and password='$password'");
    //hitung jumlah data
    $hitung= mysqli_num_rows($cekdatabase);

    if ($hitung>0){
        $_SESSION['log']="true";
        header('location: PERCOBAAN.php');     
    } else {
        header('location:Login.php');
    }
}

if (!isset($_SESSION['log'])){

}else {
    header('location:PERCOBAAN.php');
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
            background-image: url('/PROJECT/Dekor7.jpg'); /* Ganti path dengan lokasi gambar Anda */
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
    </style>
</head>
<body>
    <div class="d-flex justify-content-center align-items-center" style="height:100vh;">
        <div class="col-md-4 login-card">
            <h3 class="text-center login-title mb-4">Graceful Dekorasi</h3>
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
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.2.3/dist/js/bootstrap.bundle.min.js" crossorigin="anonymous"></script>
</body>
</html>