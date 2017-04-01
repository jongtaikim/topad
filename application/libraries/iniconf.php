<?
/* vim: set expandtab tabstop=4 shiftwidth=4 foldmethod=marker: */
/**
* 작성일: 2012-02-19
* 작성자: 김종태
* 설   명: ini 파일 컨트롤
*****************************************************************
* 
*/
define(PROCESS_PLAIN,0);
define(PROCESS_SECTION,1);
define(IFEXIST_OVERWRITE,0);
define(IFEXIST_WARNING,1);
define(IFEXIST_ERROR,2);

class IniConf {

	var $file;
	var $iniArray;
	var $debug;
	var $report;
	var $_error;
	var $_warning;
	
	function __construct($file="") {
		$this->iniArray = array();
		$this->sections = array();
		$this->_error = array();
		$this->_warning = array();
		if ($file) {
			if (is_file($file)) {
				$this->load($file);
			} else {
				$this->create($file);
			}
		}
	}

	function setDebug($mode=1) {
		$this->debug = $mode;
		return true;
	}

	function setErrorReport($mode="E") {
		$this->report = $mode;
		return true;
	}

	function create($file="",$mode=IFEXIST_WARNING) {
		if ($file) $this->file = $file;
		if (is_file($this->file)) {
			if ($mode == IFEXIST_WARNING) {
				$this->raiseWarning(sprintf(_("File already exists [ %s ]"),$this->file));
			} elseif ($mode == IFEXIST_ERROR) {
				return $this->raiseError(sprintf(_("File already exists [ %s ]"),$this->file));
			}
		}
		return true;
	}

	function load($file) {
		$this->file = $file;
		if (!file_exists($this->file)) $this->raiseError(sprintf(_("File not exists [ %s ]"),$this->file));
		return $this->_parse();
	}

	function save($file="",$mode=IFEXISTS_OVERWRITE) {
		if ($file) $this->file = $file;
		if (is_file($this->file)) {
			if ($mode == IFEXIST_WARNING) {
				$this->raiseWarning("File already exists [ $this->file ]");
			} elseif ($mode == IFEXIST_ERROR) {
				return $this->raiseError("File already exists [ $this->file ]");
			}
		}
        $parts = explode("/", $this->file);
        $filename = array_pop($parts);
        for ($i=0;$i<count($parts);$i++) {
            $_path.= $parts[$i]."/";
            if (!is_dir($path)) @mkdir($_path,0777);
        }
        return is_file($path);        
		$fp = @fopen($this->file,"w");
		if (!$fp) return $this->raiseError("Can't open file for write [ $this->file ]");
		if (!@fwrite($fp, $this->_combine())) $this->raiseError("Problem occured while write file [ $this->file ]");
		@fclose($fp);
		return true;
	}

	function clear() {
		$this->iniArray = array();
	}

	function setVar($key,$value=".",$section="") {
		if (!$section) $section = "__HAS_NO_SECTION__";
		if (!array_key_exists($section,$this->iniArray)) $this->iniArray[$section] = array(); // (PHP 4 >= 4.1.0)
        if (!preg_match("/^[a-zA-Z_\x7f-\xff][a-zA-Z0-9_\x7f-\xff]*$/", $value)) $this->iniArray[$section][$key]['quote'] = true;
		$this->iniArray[$section][$key]['value'] = $value;
		$this->debug("Save value: [ $section ] $key = $value");
		return true;
	}

	function delVar($key,$section="__HAS_NO_SECTION__") {
		if (array_key_exists($key,$this->iniArray[$section])) { // (PHP 4 >= 4.1.0)
			unset($this->iniArray[$section][$key]);
			$this->debug("Delete value: [ $section ] $key");
		} else {
			$this->raiseWarning("Nothing to delete");
		}
		return true;
	}

    function delSection($section='__HAS_NO_SECTION__') {
        if (array_key_exists($section,$this->iniArray)) {
            unset($this->iniArray[$section]);
            $this->debug("Delete section: [ $section ]");
        } else {
            $this->raiseWarning("Nothing to delete");
        }
        return true;
    }

	function getVar($key,$section="__HAS_NO_SECTION__") {
		if (!is_array($this->iniArray[$section])) {
			$this->raiseWarning("No such section");
			return;
		}
		if (array_key_exists($key,$this->iniArray[$section])) { // (PHP 4 >= 4.1.0)
			$_ret = $this->iniArray[$section][$key]['value'];
			if (eregi("(yes|true|on)",$_ret)) return true;
			elseif (ereg("(no,false,off)",$_ret)) return false;
			else return $_ret;
		} else {
			$this->raiseWarning("No such key");
			return;
		}
	}

