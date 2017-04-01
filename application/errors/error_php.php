<? if($severity !="Notice") {?>

<div style="border:1px solid #990000;padding-left:20px;margin:0 0 10px 0;">

<h4>PHP 에러가 발생하였습니다.</h4>

<p>심각성: <?php echo $severity; ?></p>
<p>에러메세지:  <strong><?php echo $message; ?></strong></p>
<p>파일명: <?php echo $filepath; ?></p>
<p>라인: <strong><?php echo $line; ?></strong></p>

</div>
<? } ?>