<?php

Route::get('/', [
    'as' => 'home', 'uses' => 'MockUpController@indexPage'
]);