	function getAll($mode=PROCESS_PLAIN) {
		return parse_ini_file($this->file, $mode);
	}

	function debug($msg) {
		if ($this->debug) echo $msg."<br>\n";
	}

	function raiseError($err) {
		$this->_error[] = $err;
		if ($this->debug || strstr($this->report,"E")) {
			echo $err."<br>\n";
		}
		return false;
	}

	function raiseWarning($warn) {
		$this->_warning[] = $warn;
		if ($this->debug || strstr($this->report,"W")) {
			echo $warn."<br>\n";
		}
	}

	function _parse() {
		$fp = @fopen($this->file,"r");
		if (!$fp) return $this->raiseError("Can't open file [ $this->file ]");
		$_section = "__HAS_NO_SECTION__";
		while (!feof($fp)) { 
			$_line = trim(fgets($fp,1024));
			if ($_line == "") {
				//$this->iniArray[$_section][] = "";
				continue;
			} elseif ($_line[0] == ";") {
				if (preg_match("/\;<\?*/",$_line)) continue;
				$this->iniArray[$_section][] = array(comment=>$_line);
				continue;
			}

			if ($_line[0] == "[" && $_line[strlen($_line) -1] == "]") {
				$_section = substr($_line, 1, strlen($_line) - 2);
				continue;
			} elseif (strstr($_line,";")) {
				$_pos = strpos($_line,"=");
				$_key = rtrim(substr($_line, 0, $_pos));
				$_data = ltrim(substr($_line ,$_pos + 1));
				if ($_data[0] == "\"") {
					$_endQuote = strpos($_data,"\"",1);
					if ($_endQuote === false) {
						$this->raiseWarning("Quotation is not valid at $_section.$_key");
						$_value = "";
					} else {
						$_quote = true;
						$_value = substr($_data,1,$_endQuote - 1);
						$_sep = strpos($_data,";",$_endQuote);
						$_comment = substr($_data, $_sep + 1);
					}
				} else {
					$_sep = strpos($_data,";");
					$_value = substr($_data, 0, $_sep);
					$_comment = substr($_data, $_sep + 1);
				}
			} else {
				$_pos = strpos($_line, "=");
				$_key = rtrim(substr($_line, 0, $_pos));
				$_value = ltrim(substr($_line, $_pos + 1));
				if ($_value[0] == "\"") {
					$_endQuote = strpos($_value,"\"",2);
                    $_quote = true;
					if ($_endQuote === false) {
						$_value = substr($_value,1);
					} else {
						$_value = substr($_value,1,$_endQuote - 1);
					}
				}
			}

			$this->iniArray[$_section][$_key] = array (
                    value => $_value,
                    comment => $_comment
                );
			if ($_quote) $this->iniArray[$_section][$_key]['quote'] = true;
			unset($_pos,$_key,$_value,$_comment,$_quote);
		}
		return true;
	}

	function _combine($safe=true) {
		$_retStr = ($safe) ? ";<?php die(')'); /* WebApp Configuration File : DO NOT EDIT MANUALY */?>\n" : "";
		if (count($this->iniArray) <= 0) {
			return $this->raiseError("Nothing to save");
		} else {
			$_sections = array_keys($this->iniArray);
			for ($i=0; $i<count($_sections); $i++) {
				if ($_sections[$i] != "__HAS_NO_SECTION__") $_retStr.= "\n[".$_sections[$i]."]\n";
				foreach($this->iniArray[$_sections[$i]] as $_key=>$_data) {
					if (is_numeric($_key)) {
						$_retStr.= ($_data['comment']) ? $_data['comment'] : "";
					} else {
						$_retStr.= "$_key = ";
						
						//2008-02-27 종태 더블쿼터문제땜시 수정("가 글로벌값에 포함되 사이트가 안떴음..)
						if(strstr($_data['value'],'"' )) {
						$_data['value'] = str_replace('"','',$_data['value']);
						$_retStr.= ($_data['quote']) ? '"'.$_data['value'].'"' : $_data['value'];
						}else{
						$_retStr.= ($_data['quote']) ? '"'.$_data['value'].'"' : $_data['value'];
						}
						
						
						$_retStr.= ($_data['comment']) ? $_data['comment'] : "";
					}
					$_retStr.= "\n";
				}
			}
		}
		return $_retStr;
	}
}

?>