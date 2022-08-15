9. delete pictures from google bucket except logo
10. admin api
11. test with the mainnet
12. records network api calls
13. retry once again after failed
14. notify admin if retry failed and ban the account
15. look for tron specific api

# App start checklist

1. visit the deployed url

# ENV checklist

1. APP_ENV=staging/production
2. APP_URL=deployed domain url
3. CLIENT_URL=deployed domain url
4. NODE_URL=http://localhost:{deployed port} [disable request to this port except for localhost/127.0.0.1]
5. NOTICE_EMAIL=liu66375@gmail.com
6. DB_DATABASE
7. DB_USERNAME
8. DB_PASSWORD
9. GOOGLE_BUCKET_NAME=new google bucket name
10. REDIS_PASSWORD=
11. MAIL_MAILER=smtp
12. MAIL_HOST=smtp.googlemail.com
13. MAIL_PORT=465
14. MAIL_USERNAME=support@my-trade
15. MAIL_PASSWORD=bidulhmcahezombb
16. MAIL_ENCRYPTION=ssl
17. MAIL_FROM_ADDRESS=support@my-trade.me
18. MAIL_FROM_NAME="${APP_NAME}"

# If it's production env run `php artisan key:generate` right afer setting up env file
