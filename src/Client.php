<?php

namespace PressLib;

class Client {
  
  protected $host;

  public $ping;

  protected $resources = [];
  
  public function __construct($host) {
    $this->host = $host;
    $this->ping = "pong";
  }

  public function host() {
    return $this->host;
  }
  
  public function __get($var) {
    if (!isset($this->resources[$var])) {
      $className = ucfirst($var);
      $fqName = "PressLib\Resources\\{$className}";
      
      $this->resources[$var] = new $fqName($this->host);
    }

    return $this->resources[$var];
  }
}
