<h2>Vytvoření nové postavy</h2>


<form action="/drd/character/create" method="post">
    <div>
    Jméno: <input type="text" name="name" /><br />
    Magenergie: <input type="text" name="mana" /><br />
    Životy: <input type="text" name="hitpoint" /><br />
    Popis: <textarea name="description" rows="7" cols="40"></textarea><br />
    Předměty: <textarea name="items" rows="7" cols="40"></textarea><br />
    Rasa: 
    <select name="race">
        <?php foreach ($this->races as $race) { ?>
         <option value="<?php echo $race["drd_races_id"];  ?>"><?php echo $race["name"];  ?></option>
        <?php  } ?>
    </select><br />
    Povolání: 
    <select name="class">
        <?php foreach ($this->classes as $class) { ?>
         <option value="<?php echo $class["drd_class_id"];  ?>"><?php echo $class["name"];  ?></option>
        <?php  } ?>
    </select><br />
    <input type="submit" value="Vytvořit"/>
    </div>
</form>

<?php



