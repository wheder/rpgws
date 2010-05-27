<?php if($this->err) {?>
<h3>Chyba při přidávání příspěvku.</h3>
<?php } else { ?>
<h3>Příspěvek přidán</h3>
<?php } ?>
<p><?php echo $this->msg; ?></p>