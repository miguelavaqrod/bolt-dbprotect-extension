DB Protect extension
=========================

First, thanks a lot Bolt staff for creating Password Protect extension.
This is just a fork of that extension so it can serve my own needs.

The "DB Protect extension" is a small extension that allows you to 
protect one or more of your pages with a username and password extracted (and compared) from a table in a database.
Use it by simply placing the following in your template:

    {{ dbprotect() }}

You can put this either in your template, to protect an entire contenttype, or just
place it in the body text of a record.

People who do not yet have access will automatically be redirected to a
page, where they will be asked to provide the password.

See `config.yml` for the options.

**Note:** This 'protection' should not be considered 'secure'. Credebtials will be sent
over the internet in plain text, so it can be intercepted if people use it on a
public WiFi network, or something like that.

The 'credentials' page
-------------------
The page you've set as the `redirect:` option in the `config.yml` can be any Bolt
page you want. It can be a normal page, where you briefly describe why the user was
suddenly redirected here. And, perhaps you can give instructions on how to acquire
the password, if they don't have it. When the user provides the correct password,
they will automatically be redirected back to the page they came from.

To insert the actual form with the username and password fields, simply use:

    {{ dbform() }}

Like above, you can do this either in the template, or simply in the content of
the page somewhere.

**Tip:** do not be a dumbass and require a login on the page you're redirecting to!
Your visitors will get stuck in an endless loop, if you do.