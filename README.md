# Gravity Forms Login
A variety of actions using Gravity Forms as a front-end.

## Getting Started

The easiest way to install this package is by using composer from your terminal:

```bash
composer require moxie-lean/wp-gforms --save
```

Or by adding the following lines on your `composer.json` file

```json
"require": {
  "moxie-lean/wp-gforms": "dev-master"
}
```

This will download the files from the [packagelist site](https://packagist.org/packages/moxie-lean/wp-gforms-login) 
and set you up with the latest version located on master branch of the repository. 

After that you can include the `autoload.php` file in order to
be able to autoload the class during the object creation.

```php
include '/vendor/autoload.php';
```

## Usage

This package contains a number of actions which hook into Gravity Forms and run when the form is submitted. They rely on certain Admin Labels being set-up in the form builder.

### Login
Logs the user into WordPress.

```
\Lean\Gforms\Actions\Login::init( $form_id );
```

Requires fields with the following Admin Labels:
- user_login - either the username or email.
- user_pass  - the password.

### Password Reset
Sends a password reset email to the user.

```
\Lean\Gforms\Actions\ResetPassword::init( $form_id );
```

Requires fields with the following Admin Labels:
- user_login - either the username or email.

### Signup
Creates a new user account from the form data.

```
\Lean\Gforms\Actions\Signup::init( $form_id );
```

Requires fields with the following Admin Labels:
- user_email - the email.
- user_pass  - the password.

Optionally can have fields with any of the following Admin Labels:
- user_login   - the username, generated automatically if not used.
- display_name - the display name.
- first_name   - the first name.
- last_name    - the last name.
