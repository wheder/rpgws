<?php
if($this->err)
{
?>
<h3>Nepodařilo se registrovat nový účet.</h3>
<?php 
} else {
?>
<h3>Účet registrován.</h3>
<?php
}
?>
<p>
<?php echo $this->msg; ?>
</p>

