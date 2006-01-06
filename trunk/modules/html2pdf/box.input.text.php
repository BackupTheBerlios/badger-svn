<?php
// $Header: /cvsroot/html2ps/box.input.text.php,v 1.17 2005/12/13 18:24:11 Konstantin Exp $

/// define('SIZE_SPACE_KOEFF',1.65); (defined in tag.input.inc.php)

class TextInputBox extends InlineControlBox {
  function &create(&$root) {
    $box =& new TextInputBox($root);
    return $box;
  }

  function TextInputBox(&$root) {
    // Call parent constructor
    $this->InlineBox();

    // Control size
    $size = (int)$root->get_attribute("size"); 
    if (!$size) { $size = DEFAULT_TEXT_SIZE; };
    
    // Text to be displayed
    if ($root->has_attribute('value')) {
      $text = str_pad($root->get_attribute("value"), $size, " ");
    } else {
      $text = str_repeat(" ",$size*SIZE_SPACE_KOEFF);
    };

    // TODO: international symbols! neet to use somewhat similar to 'process_word' in InlineBox
    push_css_text_defaults();
    $this->add_child(TextBox::create($text, 'iso-8859-1'));
    pop_css_defaults();
  }

  function show(&$viewport) {   
    // Now set the baseline of a button box to align it vertically when flowing isude the 
    // text line
    $this->default_baseline = $this->content[0]->baseline + $this->get_extra_top();
    $this->baseline         = $this->content[0]->baseline + $this->get_extra_top();

    return GenericContainerBox::show($viewport);
  }

  function to_ps(&$psdata) {
    $psdata->write("box-input-text-create\n");
    $this->to_ps_common($psdata);
    $this->to_ps_css($psdata);
    $this->to_ps_content($psdata);
    $psdata->write("add-child\n");
  }
}
?>