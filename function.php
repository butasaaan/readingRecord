<?php

//=================
//ログ
//=================
ini_set('log_errors','on');
ini_set('error_log','php.log');
ini_set('display_errors',1);

//=================
//デバッグ
//=================
$debug_flg = false;
function debug($str){
  global $debug_flg;
  if($debug_flg){
    error_log('デバッグ:[[['.$str.']]]');
  }
}

// =============================
// セッション準備・セッション有効期限を延ばす
// =============================
// セッションファイルの置き場を変更する
session_save_path("/var/tmp/");
// ガーベージコレクションが削除するセッションの有効期限を設定
ini_set('session.gc_maxlifetime',60*60*24*30);
// ブラウザを閉じても削除されないようにクッキー自体の有効期限を延ばす
ini_set('session.cookie_lifetime',60*60*24*30);
// セッションを使う
session_start();
// 現在のセッションIDを新しく生成したものと置き換える（なりすましのセキュリティ対策）
session_regenerate_id();

//=================
//定数
//=================
define('MSG01','入力必須です。');
define('MSG02','E-mailの形式で入力してください。');
define('MSG03','文字以上で入力してください。');
define('MSG04','文字以内で入力してください。');
define('MSG05','再入力の値と違います。');
define('MSG06','半角で入力してください。');
define('MSG07','エラーが発生しました。しばらく経ってからやり直してください。');
define('MSG08','そのメールアドレスは既に登録されています。');
define('MSG09','メールアドレス または パスワードが違います。');
define('MSG10','パスワードが違います。');
define('MSG11','数字で入力してください。');
define('MSG12','今日以前の日付で入力してください。');
define('MSG13','一度に登録できるのは1000ページまでです。同じ日付で複数回登録すると、ページ数が加算されます。');

//=================
//ゲストログイン
//=================
if(!empty($_SESSION['user_id'])){
  if($_SESSION['user_id'] == 1){
    $gestLoginFlg = true;
  }else{
    $gestLoginFlg = false;
  }
}


