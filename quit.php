<?php
  require('function.php');
  require('auth.php');
  debug(print_r($_SESSION,true));
  $quit_flg = false;
  if(!empty($_POST['quit'])){
    $pass = $_POST['pass'];
    // バリデーション
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
        // パスワード照合
        $sql = 'SELECT password FROM rr_users WHERE id = :user_id AND delete_flg = 0';
        $data = array(':user_id' => $_SESSION['user_id']);
        $stmt = queryPost($dbh,$sql,$data);
        $result = $stmt->fetch(PDO::FETCH_ASSOC);

        if(!empty($result) && password_verify($pass,array_shift($result))){
          debug('パスワードがマッチしました。');
          debug('退会処理をします。');
          // デリートフラグに１を立てる
          $sql1 = 'UPDATE rr_users SET delete_flg = 1 WHERE id = :user_id';
          $sql2 = 'DELETE FROM pages WHERE user_id = :user_id';
          $stmt1 = queryPost($dbh,$sql1,$data);
          $stmt2 = queryPost($dbh,$sql2,$data);
          if($stmt1){
            debug('退会処理が完了しました。');
            unset($_SESSION['login_date']);
            unset($_SESSION['login_limit']);
            unset($_SESSION['user_id']);
            $quit_flg = true;
            // header('Location:signup.php');
          }
        }else{
          debug('パスワードがアンマッチです。');
          $err_msg['pass'] = MSG10;
        }

      } catch (Exception $e) {
        error_log('エラー発生:'. $e->getMessage());
        $err_msg['common'] = MSG07;
      }
    }
  }

?>
<?php
  $page_title = '退会';
  require('head.php');
  require('header.php');
?>
    <main>
      <h1>退会</h1>
      <form id="quit" action="" method="post">
        <?php if($gestLoginFlg){ ?>
          <p>ゲストログイン状態では退会はできません。</p>
          <div class="button button-margin">
            <a href="mypage.php">マイページへ</a>
          </div>
        <?php }elseif(!$gestLoginFlg && empty($_POST)){ ?>
          <!-- 確認画面 -->
          <p>退会すると登録されたすべての情報が消去されます。<br>本当に退会しますか？</p>
          <div class="button">
            <input type="submit" name="yes" value="退会する">
          </div>
        <?php }elseif(!empty($_POST) && !$quit_flg){ ?>
          <!-- パスワード入力画面 -->
          <p class="err-common"><?php getErrMsg('common',false); ?></p>
          <p>パスワードを入力してください。</p>
          <label>
            <input type="password" name="pass" value="<?php if(!empty($_POST['pass'])) echo $_POST['pass']; ?>">
            <p class="err-msg"><?php getErrMsg('pass'); ?></p>
          </label>
          <div class="button">
            <input type="submit" name="quit" value="退会">
          </div>
        <?php }elseif($quit_flg){ ?>
          <p>退会処理が完了しました。<br>ご利用ありがとうございました。</p>
          <div class="button button-margin">
            <a href="signup.php">新規登録画面へ</a>
          </div>

        <?php } ?>
      </form>
    </main>
<?php
require('footer.php');
?>
