# Pivotal Crawler

Pivotal Crawler is a web application for get pivotal id from CI and push to google sheet. Pivotal crawler available manage the project latest build from CI.

## Setup
### General Setup
- if you run this on PHP7, no need to change any file, but when it run on PHP5, don't forget to remove the type hinting on `app\Lib`
- do `composer install`
- then change the permission on storage and bootstrap
- setup the database that will be used on `.env`
- do `php artisan migrate` to create the table

### Setup Pivotal
- enter your apiKey from pivotal tracker to `config/pivotal.php`
- setup Pivotal Project ID that related to a workgroup to the same file

### Setup Google Sheet
- create new project on console.google.com based on GoogleAPI.txt
- download the client_secret.json, put it on the same root level as this file
- do `php public/getkey.php` to get the credentials
- after got the credentials, change the `config/google.php`

	fill the `app_name`

	give the `client_secret_path` based on where the `client_secret.json` saved earlier, or copy from `getkey.php`

	give `spreadsheet_id` that will be used on this project

	give `credentials` path, just copy it from `getkey.php`

## How to use
- Choose the project
- Copy/Paste latest child tag revision from CI
- Put `[#pivotal_id] pivotal_title` on the `/create`
- Save it, and it will saved to system and configured google sheet
