<?php
if($this->err)
{
?>
<h3>Nepodařilo se vytvořit nové heslo.</h3>
<?php 
} else {
?>
<h3>Heslo bylo změněno.</h3>
<?php
}
?>
<p>
<?php echo $this->msg; ?>
</p>
