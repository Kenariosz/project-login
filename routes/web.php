<?php

// Home Routes
Route::get('/', 'HomeController@index');
Route::get('/home', 'HomeController@index');
Route::get('/login-attempts', 'HomeController@listSummarisedLA');

// Authentication Routes...
$this->get('login', 'Authentication\LoginController@showLoginForm')->name('login');
$this->post('login', 'Authentication\LoginController@login');
$this->post('logout', 'Authentication\LoginController@logout')->name('logout');

// Registration Routes...
$this->get('register/activation/{activationToken}', 'Authentication\RegisterController@activeUser');
$this->get('register', 'Authentication\RegisterController@showRegistrationForm');
$this->post('register', 'Authentication\RegisterController@register');
