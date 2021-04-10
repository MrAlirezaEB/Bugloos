<?php 

/**
 * SortHelper Trait
 * 
 * 
 * @package    MrAlirezaEb\BugloosTest
 * @author     Alireza Ebrahimzade <mr.alireza.eb@gmail.com>
 */

namespace MrAlirezaEb\BugloosTest\Traits;

trait SortHelper
{
    /**
     * protected method
     * this method will be used to sort datas
     */
    protected function sortBySubArrayValue(&$array, $key, $dir='asc') {
 
        $sorter=array();
        $rebuilt=array();
        //make sure we start at the beginning of $array
        reset($array);
     
        foreach($array as $i => $value) {
          $sorter[$i]=$value[$key];
        }
        //sort the built array of key values
        if ($dir == 'asc') asort($sorter);
        if ($dir == 'desc') arsort($sorter);
        //build the returning array and add the other values associated with the key
        foreach($sorter as $i => $value) {
          $rebuilt[$i]=$array[$i];
        }
     
        $array=$rebuilt;
    }

    /**
     * protected method
     * this method will be used to delete an value from array
     */
    protected function unsetValue(array $array, $value, $strict = TRUE)
    {
        if(($key = array_search($value, $array, $strict)) !== FALSE) {
            unset($array[$key]);
        }
        return $array;
    }
}