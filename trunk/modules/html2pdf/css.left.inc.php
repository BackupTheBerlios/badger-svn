<?php
// $Header: /cvsroot/html2ps/css.left.inc.php,v 1.3 2005/12/13 18:24:11 Konstantin Exp $

class CSSLeft extends CSSProperty {
  function CSSLeft() { $this->CSSProperty(false, false); }

  function default_value() { return null; }

  function parse($value) {
    return units2pt($value);
  }

  function value2ps($value) {
    return $value;
  }
}

register_css_property('left', new CSSLeft);

?>