<?php
    // enable sessions
    session_start();
    $prompt = " ";

    // if username and password were submitted, check them
    if (isset($_POST["user"]) && isset($_POST["pass1"]) && isset($_POST["pass2"]))
    {

        // connect to database
        if (($connection = mysql_connect("localhost", "cf", "123456")) === false)
            die("Could not connect to database");

        // select database
        if (mysql_select_db("tzfx", $connection) === false)
            die("Could not select database");

        $name = sprintf("SELECT 1 FROM `users` WHERE `user` = '%s'", mysql_real_escape_string($_POST["user"]));

        $result = mysql_query($name);
        if (mysql_num_rows($result) == 0) {
          // prepare SQL
          $sql = sprintf("INSERT INTO `users` (`user`, `pass`) VALUES ('%s' , AES_ENCRYPT('%s', '%s'))",
                       mysql_real_escape_string($_POST["user"]),
                       mysql_real_escape_string($_POST["pass1"]),
                       mysql_real_escape_string($_POST["pass1"]));

          // execute query
          $result = mysql_query($sql);
          if ($result === false) {
            die("Could not query database");
          } else {
            // remember that user's logged in
            $_SESSION["authenticated"] = true;
            $_SESSION["user"] = $_POST["user"];

            // redirect user to home page, using absolute path, per
            // http://us2.php.net/manual/en/function.header.php
            $host = $_SERVER["HTTP_HOST"];
            $path = rtrim(dirname($_SERVER["PHP_SELF"]), "/\\");
            header("Location: http://$host$path/index.php");
            exit;
          }
        } else {
          $prompt = "用户名已存在，请重新输入";
        }
    
    }

  require '../includes/header.php';
?>

  <script>

    function validate()  {
      if (document.forms.registration.user.value == "") {
        alert("用户名不能为空");
        return false;
      }
      else if (document.forms.registration.pass1.value == "") {
        alert("密码不能为空");
        return false;
      }
      else if (document.forms.registration.pass1.value != document.forms.registration.pass2.value) {
        alert("两次必须输入同样的密码");
        return false;
      }

      return true;
    }

  </script>

    <h1>用户注册</h1>

    <br />
    <form action="<?php  print($_SERVER["PHP_SELF"]) ?>" method="post" name="registration" onsubmit="return validate();">
    <table>
      <tr>
        <td>用户名:</td>
        <td><input name="user" type="text"></td>
      </tr>
      <tr>    
        <td>设置密码:</td>
        <td><input name="pass1" type="text"></td>
      </tr>
      <tr>      
        <td>确认密码:</td>
        <td><input name="pass2" type="text"></td>
      <tr>
        <td></td>
        <td><input type="submit" class="push" value="注册"></td>
      </tr>
    </table>   
    </form>

    <p><?php print($prompt) ?></p>
    

<?php
  require '../includes/footer.php';
?>
