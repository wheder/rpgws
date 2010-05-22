<form action="/user/registration/register" method="post">
<p>
    Nick: <input type="text" name="nick" maxlength="<?php echo $this->nick_max ?>" /><br />
    E-mail: <input type="text" name="mail" maxlength="<?php echo $this->mail_max ?>"/><br />
    <input type="submit" value="Registrovat"/>
</p>
</form>