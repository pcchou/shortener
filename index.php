<?php
/*
 * 請將本程式放在站點根目錄，否則將無法正確運作
 * 作者: Birkhoff Lee (site: b.irkhoff.com)
 * 作者 E-mail: b@irkhoff.com  (有問題歡迎詢問)
 * 尊重著作權，請保留作者資訊
*/
header("Content-type: text/html; charset=utf-8");

/*  必讀
 *	第一次使用，請務必訪問一次 
 *  http://站點網址/index.php?action=regenerate_config
 *  來生成資料庫檔案，然後將下面的 $regenerate_config = true;
 *  改成 $regenerate_config = false;
*/
//---------------- 設定 ---------------//
/*
 * $newURL
 * 此為本程式所在站點的網址
 * 請在開頭加上 http:// 字串尾巴要有 /
*/
$newURL = 'http://site/';

/*
 * $json
 * 此為短網址資料庫存放位置
 * 請在開頭加上 /
 * 請一併更改 redirect.php 的相同一行
*/
$json = '/k8agJa1__.json';

/*
 * $regenerate_config
 * true : 可以透過 http://site/index.php?action=regenerate_config 來重置設定
 * false : 不可以重置設定（建議值）
*/
$regenerate_config = true;

//---------------- 請勿更改 ---------------//
$json = dirname(__FILE__) . $json;
if($newURL == 'http://site/'){
	die('請更改 index.php 中的站點網址!');
}
?>
<html>
  <head>
    <meta charset="utf-8">
    <title>URL Shortener - Powered by Birkhoff</title>
    <link rel="stylesheet" href="css/style.css">
    <link href='http://fonts.googleapis.com/css?family=Lato:400,700' rel='stylesheet' type='text/css'>
  </head>
  <body>
    <form action="#" method="post" name="Shortener">
      <div class="header">
         <p>URL SHORTENER</p>
      </div>
      <div class="description">
        <p><?php

if(isset($_POST['action']) and $_POST['action'] == 'generate'){
	if(	isset($_POST['url']) and
	stripos($_POST['url'], 'http') !== FALSE and 
	stripos($_POST['url'], ':') !== FALSE and 
	stripos($_POST['url'], '//') !== FALSE and 
	stripos($_POST['url'], '.') !== FALSE and 
	stripos($_POST['url'], '\r') === FALSE and 
	stripos($_POST['url'], '\n') === FALSE and 
	stripos($_POST['url'], '%00') === FALSE and 
	stripos($_POST['url'], '"') === FALSE and 
	stripos($_POST['url'], '\'') === FALSE and 
	stripos($_POST['url'], '{') === FALSE and 
	stripos($_POST['url'], '}') === FALSE){
		
		$done = false;
		$url = $_POST['url'];
		$valueADD = ' value="' . $_POST['url'] . '"';
		$urlJSON = json_decode(file_get_contents($json), true);
		foreach ($urlJSON as $key => $value) {
			if($value == $url and $done == false and !isset($_POST['id'])){
				$newURL .= $key;
				echo '完成！短網址：<a href="' . $newURL . '">' . $newURL . '</a>';
				$done = true;
			}
		}
		if(!$done){
			if(!isset($_POST['id'])){
				$x = sprintf("%u", crc32($url));
				$id = '';
				while($x > 0){
					$s = $x % 62;
					if ($s > 35){
						$s = chr($s + 61);
					} elseif ($s > 9 && $s <= 35){
						$s = chr($s + 55);
					}
					$id .= $s;
					$x = floor($x/62);
				}
				$urlJSON[$id] = $url;
				$fn = fopen($json, "w");
				foreach ($urlJSON as $key => $value) {
				    $ukey = urlencode($key);
				    $uvalue = urlencode($value);
				    $new_urlJSON[$ukey] = $uvalue;
				}
				fwrite($fn, urldecode(json_encode($new_urlJSON)));
				fclose($fn);

				$newURL .= $id;
				echo '完成！短網址：<a href="' . $newURL . '">' . $newURL . '</a>';
			} elseif(strlen($_POST['id'])!==5){
				echo '發生錯誤！請確認您的 自定代碼 長度為 5 個字元。';
			} elseif(!preg_match("/^(([a-z]+[0-9]+)|([0-9]+[a-z]+))[a-z0-9]*$/i", $_POST['id'])){
				echo '發生錯誤！請確認您的 自定代碼 不含有大小寫英文字母及數字以外的字元。';
			} else {
				$id = $_POST['id'];
				$urlJSON[$id] = $url;
				$fn = fopen($json, "w");
				foreach ($urlJSON as $key => $value) {
				    $ukey = urlencode($key);
				    $uvalue = urlencode($value);
				    $new_urlJSON[$ukey] = $uvalue;
				}
				fwrite($fn, urldecode(json_encode($new_urlJSON)));
				fclose($fn);

				$newURL .= $id;
				echo '完成！短網址：<a href="' . $newURL . '">' . $newURL . '</a>';
			}
		}
	} else {
		echo '發生錯誤！請確認您的 URL 符合正確格式：http(s)://*.*(/*)';
		$valueADD = ' value="' . $_POST['url'] . '"';
	}
} elseif(isset($_GET['action']) and $_GET['action'] == 'regenerate_config' and $regenerate_config) {
	$urlJSON = array('' => '');
	$fn = fopen($json, "w");
	fwrite($fn, json_encode($urlJSON));
	fclose($fn);

	echo '資料庫生成成功，請務必將 $regenerate_config 的值改為 false 以保資料庫安全!';
} else {
	echo '本服務開放給每個人使用，如果有不懂的地方或建議，煩請來信至 b[at]irkhoff.com 告知。感謝您。';
}
$valueADD = (isset($valueADD)) ? $valueADD : '';
?></p>
      </div>
      <div class="input">
        <input type="text" class="button" id="url" name="url" placeholder="http://www.google.com">
        <input type="text" class="button" id="id" name="id" placeholder="自定代碼 (可空)" maxlength="5">
        <input type="submit" class="button" id="submit" value="SHORTEN!">
      </div>
      <input type="hidden" id="action" name="action" value="generate">
    </form>
  </body>
</html>