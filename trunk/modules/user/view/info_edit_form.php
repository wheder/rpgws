<?php
if($this->err)
{
?>
<h3>Chyba:</h3>
<p><?php echo $this->msg; ?></p>
<?php 
} else {
?>

<form action="user/info/edit" method="post">
<p>
    Nick: <input type="text" name="nick" maxlength="<?php echo $this->nick_max; ?>" value="<?php echo $this->user->nick;?>"/><br />
    <br />
    Změna hesla:<br />
    Staré heslo: <input type="password" name="oldpass" maxlength="<?php echo $this->pass_max; ?>"/><br />
    Nové heslo: <input type="password" name="newpass" maxlength="<?php echo $this->pass_max ?>"/><br />
    Nové heslo (znovu pro kontrolu): <input type="password" name="newpass2" maxlength="<?php echo $this->pass_max ?>"/><br />
    <br />
</p>
<?php if(!empty($this->fields)) { ?>
    Nepovinné údaje:<br />
    <table>
    <tr>
    	<th>Název</th>
    	<th>Hodnota</th>
    	<th>Veřejný</th>
    </tr>

<?php foreach($this->fields as $field) {
       echo "    <tr>\n";
       echo "        <td>$field:</td>\n";
       echo "        <td><input type=\"text\" name=\"$field\" value=\"" . $this->user->get_detail($field) . "\" /></td>";
       echo "        <td><input type=\"checkbox\" name=\"public_$field\"";
       if($this->user->is_public($field)) echo " checked=\"checked\"";
       echo " value=\"1\"></td>\n";
       echo "    </tr>\n"; 
   }
}?>
   </table>
<p>
   <br />
   <input type="submit" value="Odeslat" />
</p>
</form>

<?php }?>
