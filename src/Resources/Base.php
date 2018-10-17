<?php

namespace PressLib\Resources;

use GuzzleHttp\Client as GClient;

class Base {
  protected $client;
  protected $endpoint;
  protected $base;
  public function __construct($host, $endpoint="") {
    $this->base = "{$host}/wp-json/";
    $this->endpoint = $endpoint;
    $this->client = new GClient([
      'base_uri' => $this->base,
      'timeout' => 30.0
    ]);

  }

  protected function _handleResponse($response) {
    if ($response->getStatusCode() !== 200) {
      throw new \Error('Resource Error');
    }
    $body = $response->getBody();
    $payload = $body->getContents();

    return json_decode($payload);
  }

  protected function _pullRendered(&$obj) {
    foreach($obj as $key => $val) {
      if(is_object($val) && property_exists($val, 'rendered')) {
        $obj->$key = $val->rendered;
      }
    }
  }

  public function  getEndpoint() {
    return $this->client->getConfig('base_uri');
  }

  public function list($args = []) {
    $resp = $this->client->get($this->endpoint);
    $data = $this->_handleResponse($resp);

    foreach($data as $obj) {
      $this->_pullRendered($obj);
    }

    return $data;
  }

  public function bySlug($slug) {
    $resp = $this->client->get($this->endpoint, [ 'query' => [ 'slug' => $slug ] ]);
    
    $data = $this->_handleResponse($resp);
    
    if (count($data) === 0) {
      throw new \Error('Resource not found');
    }

    $data = $data[0];
    // Make rendered the parent item.
    $this->_pullRendered($data); 

    $links = (property_exists($data, '_links')) ? $data->_links : [];
    unset($data->_links);
    
    // echo "<pre>";
    // echo str_replace("<", "&gt;", print_r($links, true));
    // die();
    $postTagSlug = null;
    $postCatSlug = null;
    if (property_exists($links, 'wp:term') && is_array($links->{'wp:term'})) {
      foreach($links->{'wp:term'} as $termObj) {
        if ($termObj->taxonomy == 'post_tag') {
          $postTagSlug = $termObj->href;
        }

        if ($termObj->taxonomy == 'category') {
          $postCatSlug = $termObj->href;
        }
      }
    }
    
    if (property_exists($data, 'categories') && is_array($data->categories) && count($data->categories) > 0 && $postCatSlug !== null) {
      $catEp = str_replace($this->base, '', $postCatSlug);
      $catResp = $this->client->get($catEp);

      $catData = $this->_handleResponse($catResp);
      $catDataArr = [];
      foreach($catData as $cat) {
        $obj = new \stdClass();
        $obj->name = $cat->name;
        $obj->slug = $cat->slug;
        $obj->description = $cat->description;

        $catDataArr[] = $obj;
      }

      $data->categories = $catDataArr;
    }
    
    if (property_exists($data, 'tags') && is_array($data->tags) && count($data->tags) > 0 && $postTagSlug !== null) {
      $tagEp = str_replace($this->base, '', $postTagSlug);
      $tagResp = $this->client->get($tagEp);

      $tagData = $this->_handleResponse($tagResp);
      $tagDataArr = [];
      foreach($tagData as $cat) {
        $obj = new \stdClass();
        $obj->name = $cat->name;
        $obj->slug = $cat->slug;
        $obj->description = $cat->description;

        $tagDataArr[] = $obj;
      }

      $data->tags = $tagDataArr;
    }
    return $data;
  }

  public function expandTags($resource) {
    $resp = $this->client->get();
  }
}
