<?php

namespace PressLib\Resources;
use PressLib\Resources\Base;

class Menus extends Base {
  public function __construct($host) {
    parent::__construct($host, 'menus/v1/menus');
  }
}
