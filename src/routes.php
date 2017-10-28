<?php

Route::get('crudgenerator1', function(){
    echo 'Hello from the calculator package!';
});


Route::get('crud-generator', 'AbdulQadir\CRUDGenerator\CRUDController@index');

Route::post('generate-crud', 'AbdulQadir\CRUDGenerator\CRUDController@generatecrud');