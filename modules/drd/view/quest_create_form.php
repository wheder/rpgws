<h2>Nový quest</h2>
<?php if($this->err) 
{
    echo "<p>" . $this->msg .  "</p>";  
}
else
{
?>
<form action="/drd/quest/create" method="post">
<p>
	Popis:<br />
	<textarea rows="5" cols="40" name="desc"></textarea>
	<br />
	<input type="submit" value="Vytvořit" />
</p>
</form>
<?php } ?>