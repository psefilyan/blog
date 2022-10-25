<?php

    namespace App\Http\Controllers;

    use Carbon\Carbon;


    class LRUCache extends Controller {
        public $capacity;
        public $cache = [];
        public $last_used = [];

        public function __construct(int $capacity, $cache, $last_used) {
            $this->capacity = $capacity;
            $this->cache = $cache;
            $this->last_used = $last_used;
        }

        public function put(string $key, $value, int $expire) {
            if(count($this->cache) == $this->capacity && !isset($this->cache[$key])){
                $last = end($this->last_used);
                $this->remove($last);
                array_pop($this->last_used);
            }
            $this->cache[$key] = $value;
            $this->cache[$key]['exp_date'] = Carbon::now()->addSeconds($expire);
        }

        /**
         * @throws \Exception
         */
        public function get(string $key) {
            if(isset($this->cache[$key]) && $this->cache[$key]['exp_date'] >= Carbon::now()){
                array_unshift($this->last_used, $this->cache[$key]);
                return $this->cache[$key];
            }
            else {
                throw new \Exception('The item with this key not found or it is expired');
            }
        }

        public function has(string $key): bool {
            return isset($this->cache[$key]) && $this->cache[$key]['exp_date'] >= Carbon::now();
        }

        public function remove(string $key) {
            if(isset($this->cache[$key]))
                unset($this->cache[$key]);
        }

        public function size(): int {
            return count($this->cache);
        }
    }
