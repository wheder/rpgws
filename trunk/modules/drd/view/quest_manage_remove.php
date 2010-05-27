<?php
if($this->err) {
?>
<h2>Postavy nebyly odebrány</h2>
<?php } else { ?>
<h2>Postavy odebrány</h2>
<?php } 
    echo "<p>" . $this->msg . "</p>";
?>
