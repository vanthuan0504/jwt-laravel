<?php

namespace App;

class Room {
    protected $people = [];

    public function __construct($people = []) {
        $this->people = $people;
    }
    
    public function has($person) {
        return in_array($person, $this->people);
    }

    public function add($person) {
        array_push($this->people, $person);
        return $this->people;

    }
    public function remove($person) {
        if (($key = array_search($person, $this->people)) !== false) {
            unset($this->people[$key]);
        }   
        return $this->people;
    }
}