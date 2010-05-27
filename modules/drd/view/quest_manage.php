<h1>Správa questu</h1>
<?php
  if($this->err) {
     echo "<p>" . $this->msg . "</p>"; 
  } else {
?>
<h2>Postavy v questu:</h2>
<ul>
  <?php 
  $in_quest = array();
  if(!empty($this->quest_chars)) foreach($this->quest_chars AS $char) {
     $in_quest[] = $char->character_id;
     ?>
  	<li><?php echo $char->name;?> - 
  	  <a href="/drd/character/modify_form/<?php echo $char->character_id;?>">upravit</a>
  	  <a href="/drd/quest/manage_remove/<?php echo $this->quest->quest_id; ?>/<?php echo $char->character_id;?>">odsranit z questu</a>
  	</li>
  <?php }?>
</ul>
<hr />
<h2>Přidání postavy:</h2>
<form action="/drd/quest/manage_add/<?php echo $this->quest->quest_id?>" method="post">
    <p>
    Postavy:
    </p>
    <ul> 
<?php if(!empty($this->add_chars)) foreach($this->add_chars AS $char) {
    if(!in_array($char->character_id, $in_quest) && $this->gm != $char->owner) {
        ?>
        <li><?php echo $char->name;?> (povolání: <?php echo $char->class->name;?>,
          rasa: <?php echo $char->race->name;?>,
          id: <?php echo $char->character_id;?>)
          <input type="checkbox" name="char_<?php echo $char->character_id; ?>" value="1"/>
        </li>
        <?php         
    }
} ?>
	</ul>    
    
    <input type="submit" value="Přidat" />
    
</form>
<?php } ?>