##setup ?

1. put `MY_controller` and put it inside application/core.
2. change your controllers class to extend MY_controller
3. make sure you autoload session,database,html helper
4. create model User with required login functions.login(),in_group(),pwd_has_changed()


###Dependecies :

1. models/user.php : an example is provided
2. helpers/MY_html_helper.php : depend on alerts() function and some javascript for proper alerts showing.

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
    
    <script>
			/*-----------example using jquery alertify plugin of how i use function alerts()-----------*/
			function alertFeed(){
				$.get("<?=base_url($this->router->fetch_class())?>/alerts", function(data){
					data=JSON.parse(data);
					$.each(data, function(i) {alertify.log(data[i].msg, data[i].type);});
				});	
			}
			
			setInterval(function(){alertFeed()},30000);
			/*-----------END alertify-----------*/
			
			$(function (){
				alertify.set({ delay: 10000 });
				alertFeed();
			});
			
			/* please note that inline_js need to be inside <script> tags */
			<?=@$inline_js?>
		</script>
    
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
  	
  	//$this->auth(); //uncomment and controller will require a loged in user !
  	
    $this ->_asset('jquery-2.0.3.min.js')         //will detect that file is js and loadit into $js
          ->_asset('style.css')                   //will add link tag for it and assign it to $css file auto
          ->_asset('alert("hello folks");','js');//will add this like of js to $inline_js
  
  }
  
  public function index(){
    
    $this->load->model('user');
    $data = $this->user->get_profile();
    
    $this->outv('user/profile',$data)   //This will load views/table_views.php into $content variable
         ->out('<hr>');                // === >  $content .= '<hr>';
    
    $data = $this->user->get_friends_list();
    $this->outv('users/list',$data); // loading another view into $content
    
    $data = $this->user->online_friends();
    $this->outv('chat/online_chats',$data,'sidebar'); //will load views/chat/online_chats.php into $sidebar
    
    ///.... after u load what every part of the page that i want into each section of page u can simply run
    $this->_flush();
  }

}
```



###Avaliable auth ::


;CI rocks;


##NOTE
this is still a concept under testing.

this is just a demonstration of how u can forget about the need for a templating lib. that will slow you down.

read the code.. understand the concept and ty :).
