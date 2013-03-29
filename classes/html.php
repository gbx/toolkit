<?php

// direct access protection
if(!defined('KIRBY')) die('Direct access is not allowed');

/**
 * Html
 * 
 * Html builder for the most common elements
 * 
 * @package Kirby Toolkit
 */
class Html {

  /**
    * Converts a string to a html-safe string
    *
    * @param  string  $string
    * @param  boolean $keepTags True: lets stuff inside html tags untouched. 
    * @return string  The html string
    */  
  static public function encode($string, $keepTags = true) {
    if($keepTags) {
      return stripslashes(implode('', preg_replace_callback('/^([^<].+[^>])$/', create_function('$match', 'return htmlentities($match[1], ENT_COMPAT, "utf-8");'), preg_split('/(<.+?>)/', $string, -1, PREG_SPLIT_DELIM_CAPTURE))));
    } else {
      return htmlentities($string, ENT_COMPAT, 'utf-8');
    }
  }

  /**
    * Removes all html tags and encoded chars from a string
    *
    * @param  string  $string
    * @return string  The html string
    */  
  static public function decode($string) {
    $string = strip_tags($string);
    return html_entity_decode($string, ENT_COMPAT, 'utf-8');
  }

  /**
   * Converts lines in a string into html breaks
   *
   * @param string $string
   * @return string 
   */
  static public function breaks($string) {
    return nl2br($string);
  }

  /**
   * Generates an Html tag with optional content and attributes
   * 
   * @param string $name The name of the tag, i.e. "a"
   * @param mixed $content The content if availble. Pass null to generate a self-closing tag, Pass an empty string to generate empty content
   * @param array $attr An associative array with additional attributes for the tag
   * @return string The generated Html
   */
  static public function tag($name, $content = null, $attr = array()) {

    $html = '<' . $name;
    $attr = self::attr($attr);

    if(!empty($attr)) $html .= ' ' . $attr;
    if(!is_null($content)) {
      $html .= '>' . $content . '</' . $name . '>';
    } else {
      $html .= ' />';
    }

    return $html; 

  }

  /**
   * Generates a single attribute or a list of attributes
   * 
   * @param string $name mixed string: a single attribute with that name will be generated. array: a list of attributes will be generated. Don't pass a second argument in that case. 
   * @param string $value if used for a single attribute, pass the content for the attribute here
   * @return string the generated html
   */
  static public function attr($name, $value = null) {
    if(is_array($name)) {
      $attributes = array();
      foreach($name as $key => $val) {
        $a = self::attr($key, $val);
        if($a) $attributes[] = $a;
      }
      return implode(' ', $attributes);
    }  

    if(empty($value) && $value !== '0') return false;
    return $name . '="' . $value . '"';    
  }

  /**
   * Generates an a tag
   * 
   * @param string $href The url for the a tag
   * @param mixed $text The optional text. If null, the url will be used as text
   * @param array $attr Additional attributes for the tag
   * @return string the generated html
   */
  static public function a($href, $text = null, $attr = array()) {
    $attr = array_merge(array('href' => $href), $attr);
    if(empty($text)) $text = $href;
    return self::tag('a', $text, $attr);
  }

  /**
   * Generates an "a mailto" tag
   * 
   * @param string $href The url for the a tag
   * @param mixed $text The optional text. If null, the url will be used as text
   * @param array $attr Additional attributes for the tag
   * @return string the generated html
   */
  static public function email($email, $text = null, $attr = array()) {
    $email = str::encode($email, 3);
    $attr  = array_merge(array('href' => 'mailto:' . $email), $attr);
    if(empty($text)) $text = $email;
    return self::tag('a', $text, $attr);
  }



  /**
   * Generates a div tag
   * 
   * @param string $content The content for the div
   * @param array $attr Additional attributes for the tag
   * @return string the generated html
   */
  static public function div($content, $attr = array()) {
    return self::tag('div', $content, $attr);
  }

  /**
   * Generates a p tag
   * 
   * @param string $content The content for the p tag
   * @param array $attr Additional attributes for the tag
   * @return string the generated html
   */
  static public function p($content, $attr = array()) {
    return self::tag('p', $content, $attr);
  }

  /**
   * Generates a span tag
   * 
   * @param string $content The content for the span tag
   * @param array $attr Additional attributes for the tag
   * @return string the generated html
   */
  static public function span($content, $attr = array()) {
    return self::tag('span', $content, $attr);
  }

