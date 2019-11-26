<?php
  require('function.php');
  require('auth.php');

  if(!empty($_POST)){
    $email = $_POST['email'];
    $pass = $_POST['pass'];
    $pass_re = $_POST['pass_re'];

    // Emailバリデーション
    validRequired($email,'email');
    if(empty($err_msg['email'])){
      validEmail($email,'email');
      validMaxLen($email,'email');
      validEmailDup($email);
    }
    // パスワードバリデーション
    validMatch($pass,$pass_re,'pass');
    if(empty($err_msg['pass'])){
      validRequired($pass,'pass');
      validRequired($pass_re,'pass_re');
      validHalf($pass,'pass');
      validMinLen($pass,'pass');
      validMaxLen($pass,'pass',50);
    }

    if(empty($err_msg)){
      debug('バリデーションOK');

      try {
        $dbh = dbConnect();
        $sql = 'INSERT INTO rr_users (email,password,login_time, create_date) VALUES (:email,:password,:login_time, :create_date)';
        $data = array(':email' => $email, ':password' => password_hash($pass,PASSWORD_DEFAULT), ':login_time' => date('Y-m-d H:i:s'), ':create_date' => date('Y-m-d H:i:s'));

        $stmt = queryPost($dbh,$sql,$data);
        if($stmt){
          $_SESSION['login_date'] = time();
          $_SESSION['login_limit'] = 60 * 60;
          $_SESSION['user_id'] = $dbh->lastInsertId();
          debug(print_r($_SESSION,true));
          header("Location:mypage.php");
        }
      } catch (\Exception $e) {
        error_log('エラー発生:' . $e->getMessage());
        $err_msg['common'] = MSG07;
      }
    }
  }
?>
<?php
  $page_title = '新規登録';
  require('head.php');
?>
<?php require('header.php'); ?>
    <main>
      <h1>新規登録</h1>
      <form id="login" action="" method="post">
        <p class="err-common"><?php getErrMsg('common',false); ?></p>
        <label><p class="title">E-mail</p>
          <input type="text" name="email" value="<?php if(!empty($_POST['email'])) echo $_POST['email']; ?>">
          <p class="err-msg"><?php getErrMsg('email'); ?></p>
        </label>
        <label><p class="title">パスワード</p>
          <input type="password" name="pass" value="<?php if(!empty($_POST['pass'])) echo $_POST['pass']; ?>">
          <p class="err-msg"><?php getErrMsg('pass'); ?></p>
        </label>
        <label><p class="title">パスワード再入力</p>
          <input type="password" name="pass_re" value="<?php if(!empty($_POST['pass_re'])) echo $_POST['pass_re']; ?>">
          <p class="err-msg"><?php getErrMsg('pass_re'); ?></p>
        </label>
        <div class="button">
          <input type="submit" value="SIGN UP">
        </div>
        <p class="guest"><a href="guestLogin.php">ゲストでログインする</a></p>
      </form>
    </main>
<?php
require('footer.php');
?>
