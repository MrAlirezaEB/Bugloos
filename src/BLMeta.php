<?php
/**
 * BLMeta
 * 
 * 
 * @package    MrAlirezaEb\BugloosTest
 * @author     Alireza Ebrahimzade <mr.alireza.eb@gmail.com>
 */


namespace MrAlirezaEb\BugloosTest;

use Exception;

class BLMeta
{
    public $key;
    public $value;

    public function __construct(string $key , $value) {
        $this->key = $key;
        $this->value = $value;
    }

    // public methods ---------------------------------------------------------------------

    /**
     * static method
     * this method make an instance of  BLMeta::class by static method
     */
    public static function make(string $key ,  $value) : self
    {
        return new self($key , $value); 
    }

    /**
     * static method
     * this method make an array of instance of  BLMeta::class by static method
     */
    public static function makeBatch(array $data) : array
    {
        
        if( self::validateBatchArray($data) )
        {
            $_instances = array();
            foreach($data as $meta)
            {
                array_push($_instances , self::make($meta['key'] , $meta['value']));
            }
            return $_instances;
        }
    }

    // private methods ---------------------------------------------------------------------

    /**
     * private method
     * this method validates batch array 
     */
    private function validateBatchArray($data) : bool
    {
        if(gettype($data)!='array')
        {
            throw new Exception("meta is not valid : metas should be array");
        }
        foreach($data as $meta)
        {
            foreach(array_keys($meta) as $key)
            {
                if(!in_array($key,['key','value']))
                {
                    throw new Exception("meta is not valid : this key is unusable '$key'");
                }
            }
        }
    }
}