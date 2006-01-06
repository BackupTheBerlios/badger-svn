<?php
// $Header: /cvsroot/html2ps/css.content.inc.php,v 1.1 2005/10/06 03:58:42 Konstantin Exp $

class CSSContent extends CSSProperty {
  function CSSContent() { $this->CSSProperty(false, false); }

  function default_value() { return ""; }

  // CSS 2.1 p 12.2: 
  // Value: [ <string> | <uri> | <counter> | attr(X) | open-quote | close-quote | no-open-quote | no-close-quote ]+ | inherit
  //
  // TODO: process values other than <string>
  //
  function parse($value) {
    return css_remove_value_quotes($value);
  }

  function value2ps($value) {}
}

register_css_property('content', new CSSContent);

?>