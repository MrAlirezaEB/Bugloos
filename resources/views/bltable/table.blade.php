<h1>{{$table->header->title}}</h1>
<div class="container">
    <form id='blt-form' action="" method="get">
      <div class="header_wrap">
        <div class="num_rows">
        
                <div class="form-group"> 	<!--		Show Numbers Of Rows 		-->
                     <select class  ="form-control" name="blt_count_per_page">
                         @foreach ($config->count_per_page as $item)
                         <option value="{{$item}}" @if ($table->count != $table->count_per_page && $table->count_per_page==$item)
                          selected
                      @endif>{{$item}}</option>
                         @endforeach
                         
                        <option value="" @if ($table->count == $table->count_per_page)
                            selected
                        @endif>Show ALL Rows</option>
                        </select>
                     
                  </div>
        </div>
        <div class="tb_search" style="display: flex">
          <select class  ="form-control" name="blt_search_source">
            @foreach ($table->header->columns as $item)
            @if (isset($item->searchable) && $item->searchable)
            <option value="{{$item->source}}" @if (request()->blt_search_source == $item->source)
              selected
          @endif>{{$item->name}}</option>
            @endif
            @endforeach
            
           <option value="" @if (request()->blt_search_source == '')
               selected
           @endif>All</option>
           </select>
          <input type="text" name='blt_search'  placeholder="Search.." class="form-control" value="{{request()->blt_search}}">
        </div>
      </div>
    </form>
<table class="table table-striped table-class" id= "table-id">

  
<thead>
<tr>
  @foreach ($table->header->columns as $item)
  <th>
    @if (isset($item->sortable) && $item->sortable)
    <a href="{{ request()->fullUrlWithQuery(['blt_sort_by' => $item->source]) }}">{{$item->name}}</a>
    @else
    {{$item->name}}
    @endif
  </th>
  @endforeach
    
  </tr>
</thead>
<tbody>
  @foreach ($table->rows as $item)
    <tr>
      @foreach ($table->header->columns as $header)
    <td>{{$item[$header->source]}}</td>
    @endforeach
  </tr>
  @endforeach
  
  <tbody>
</table>

<!--		Start Pagination -->
          <div class='pagination-container'>
              <nav>
                <ul class="pagination">
                 @for ($i = 1; $i <= $table->pages; $i++)
                 
                 <li @if (request()->blt_page==$i) class="active" disabled='disabled' @endif><a href="{{ request()->fullUrlWithQuery(['blt_page' => $i]) }}">{{$i}}</a></li>
                      
                 @endfor
                </ul>
              </nav>
          </div>
    <div class="rows_count">Showing {{($table->count_per_page*($table->current_page-1))+1}} to {{$table->count_per_page*$table->current_page-1}} of {{$table->count}} entries</div>

</div> <!-- 		End of Container -->



<link rel='stylesheet' href='https://maxcdn.bootstrapcdn.com/bootstrap/3.3.6/css/bootstrap.min.css'><link rel="stylesheet" href="./style.css">
<script src='https://cdnjs.cloudflare.com/ajax/libs/jquery/2.2.2/jquery.min.js'></script>
<script src='https://maxcdn.bootstrapcdn.com/bootstrap/3.3.6/js/bootstrap.min.js'></script><script  src="./script.js"></script>
<style>
    body{

background-color: #eee; 
}

table th , table td{
text-align: center;
}

table tr:nth-child(even){
background-color: #e4e3e3
}

th {
background: #333;
color: #fff;
}

.pagination {
margin: 0;
}

.pagination li:hover{
cursor: pointer;
}

.header_wrap {
padding:30px 0;
}
.num_rows {
width: 20%;
float:left;
}
.tb_search{
width: 40%;
float:right;
}
.pagination-container {
width: 70%;
float:left;
}

.rows_count {
width: 20%;
float:right;
text-align:right;
color: #999;
}
</style>

<script>
  $('select').change(function(){
    $('#blt-form').submit();

  })
</script>