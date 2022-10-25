<?php

    namespace App\Http\Controllers;

    use Carbon\Carbon;


    class LRUCache extends Controller {
        public $capacity;
        public $cache = [];
        public $least_used = [];


        public function __construct(int $capacity) {
            $this->capacity = $capacity;
        }

        public function put(string $key, $value, int $expire) {
            if(count($this->cache) == $this->capacity && !isset($this->cache[$key])){
                $arr = array_keys($this->least_used, min($this->least_used));
                $this->remove($arr[0]);
            }
            $this->least_used[$key] = 0;

            $this->cache[$key] = $value;
            $this->cache[$key]['exp_date'] = Carbon::now()->addSeconds($expire);
        }

        /**
         * @throws \Exception
         */
        public function get(string $key) {
            if(isset($this->cache[$key]) && $this->cache[$key]['exp_date'] >= Carbon::now()){
                $this->least_used[$this->cache[$key]]++;


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
