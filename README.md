# Team Project
#### Piotr Puchacz | Michał J. Sidor

### Steps required to run project on your local machine

1. Clone repo

        git clone https://github.com/MJSidor/inz.git
2. Install dependencies

        composer install
3. Run MySQL server and setup databe
        
        php bin/console doctrine:database:create inzynierka
4. Migrate tables to database

        php bin/console doctrine:migrations:migrate
5. Fill database with data

        php bin/console doctrine:fixtures:load
6. Download video assets from [here](https://drive.google.com/file/d/1J2-hb2bEUVPGDWgUQSn_cNrLl8FpSD83/view?usp=sharing) and paste to floder `public`
7. Start Symfony Built-in server by running

        php bin/console server:run
