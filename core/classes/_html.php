<?php


class _ah_html {

	public function _js($file) {
		if (is_array($file)) {
			$return = '';
			foreach ($file as $js){
				$return .= '<script type="text/javascript" src="'.AH_INC.$js.'"></script>';
			}
		}
		else{
			$return = '<script type="text/javascript" src="'.AH_INC.$file.'"></script>';
		}
		return $return;
	}
	public function _css($file) {
		if (is_array($file)) {
			$return = '';
			foreach ($file as $css){
				$return .= '<link href="'.AH_INC.$css.'" rel="stylesheet" type="text/css" media="screen" />';
			}
		}
		else {
			$return = '<link href="'.AH_INC.$file.'" rel="stylesheet" type="text/css" media="screen" />';
		}
		return $return;
	}
	public function _css_compress($style){
		$css = '<style>';
		$css .= str_replace('; ',';',str_replace(' }','}',str_replace('{ ','{',str_replace(array("\r\n","\r","\n","\t",'  ','    ','    '),"",preg_replace('!/\*[^*]*\*+([^/][^*]*\*+)*/!','',$style)))));
		$css .= '</style>';
		return $css ;
	}

}

?>