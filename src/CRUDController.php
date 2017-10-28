<?php

namespace AbdulQadir\CRUDGenerator;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class CRUDController extends Controller
{
	 public function index()
    {
        $tables = \DB::select('SHOW TABLES');

        // print_r($tables);return;
        return view('crud::home')->with(['tables' => $tables]);
    }
	
    public function generatecrud()
    {
        $post = request()->all();

        if ($post['table'] == "Select Table")
            return redirect('/');

        $class = $post['table'];
        
        $columns  = \DB::getSchemaBuilder()->getColumnListing($class);
        
        $this->generateController($class);
        $this->generateModel($class,$columns);
        $this->generateIndex($class,$columns);
        $this->generateCreateView($class,$columns);
        $this->generateView($class,$columns);
        $this->createRoute($class);

        \Session::flash('flash_message', 'Your '.$post['table'].' table CRUD has been successfully generated <a href="/'.$post['table'].'" class="alert-link">Click here</a>. to view');
            return redirect('/');
    }


    public function generateModel($class,$columns)
    {
        $fillable = " protected \$fillable = [ ";

        foreach ($columns as $key => $value) {
            if ($key == count($columns) -1)
            $fillable .= "'$value'";
        else
            $fillable .= "'$value',";
        }

        $fillable .= "];";
// foreach($tables as $table)
// {
//     // print_r($table->Tables_in_laravel);
// }
        // return "ASDf";

$myfile = fopen(app_path()."//".ucfirst($class).".php", "w") or die("Unable to open file!");
$txt = "<?php
namespace App;

use Illuminate\Database\Eloquent\Model;

class ".ucfirst($class)." extends Model
{
    $fillable
}
";
fwrite($myfile, $txt);
fclose($myfile);
    }
    public function generateIndex($table_name,$columns)
    {
        $header = '<th scope="col">#</th>';
        $col = " <td>{{ \$key }}</td> \n"; 

        foreach ($columns as $key => $value) {
        $header .= '<th scope="col">'.ucwords(str_replace("_", " ", $value)).'</th>';
        $col .= " <td>{{ \$value->$value }}</td> \n"; 
        }   
        $class = ucfirst($table_name);

        $create = '
@extends(\'layouts.blank\')

@push(\'stylesheets\')
    <!-- Example -->
    <!--<link href=" <link href="{{ asset("css/myFile.min.css") }}" rel="stylesheet">" rel="stylesheet">-->
@endpush

@section(\'main_container\')

    <div class="right_col" role="main">
             @if(Session::has("flash_message"))
            <div class="alert alert-success">
                {{ Session::get("flash_message") }}
            </div>
        @endif

        <h1>'.$class.' </h1>

         <p>
        <a class="btn btn-success" href="/'.$table_name.'/create">Create '.$class.'</a>    
        </p>
<div class="panel panel-default">
  <div class="panel-heading">Panel Heading</div>
  <div class="panel-body">


        <table class="table table-striped table-bordered">
  <thead>
    <tr>
    '.$header.'
    </tr>
  </thead>
  <tbody>
    <?php foreach ($'.$table_name.' as $key => $value): ?>
        
         <tr>
           '.$col.'

<td>
    <a href="/'.$table_name.'/show/{{$value->id}}" title="View" aria-label="View" data-pjax="0"><span class="glyphicon glyphicon-eye-open"></span></a> 
    <a href="/'.$table_name.'/edit/{{$value->id}}" title="Update" aria-label="Update" data-pjax="0"><span class="glyphicon glyphicon-pencil"></span></a> 
    <a href="/'.$table_name.'/destroy/{{$value->id}}" title="Delete" aria-label="Delete" data-pjax="0" data-confirm="Are you sure you want to delete this item?" data-method="post"><span class="glyphicon glyphicon-trash"></span></a></td>
    </tr>
    <?php endforeach ?>
  </tbody>
</table>


  </div>
</div>

</div>
@endsection';

    // print_r(base_path()."\\resources\\views\\$table_name");
    if (!file_exists(base_path()."\\resources\\views\\$table_name")) {
    mkdir(base_path()."\\resources\\views\\$table_name", 0777, true);
}

$myfile = fopen(base_path()."\\resources\\views\\$table_name\\index.blade.php", "w") or die("Unable to open file!");
fwrite($myfile, $create);
fclose($myfile);

    }


    public function generateController($table_name)
    {
        $class = ucfirst($table_name);

     $controller = "<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\\".$class.";

class ".$class."Controller extends Controller
{
    
     public function __construct()
    {
        \$this->middleware('auth');
    }


    public function index()
    {
        \$$table_name = $class::all();

        \$data = ['$table_name' => \$$table_name];
        return view('$table_name.index')->with(\$data);
    }

   
    public function create()
    {
        //
        return view('$table_name.create');

    }

    
    public function store(Request \$request)
    {
        $class::create(request()->all());

        return redirect('$table_name');
        // dd(\$request->all());
        //
    }

    
    public function show(\$id)
    {
         \$$table_name = $class::find(\$id);


        return view('$table_name.view')->with(['$table_name' => \$$table_name]);
    }

    public function edit(\$id)
    {
        \$table_name = $class::find(\$id);


        return view('$table_name.update')->with(['$table_name' => $table_name]);
    }

    public function update(Request \$request, \$id)
    {
        \$$table_name = $class::findOrFail(\$id);

        \$".$table_name."->fill(\$request->all());

        \$".$table_name."->save();
        \Session::flash('flash_message', '$class successfully updated!');
        return redirect('/$table_name');
    }

    public function destroy(\$id)
    {
        \$$table_name = $class::find(\$id);

        if (\$$table_name)
            \$$table_name ->delete();
        else
            echo \"No exist\";

        return redirect('$table_name');
        //
    }
}
";

$myfile = fopen(base_path()."\\app\\http\\Controllers\\".$class."Controller.php", "w") or die("Unable to open file!");
fwrite($myfile, $controller);
fclose($myfile);   
    }

    public function createRoute($table_name)
    {

        $myfile = fopen(base_path()."\\routes\\web.php", "a") or die("Unable to open file!");
        $class = ucfirst($table_name);

        $routes = "

Route::get('$table_name', '".$class."Controller@index');
Route::get('$table_name/show/{id}','".$class."Controller@show');
Route::get('$table_name/create', '".$class."Controller@create');
Route::post('$table_name/store','".$class."Controller@store');
Route::get('$table_name/edit/{id}','".$class."Controller@edit');
Route::post('$table_name/update/{id}','".$class."Controller@update');
Route::get('$table_name/destroy/{id}', '".$class."Controller@destroy');
        ";

        fwrite($myfile,$routes);
        fclose($myfile);

    }


    public function generateCreateView($table_name,$columns){

$fields = "";

    foreach ($columns as $key => $value) {
        $fields .= '  <div class="form-group">
    <label for="">'.ucwords(str_replace("_", " ", $value)).'</label>
    <input type="text" class="form-control"  name='.$value.' aria-describedby="emailHelp" >
  </div>';
    }
        $create_view = '
@extends("layouts.blank")

@push("stylesheets")
    <!-- Example -->
    <!--<link href=" <link href="{{ asset("css/myFile.min.css") }}" rel="stylesheet">" rel="stylesheet">-->
@endpush

@section("main_container")

    <div class="right_col" role="main">
        <h1>Create</h1>
        
        <form action="/'.$table_name.'/store" method="post">

            {{ csrf_field() }}

            '.$fields.'

  <button type="submit" class="btn btn-primary">Submit</button>
</form>
    </div>

@endsection';

$myfile = fopen(base_path()."\\resources\\views\\$table_name\\create.blade.php", "w") or die("Unable to open file!");
fwrite($myfile, $create_view);
fclose($myfile);

    }

    public function generateView($table_name,$columns)
    {
        $rows = "";

        foreach ($columns as $key => $value) {
            $rows .= '<tr>
  <th>'.ucwords(str_replace("_", " ", $value)).'</th>
  <td>{{$'.$table_name.'->'.$value.'}}</td>
</tr>';
        }

        $view = '
@extends("layouts.blank")

@push("stylesheets")
    <!-- Example -->
    <!--<link href=" <link href="{{ asset("css/myFile.min.css") }}" rel="stylesheet">" rel="stylesheet">-->
@endpush

@section("main_container")

    <div class="right_col" role="main">

 <h1>{{$'.$table_name.'->id}}</h1>

    <p>
        <a class="btn btn-primary" href="/'.$table_name.'/edit/{{$'.$table_name.'->id}}">Update</a>        <a class="btn btn-danger" href="/'.$table_name.'/destroy/{{$'.$table_name.'->id}}" data-confirm="Are you sure you want to delete this item?" data-method="post">Delete</a>    </p>

    <table id="w0" class="table table-striped table-bordered detail-view">

'.$rows.'
</table>
</div>
    </div>
</div>

</div>
@endsection';

$myfile = fopen(base_path()."\\resources\\views\\$table_name\\view.blade.php", "w") or die("Unable to open file!");
fwrite($myfile, $view);
fclose($myfile);

    }


}
