<?php
if($this->err) {
?>
<h2>Quest nebyl vytvořen</h2>
<?php } else { ?>
<h2>Quest vytvořen</h2>
<?php } 
    echo "<p>" . $this->msg . "</p>";
?>
