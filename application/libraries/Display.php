<?
/* vim: set expandtab tabstop=4 shiftwidth=4 foldmethod=marker: */
/**
* 작성일: 2012-02-10
* 작성자: 김종태
* 설   명: 템플릿 언더바 확장
*****************************************************************
* 
*/

require_once dirname(__FILE__)."/Template_.php";

class Display extends Template_ {
	var $template_dir = '.';
	var $compile_dir = 'application/cache/template';
	var $cache_dir = 'application/cache/output';
	var $prefilter = 'emulate_include|adjustPath|customtag';
	var $postfilter = 'arrangeSpace';
	var $html_head = '';
	var $html_body = '';
	var $layout = '';


	function __construct()  {
		if (func_num_args()) $this->setLayout(func_get_arg());
	}

	function setLayout($conf) {
	
		if(!$conf) $conf ="@sub";

		if($_GET[ch]) $conf = $_GET[ch];

		if ($conf{0} == '@') {
			$_file = Display::getTemplate('layout.php');
			$conf = substr($conf,1);
		} else {
			$_file = 'application/config/layout.php';
		}
		
		$this->layout = $conf;
		$layout_conf = @parse_ini_file($_file,true);

		$layers = $layout_conf[$this->layout];
		@array_walk($layers,array(&$this,'cb_apply_theme'));
		
		$this->define($layers);

		
		
	}

	function define($arg, $path='') {
		if ($path) {
			$path = $this->cb_apply_theme($path);
			$this->_define($arg, $path);
		} else {
			foreach ((array)$arg as $fid => $path) {
				$path = $this->cb_apply_theme($path);
				$this->_define($fid, $path);
			}
		}
	}

	function define_doc($area,$str) {
		$this->define('#'.$area,$str);
	}


	function getTemplate($filename, $THEME = THEME) {
	
		$tpl_order = array(

			'html/'.$filename,
			'application/config/'.$THEME.'/'.$filename,
			'application/views/'.$filename,
			'application/libraries/src/tpl/'.$filename,
			'application/'.$filename,
			''.$filename,
		);
		


		for ($i=0,$cnt=count($tpl_order); $i<$cnt;$i++) {
			$template = $tpl_order[$i];
		//	echo $template."<br>";
			if (!is_file($template)) continue;
			
			return $template;
			break;
		}
	}


	function array_merge_recursive2($arr1, $arr2) {
		if (!is_array($arr1) or !is_array($arr2)) return $arr2;
		foreach ($arr2 AS $key2 => $val2) $arr1[$key2] = Display::array_merge_recursive2(@$arr1[$key2], $val2);
		return $arr1;
	}



	function parse($arg,$var='') {	// for 하위호환성 유지 (루프 영역의 데이타를 개별적으로 중첩 assign 한다)
		if (is_array($var)) return $this->assign($arg,$var);

	}

    function push_head($str) {
        $this->html_head.= $str."\n";
    }

    function push_body($str) {
        $this->html_body.= $str."\n";
    }

	function printAll() {
		global $REMOTE_ADDR;
		if ($this->tpl_['CONTENT']) {

			if ($GLOBALS['DOC_TITLE']) {
				$this->assign('title_text',$GLOBALS['DOC_TITLE']);
			}
            if ($this->html_head || $this->html_body) {
			
                $output = $this->fetch('LAYOUT');
                echo preg_replace(
                    array('@</head@','@</body@'),
                    array($this->html_head.'</head', $this->html_body.'</body'),
                    $output
                );
            } else {

				
				$this->print_('LAYOUT');
            }
		}
	}

	function cb_apply_theme(&$arr) {
		if ($arr{0} == '@') {
			$arr = Display::getTemplate(substr($arr,1));
		}
		return $arr;
	}


 function text_cut($str, $len, $suffix="...") { 
   if ($len >= strlen($str)) return $str;
   $klen = $len - 1;
   while (ord($str{$klen}) & 0x80) $klen--;
   return substr($str, 0, $len - ((($len + $klen) & 1) ^ 1)) . $suffix;
 }




}

?>
