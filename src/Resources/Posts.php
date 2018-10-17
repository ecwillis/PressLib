<?php

namespace PressLib\Resources;
use PressLib\Resources\Base;

class Posts extends Base {
  public function __construct($host) {
    parent::__construct($host, 'wp/v2/posts');
  }
}
