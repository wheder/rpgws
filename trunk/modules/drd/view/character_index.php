<h2>Seznam všech postav</h2>
<ul>
<?php

foreach ($this->characters as $char) {
    
    echo "<li>";
    echo "<a href='/drd/character/view/$char'>";
    echo $char;
    echo "</a>";
    echo "</li>";
    
}
?>

</ul>