<?php
  $json_str = file_get_contents('php://input'); //接收request的body
  $json_obj = json_decode($json_str); //轉成json格式
  
  $myfile = fopen("log.txt", "w+") or die("Unable to open file!"); //設定一個log.txt來印訊息
  fwrite($myfile, "\xEF\xBB\xBF".$json_str); //在字串前面加上\xEF\xBB\xBF轉成utf8格式
  
  $sender_userid = $json_obj->events[0]->source->userId; //取得訊息發送者的id
  $sender_txt = $json_obj->events[0]->message->text; //取得訊息內容
  $sender_replyToken = $json_obj->events[0]->replyToken; //取得訊息的replyToken
  
  $sender_txt=rawurlencode($sender_txt); //因為使用get的方式呼叫luis api，所以需要轉碼
  $ch = curl_init('https://westus.api.cognitive.microsoft.com/luis/v2.0/apps/89c4cdc5-40c5-434d-81c5-8ad158c9f945?subscription-key=60c72c72081d42daa45097d30699c2eb&timezoneOffset=-360&q='.$sender_txt);                                                                      
  curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "GET");                                                                                                                          
  curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
  $result_str = curl_exec($ch);
  fwrite($myfile, "\xEF\xBB\xBF".$result_str); //在字串前加上\xEF\xBB\xBF轉成utf8格式
  $result = json_decode($result_str);
  $ans_txt = $result -> topScoringIntent -> intent;
  $ans_intent = $result -> entities[0] -> entity;
  $response = array (
    "to" => $sender_userid,
    "messages" => array (
      array (
        "type" => "text",
        "text" => $ans_txt." ".$ans_intent
      )
    )
  );
  
  
 fwrite($myfile, "\xEF\xBB\xBF".json_encode($response)); //在字串前面加上\xEF\xBB\xBF轉成utf8格式
  $header[] = "Content-Type: application/json";
  $header[] = "Authorization: Bearer BjX2HZt6/dkTkPYf5+qAEvjXy97/Udan1/bwJFMFv6Jqsgtxbm1HDFqvNekUrDiXxvI3+VyI2N4WOQ9/yjn5M8+fbnVHzs02lJVRp25yyjeG54SwQsWW+4M7Sivz8mYfh5gpP6myJgNJ7HsCGycDugdB04t89/1O/w1cDnyilFU=";
  $ch = curl_init("https://api.line.me/v2/bot/message/push");
  curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "POST");
  curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($response));                                                                  
  curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);                                                                      
  curl_setopt($ch, CURLOPT_HTTPHEADER, $header);                                                                                                   
  $result = curl_exec($ch);
  curl_close($ch);
?>
