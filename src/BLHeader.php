<?php
/**
 * BLHeader
 * 
 * 
 * @package    MrAlirezaEb\BugloosTest
 * @author     Alireza Ebrahimzade <mr.alireza.eb@gmail.com>
 */


namespace MrAlirezaEb\BugloosTest;

use Exception;
use MrAlirezaEb\BugloosTest\Traits\SortHelper;
use MrAlirezaEb\BugloosTest\Models\BLHeaderSchema;
use MrAlirezaEb\BugloosTest\BLHeaderColumn;

class BLHeader
{
    use SortHelper;

    private $title;
    private $columns;
    private $slug;
    public $dynamic;
    public $sort_by;
    public $count_per_page;

    public function __construct(string $title = null , bool $dynamic = false , string $sort_by = null , int $count_per_page = 0)
    {
        $this->title = $title;
        $this->columns = array();
        $this->slug = null;
        $this->dynamic = $dynamic;
        $this->sort_by = $sort_by;
        $this->count_per_page = $count_per_page;
    }

    public function __get($name)
    {
        return $this->$name;
    }

    /**
     * static method
     * this method returns a instance on class by initialing it by database 
     */
    public static function get(string $slug)
    {
        //checks if there is any schema in database with this slug name
        if($header = self::checkSlugExist($slug))
        {
            // initial this instance with stored schema in database
            return self::headerSync($header);
        }
        else{
            throw new Exception("header is not valid : this header identified by '$slug' is not exists");
        }
    }

    // public methods ---------------------------------------------------------------------
    
    /**
     * public method
     * this method adds column (BLHeaderColumn) to header 
     */
    public function addColumn(BLHeaderColumn $column)
    {
        array_push($this->columns , $column);
    }

    /**
     * public method
     * this method adds batch columns (BLHeaderColumn) to header 
     */
    public function addColumns(array $columns)
    {
        foreach($columns as $column)
        {
            $this->addColumn($column);
        }
    }

    /**
     * public method
     * this method removes column (BLHeaderColumn) from header
     */
    public function removeColumn(BLHeaderColumn $column)
    {
        // this method comes from SortHelper::Trait
        $this->unsetValue($this->columns , $column);
    }

    /**
     * public method
     * this method store this header as an schema on database
     */
    public function store(string $slug)
    {
        $new = new BLHeaderSchema();
        $new->slug = $slug;
        $new->content = json_encode(['title' => $this->title , 'columns' =>  $this->columns]);
        $new->dynamic = $this->dynamic;
        $new->sort_by = $this->sort_by;
        $new->count_per_page = $this->count_per_page;
        $new->save();
    }

    /**
     * public method
     * this method store this header as an schema on database
     */
    public function update(string $slug = null)
    {
        if(!$slug)
        {
            $slug = $this->slug;
        }
        if($schema = BLHeaderSchema::where('slug',$slug)->first())
        {
            $schema->content = json_encode(['title' => $this->title , 'columns' =>  $this->columns]);
            $schema->dynamic = $this->dynamic;
            $schema->sort_by = $this->sort_by;
            $schema->count_per_page = $this->count_per_page;
            $schema->save();
        }
    }

    /**
     * public method
     * this method sets sortable toggle value
     */
    public function setSlug(string $slug)
    {
        $this->slug = $slug;
    }

    // private methods ---------------------------------------------------------------------

    /**
     * private method
     * this method checks database for identified slug name existence
     */
    private static function checkSlugExist(string $slug)
    {
        if($schema = BLHeaderSchema::where('slug', $slug)->first())
        {
            $schema->content = json_decode($schema->content, true);
            return $schema;
        }
        else{
            return false;
        }
    }

    /**
     * private method
     * this method make an instance from database by identified slug name
     */
    private static function headerSync($header)
    {
        $_instance = new self($header->content['title']);
        $_instance->setSlug($header->slug);
        foreach($header->content['columns'] as $columnJson)
        {
            $_instance->addColumn(self::jsonToObject($columnJson));
        }
        $_instance->dynamic = $header->dynamic;
        $_instance->sort_by = $header->sort_by;
        $_instance->count_per_page = $header->count_per_page;
        return $_instance;
    }

    /**
     * private method
     * this method makes json decoded array to an BLHeaderColumn object
     */
    private static function jsonToObject($columnJson)
    {
        $column = new BLHeaderColumn($columnJson['name'], $columnJson['source'] , $columnJson['type'] , $columnJson['width'] , $columnJson['searchable'] ,$columnJson['sortable']);
        return $column;
    }
}

