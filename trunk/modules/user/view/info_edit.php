<?php
if($this->err)
{
?>
<h3>Nepodařilo se změnit údaje.</h3>
<?php 
} else {
?>
<h3>Údaje změněny.</h3>
<?php
}
?>
<p>
<?php echo $this->msg; ?>
</p>
