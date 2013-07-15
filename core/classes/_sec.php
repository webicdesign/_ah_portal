<?php
class _ah_sec
{
    function _ah_sec() {
        self::_request_filtering();
    }

    # clean request
    private function _request_filtering() {
        if (isset($_POST) || isset($_GET) || isset($_REQUEST)) {
            # $_GET request xss filtering
            foreach ($_GET as $key => $value) {
                if (is_array($_GET[$key])) {
                    $g = array();
                    foreach ($_GET[$key] as $ky => $val) {
                        $g[$ky] = self::_xss_filter_I($val);
                    }
                    $_GET[$key] = $g;
                }
                else {
                    $_GET[$key] = self::_xss_filter_I($value);
                }
            }
            # $_POST request xss filtering
            foreach ($_POST as $key => $value) {
                if (is_array($_POST[$key])) {
                    $g = array();
                    foreach ($_POST[$key] as $ky => $val) {
                        $g[$ky] = self::_xss_filter_I($val);
                    }
                    $_POST[$key] = $g;
                }
                else {
                    $_POST[$key] = self::_xss_filter_I($value);
                }
            }
        }
    }
    # xss filtering 1
    public function _xss_filter_I($string, $esc_type = 'htmlall') {
        switch ($esc_type) {
            case 'css':
                    $string = str_replace(array('<', '>', '\\'), array('&lt;', '&gt;', '&#47;'), $string);
                    $string = preg_replace('/j\s*[\\\]*\s*a\s*[\\\]*\s*v\s*[\\\]*\s*a\s*[\\\]*\s*s\s*[\\\]*\s*c\s*[\\\]*\s*r\s*[\\\]*\s*i\s*[\\\]*\s*p\s*[\\\]*\s*t\s*[\\\]*\s*:/i', 'blocked', $string);
                    $string = preg_replace('/@\s*[\\\]*\s*i\s*[\\\]*\s*m\s*[\\\]*\s*p\s*[\\\]*\s*o\s*[\\\]*\s*r\s*[\\\]*\s*t/i','blocked', $string);
                    $string = preg_replace('/e\s*[\\\]*\s*x\s*[\\\]*\s*p\s*[\\\]*\s*r\s*[\\\]*\s*e\s*[\\\]*\s*s\s*[\\\]*\s*s\s*[\\\]*\s*i\s*[\\\]*\s*o\s*[\\\]*\s*n\s*[\\\]*\s*/i', 'blocked', $string);
                    $string = preg_replace('/b\s*[\\\]*\s*i\s*[\\\]*\s*n\s*[\\\]*\s*d\s*[\\\]*\s*i\s*[\\\]*\s*n\s*[\\\]*\s*g:/i', 'blocked', $string);
                return $string;
            case 'html':
                return str_replace(array('<', '>'), array('&lt;' , '&gt;'), $string);
            case 'htmlall':
                return htmlentities($string, ENT_QUOTES, "UTF-8");
            case 'url':
                return rawurlencode($string);
            case 'query':
                return urlencode($string);
            case 'quotes':
                return preg_replace("%(?<!\\\\)'%", "\\'", $string);
            case 'hex':
                    $s_return = '';
                    for ($x=0; $x < strlen($string); $x++) {
                        $s_return .= '%' . bin2hex($string[$x]);
                    }
                return $s_return;
            case 'hexentity':
                    $s_return = '';
                    for ($x=0; $x < strlen($string); $x++) {
                        $s_return .= '&#x' . bin2hex($string[$x]) . ';';
                    }
                return $s_return;
            case 'decentity':
            $s_return = '';
                    for ($x=0; $x < strlen($string); $x++) {
                        $s_return .= '&#' . ord($string[$x]) . ';';
                    }
                return $s_return;
            case 'js':
                return strtr($string, array('\\'=>'\\\\',"'"=>"\\'",'"'=>'\\"',"\r"=>'\\r',"\n"=>'\\n','</'=>'<\/'));
            case 'mail':
                return str_replace(array('@', '.'),array(' [AT] ', ' [DOT] '), $string);
            case 'nonstd':
            $_res = '';
                    for($_i = 0, $_len = strlen($string); $_i < $_len; $_i++) {
                        $_ord = ord($string{$_i});
                        $_res .= ($_ord >= 126) ? '&#' . $_ord . ';' : $string{$_i};
                    }
                return $_res;
            case 'st':
                return strip_tags($string);
            default:
                return $string;
        }
    }
    # xss filtering 2
    public function _xss_filter_II($data) {
        # Fix &entity\n;
        $data = str_replace(array('&amp;','&lt;','&gt;'), array('&amp;amp;','&amp;lt;','&amp;gt;'), $data);
        $data = preg_replace('/(&#*\w+)[\x00-\x20]+;/u', '$1;', $data);
        $data = preg_replace('/(&#x*[0-9A-F]+);*/iu', '$1;', $data);
        $data = html_entity_decode($data, ENT_COMPAT, 'UTF-8');
        # Remove any attribute starting with "on" or xmlns
        $data = preg_replace('#(<[^>]+?[\x00-\x20"\'])(?:on|xmlns)[^>]*+>#iu', '$1>', $data);
        # Remove javascript: and vbscript: protocols
        $data = preg_replace('#([a-z]*)[\x00-\x20]*=[\x00-\x20]*([`\'"]*)[\x00-\x20]*j[\x00-\x20]*a[\x00-\x20]*v[\x00-\x20]*a[\x00-\x20]*s[\x00-\x20]*c[\x00-\x20]*r[\x00-\x20]*i[\x00-\x20]*p[\x00-\x20]*t[\x00-\x20]*:#iu', '$1=$2nojavascript...', $data);
        $data = preg_replace('#([a-z]*)[\x00-\x20]*=([\'"]*)[\x00-\x20]*v[\x00-\x20]*b[\x00-\x20]*s[\x00-\x20]*c[\x00-\x20]*r[\x00-\x20]*i[\x00-\x20]*p[\x00-\x20]*t[\x00-\x20]*:#iu', '$1=$2novbscript...', $data);
        $data = preg_replace('#([a-z]*)[\x00-\x20]*=([\'"]*)[\x00-\x20]*-moz-binding[\x00-\x20]*:#u', '$1=$2nomozbinding...', $data);
        # Only works in IE: <span style="width: expression(alert('Ping!'));"></span>
        $data = preg_replace('#(<[^>]+?)style[\x00-\x20]*=[\x00-\x20]*[`\'"]*.*?expression[\x00-\x20]*\([^>]*+>#i', '$1>', $data);
        $data = preg_replace('#(<[^>]+?)style[\x00-\x20]*=[\x00-\x20]*[`\'"]*.*?behaviour[\x00-\x20]*\([^>]*+>#i', '$1>', $data);
        $data = preg_replace('#(<[^>]+?)style[\x00-\x20]*=[\x00-\x20]*[`\'"]*.*?s[\x00-\x20]*c[\x00-\x20]*r[\x00-\x20]*i[\x00-\x20]*p[\x00-\x20]*t[\x00-\x20]*:*[^>]*+>#iu', '$1>', $data);
        # Remove namespaced elements (we do not need them)
        $data = preg_replace('#</*\w+:\w[^>]*+>#i', '', $data);
        do {
            # Remove really unwanted tags
            $old_data = $data;
            $data = preg_replace('#</*(?:applet|b(?:ase|gsound|link)|embed|frame(?:set)?|i(?:frame|layer)|l(?:ayer|ink)|meta|object|s(?:cript|tyle)|title|xml)[^>]*+>#i', '', $data);
        }
        while ($old_data !== $data);
        return $data;
    }
}