Datalea - a Random Test Data Generator
======================================

Install
-------

* copy config sample file and edit the copy with your environment values

```bash
cp app/config/config.php.dist app/config/config.php
```

* get composer http://getcomposer.org/ and install dependencies

```bash
    curl -s https://getcomposer.org/installer | php
```

* install dependencies
    
```bash
    php composer.phar install
```

* set you web server document root to web directory

* clean cache

```bash
    php app/console cache:clear
```

Licensing
---------

License GPL 3

* Copyright (C) 2012-2013 Spyrit Systeme

This file is part of Datalea.

Datalea is free software: you can redistribute it and/or modify
it under the terms of the GNU General Public License as published by
the Free Software Foundation, either version 3 of the License, or
(at your option) any later version.

Datalea is distributed in the hope that it will be useful,
but WITHOUT ANY WARRANTY; without even the implied warranty of
MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
GNU General Public License for more details.

You should have received a copy of the GNU General Public License
along with Datalea.  If not, see <http://www.gnu.org/licenses/>.