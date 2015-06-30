<?php

$dir = $_POST['target_dir'];
$files = scandir($dir);
$files = array_filter($files, function ($file) {
  return !in_array($file, array('.', '..'));
});

$file_list = array();
foreach ($files as $file) {
  // 隠しファイルをリストから取り除く
  if(substr($file,0,1) == '.'){
  	continue;
  }

  $fullpath = rtrim($dir, '/') . '/' . $file;
  if (is_file($fullpath)) {
  	$file_list[] = $file;
  }
}
?>

<!DOCTYPE html>
<html>
<head>
<meta charset="UTF-8">	
<style text/css>
	.btn {
		display: block;
		margin:0 auto;
  	text-decoration: none;
  	font-weight: bold;
  	text-align: center;
  	font-size: 13px;
		width:80px;
		background-color:#4169E1;
		color:white;
		padding:10px 20px;
		border-radius:5px;
	}
	.btn:hover {
		opacity:0.7;
	}
</style>
</head>
<body>
<?php

if ($_POST["target_dir"]) {
	$directory = $_POST["target_dir"];
	$content = "
	<!DOCTYPE html>
	<html>
	<head>
	<meta charset='UTF-8'>
	</head>
	<body>
	<table rules='all' style='border:solid 2px;'>
	<thead>
	<tr>
	<td>お気に入り</td>
	<td>ファイル名</td>
	<td>重要度</td>
	<td>色</td>
	<td>説明</td>
	</tr>
	</thead>
	<tbody>";
	foreach($file_list as $file){
		$post_reader = str_replace(".", "_", $file);
		$content .= "<tr>";
		$content .= "<td>";
		if($_POST['fav_'.$post_reader] == 'true'){
			$content.= "<span>★</span>";
		}
		$content .= "</td>";

		$content .= "<td>";
		if($_POST['color_'.$post_reader] == 'red'){
			$content .= '赤';
		}else if($_POST['color_'.$post_reader] == 'blue'){
			$content .= '青';
		}else if($_POST['color_'.$post_reader] == 'green'){
			$content .= '緑';
		}else if($_POST['color_'.$post_reader] == 'yellor'){
			$content .= '黄';
		}else if($_POST['color_'.$post_reader] == 'black'){
			$content .= '黒';
		}else{
			$content .= '白';
		}
		$content .= "</td>";

		$content .= "<td>".$file."</td>";
		$content .= "<td>".$_POST['important_'.$post_reader]."</td>";

		$content .= "<td>";
		if($_POST['explain_'.$post_reader]){
			$content .= $_POST['explain_'.$post_reader];
		}
		$content .= "</td>";
		$content .= "</tr>";

	}
  $content .= "
  </tbody>
	</body>
	</html>
	";

	$directory = mb_convert_encoding($directory, "UTF8", "AUTO");

	if(get_magic_quotes_gpc()) { $directory = stripslashes($directory); } 

	$filename = "index.html";

	$handle = fopen( $directory."/".$filename, 'w');
	fwrite( $handle, $content);
	fclose( $handle );

	echo $filename. "を生成し、書き込みを行いました。";
} else {
	echo "index-generator.phpからフォームを送信してください。";
}
?>
<p><a class='btn' href='index-generator.php'>戻る</a></p>
</body>
</html>