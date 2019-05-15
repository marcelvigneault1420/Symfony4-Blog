### Verify everything is installed
- **cmd**: php -v
- **cmd**: composer --version
- **cmd**: git --version

### When pull old project
- **cmd**: composer update
- _Verify the .env file with the database connection_
- **cmd**: php bin/console doctrine:database:create
- **cmd**: php bin/console doctrine:migrations:migrate
- **cmd**: php bin/console doctrine:migrations:status

### When starting a new project
- **cmd**: composer create-project symfony/skeleton DIR_NAME
- **cmd**: composer update

### Libraries
- Maker  
  **cmd**: composer require maker  
- Twig  
  **cmd**: composer require twig  
- Monolog  
  **cmd**: composer require logger  
    **Config**: C:\Users\Marcel\Documents\Projects\PHP\Symfony\OpenClassRoom\config\packages\dev\monolog.yaml  
    ```
    monolog:
		  handlers:
			  info:
				  level: debug
				  type: stream
				  path: "%kernel.logs_dir%/app.log"
				  channels: ["app"]
    ```
- Debug  
  **cmd**: composer require debug  
- Doctrine  
  **cmd**: composer require orm-pack  
  - Doctrine Extentions  
    **cmd**: composer req stof/doctrine-extensions-bundle  
    **Config**: C:\Users\Marcel\Documents\Projects\PHP\Symfony\OpenClassRoom\config\packages\dev\monolog.yaml  
      ```
      stof_doctrine_extensions:
			  default_locale: en_US
			  orm:
				  default:
					  sluggable: true
      ```
### Other commands  
- Clear Cache  
  **cmd**: php bin/console cache:clear  
  **cmd**: php bin/console cache:clear --env=prod  
- Create controller  
  **cmd**: php bin/console make:controller  
- Create database  
  _# .env (or override DATABASE_URL in .env.local to avoid committing your changes)_  
	_DATABASE_URL=mysql://Marcel:1d194hb2caA@127.0.0.1:3306/OpenClassRoom_  
  **cmd**: php bin/console doctrine:database:create  
  **cmd**: php bin/console make:entity  
  **cmd**: php bin/console make:migration  
  **cmd**: php bin/console doctrine:migrations:migrate  
  _Add @ORM\HasLifecycleCallbacks and @ORM\PrePersist if you want the events of prepersist_  
- Create form  
  **cmd**: composer require symfony/form  
  **cmd**: composer require symfony/security-csrf  
