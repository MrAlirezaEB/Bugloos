<?php
/**
 * BLHeaderColumn
 * 
 * 
 * @package    MrAlirezaEb\BugloosTest
 * @author     Alireza Ebrahimzade <mr.alireza.eb@gmail.com>
 */


namespace MrAlirezaEb\BugloosTest;

use Exception;


class BLHeaderColumn
{
    public $type;
    public $name;
    public $source;
    public $width;
    public $searchable;
    public $sortable;

    public function __construct( string $name , string $source , string $type = 'string' , int $width = 50 , bool $searchable = true , bool $sortable = true ) 
    {
        $this->type = $type;
        $this->name = $name;
        $this->source = $source;
        $this->width = $width;
        $this->searchable = $searchable;
        $this->sortable = $sortable;
    }

    // public methods ---------------------------------------------------------------------

    /**
     * static method
     * this method make an instance of  BLHeaderColumn::class by static method
     */
    public static function make(string $name , string $source) : self
    {
        return new self($name , $source); 
    }

    /**
     * static method
     * this method make an array of instance of  BLHeaderColumn::class by static method
     */
    public static function makeBatch(array $data) : array
    {
        
        if( self::validateBatchArray($data) )
        {
            $_instances = array();
            foreach($data as $column)
            {
                array_push($_instances , self::make($column['name'] , $column['source']));
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
            throw new Exception("column is not valid : columns should be array");
        }
        foreach($data as $column)
        {
            foreach(array_keys($column) as $key)
            {
                if(!in_array($key,['name','source']))
                {
                    throw new Exception("column is not valid : this key is unusable '$key'");
                }
            }
        }
    }
}