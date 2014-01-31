<?php defined('SYSPATH') OR die('No direct script access.');

/**
 * Достаем код для вставки в хтмл
 * Class Kohana_zAssets_Source_Code
 */
class Kohana_zAssets_Source_Code extends Assets_Source {

    public function check(){

        if(!is_string($this->source))
            return false;
        return true;
    }

    /**
     * Код для вставки <script>code</script>
     * @return mixed|string
     */
    public function html(){
        return $this->collection->assets->before_source($this).$this->source.$this->collection->assets->after_source($this);
    }

    /**
     * Для проверки уникальности кода
     * @return mixed|string
     */
    public function hash(){
        return md5($this->source());
    }

    /**
     * кусок кода, который добавим в ассет
     * @return mixed|string
     */
    public function content(){
        return $this->source();
    }
}
