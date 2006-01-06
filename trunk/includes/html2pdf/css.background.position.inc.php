<?php
// The background-position value is an array containing two array for X and Y position correspondingly
// each coordinate-position array, in its turn containes two values:
// first, the numeric value of percentage or units
// second, flag indication that this value is a percentage (true) or plain unit value (false)

define('LENGTH_REGEXP',"(?:-?\d*\.?\d+(?:em|ex|px|in|cm|mm|pt|pc)\b|-?\d+(?:em|ex|px|in|cm|mm|pt|pc)\b)");
define('PERCENTAGE_REGEXP',"\b\d+%");
define('TEXT_REGEXP',"\b(?:top|bottom|left|right|center)\b");

define('BG_POSITION_SUBVALUE_TYPE_HORZ',1);
define('BG_POSITION_SUBVALUE_TYPE_VERT',2);

class CSSBackgroundPosition extends CSSSubProperty {
  function default_value() {
    return new BackgroundPositionValue(array(array(0,true),
                                             array(0,true)));
  }

  function build_subvalue($value) {
    if ($value === "left" ||
        $value === "top") {
      return array(0, true);
    };

    if ($value === "right" ||
        $value === "bottom") {
      return array(100, true);
    };

    if ($value === "center") {
      return array(50, true);
    };

    if (substr($value, strlen($value)-1,1) === "%") {
      return array((int)$value, true);
    } else {
      return array($value, false);
    };
  }

  function build_value($x, $y) {
    return array(CSSBackgroundPosition::build_subvalue($x),
                 CSSBackgroundPosition::build_subvalue($y));
  }

  function detect_type($value) {
    if ($value === "left" || $value === "right") { return BG_POSITION_SUBVALUE_TYPE_HORZ; };
    if ($value === "top" || $value === "bottom") { return BG_POSITION_SUBVALUE_TYPE_VERT; };
    return null;
  }

  // See CSS 2.1 'background-position' for description of possible values
  //
  function parse_in($value) {
    if (preg_match("/(".LENGTH_REGEXP."|".PERCENTAGE_REGEXP."|".TEXT_REGEXP."|\b0\b)\s+(".LENGTH_REGEXP."|".PERCENTAGE_REGEXP."|".TEXT_REGEXP."|\b0\b)/", $value, $matches)) {
      $x = $matches[1];
      $y = $matches[2];

      //print($value."<br>");
//            print($x."<br>");
//            print($y."<br>");

      $type_x = CSSBackgroundPosition::detect_type($x);
      $type_y = CSSBackgroundPosition::detect_type($y);

      if ($type_x == null && $type_y == null) {
        return CSSBackgroundPosition::build_value($x,$y);
      };

      if ($type_x == BG_POSITION_SUBVALUE_TYPE_HORZ || 
          $type_y == BG_POSITION_SUBVALUE_TYPE_VERT) {
        return CSSBackgroundPosition::build_value($x,$y);
      };

      return CSSBackgroundPosition::build_value($y,$x);
    };
    
//     if (preg_match("/\b(top\s+left|left\s+top)\b/",$value))           { return array(array(0, true),   array(0, true)); };
//     if (preg_match("/\b(top\s+center|center\s+top)\b/",$value))       { return array(array(50, true),  array(0, true)); };
//     if (preg_match("/\b(top\s+right|right\s+top)\b/",$value))         { return array(array(100, true), array(0, true)); };

//     if (preg_match("/\b(left\s+center|center\s+left)\b/",$value))     { return array(array(0, true),   array(50, true)); };
//     if (preg_match("/\b(center\s+center)\b/",$value))                 { return array(array(50, true),  array(50, true)); };
//     if (preg_match("/\b(right\s+center|center\s+right)\b/",$value))   { return array(array(100, true), array(50, true)); };

//     if (preg_match("/\b(bottom\s+left|left\s+bottom)\b/",$value))     { return array(array(0, true),   array(100, true)); };
//     if (preg_match("/\b(bottom\s+center|center\s+bottom)\b/",$value)) { return array(array(50, true),  array(100, true)); };
//     if (preg_match("/\b(bottom\s+right|right\s+bottom)\b/",$value))   { return array(array(100, true), array(100, true)); };

    // These values should be processed separately at lastt
    if (preg_match("/\b(top)\b/",$value))    { return array(array(50, true),  array(0, true)); };
    if (preg_match("/\b(center)\b/",$value)) { return array(array(50, true),  array(50, true)); };
    if (preg_match("/\b(bottom)\b/",$value)) { return array(array(50, true),  array(100, true)); };
    if (preg_match("/\b(left)\b/",$value))   { return array(array(0, true),   array(50, true)); };
    if (preg_match("/\b(right)\b/",$value))  { return array(array(100, true), array(50, true)); };

//     if (preg_match("/(".LENGTH_REGEXP."|".PERCENTAGE_REGEXP."|\b0\b)\s+(".LENGTH_REGEXP."|".PERCENTAGE_REGEXP."|\b0\b)/", $value, $matches)) {
//       $x = $matches[1];
//       $y = $matches[2];

//       return CSSBackgroundPosition::build_value($x,$y);
//     };

    //print("X");

    if (preg_match("/".LENGTH_REGEXP."|".PERCENTAGE_REGEXP."/", $value, $matches)) {
      $x = $matches[0];
      return CSSBackgroundPosition::build_value($x,"50%");
    };

    //print("Y");

    return null;
  }

  function parse($value) {
    return new BackgroundPositionValue(CSSBackgroundPosition::parse_in($value));
  }
}
?>