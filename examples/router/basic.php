<?php

// include the toolkit
require('../../bootstrap.php');

// simulate a current url
$currentURL = 'http://mydomain.com/api/users/getkirby';
$currentURL = 'http://mydomain.com/api/users/getkirby/posts';

// register a new GET route 
router::get('api/users/(:any)', function($username) {
  echo 'user profile for: ' . $username;
});

// register another GET route
router::get('api/users/(:any)/posts', function($username) {
  echo 'posts for user: ' . $username . '...';
});

// check for a matching route 
if($route = router::match($currentURL)) {
  // call the route action and pass all variables from the url
  call_user_func_array($route->action(), $route->options());
}