//=================
//バリデーション関数
//=================
$err_msg = array();
// 入力必須バリデーション
function validRequired($str,$key){
  global $err_msg;
  if($str === ""){
    $err_msg[$key] = MSG01;
  }
}
// Email形式バリデーション
function validEmail($str,$key){
  global $err_msg;
  if(!preg_match("/^([a-zA-Z0-9])+([a-zA-Z0-9\?\*\[|\]%'=~^\{\}\/\+!#&\$\._-])*@([a-zA-Z0-9_-])+\.([a-zA-Z0-9\._-]+)+$/", $str)){
    $err_msg[$key] = MSG02;
  }
}
// 最少文字数バリデーション
function validMinLen($str,$key,$min = 6){
  global $err_msg;
  if(mb_strlen($str) < $min){
    $err_msg[$key] = $min.MSG03;
  }
}
// 最大文字数バリデーション
function validMaxLen($str,$key,$max = 255){
  global $err_msg;
  if(mb_strlen($str) > $max){
    $err_msg[$key] = $max.MSG04;
  }
}
// 再入力一致バリデーション
function validMatch($str1,$str2,$key){
  global $err_msg;
  if($str1 !== $str2){
    $err_msg[$key] = MSG05;
  }
}
// 半角英数字バリデーション
function validHalf($str,$key){
  global $err_msg;
  if(!preg_match("/^[a-zA-Z0-9]+$/", $str)){
    $err_msg[$key] = MSG06;
  }
}
// Email重複バリデーション
function validEmailDup($email){
  global $err_msg;
  try {
    $dbh = dbConnect();
    $sql = 'SELECT count(*) FROM rr_users WHERE email = :email AND delete_flg = 0';
    $data = array(':email' => $email);
    $stmt = queryPost($dbh,$sql,$data);

    $result = $stmt->fetch(PDO::FETCH_ASSOC);
    if(!empty(array_shift($result))){
      $err_msg['email'] = MSG08;
    }
  } catch (Exception $e) {
    error_log('エラー発生:' . $e->getMessage());
    $err_msg['common'] = MSG07;
  }
}
// 数字バリデーション
function validNum($str,$key){
  global $err_msg;
  if(!preg_match("/^[0-9]+$/", $str)){
    $err_msg[$key] = MSG11;
  }
}
// 日付バリデーション
function validDate($str,$key){
  global $err_msg;
  $str = strtotime($str);
  $today = strtotime(date('Y-m-d'));
  if($str > $today){
    $err_msg[$key] = MSG12;
  }
}
// 最大数値バリデーション
function validMaxNum($str,$key){
  global $err_msg;
  if($str > 1000){
    $err_msg[$key] = MSG13;
  }
}
// エラーメッセージ取得関数
function getErrMsg($key,$before = true){
  global $err_msg;
  if(!empty($err_msg[$key])){
    if($before){
      echo '▲'.$err_msg[$key];
    }else{
      echo '<i class="fas fa-exclamation-circle"></i>  '.$err_msg[$key];
    }
  }
}
//=================
// データベース
//=================
// データベース接続
function dbConnect(){
  $dsn = 'mysql:dbname=readingrecord;host=localhost;charset=utf8';
  $user = 'root';
  $password = 'root';
  $options = array(
    PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
    PDO::MYSQL_ATTR_USE_BUFFERED_QUERY => true,
  );
  $dbh = new PDO($dsn,$user,$password,$options);
  return $dbh;
}
// クエリ実行
function queryPost($dbh,$sql,$data){
  $stmt = $dbh->prepare($sql);
  if(!$stmt->execute($data)){
    debug('クエリに失敗しました。');
    debug('失敗したSQL:'.print_r($stmt,true));
    $err_msg['common'] = MSG07;
    return 0;
  }
  debug('クエリ成功。');
  return $stmt;
}
// 現在の総ページを取得
function getTotalPage(){
  try {
    $dbh = dbConnect();
    $sql = 'SELECT ttl_page FROM rr_users WHERE id = :user_id AND delete_flg = 0';
    $data = array(':user_id' => $_SESSION['user_id']);
    $stmt = queryPost($dbh,$sql,$data);
    $result = $stmt->fetch(PDO::FETCH_ASSOC);
    if($result){
      return $result['ttl_page'];
    }
  } catch (Exception $e) {
    error_log('エラー発生:'. $e->getMessage());
    $err_msg['common'] = MSG07;
  }
}
// ページ登録
function registPage($read_date,$score,$total){
  try {
    $dbh = dbConnect();
    // 新規ログ登録用
    // 同じ日付で既に登録されていないか確認
    $sql = 'SELECT read_date,page FROM pages WHERE user_id = :u_id AND read_date = :read_date';
    $data = array(':u_id' => $_SESSION['user_id'], ':read_date' => $read_date);
    $stmt = queryPost($dbh,$sql,$data);
    $result = $stmt->fetch(PDO::FETCH_ASSOC);

    if($result){
      debug('同じ日付で既に登録されています。データを更新します。');
      // 登録済みの場合ページ数を加算
      $sql = 'UPDATE pages SET page = :page WHERE user_id = :u_id AND read_date = :read_date';
      $data = array(':page' => $score + $result['page'],':u_id' => $_SESSION['user_id'], ':read_date' => $read_date);
      $stmt1 = queryPost($dbh,$sql,$data);
    }else{
      debug('新規で登録します。');
      // 未登録の場合、新規登録する
      $sql = 'INSERT INTO pages (user_id, read_date, page, create_date) VALUES (:u_id, :read_date, :page, :create_date)';
      $data = array(':u_id' => $_SESSION['user_id'], ':read_date' => $read_date, ':page' => $score, ':create_date' => date("Y-m-d H:i:s"));
      $stmt1 = queryPost($dbh,$sql,$data);
    }

    if($stmt1){
      // 総ページ更新用
      debug('総ページを更新します');
      $sql = 'UPDATE rr_users SET ttl_page = :total WHERE id = :u_id AND delete_flg = 0';
      $data = array(':total' => $total, ':u_id' => $_SESSION['user_id']);
      $stmt = queryPost($dbh,$sql,$data);
      if($stmt){
        $_SESSION['message'] = '<i class="far fa-check-circle"></i>  登録に成功しました。';

      }
    }

  } catch (Exception $e) {
    error_log('エラー発生:'. $e->getMessage());
    $err_msg['common'] = MSG07;
  }
}
// ページリスト取得
function getPages($currentMinNum = 1,$sort, $span = 20){
  try {
    $dbh = dbConnect();
    // 件数取得用のSQL文
    $sql = 'SELECT id FROM pages WHERE user_id = :u_id';
    $data = array(':u_id' => $_SESSION['user_id']);
    $stmt = queryPost($dbh,$sql,$data);
    $rst['total'] =$stmt->rowCount(); //総レコード数
    $rst['total_page'] = ceil($rst['total']/$span);
    if(!$stmt){
      return false;
    }

    // ページング用のSQL文作成
    $sql = 'SELECT id,page,read_date FROM pages WHERE user_id = :u_id ORDER BY ';
    $data = array(':u_id' => $_SESSION['user_id']);
    switch ($sort) {
      case 1:
        $sql .= 'read_date DESC';
        break;
      case 2:
        $sql .= 'read_date ASC';
        break;
      case 3:
        $sql .= 'page DESC';
        break;
      case 4:
        $sql .= 'page ASC';
        break;
    }
    $sql .= ' LIMIT '.$span.' OFFSET '.(($currentMinNum - 1) * $span );
    $stmt = queryPost($dbh,$sql,$data);
    if($stmt){
      $rst['data'] = $stmt->fetchall();
      return $rst;
    }else{
      return false;
    }

  } catch (Exception $e) {
    error_log('エラー発生:'. $e->getMessage());
    $err_msg['common'] = MSG07;
  }
}
function getListForGraph($start_date,$end_date){
  try {
    $dbh = dbConnect();
    $sql = 'SELECT read_date,page FROM pages WHERE user_id = :u_id AND read_date BETWEEN :start_date AND :end_date ORDER BY read_date ASC';
    $data = array(':u_id' => $_SESSION['user_id'],':start_date' => $start_date, ':end_date' => $end_date);
    $stmt = queryPost($dbh,$sql,$data);
    if($stmt){
      return $stmt->fetchall();
    }
  } catch (Exception $e) {
    error_log('エラー発生:'. $e->getMessage());
    $err_msg['common'] = MSG07;
  }
}
//=================
// その他
//=================
// サニタイズ
function sanitize($str){
  return htmlspecialchars($str,ENT_QUOTES);
}
// ページネーション
// $currentPageNum : 現在のページ数
// $totalPageNum : 総ページ数
// $link : 検索用GETパラメータリンク
// $pageColNum : ページネーション表示数
function pagination( $currentPageNum, $totalPageNum, $link = '', $pageColNum = 5){
  // 現在のページが、総ページ数と同じ　かつ　総ページ数が表示項目数以上なら、左にリンク４個出す
  if( $currentPageNum == $totalPageNum && $totalPageNum > $pageColNum){
    $minPageNum = $currentPageNum - 4;
    $maxPageNum = $currentPageNum;
  // 現在のページが、総ページ数の１ページ前なら、左にリンク３個、右に１個出す
  }elseif( $currentPageNum == ($totalPageNum-1) && $totalPageNum > $pageColNum){
    $minPageNum = $currentPageNum - 3;
    $maxPageNum = $currentPageNum + 1;
  // 現ページが2の場合は左にリンク１個、右にリンク３個だす。
  }elseif( $currentPageNum == 2 && $totalPageNum > $pageColNum){
    $minPageNum = $currentPageNum - 1;
    $maxPageNum = $currentPageNum + 3;
  // 現ページが1の場合は左に何も出さない。右に５個出す。
  }elseif( $currentPageNum == 1 && $totalPageNum > $pageColNum){
    $minPageNum = $currentPageNum;
    $maxPageNum = 5;
  // 総ページ数が表示項目数より少ない場合は、総ページ数をループのMax、ループのMinを１に設定
  }elseif($totalPageNum < $pageColNum){
    $minPageNum = 1;
    $maxPageNum = $totalPageNum;
  // それ以外は左に２個出す。
  }else{
    $minPageNum = $currentPageNum - 2;
    $maxPageNum = $currentPageNum + 2;
  }

  echo '<div class="pagination">';
    echo '<ul class="pagination-list">';
      if($currentPageNum != 1){
        echo '<li class="list-item "><a href="?p=1'.$link.'">&lt;</a></li>';
      }
      for($i = $minPageNum; $i <= $maxPageNum; $i++){
        echo '<li class="list-item ';
        if($currentPageNum == $i ){ echo 'active'; }
        echo '"><a href="?p='.$i.$link.'">'.$i.'</a></li>';
      }
      if($currentPageNum != $maxPageNum && $maxPageNum > 1){
        echo '<li class="list-item"><a href="?p='.$maxPageNum.$link.'">&gt;</a></li>';
      }
    echo '</ul>';
  echo '</div>';
}

