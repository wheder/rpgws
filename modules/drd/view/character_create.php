<?php
if($this->err)
{
?>
<h3>Nepodařilo se vytvořit postavu.</h3>
<?php 
} else {
?>
<h3>Postava vytvořena</h3>
<?php 
}
?>
<p><?php echo $this->msg; ?></p>