<?php
if($this->err)
{
?>
<h3>Chyba:</h3>
<p><?php echo $this->msg; ?></p>
<?php 
} else {
?>
<h3>Informace o uzivateli <?php echo $this->user->nick; ?></h3>

<?php if(empty($this->details)) {?>
<p>Uživatel nepovolil zobrazení žádných informací, nebo je nemá nastavené.</p>;
<?php } else {?>
<table>
  <tr>
    <th>Název</th>
    <th>Hodnota</th>
  </tr>
  
  <?php foreach($this->details as $field => $detail) {?>
  <tr>
    <td><?php echo $field;?></td>
    <td><?php echo $detail;?></td>
  </tr>
<?php } ?>
</table>
<?php }
}?>

