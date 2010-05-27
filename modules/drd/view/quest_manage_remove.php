<?php
if($this->err) {
?>
<h2>Postavy nebyly přidány</h2>
<?php } else { ?>
<h2>Postavy přidány</h2>
<?php } 
    echo "<p>" . $this->msg . "</p>";
?>
