<?php
if($this->err) {
?>
<h3>Změna se nezdařila</h3>
<?php 
} else {
?>
<h3>Změna úspěšná</h3>
<?php 
}
echo "<p>" . $this->msg . "</p>";
?>
