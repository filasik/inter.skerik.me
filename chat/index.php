<?php 
  session_start();
  if(isset($_SESSION['unique_id'])){
    header("location: users.php");
  }
?>

<?php include_once "header.php"; ?>
<body>
  <div class="wrapper">
    <section class="form signup">
      <header>Zasílání zpráv</header>
      <form action="#" method="POST" enctype="multipart/form-data" autocomplete="off">
        <div class="error-text"></div>
        <div class="name-details">
          <div class="field input">
            <label>Jméno</label>
            <input type="text" name="fname" placeholder="Jméno" required>
          </div>
          <div class="field input">
            <label>Příjmení</label>
            <input type="text" name="lname" placeholder="Příjmení" required>
          </div>
        </div>
        <div class="field input">
          <label>Email</label>
          <input type="text" name="email" placeholder="Email" required>
        </div>
        <div class="field input">
          <label>Heslo</label>
          <input type="password" name="password" placeholder="Zadejte nové heslo" required>
          <i class="fas fa-eye"></i>
        </div>
        <div class="field image">
          <label>Obrázek profilu</label>
          <input type="file" name="image" accept="image/x-png,image/gif,image/jpeg,image/jpg" required>
        </div>
        <div class="field button">
          <input type="submit" name="submit" value="Vstoupit do chatu">
        </div>
      </form>
      <div class="link">Už máte účet? <a href="login.php">Přihlásit se</a></div>
    </section>
  </div>

  <script src="javascript/pass-show-hide.js"></script>
  <script src="javascript/signup.js"></script>

</body>
</html>
