<?php
require('function.php');

if(!empty($_GET['p_id'])){
  $p_id = $_GET['p_id'];
  try {
    $dbh = dbConnect();
    // 消去するページ数と総ページ数を取得
    $sql = 'SELECT p.page,u.ttl_page AS total FROM pages AS p LEFT JOIN rr_users AS u ON p.user_id = u.id WHERE p.user_id = :u_id AND u.delete_flg = 0 AND p.id = :p_id';
    $data = array(':u_id' => $_SESSION['user_id'], ':p_id' => $p_id);
    $stmt = queryPost($dbh,$sql,$data);
    if($stmt){
      $result = $stmt->fetch(PDO::FETCH_ASSOC);
      debug(print_r($result,true));
      // 総ページから消去する分のページを引く
      $ttl_page = $result['total'] - $result['page'];
      echo $ttl_page;
      $sql1 = 'UPDATE rr_users SET ttl_page = :ttl_page WHERE id = :u_id';
      $sql2 = 'DELETE FROM pages WHERE user_id = :u_id AND delete_flg = 0 AND id = :p_id';
      $data1 = array(':ttl_page' => $ttl_page, ':u_id' => $_SESSION['user_id']);
      $stmt1 = queryPost($dbh,$sql1,$data1);
      $stmt2 = queryPost($dbh,$sql2,$data);
      if($stmt1 && $stmt2){
        debug('削除しました。');
        $_SESSION['message'] = '削除しました。';
        $url = (!empty($_SERVER['HTTP_REFERER'])) ? $_SERVER['HTTP_REFERER'] : 'pagelog.php';
        header('Location:'.$url);
      }
    }

  } catch (Exception $e) {
    error_log('エラー発生:'. $e->getMessage());
    $_SESSION['message'] = '削除に失敗しました。';
    $url = (!empty($_SERVER['HTTP_REFERER'])) ? $_SERVER['HTTP_REFERER'] : 'pagelog.php';
    header('Location:'.$url);
  }

}
