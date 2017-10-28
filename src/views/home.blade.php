<!DOCTYPE html>
<html>
<head>
	<title>Laravel Simple CRUD Generator</title>
        <link href="{{ asset("css/bootstrap.min.css") }}" rel="stylesheet">
</head>
<body>

@if(Session::has("flash_message"))
<div class="row">
	<div class="col-md-3"></div>
	<div class="col-md-6">
<div class="alert alert-success" role="alert">
                {!! html_entity_decode(Session::get("flash_message")) !!}
</div>
	</div>
</div>
@endif

<center>
<div class="row">
	<h1 style="color:#70B5DE; ">Simple Laravel</h1>
<img src="/images/t1.png" style="margin-top: -40px">
	<h1 style="color:#70B5DE; margin-top: -20px">G e n e r a t o r</h1>
</div>
<br>
<div class="row">
<div class="col-md-3"></div>

<div class="col-md-6">	
	<div class="form-group">
 <div class="form-group">
 	<form action="/generate-crud" method="post">
	
		{{ csrf_field() }}

    <select class="form-control" id="exampleFormControlSelect1" name="table">
      <option>Select Table</option>
      @foreach($tables as $table)
      <option>{{$table->Tables_in_laravel}}</option>
      @endforeach
    </select>
  </div>
  <button type="submit" class="btn btn-success">Generate CRUD</button>
 	</form>
  </div> 
</div>
</div>
</center>

</body>
</html>