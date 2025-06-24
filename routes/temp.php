<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

Route::get('/temp/check-db', function () {
    $columns = Schema::getColumnListing('clients');
    $client = \App\Models\Client::first();
    
    return [
        'columns' => $columns,
        'first_client' => $client ? $client->toArray() : null,
    ];
});
