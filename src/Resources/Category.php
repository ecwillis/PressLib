<?php

namespace PressLib\Resources;
use PressLib\Resources\Base;

class Category extends Base {
  public function __construct($host) {
    parent::__construct($host, 'wp/v2/categories');
  }

  public function getAll() {
    $count = 1;
    $page = 1;

    $catArr = [];

    while ($count > 0) {
      $resp = $this->client->get($this->endpoint, [ 'query' => [ 'per_page' => 100, 'page' => $page ]]);
      $cats = $this->_handleResponse($resp);

      $count = count($cats);
      if ($count > 0) {
        $catArr = array_merge($catArr, $cats);
      }

      $page++;
    }

    return $catArr;
  }


  protected function _getChildren(&$list, $id, $depth, $curr=1) {
    $result = [];
    foreach($list as $key => $val) {
      if ($val->parent == $id) {
        $result[$val->id] = $val;
        unset($list[$key]);
        if ($depth == 0 || $curr < $depth) {
          $result[$val->id]->children = $this->_getChildren($list, $val->id, $depth, $curr + 1);
        }  
      }
    }
    return $result;
  }

  public function getMap($depth=0) {
    $all = $this->getAll();
    
    $map = [];

    foreach($all as $key => $cat) {
      if ($cat->parent == 0) {
        $map[$cat->id] = $cat;
        unset($all[$key]);
        $map[$cat->id]->children = $this->_getChildren($all, $cat->id, $depth);
      }
    }

    return $map;
  }
}
