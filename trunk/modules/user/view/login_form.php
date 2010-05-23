<form action="/user/login/login" method="post">
<p>
    Nick: <input type="text" name="nick" maxlength="<?php echo $this->nick_max ?>" /><br />
    Heslo: <input type="password" name="pass" maxlength="<?php echo $this->pass_max ?>"/><br />
    <input type="submit" value="Přihlásit"/>
</p>
</form>