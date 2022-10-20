<?php

namespace App\Http\Controllers;

use Carbon\Carbon;
use Illuminate\Http\Request;


class LRUCache extends Controller
{
    public $capacity;
    public $cache=[];

    public function __construct(int $capacity)
    {
        $this->capacity = $capacity;

    }

    public function put(string $key, $value, int $expire){
        if(count($this->cache)==$this->capacity)
        {
            array_shift($this->cache);
        }
        $this->cache[$key] =$value;
        $this->cache[$key]['exp_date'] =Carbon::now()->addSeconds($expire);


    }

    /**
     * @throws \Exception
     */
    public function get(string $key){
        if(isset($this->cache[$key]) && $this->cache[$key]['exp_date'] >= Carbon::now())
        {
            return $this->cache[$key];
        }
        else{
            throw new \Exception('The item with this key not found or it is expired');
        }
    }
    public function has(string $key) :bool
    {
        return isset($this->cache[$key]) && $this->cache[$key]['exp_date'] >= Carbon::now();
    }
    public function remove(string $key){
        if (isset($this->cache[$key]))
            unset($this->cache[$key]);
    }
    public function size():int{
        return count($this->cache);
    }
}
