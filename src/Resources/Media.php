<?php

namespace PressLib\Resources;
use PressLib\Resources\Base;

class Media extends Base {
  public function __construct($host) {
    parent::__construct($host, 'wp/v2/media');
  }

  public function byParent($id, $size="full") {
    $resp = $this->client->get($this->endpoint, [ 'query' => [ 'parent' => $id ] ]);
    
    $data = $this->_handleResponse($resp);
    
    if (count($data) === 0) {
      throw new \Error('Resource not found');
    }
    $return = [];

    foreach ($data as $att) {
      $detailSizes = $att->media_details->sizes;
      try {
        $img = $detailSizes->{$size};
      } catch ( ErrorException $e) { 
        $img = $detailSizes->full;
      }

      $img->id = $att->id;

      $return[] = $img;
    }
    return $return;
  }
}

