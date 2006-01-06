<?php
// $Header: /cvsroot/html2ps/box.button.php,v 1.18 2005/12/24 15:37:56 Konstantin Exp $

class ButtonBox extends InlineControlBox {
  function ButtonBox(&$root) {
    // Call parent constructor
    $this->InlineBox();

    // Button height includes vertical extra space; adjust the height constraint
    $hc = $this->get_height_constraint();
    if (!is_null($hc->constant)) {
      $hc->constant[0] -= $this->_get_vert_extra();
    };
    $this->put_height_constraint($hc);
   
    // Determine the button text 
    if ($root->has_attribute("value")) {
      $text = $root->get_attribute("value");
    } else {
      switch ($root->tagname()) {
      case "submit":
        $text = DEFAULT_SUBMIT_TEXT;
        break;
      case "reset":
        $text = DEFAULT_RESET_TEXT;
        break;
      case "button":
        $text = DEFAULT_BUTTON_TEXT;
        break;
      default:
        $text = DEFAULT_BUTTON_TEXT;
        break;
      }
    };

    // If button width is not constrained, then we'll add some space around the button text
    $text = " ".$text." ";

    $ibox = InlineBox::create_from_text($text);
    for ($i=0; $i<count($ibox->content); $i++) {
      $this->add_child($ibox->content[$i]);
    };
  }

  function &create(&$root) {
    $box =& new ButtonBox($root);
    return $box;
  }

  function show(&$viewport) {   
    // Now set the baseline of a button box to align it vertically when flowing isude the 
    // text line
    $this->default_baseline = $this->content[0]->baseline + $this->get_extra_top();
    $this->baseline         = $this->content[0]->baseline + $this->get_extra_top();

    return GenericContainerBox::show($viewport);
  }

  function to_ps(&$psdata) {
    $psdata->write("box-button-create\n");
    $this->to_ps_common($psdata);
    $this->to_ps_css($psdata);
    $this->to_ps_content($psdata);
    $psdata->write("add-child\n");    
  }
}
?>