<?php
/**
 * BLTable
 * 
 * 
 * @package    MrAlirezaEb\BugloosTest
 * @author     Alireza Ebrahimzade <mr.alireza.eb@gmail.com>
 */


namespace MrAlirezaEb\BugloosTest;

use Exception;
use GuzzleHttp\Client;
use Illuminate\Http\Request;

class BLTable
{
    protected $rows;
    protected $header;
    protected $meta;
    protected $pages;
    protected $current_page;
    protected $sort_by;
    protected $order;
    protected $count;
    protected $count_per_page;



    public function __construct( array $header , $data=null , array $meta=null)
    {
        $this->setHeader($header);
        $this->setRows($data);
        $this->setMeta($meta);
        $this->search();
        $this->sortBy();
        $this->count = count($this->rows);
        $this->count_per_page = count($this->rows);
        $this->current_page = 1;
        $this->pages = 1;
        if(request()->has('blt_count_per_page'))
        {
            $this->count_per_page = intval(request()->blt_count_per_page);
        }
    }

    // public methods ---------------------------------------------------------------------
    /**
     * public method
     * this method return final result as json 
     */
    public function toJSON()
    {
        return (object) [
            'header'=>$this->header,
            'rows'=>$this->rows,
            'meta'=>$this->meta,
            'pages'=>$this->pages,
            'current_page'=>$this->current_page,
            'sort_by'=>$this->sort_by,
            'order'=>$this->order,
            'count'=>$this->count,
            'count_per_page'=>$this->count_per_page,
        ];
    }
    /**
     * public method
     * this method echo final result as a blade view ( with styles) 
     */
    public function show()
    {
        $table = (object) [
            'header'=>$this->header,
            'rows'=>$this->rows,
            'meta'=>$this->meta,
            'pages'=>$this->pages,
            'current_page'=>$this->current_page,
            'sort_by'=>$this->sort_by,
            'order'=>$this->order,
            'count'=>$this->count,
            'count_per_page'=>$this->count_per_page,
        ];

        $html = view('bltable::bltable.table')->with([
            'table'=>$table,
            'config'=>(object) config()->get('bltable')
        ])->render();
        return $html;
    }
    /**
     * public method
     * this method return final result as json 
     */
    public function paginate(int $count)
    {
        if(request()->has('blt_count_per_page'))
        {
            $count = request()->blt_count_per_page;
        }
        $this->count_per_page = $count;
        $this->pages = ceil($this->count/$this->count_per_page*1.0);
        if(request()->has('blt_page') && request()->blt_page !='')
        {
            $this->current_page = request()->blt_page;
            $this->rows = array_slice($this->rows , ($this->current_page-1 )*$this->count_per_page , $this->count_per_page);
        }
        else{
            $this->rows = array_slice($this->rows , ($this->current_page-1 )*$this->count_per_page , $this->count_per_page);
        }
        return $this;
    }
    // private methods ---------------------------------------------------------------------

    /**
     * private method
     * this method sorts row by sortable columns 
     */
    private function search()
    {
        $source = request()->blt_search_source;
        $search = request()->blt_search;
        $rows = (array) $this->rows;
        $arr = array();
        if($search !='')
        {
            foreach($this->header->columns as $col)
            {
                if($col['source']==$source)
                {
                    foreach($rows as $item)
                    {
                        if(strpos($item[$source] , $search))
                        {
                            array_push($arr , $item);
                        }
                    }   
                }
            }
            $this->rows = (object) $arr;
        }
    }

    /**
     * private method
     * this method sorts row by sortable columns 
     */
    private function sortBy()
    {
        $source = request()->blt_sort_by;
        $order = request()->blt_order;
        $rows = (array) $this->rows;
        foreach($this->header->columns as $col)
        {
            if($col['source']==$source)
            {
                $this->sortBySubArrayValue($rows , $source);   
            }
        }
        $this->rows = $rows;
        $this->sort_by = $source;
        $this->order = $order;
    }

    /**
     * private method
     * this method set header of table 
     */
    private function setHeader($header)
    {
        if($this->headerValidator($header))
        {
            $this->header = (object) $header;
        }
    }

    /**
     * private method
     * this method set rows of data by header condition 
     */
    private function setRows($data)
    {
        if(in_array(gettype($data) , ['array', 'object' , 'NULL']))
        {
            switch(gettype($data))
            {
                case "NULL":
                    $this->rows = null;
                break;
                // for test case Eloquent\Collection is a good choice (just considered laravel models)
                case "object":
                    if(get_class($data)=="Illuminate\Database\Eloquent\Collection")
                    {
                        $rows = $this->initRows($data);
                    }
                    else{
                        throw new Exception('not valid class for data, data only accepts "Illuminate\Database\Eloquent\Collection" : '.get_class($data));
                    }
                break;
                // will be used in case of third-party APIs
                case "array":
                    if(isset($data['url']))
                    {
                        $client = new Client(); #this package (GuzzleHttp) is better than file_get_contents or cURL libs
                        $response = $client->request(isset($data['method']) ? $data['method'] : 'GET', $data['url'] ,['header'=>['Accept'=>'application/json']]); #for test case I prefer to get a json response but in future it can get developed in other ways
                        $body = $response->getBody();
                        $result = json_decode($body, true);
                        $rows = $this->initRows($result);
                        $this->rows = $rows;
                    }
                    else{
                        throw new Exception('not valid data : url most be set');
                    }
                break;
            } 
        }
        else{
            throw new Exception('not valid type of data : '.gettype($data));
        }
    }

    /**
     * private method
     * this method set meta of table 
     */
    private function setMeta($meta)
    {
        if(in_array(gettype($meta),['array','NULL']))
        {
            $this->meta = (object) $meta;
        }
        else{
            throw new Exception("meta is not valid : only arrays acceptable");
        }
    }

    /**
     * private method
     * this method is for validating header array values and keys 
     */
    private function headerValidator($header) : bool
    {
        //checks correct keys for header
        foreach(array_keys($header) as $key)
        {
            if(!in_array($key,['title','columns']))
            {
                throw new Exception("header is not valid : this key is unusable '$key'");
            }
        }
        if(isset($header['columns']) && !empty($header['columns']))
        {
            if(gettype($header['columns'])!='array')
            {
                throw new Exception(`header is not valid : columns is array`);
            }
            foreach($header['columns'] as $column)
            {
                foreach(array_keys($column) as $key)
                {
                    if(!in_array($key,['type','name','source' , 'width' , 'searchable' , 'sortable']))
                    {
                        throw new Exception("header is not valid : this key is unusable '$key'");
                    }
                }
            }
        }
        

        return true;
    }

    /**
     * private method
     * this method exert header conditions on data and returns an array of rows
     */
    private function initRows($data)
    {
        $arr = array();
        foreach($data as $row)
        {
            $index = [];
            foreach($this->header->columns as $col)
            {
                if(isset($row[$col['source']]))
                {
                    $value = $row[$col['source']];
                    isset($col['type']) ? settype($value , $col['type']) : $value;
                    $index[$col['source']] = $value;
                }
                else{
                    $index[$col['source']] = null;
                }
            }
            array_push($arr,$index);
        }
        return (object) $arr;
    }

    /**
     * private method
     * this method will be used to sort datas
     */
    private function sortBySubArrayValue(&$array, $key, $dir='asc') {
 
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
}