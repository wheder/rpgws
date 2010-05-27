<h2>Změna postavy</h2>

<?php if($this->err) {
    echo "<p>" . $this->msg . "</p>";
} else {
?>
<form action="/drd/character/modify/<?php echo $this->char->character_id; ?>" method="post">
    <div>
    Jméno: <input type="text" name="name" value="<?php echo $this->char->name; ?>" /><br />
    Magenergie: <input type="text" name="mana" value="<?php echo $this->char->mana;?>"/><br />
    Životy: <input type="text" name="hitpoint" value="<?php echo $this->char->hit_points;?>" /><br />
    Popis: <textarea name="description" rows="7" cols="40"><?php echo $this->char->description;?></textarea><br />
    Předměty: <textarea name="items" rows="7" cols="40"><?php echo $this->char->items;?></textarea><br />
    <input type="submit" value="Uložit"/>
    </div>
</form>
<?php 
}
?>