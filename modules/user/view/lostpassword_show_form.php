<form action="/user/lostpassword/send_password" method="post">
<p>
    Nick: <input type="text" name="nick" maxlength="<?php echo $this->nick_max ?>" /><br />
    E-mail: <input type="text" name="mail" maxlength="<?php echo $this->mail_max ?>"/><br />
    <br />
    Datum narozeni:<br />
    Den: <select name="day">
        <?php for($i = 1; $i < 32; $i++) { ?>
             <option value="<?php echo $i; ?>"><?php echo $i;?></option>    
         <?php   }   ?>
        </select>
    Měsíc: <select name="month">
    	<?php for($i = 1; $i < 13; $i++) { ?>
             <option value="<?php echo $i; ?>"><?php echo $i;?></option>    
         <?php   }   ?>
    </select>
    Rok: <select name="year">
    	<?php
    	   $year_end = date("Y", time());
    	   for($i = 1930; $i <= $year_end; $i++) {	?>
    	   <option value="<?php echo $i; ?>"><?php echo $i;?></option>
    	<?php } ?>
    </select><br />
    <input type="submit" value="Registrovat"/>
</p>
</form>