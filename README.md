# TMCMSCMS
"TMCMS" - CMS that I started to create in 2011. I still update it as people require something "custom made". As the time goes I improve or rewrite CMS to use latest technologies that I know.

TMCMS is my hobby mostly and something that I use when client ask to not use WordPress or other CMS. I also found many existing CMS too bulky or hard to understand, I don't think that my CMS is some kind of solution for this. But it's definetely less than majority of what out there.
Right now as there are tons of separations between RESTful API, Backend CMS system and Frontend webapps. I can't find any proper CMS that will be able mostly working with API without including MySQL. Previously I was trying to make my CMS light as much as possible obviously using PHP/MySQL.
Now I try to make it a dynamic backend system for communication with API. From CMS to dynamic and configurable API-CMS.

If you find this project interesting I wouldn't mind you to join and contribute or just give ideas what could be made to make it better.

## Instalation
The best and easiest way to install CMS is through composer. It will allow you to keep up with updates. This is the best choise if you want to only develop your own modules, without touching core files.
```
{
    "name": "TMCMS Demo",
    "require": {
        "maxtream/themages": "4.0.0"
    }
}
```

## Dependencies
- Composer
- SlimPHP framework 3
- MySQL/MariaDB

## Demo
Demo can be found [on website](https://cms.maxorlovsky.com)