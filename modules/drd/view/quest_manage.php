<h1>Správa questu</h1>
<?php
  if($this->err) {
     echo "<p>" . $this->msg . "</p>"; 
  } else {
?>
<h2>Postavy v questu:</h2>
<ul>
  <?php if(!empty($this->quest_chars)) foreach($this->quest_chars AS $char) {?>
  	<li><?php echo $char->name;?> - 
  	  <a href="/drd/character/modify_form/<?php echo $char->character_id;?>">upravit</a>
  	  <a href="/drd/quest/manage_remove/<?php echo $char->character_id;?>">odsranit z questu</a>
  	</li>
  <?php }?>
</ul>
<hr />
<h2>Přidání postavy:</h2>
<form action="/drd/quest/manage_add/" method="post">
    <p>
    tady bude form na pridani postavy<br />
    
    <input type="submit" value="Přidat" />
    </p>
</form>
<?php } ?>