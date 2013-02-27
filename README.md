# Mp for PHP

You could creat an  web app by copy /www, and change config/config.php

Application :

    /www
    /www/application/
    /www/application/config/config.php
    /www/application/controllers/
    /www/application/models/
    /www/application/plugins/
    /www/application/helpers/
    /www/application/template/
    /www/application/libs/
    /www/application/logs/
    /www/application/cache/

Static file :
    /www/html/

Core :

    /mp
    
## Usage

To use MP, use the following:

Load LIB example:

    load("name")->method();
    
    
Load model example:

    load("name", "model")->method();
    
Load helper example:

    load("name", "helper")->method();
    
    
Load plugin example:

    load("name", "plugin")->method();
