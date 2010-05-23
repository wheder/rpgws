<?php
if($this->err)
{
?>
<h3>Nepodařilo se přihlásit.</h3>
<?php 
} else {
?>
<h3>Uživatel přihlášen.</h3>
<?php
}
?>
<p>
<?php echo $this->msg; ?>
</p>