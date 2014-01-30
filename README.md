##install ?

1. download `MY_controller` and put it inside application/core.
2. change your controllers class to extend MY_controller



##usage

first you need to edit $body to point to your base template; default location is `views/base/body.php`


###example of body.php
```
<html>
    <head>
        <title><?=@$title?></title>
        <?=@$css?>
        
        <?=@$inline_css;?>
        
    </head>

    <body>
    
        <div class='sidebar'>
            <?=@$sidebar?>
        </div>
    
        ....
        <?=@$content?>
        ...
    
    <?=@$js?>
    
    <?=@$inline_js?>
    
    </body>
</html>
```

###example of controller
```
<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Welcome extends MY_Controller {

  public function __construct()
  {
  	parent::__construct();
    $this ->_asset('jquery-2.0.3.min.js')         //will detect that file is js and loadit into $js
          ->_asset('style.css')                   //will add link tag for it and assign it to $css file auto
          ->_asset('alert("hello folks");','js');//will add this like of js to $inline_js
  
  }
  
  public function index(){
    $this->load->model('user');
    $data = $this->user->get_profile();
    
    $this->_outv('user/profile',$data)   //This will load views/table_views.php into $content variable
         ->_out('<hr>');                // === >  $content .= '<hr>';
    
    $data = $this->user->get_friends_list();
    $this->_outv('users/list',$data); // loading another view into $content
    
    $data = $this->user->online_friends();
    $this->_outv('chat/online_chats',$data,'sidebar'); //will load views/chat/online_chats.php into $sidebar
    
    ///.... after u load what every part of the page that i want into each section of page u can simply run
    $this->_flush();
  }

}
```



###Avaliable options ::
this was to answer a stackoverflow question :p !; yet i use this concept in real projects and its working good for me;

;CI rocks;


##NOTE

this is just a demonstration of how u can forget about the need for a templating lib. that will slow you down.

read the code.. understand the concept and ty :).