  /**
   * Generates an img tag
   * 
   * @param string $src The url of the image
   * @param array $attr Additional attributes for the image tag
   * @return string the generated html
   */
  static public function img($src, $attr = array()) {
    $attr = array_merge(array('src' => $src, 'alt' => f::filename($src)), $attr);
    return self::tag('img', null, $attr);
  }

  /**
   * Generates an stylesheet link tag
   * 
   * @param string $href The url of the css file
   * @param string $media An optional media type (screen, print, etc.)
   * @param array $attr Additional attributes for the link tag
   * @return string the generated html
   */
  static public function stylesheet($href, $media = null, $attr = array()) {
    $attr = array_merge(array('rel' => 'stylesheet', 'href' => $href, 'media' => $media), $attr);
    return self::tag('link', null, $attr);
  }

  /**
   * Generates an script tag
   * 
   * @param string $src The url of the javascript file
   * @param boolean $async Optional HTML5 async attribute
   * @param array $attr Additional attributes for the script tag
   * @return string the generated html
   */
  static public function script($src, $async = false, $attr = array()) {
    $attr = array_merge(array('src' => $src, 'async' => r($async, 'async')), $attr);
    return self::tag('script', '', $attr);
  }

  /**
   * Generates a favicon link tag
   * 
   * @param string $href The url of the favicon file
   * @param array $attr Additional attributes for the link tag
   * @return string the generated html
   */
  static public function favicon($href, $attr = array()) {
    $attr = array_merge(array('rel' => 'shortcut icon', 'href' => $href), $attr);
    return self::tag('link', null, $attr);

  }

  /**
   * Generates an iframe
   * 
   * @param string $src The url of the iframe content
   * @param array $attr Additional attributes for the link tag
   * @param string $placeholder Text to be shown when the iframe can not be displayed.
   * @return string the generated html
   */
  static public function iframe($src, $attr = array(), $placeholder = '') {
    $attr = array_merge(array('src' => $src), $attr);    
    return self::tag('iframe', $placeholder, $attr);
  }

  /**
   * Generates the HTML5 doctype
   * 
   * @return string the generated html
   */
  static public function doctype() {
    return '<!DOCTYPE html>';
  }

  /**
   * Generates the charset metatag
   * 
   * @param string $charset
   * @return string the generated html
   */
  static public function charset($charset = 'utf-8') {
    return '<meta charset="' . $charset . '" />';
  }

  /**
   * Generates a canonical link tag
   * 
   * @param string $href The canonical url of the current page
   * @param array $attr Additional attributes for the link tag
   * @return string the generated html
   */
  static public function canonical($href, $attr = array()) {
    $attr = array_merge(array('href' => $href, 'rel' => 'canonical'), $attr);    
    return self::tag('link', null, $attr);
  }

  /**
   * Generates a HTML5 shiv script tag with additional comments for older IEs
   * 
   * @return string the generated html
   */
  static public function shiv() {
    $html  = '<!--[if lt IE 9]>' . PHP_EOL;
    $html .= '<script src="//html5shiv.googlecode.com/svn/trunk/html5.js"></script>' . PHP_EOL;
    $html .= '<![endif]-->' . PHP_EOL;
    return $html;
  }

  /**
   * Generates a description meta tag
   * 
   * @param string $description 
   * @param array $attr Additional attributes for the meta tag
   * @return string the generated html
   */
  static public function description($description, $attr = array()) {
    $attr = array_merge(array('name' => 'description', 'content' => $description), $attr);    
    return self::tag('meta', null, $attr);
  }

  /**
   * Generates a keywords meta tag
   * 
   * @param string $keywords 
   * @param array $attr Additional attributes for the meta tag
   * @return string the generated html
   */
  static public function keywords($keywords, $attr = array()) {
    $attr = array_merge(array('name' => 'keywords', 'content' => $keywords), $attr);    
    return self::tag('meta', null, $attr);
  }

