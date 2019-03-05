<?php

class BaseCode
{

    const DIMENSION = 5;

    public static function encode($data) {
        if(!is_string($data)) {
            return false;
        }
        $code = self::code(base64_encode(base64_encode(base64_encode($data))));

        return $code;
    }

    public static function decode($code) {
        if(!is_string($code)) {
            return false;
        }
        $data = base64_decode(base64_decode(base64_decode(self::code($code, true))));

        return $data;
    }

    private static function code($text, $decode = false) {

        $o = $s1 = $s2 = array();
        $basea = array('?','(','@',';','$','#',"]","&",'*');
        $basea = array_merge($basea, range('a','z'), range('A','Z'), range(0,9));
        $basea = array_merge($basea, array('!',')','_','+','|','%','/','[','.',' '));
        $dimension = self::DIMENSION;
        for($i=0;$i<$dimension;$i++) {
            for($j=0;$j<$dimension;$j++) {
                $s1[$i][$j] = $basea[$i*$dimension+$j];
                $s2[$i][$j] = str_rot13($basea[($dimension*$dimension-1) - ($i*$dimension+$j)]);
            }
        }
        unset($basea);
        $m = floor(strlen($text)/2)*2;
        $symbl = $m==strlen($text)?'':$text[strlen($text)-1];
        $al = array();
        for ($ii=0; $ii<$m; $ii+=2) {
            $symb1 = $symbn1 = strval($text[$ii]);
            $symb2 = $symbn2 = strval($text[$ii+1]);
            $a1 = $a2 = array();
            for($i=0;$i<$dimension;$i++) { // search symbols in Squares
                for($j=0;$j<$dimension;$j++) {
                    if ($decode) {
                        if ($symb1===strval($s2[$i][$j]) ) $a1=array($i,$j);
                        if ($symb2===strval($s1[$i][$j]) ) $a2=array($i,$j);
                        if (!empty($symbl) && $symbl===strval($s2[$i][$j])) $al=array($i,$j);
                    }
                    else {
                        if ($symb1===strval($s1[$i][$j]) ) $a1=array($i,$j);
                        if ($symb2===strval($s2[$i][$j]) ) $a2=array($i,$j);
                        if (!empty($symbl) && $symbl===strval($s1[$i][$j])) $al=array($i,$j);
                    }
                }
            }
            if (sizeof($a1) && sizeof($a2)) {
                $symbn1 = $decode ? $s1[$a1[0]][$a2[1]] : $s2[$a1[0]][$a2[1]];
                $symbn2 = $decode ? $s2[$a2[0]][$a1[1]] : $s1[$a2[0]][$a1[1]];
            }
            $o[] = $symbn1.$symbn2;
        }
        if (!empty($symbl) && sizeof($al)) // last symbol
            $o[] = $decode ? $s1[$al[1]][$al[0]] : $s2[$al[1]][$al[0]];

        return implode('',$o);

    }

}