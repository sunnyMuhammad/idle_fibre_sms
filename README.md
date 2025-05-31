First clone the project


Then Open the terminal and enter the following commands

 `composer i`
 `npm i`
 `cp .env.example .env`
 `php artisan key:generate`
 `php artisan migrate`

Then set JWT_KEY in .env file

After all of this run

 `php artisan serve`
 `npm run dev`
