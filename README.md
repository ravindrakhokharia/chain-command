Chain Command Project
=====================
Chain command allows to implement command chaining functionality, It allows to register internal/3rd party commands to be a member of a command chain. When a main command is ran, all the other commands registered in the chain will be executed as well. Chain command cannot be executed on their own.

Project Setup steps
-------------------
###Step 1:
Clone repository

```bash
 git clone git@github.com:ravindrakhokharia/chain-command.git
```

###Step 2:
Setup the project using docker command

```bash
 cd chain-command
 docker-compose up
```

###Step 3 
Demo - Run the command 

```bash
 docker-compose exec php php app/console foo:hello
```

###Step 4
Review logs

```bash
 docker-compose exec php less var/log/command.log
```

Tests
----
Run PhpUnit Test cases

```bash
docker-compose exec php bin/phpunit lib/ChainBundle/tests
```
