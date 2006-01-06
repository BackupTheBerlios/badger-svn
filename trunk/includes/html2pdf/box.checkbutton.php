<?php
// $Header: /cvsroot/html2ps/box.checkbutton.php,v 1.11 2005/12/13 18:24:11 Konstantin Exp $

class CheckBox extends GenericBox {
  var $checked;

  function &create(&$root) {
    $box =& new CheckBox($root);
    return $box;
  }

  function CheckBox(&$root) {
    // Call parent constructor
    $this->GenericBox();

    // Check the box state
    $this->checked = $root->has_attribute('checked');

    // Setup box size:
    $this->default_baseline = units2pt(CHECKBOX_SIZE);
    $this->height           = units2pt(CHECKBOX_SIZE);
    $this->width            = units2pt(CHECKBOX_SIZE);
  }

  // Inherited from GenericBox
  function get_min_width(&$context) { return $this->get_full_width(); }
  function get_max_width(&$context) { return $this->get_full_width(); }

  function reflow(&$parent, &$context) {  
    GenericBox::reflow($parent, $context);
    
    // set default baseline
    $this->baseline = $this->default_baseline;
    
//     // Vertical-align
//     $this->_apply_vertical_align($parent);

    // append to parent line box
    $parent->append_line($this);

    // Determine coordinates of upper-left _margin_ corner
    $this->guess_corner($parent);

    // Offset parent current X coordinate
    $parent->_current_x += $this->get_full_width();

    // Extend parents height
    $parent->extend_height($this->get_bottom_margin());
  }

  function show(&$viewport) {   
    // Cet check center
    $x = ($this->get_left() + $this->get_right()) / 2;
    $y = ($this->get_top() + $this->get_bottom()) / 2;

    // Calculate checkbox size
    $size = $this->get_width() / 3;

    // Draw checkbox
    $viewport->setlinewidth(0.25);
    $viewport->moveto($x - $size, $y + $size);
    $viewport->lineto($x + $size, $y + $size);
    $viewport->lineto($x + $size, $y - $size);
    $viewport->lineto($x - $size, $y - $size);
    $viewport->closepath();
    $viewport->stroke();

    // Draw checkmark if needed
    if ($this->checked) { 
      $check_size = $this->get_width() / 6;

      $viewport->moveto($x - $check_size, $y + $check_size);
      $viewport->lineto($x + $check_size, $y - $check_size);
      $viewport->stroke();

      $viewport->moveto($x + $check_size, $y + $check_size);
      $viewport->lineto($x - $check_size, $y - $check_size);
      $viewport->stroke();
    }

    return true;
  }

  function to_ps(&$psdata) {
    $psdata->write("box-checkbutton-create\n");
    $psdata->write(($this->checked ? "true" : "false")." 1 index box-checkbutton-put-checked\n");
    $psdata->write("add-child\n");
  }
}
?>