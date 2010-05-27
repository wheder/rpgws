<h2>Přehled postav</h2>
<?php
if ($this->err) {
    
    echo "<p>".$this->mess."</p>";
    
    return;
}

foreach ($this->characters as $char) {
    ?>
    <div>
    Jméno: <?php echo $char["name"] ?><br />
    Rasa: <?php echo $char["race"] ?><br />
    Povolání: <?php echo $char["class"] ?><br />
    Popis: <?php echo $char["description"] ?><br />
    
    </div>
    <?php
    
}