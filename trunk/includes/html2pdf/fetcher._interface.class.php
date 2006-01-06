<?php
class Fetcher {
  function get_data($data_id) {
    die("Oops. Inoverridden 'get_data' method called in ".get_class($this));
  }

  function error_message() {
    die("Oops. Inoverridden 'error_message' method called in ".get_class($this));
  }
}
?>