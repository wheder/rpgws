<h3>Seznam uživatelů v DB.</h3>
<?php
if($this->err)
{
?>
<p>
	<?php echo $this->msg; ?>
</p>
<?php 
} else {
?>
<table>
  <tr>
    <th>Uživatel</th>
    <th>Poslední akce</th>
    <th>Podrobnosti</th>
  </tr>
<?php foreach($this->users as $user) {?>
  <tr>
    <td><?php echo $user['nick'] ?></td>
    <td>
<?php 
if($user['action'] + session_cache_expire()*60 >= $user['time']) {
    echo date("H:i:s", $user['action']);
} else {
    echo "offline";
}
?>  
     </td>
    <td><a href="/user/info/show_user/<?php echo $user['nick']?>">Zobrazit</a></td>
  </tr>
<?php } ?>
</table>

<?php } ?>
