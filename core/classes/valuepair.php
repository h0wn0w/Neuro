<?php

class ValuePair {
  public $one = '';
  public $two = '';

  function __construct($one = '', $two = '') {
    $this->one = $one;
    $this->two = $two;
  }
}