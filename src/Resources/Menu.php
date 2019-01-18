<?php

namespace PressLib\Resources;
use PressLib\Resources\Base;

class Menu extends Base {
  public function __construct($host) {
    parent::__construct($host, 'menus/v1/menus');
  }

  public function bySlug($slug) {
    $resp = $this->client->get($this->endpoint . "/" . $slug);
    $data = $this->_handleResponse($resp);

    return $data;
  }
}
