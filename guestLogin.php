<?php
require('function.php');
require('auth.php');

$email = 'sample@gmail.com';
$pass = 'sample';

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
    $_SESSION['login_limit'] = 60 * 60;

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
