<footer id="footer">
  <small>ページで読書記録！ &copy; 2019 kogebuta. All Rights Reserved.</small>
  <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.4.1/jquery.min.js"></script>
  <script>
    $(function(){
      // フッター下部固定
      var $ftr = $('#footer');
      if( window.innerHeight > $ftr.offset().top + $ftr.outerHeight() ){
        $ftr.attr({'style': 'position:fixed; top:' + (window.innerHeight - $ftr.outerHeight()) +'px;' });
      }
      // 全角数字を半角数字に変換
      $(".js-half").change(function(){
        var before = $(this).val();
        var after = before.replace(/[０-９]/g,function(s){ return String.fromCharCode(s.charCodeAt(0)-0xFEE0) });
        $(this).val(after);
      });
      // メッセージ表示
      var $jsShowMsg = $('#js-show-msg');
      var msg = $jsShowMsg.text();
      if(msg.replace(/^[\s　]+|[\s　]+$/g, "").length){
        $jsShowMsg.slideToggle('slow');
        setTimeout(function(){ $jsShowMsg.slideToggle('slow'); }, 3000);
      }
    });
  </script>
</footer>
</body>
</html>
