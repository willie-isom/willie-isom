<?php
  $json_str = file_get_contents('php://input'); //接收request的body
  $json_obj = json_decode($json_str); //轉成json格式
  
  $myfile = fopen("Log.txt", "w+") or die("Unable to open file!"); //設定一個Log.txt來印訊息
  fwrite($myfile, "\xEF\xBB\xBF".$json_str); //\xEF\xBB\xBF轉成utf8格式
?>
