<?
/* vim: set expandtab tabstop=4 shiftwidth=4 foldmethod=marker: */
/**
* 작성일: 2009-11-09
* 작성자: 김종태
* 설   명: 성적데이터 업로드
*****************************************************************
* 
*/

$tpl->assign(array('doc_title'=>"<img src='/image/icon/cog_edit.png' align='absmiddle'> 성적데이터 업로드"));
$DB = &WebApp::singleton('DB');

switch ($REQUEST_METHOD) {
	case "GET":
	
	$tpl->setLayout('@admin');
	$tpl->define("CONTENT", $this->display->getTemplate("admin/sch/upload.htm"));
	
	 break;
	case "POST":
	include 'fileupload.inc';
	

	if($upfile1) {
	$file = new FileUpload("upfile1"); // datafile은 form에서의 이름 
	$file->Path = _DOC_ROOT."/hosts/".HOST."/tmp/";  // 마지막에 /꼭 붙여야함

	//$file->file_mkdir(); 
	if(!$file->Ext("xls"))  {
	echo '<script>alert("엑셀 파일만 가능합니다.");   history.go(-1); </script>';
	exit;
	 }
	$mk = mktime();

	$file->file_rename(); 
	if(!$file->upload()){
	echo '<script>alert("업로드에 실패 했습니다.");   //history.go(-1); </script>';
	exit;
	}
	$file->upload();
	//$file->Resize_Image("138","44","./hosts/".$_SERVER['HTTP_HOST']."/files/"); // 이미지일때 가로 세로 사이즈로 컨버팅


	}
	
	if($reset =="y"){
		 $sql = "delete from TAB_DATA WHERE num_oid=$_OID";
		 $DB->query($sql);
		 $DB->commit();
	}

	$xls_file = _DOC_ROOT."/hosts/".HOST."/tmp/sample.xls";
	include _DOC_ROOT."/module/Excel/reader.php";
	
	$data = new Spreadsheet_Excel_Reader();
	$data->setOutputEncoding('EUC-KR');
	$data->read($xls_file);

	error_reporting(E_ALL ^ E_NOTICE);
	

	$in = 0;
	$out = 0;
//	echo "<table border=1>";
	for ($i = 1; $i <= $data->sheets[0]['numRows']; $i++) {
		  // echo "<tr>";
		   for ($j = 1; $j <= $data->sheets[0]['numCols']; $j++) {
				  
				//  echo "<td>&nbsp;".$data->sheets[0]['cells'][$i][$j]."</td>";
				  $inque .=  ",'".$data->sheets[0]['cells'][$i][$j]."'";
				

		   }
			$inque = _OID.$inque;
			$sql = "INSERT INTO ".TAB_DATA." (
			num_oid, num_snumber, str_name, num_jumin, num_total_1, num_total_2, num_total_3, num_total_4, num_total_5, num_total_6,str_true
			) VALUES (
			$inque			
			) ";
			//echo $sql."<br><br>";
			 if($DB->query($sql)){
			 $DB->commit();

			 $in++;
			 }else{
			 $out++;
			 }
			  unset($inque);

		   //echo "</tr>\n";
		   flush();
	}
	//echo "</table>";

	$out = $out-1;
	if($reset=="y"){
	echo '<script>alert("'.($data->sheets[0]['numRows'] -1).'건중 '.$in.'건 입력되었습니다.\n실패'.$out.'건'.'");</script>';
	}else{
	echo '<script>alert("'.($data->sheets[0]['numRows'] -1).'건중 '.$in.'건 입력되었습니다.");</script>';
	}
	echo "<meta http-equiv='Refresh' Content=\"0; URL='admin.sch.list'\">";
	
	

	 break;
	}

?>