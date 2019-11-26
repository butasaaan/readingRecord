<?php
  require('function.php');
  require('auth.php');
  debug(print_r($_SESSION,true));
  if(!empty($_POST)){
    $email = $_POST['email'];
    $pass = $_POST['pass'];
    $check_flg = (!empty($_POST['check'])) ? true : false;
    // Emailバリデーション
    validRequired($email,'email');
    if(empty($err_msg['email'])){
      validEmail($email,'email');
      validMaxLen($email,'email');
    }
    // パスワードバリデーション
    validRequired($pass,'pass');
    if(empty($err_msg['pass'])){
      validMinLen($pass,'pass');
      validMaxLen($pass,'pass',50);
      validHalf($pass,'pass');
    }

    if(empty($err_msg)){
      debug('バリデーションOK');
      try {
        $dbh = dbConnect();
        $sql = 'SELECT password, id FROM rr_users WHERE email = :email AND delete_flg = 0';
        $data = array(':email' => $email);
        $stmt = queryPost($dbh,$sql,$data);
        $result = $stmt->fetch(PDO::FETCH_ASSOC);

        debug('クエリ結果の中身'.print_r($result,true));


        if(!empty($result) && password_verify($pass,array_shift($result))){
          debug('パスワードがマッチしました。');
          $_SESSION['login_date'] = time();
          $_SESSION['user_id'] = $result['id'];
          if($check_flg){
            $_SESSION['login_limit'] = 60 * 60 * 24 * 30;
          }else{
            $_SESSION['login_limit'] = 60 * 60;
          }
          debug('セッション変数の中身:'.print_r($_SESSION,true));
          header("Location:mypage.php");
        }else{
          debug('パスワードがアンマッチです。');
          $err_msg['common'] = MSG09;
        }
      } catch (Exception $e) {
        error_log('エラー発生:'. $e->getMessage());
        $err_msg['common'] = MSG07;
      }
    }
  }

?>
<?php
  $page_title = 'ログイン';
  require('head.php');
?>
<?php require('header.php'); ?>
    <main>
      <h1>LOGIN</h1>
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
        <label>
          <input class="check" type="checkbox" name="check" value="check" <?php if(!empty($_POST['check'])) echo 'checked'; ?>>ログイン状態を保持する
        </label>
        <div class="button">
          <input type="submit" value="LOGIN">
        </div>
        <p class="guest"><a href="guestLogin.php">ゲストでログインする</a></p>
      </form>
    </main>
<?php
require('footer.php');
?>
