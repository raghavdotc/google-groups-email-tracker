# google-groups-email-tracker

Schema :
btape.sql in the rool folder contains dump of the schema.

Setup:
1. Clone Repo
2. Run composer install
3. Copy the contents of sample env file using in the root folder - cp .env.example .env
4. Set the values of the following keys in the .env file
    - APP_URL - domain on which app runs - example: http://ggtracker.dev
    - DB_HOST
    - DB_DATABASE
    - DB_USERNAME
    - DB_PASSWORD
    - GOOGLE_CLIENT_ID
    - GOOGLE_CLIENT_SECRET
    - EMAIL_DOMAIN
5. Run "php artisan migrate" on the root folder. Make sure an empty database already exists before you run "php artisan migrate"
   and you have supplied the right db credentials.
6. Setup a server block on nginx to point to public/index.php in the project root folder
7. Set up Google Oauth2.0 Credentials and add the appropriate callback url in the Google Developer Console
    i.e, if APP_URL=http://ggtracker.dev
         then redirect url = http://ggtracker.dev/login/google/callback       
8. Restart the nginx server.
9. Run "php artisan queue:work" in the project root folder to initialize the daemon/worker to accept and process Jobs being 
   pushed to the queue. 
10.Optionally setup Supervisor to manage the queue workers. - https://laravel.com/docs/5.4/queues#supervisor-configuration


Architecture - 
1) User Login via Google Oauth2.0 using the appropriate <example@domain.com> for the group.
2) Last login info is stored for each email.
3) On every new login, a job(for the queue) is created to fetch the Gmail Threads since the last login
4) For every Gmail Thread a new job(for the queue) is created to fetch messages.
5) Each thread is processed and stored in the database
    a) Check if the message is not already stored
    b) Check if the sender email belongs to EMAIL_DOMAIN set in the .env
    c) Check if the subject contains a valid CLIENTID
    d) Create an entry in the emails table, once the above checks have passed
6) Tabular View of all the emails stored in our database @ APP_URL/filters
7) Tabular View of counts of emails sent to each client  @ APP_URL/dashboard
8) Sometimes, as soon as you login freshly, there are background jobs running to update the email records, 
   and before they finish running, the data on the dashboard might be wrong.
9) As an additional step, we could stop the access to an dashboards for sometime, while there are jobs running in the background
   and possibly ask the user to be notified once the jobs finish running.
   




         
