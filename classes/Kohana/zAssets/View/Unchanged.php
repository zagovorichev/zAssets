<?php defined('SYSPATH') OR die('No direct script access.');

class Kohana_zAssets_View_Unchanged extends Assets_View{

    private $_groups = array();

    private function sort($keys, &$group){

        $tmp_group = $group;
        $group=array();

        foreach($keys as $key)
            $group[] = $tmp_group[$key];

    }

    /**
     * @return $this
     */
    protected function groups(){

        $sort_groups = array();

        if(!count($this->_groups)){

            foreach($this->_collection->applicants() as $item){
                $this->_groups[$item->group()][] = array('html'=>$item->html($this), 'sort'=>$item->sort());
                $sort_groups[$item->group()][] = $item->sort();
            }

            //sorting
            foreach($this->_groups as $group=>$val){
                $this->smart_sort($sort_groups[$group]);
                $this->sort(array_keys($sort_groups[$group]), $this->_groups[$group]);
            }
        }

        return $this;
    }


    /**
     * Source (not compress/compile) script
     */
    public function html($group=''){

        $html = '';
        if(isset($this->_groups[$group])) foreach($this->_groups[$group] as $item)
            $html .= $item['html']."\r\n";

        return $html;
    }

}