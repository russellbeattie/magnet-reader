<?

	$page['title'] = 'Login';


?>

<div class="content">

<? if($page['error']){ ?>
<div class="message"><?=$page['error']?></div>
<? } ?>

<form class="loginForm" action="login" method="post">
        
        <label for="username">Username</label>
        <input type="text" id="username" name="username" size="20" value="<?=$page['username']?>"/>
        
        <label for="password">Password </label>
        <input type="password" id="password" name="password" size="20" value="<?=$page['password']?>"/>
       
		<div class="buttons">
		
		<button type="submit" class="btn btn-large">Sign In</button>

        </div>


</form>

</div>

