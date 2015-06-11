<!DOCTYPE html>
<html>
<head>
<link rel='stylesheet' href='mystyle.css' />
<script type="text/javascript" src='jquery-1.11.2.min.js'></script>
<script type="text/javascript" src='myscript.js'></script>

</head>
<body>
<div class='contents'>

<?php
$files = scandir(".");
$files = array_filter($files, function ($file) {
    return !in_array($file, array('.', '..'));
});
 
$dir = '.';
$folder_list = array();
$file_list = array();
foreach ($files as $file) {
    $fullpath = rtrim($dir, '/') . '/' . $file;
    if (is_file($fullpath)) {
    		preg_match("/(.*)\.(.*)/is",$file,$match);
        $filename_list[]      = $match[0];
        $fileextension_list[] = $match[2];
        $filepath_list[]      = $fullpath;
    }
    if (is_dir($fullpath)) {
        $folder_list[] = $file;
    }
}

$filedetail_list = array();
foreach($filepath_list as $filepath){
	$filedetail_list[] = stat($filepath);
}

?>

<table rules="all">
	<tr>
		<td rowspan= '3' colspan='2' style='height:300px; width:100px'>
			<?php 
				foreach($folder_list as $forder){
					echo $forder.'<br />';
				}
			?>
		</td>
		<td height='30px' colspan='4'><?php echo dirname(__FILE__) ?></td>
		<td height='30px' colspan='3'>検索:
			<input class='search' type="text" name="search" size='30'>
		</td>
	</tr>

	<tr height='10px'>
		<th>ファイル名</th>
		<th>拡張子</th>
		<th>サイズ</th>
		<th>作成日</th>
		<th>最終更新日時</th>
		<th>重要度</th>
		<th>色</th>
	</tr>

	<tr>
		<td rowspan='2'>
			<?php
			foreach($filename_list as $number => $filename){
				?>
				<div class='file<?php echo $number ?>'>
				<span id='fav<?php echo $number ?>'class='favstar' onClick='changeFav(this);'>★</span>
				<span class='name'><?php echo $filename ?></span><br />
				</div><?php
			}
			?>
		</td>
		<td rowspan='2'>
			<?php
			foreach($fileextension_list as $number => $extension){
			  ?><div class='file<?php echo $number ?>'><?php echo $extension ?><br /></div><?php
		  }
			?>
		</td>
		<td rowspan='2'>
			<?php
			foreach($filedetail_list as $number => $detail){
		  	?><div class='file<?php echo $number ?>'><?php echo $detail['size'] ?><br /></div><?php
		  }
		  ?>
		</td>
		<td rowspan='2'>
			<?php
			foreach($filepath_list as $number => $filepath){
				?><div class='file<?php echo $number ?>'><?php echo strftime("%Y-%m-%d",filectime($filepath)) ?><br /></div><?php

		  }
		  ?>
		</td>
		<td rowspan='2'>
			<?php
			foreach($filedetail_list as $number => $detail){
				?><div class='file<?php echo $number ?>'><?php echo strftime("%Y-%m-%d %T",$detail['mtime']) ?><br /></div><?php
		  }
		  ?>
		</td>
		<td rowspan='2'>
			<?php 
			$count = 0;
			while($count < count($filename_list)):
				?>
				<div>
				<select name="important" class='important' onChange='changeFontSize(this,<?php echo $count ?>);'>
					<option value="row" class='row'>低</option>
					<option value="normal"  class='normal'>中</option>
					<option value="high"   class='high'>高</option>
				</select>
				</div>
				<?php
				$count++;
			endwhile;
			?>
		</td>
		<td rowspan='2'>
			<?php 
			$count = 0;
			while($count < count($filename_list)):
				?>
				<div>
				<select name="color" class='color' onChange="chBackGround(this); changeFileColor(this,<?php echo $count ?>);">
					<option value="red"   class='white'>白</option>
					<option value="blue"  class='blue'>青</option>
					<option value="green" class='green'>緑</option>
					<option value="yellow" class='yellow'>黄</option>
					<option value="black" class='black'>黒</option>
					<option value="red" class='red'>赤</option>
				</select>
				</div>
				<?php
				$count++;
			endwhile;
			?>
		</td>
	</tr>
	<tr>
		<td colspan='2'>
			説明<br />
		</td>
	</tr>
</table>

<a class="btn save" href="#">保存</a>

</div>
</body>
</html>