  /**
   * An internal store for a html entities translation table
   *
   * @return array
   */  
  static public function entities() {

    return array(
      '&nbsp;' => '&#160;', '&iexcl;' => '&#161;', '&cent;' => '&#162;', '&pound;' => '&#163;', '&curren;' => '&#164;', '&yen;' => '&#165;', '&brvbar;' => '&#166;', '&sect;' => '&#167;',
      '&uml;' => '&#168;', '&copy;' => '&#169;', '&ordf;' => '&#170;', '&laquo;' => '&#171;', '&not;' => '&#172;', '&shy;' => '&#173;', '&reg;' => '&#174;', '&macr;' => '&#175;',
      '&deg;' => '&#176;', '&plusmn;' => '&#177;', '&sup2;' => '&#178;', '&sup3;' => '&#179;', '&acute;' => '&#180;', '&micro;' => '&#181;', '&para;' => '&#182;', '&middot;' => '&#183;',
      '&cedil;' => '&#184;', '&sup1;' => '&#185;', '&ordm;' => '&#186;', '&raquo;' => '&#187;', '&frac14;' => '&#188;', '&frac12;' => '&#189;', '&frac34;' => '&#190;', '&iquest;' => '&#191;',
      '&Agrave;' => '&#192;', '&Aacute;' => '&#193;', '&Acirc;' => '&#194;', '&Atilde;' => '&#195;', '&Auml;' => '&#196;', '&Aring;' => '&#197;', '&AElig;' => '&#198;', '&Ccedil;' => '&#199;',
      '&Egrave;' => '&#200;', '&Eacute;' => '&#201;', '&Ecirc;' => '&#202;', '&Euml;' => '&#203;', '&Igrave;' => '&#204;', '&Iacute;' => '&#205;', '&Icirc;' => '&#206;', '&Iuml;' => '&#207;',
      '&ETH;' => '&#208;', '&Ntilde;' => '&#209;', '&Ograve;' => '&#210;', '&Oacute;' => '&#211;', '&Ocirc;' => '&#212;', '&Otilde;' => '&#213;', '&Ouml;' => '&#214;', '&times;' => '&#215;',
      '&Oslash;' => '&#216;', '&Ugrave;' => '&#217;', '&Uacute;' => '&#218;', '&Ucirc;' => '&#219;', '&Uuml;' => '&#220;', '&Yacute;' => '&#221;', '&THORN;' => '&#222;', '&szlig;' => '&#223;',
      '&agrave;' => '&#224;', '&aacute;' => '&#225;', '&acirc;' => '&#226;', '&atilde;' => '&#227;', '&auml;' => '&#228;', '&aring;' => '&#229;', '&aelig;' => '&#230;', '&ccedil;' => '&#231;',
      '&egrave;' => '&#232;', '&eacute;' => '&#233;', '&ecirc;' => '&#234;', '&euml;' => '&#235;', '&igrave;' => '&#236;', '&iacute;' => '&#237;', '&icirc;' => '&#238;', '&iuml;' => '&#239;',
      '&eth;' => '&#240;', '&ntilde;' => '&#241;', '&ograve;' => '&#242;', '&oacute;' => '&#243;', '&ocirc;' => '&#244;', '&otilde;' => '&#245;', '&ouml;' => '&#246;', '&divide;' => '&#247;',
      '&oslash;' => '&#248;', '&ugrave;' => '&#249;', '&uacute;' => '&#250;', '&ucirc;' => '&#251;', '&uuml;' => '&#252;', '&yacute;' => '&#253;', '&thorn;' => '&#254;', '&yuml;' => '&#255;',
      '&fnof;' => '&#402;', '&Alpha;' => '&#913;', '&Beta;' => '&#914;', '&Gamma;' => '&#915;', '&Delta;' => '&#916;', '&Epsilon;' => '&#917;', '&Zeta;' => '&#918;', '&Eta;' => '&#919;',
      '&Theta;' => '&#920;', '&Iota;' => '&#921;', '&Kappa;' => '&#922;', '&Lambda;' => '&#923;', '&Mu;' => '&#924;', '&Nu;' => '&#925;', '&Xi;' => '&#926;', '&Omicron;' => '&#927;',
      '&Pi;' => '&#928;', '&Rho;' => '&#929;', '&Sigma;' => '&#931;', '&Tau;' => '&#932;', '&Upsilon;' => '&#933;', '&Phi;' => '&#934;', '&Chi;' => '&#935;', '&Psi;' => '&#936;',
      '&Omega;' => '&#937;', '&alpha;' => '&#945;', '&beta;' => '&#946;', '&gamma;' => '&#947;', '&delta;' => '&#948;', '&epsilon;' => '&#949;', '&zeta;' => '&#950;', '&eta;' => '&#951;',
      '&theta;' => '&#952;', '&iota;' => '&#953;', '&kappa;' => '&#954;', '&lambda;' => '&#955;', '&mu;' => '&#956;', '&nu;' => '&#957;', '&xi;' => '&#958;', '&omicron;' => '&#959;',
      '&pi;' => '&#960;', '&rho;' => '&#961;', '&sigmaf;' => '&#962;', '&sigma;' => '&#963;', '&tau;' => '&#964;', '&upsilon;' => '&#965;', '&phi;' => '&#966;', '&chi;' => '&#967;',
      '&psi;' => '&#968;', '&omega;' => '&#969;', '&thetasym;' => '&#977;', '&upsih;' => '&#978;', '&piv;' => '&#982;', '&bull;' => '&#8226;', '&hellip;' => '&#8230;', '&prime;' => '&#8242;',
      '&Prime;' => '&#8243;', '&oline;' => '&#8254;', '&frasl;' => '&#8260;', '&weierp;' => '&#8472;', '&image;' => '&#8465;', '&real;' => '&#8476;', '&trade;' => '&#8482;', '&alefsym;' => '&#8501;',
      '&larr;' => '&#8592;', '&uarr;' => '&#8593;', '&rarr;' => '&#8594;', '&darr;' => '&#8595;', '&harr;' => '&#8596;', '&crarr;' => '&#8629;', '&lArr;' => '&#8656;', '&uArr;' => '&#8657;',
      '&rArr;' => '&#8658;', '&dArr;' => '&#8659;', '&hArr;' => '&#8660;', '&forall;' => '&#8704;', '&part;' => '&#8706;', '&exist;' => '&#8707;', '&empty;' => '&#8709;', '&nabla;' => '&#8711;',
      '&isin;' => '&#8712;', '&notin;' => '&#8713;', '&ni;' => '&#8715;', '&prod;' => '&#8719;', '&sum;' => '&#8721;', '&minus;' => '&#8722;', '&lowast;' => '&#8727;', '&radic;' => '&#8730;',
      '&prop;' => '&#8733;', '&infin;' => '&#8734;', '&ang;' => '&#8736;', '&and;' => '&#8743;', '&or;' => '&#8744;', '&cap;' => '&#8745;', '&cup;' => '&#8746;', '&int;' => '&#8747;',
      '&there4;' => '&#8756;', '&sim;' => '&#8764;', '&cong;' => '&#8773;', '&asymp;' => '&#8776;', '&ne;' => '&#8800;', '&equiv;' => '&#8801;', '&le;' => '&#8804;', '&ge;' => '&#8805;',
      '&sub;' => '&#8834;', '&sup;' => '&#8835;', '&nsub;' => '&#8836;', '&sube;' => '&#8838;', '&supe;' => '&#8839;', '&oplus;' => '&#8853;', '&otimes;' => '&#8855;', '&perp;' => '&#8869;',
      '&sdot;' => '&#8901;', '&lceil;' => '&#8968;', '&rceil;' => '&#8969;', '&lfloor;' => '&#8970;', '&rfloor;' => '&#8971;', '&lang;' => '&#9001;', '&rang;' => '&#9002;', '&loz;' => '&#9674;',
      '&spades;' => '&#9824;', '&clubs;' => '&#9827;', '&hearts;' => '&#9829;', '&diams;' => '&#9830;', '&quot;' => '&#34;', '&amp;' => '&#38;', '&lt;' => '&#60;', '&gt;' => '&#62;', '&OElig;' => '&#338;',
      '&oelig;' => '&#339;', '&Scaron;' => '&#352;', '&scaron;' => '&#353;', '&Yuml;' => '&#376;', '&circ;' => '&#710;', '&tilde;' => '&#732;', '&ensp;' => '&#8194;', '&emsp;' => '&#8195;',
      '&thinsp;' => '&#8201;', '&zwnj;' => '&#8204;', '&zwj;' => '&#8205;', '&lrm;' => '&#8206;', '&rlm;' => '&#8207;', '&ndash;' => '&#8211;', '&mdash;' => '&#8212;', '&lsquo;' => '&#8216;',
      '&rsquo;' => '&#8217;', '&sbquo;' => '&#8218;', '&ldquo;' => '&#8220;', '&rdquo;' => '&#8221;', '&bdquo;' => '&#8222;', '&dagger;' => '&#8224;', '&Dagger;' => '&#8225;', '&permil;' => '&#8240;',
      '&lsaquo;' => '&#8249;', '&rsaquo;' => '&#8250;', '&euro;' => '&#8364;'
    );

  }

}