// レベル設定
$level = array();
$level[0] = 0;
$num = 499;

function levelCreate(){
  global $level;
  global $num;
  global $lv_img;
  // 各レベルの必要ページ数作成
  for ($i=1; $i < 52; $i++) {
    if($i < 11){
      $level[$i] = $num;
      $num += 1000;
    }elseif($i < 22){
      $level[$i] = $num;
      $num += 1500;
    }elseif($i < 31){
      $level[$i] = $num;
      $num += 2000;
    }elseif($i < 41){
      $level[$i] = $num;
      $num += 2500;
    }else{
      $level[$i] = $num;
      $num += 3000;
    }
  }
  $level[52] = 99999999999;
}

function lvimgCreate($i){
  // 各レベルで表示するイメージ
  debug('レベルに合ったimgを選択します');
  switch ($i) {
    case $i === 1:
      return "img/tree01.png";
      break;
    case $i < 5:
      return "img/tree02.png";
      break;
    case $i < 10:
      return "img/tree03.png";
      break;
    case $i < 15:
      return "img/tree04.png";
      break;
    case $i < 20:
      return "img/tree05.png";
      break;
    case $i < 25:
      return "img/tree06.png";
      break;
    case $i < 30:
      return "img/tree07.png";
      break;
    case $i < 35:
      return "img/tree08.png";
      break;
    case $i < 45:
      return "img/tree09.png";
      break;
    default:
      return "img/tree10.png";
      break;
  }
}
// スライドメッセージ表示
function getSessionFlash(){
  if(!empty($_SESSION['message'])){
    $data = $_SESSION['message'];
    $_SESSION['message'] = '';
    return $data;
  }
}
