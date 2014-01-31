<?php defined('SYSPATH') OR die('No direct script access.');

class Kohana_zAssets_Source_File extends Assets_Source{

    private $path='';

    private function path(){

        if(empty($this->path)){

            $path = $this->collection->assets->source_path.$this->source;

            if(!file_exists($path) || !is_readable($path))
                throw new AssetsException('File not found: :path', array(':path'=>$path));

            $this->path = $path;
        }
        return $this->path;

    }

    public function check(){

        try {
            $this->path();
        }catch (AssetsException $e){
            //self::$_sources->file($this->group(), $this->source(), $this->sort());
            return false;
        }
        return true;
    }

    public function html(){
        return $this->collection->assets->before_source($this).$this->collection->assets->source_host.$this->source.$this->collection->assets->after_source($this);
    }

    public function hash(){
        return md5_file($this->path());
    }

    public function content(){
        return file_get_contents($this->path());
    }
}