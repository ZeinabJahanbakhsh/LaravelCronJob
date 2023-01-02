<p align="center"><a href="https://laravel.com" target="_blank"><img src="https://raw.githubusercontent.com/laravel/art/master/logo-lockup/5%20SVG/2%20CMYK/1%20Full%20Color/laravel-logolockup-cmyk-red.svg" width="400"></a></p>

<p align="center">
<a href="https://travis-ci.org/laravel/framework"><img src="https://travis-ci.org/laravel/framework.svg" alt="Build Status"></a>
<a href="https://packagist.org/packages/laravel/framework"><img src="https://img.shields.io/packagist/dt/laravel/framework" alt="Total Downloads"></a>
<a href="https://packagist.org/packages/laravel/framework"><img src="https://img.shields.io/packagist/v/laravel/framework" alt="Latest Stable Version"></a>
<a href="https://packagist.org/packages/laravel/framework"><img src="https://img.shields.io/packagist/l/laravel/framework" alt="License"></a>
</p>

## Coding Challenge Description:
<ul>
<li>Sign up</li>
<li>Authenticate via token.</li>
<li> Get a directory name and create a directory with that name in user’s opt/myprogram/$username/" directory.</li>
<li>Get a filename and create a file with that name in </li>
<li> Get list of all directories </li>
<li>Get list of all files</li>
<li>Add your user with factory</li>
<li>Having test for endpoints.</li>
<li>Send mail usig cron job </li>

</ul>

## Used package
<p>Laravel passport for Authenticate user => <a href="https://laravel.com/docs/8.x/passport"> Read more</a></p>

## Cron job
<p>Using mailgun that is a free api to send and receive email => <a href="https://www.mailgun.com">Read more</a></p>
<p>Task Scheduling in laravel framework => <a href="https://laravel.com/docs/8.x/scheduling">Read more</a></p>

Configuration mailgun:
<p>Create an account on mailgun next you will get mail configuration as mail host, mail port, mail username, mail passwor and edit the MAIL confing details in .env:</p>

```
MAIL_MAILER=mailgun
MAIL_HOST=smtp.mailgun.org
MAIL_PORT=587
MAIL_USERNAME=postmaster@.....
MAIL_PASSWORD=784.....
MAIL_ENCRYPTION=tls
MAIL_FROM_ADDRESS=qazx@sandboxda........mailgun.org
MAIL_FROM_NAME="${APP_NAME}"
MAILGUN_ENDPOINT=api.mailgun.net
MAILGUN_DOMAIN=sandbox..........mailgun.org
MAILGUN_SECRET=7ea..........

```
