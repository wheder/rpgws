<h2>Přehled postav</h2>
<?php
if ($this->err) {
    
    echo "<p>".$this->mess."</p>";
    
    return;
}

if(!empty($this->characters)) foreach ($this->characters as $char) {
    ?>
    <div>
    Jméno: <?php echo htmlspecialchars($char->name); ?><br />
    Rasa: <?php echo htmlspecialchars($char->race->name) ?><br />
    Povolání: <?php echo htmlspecialchars($char->class->name) ?><br />
    Popis: <?php echo htmlspecialchars($char->description) ?><br />
    
    </div>
    <?php
    
}
?>