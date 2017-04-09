## 简介

Polymer是一个基于Slim、Doctrine、Twig的MVC框架!设计上只是将其定位于一个'胶水型'的框架,
其本身并没有对Doctrine、Slim、Twig进行任何的封装!只是将这三者进行了整合!

[Slim参考文档](https://www.slimframework.com/docs/)

[Doctrine参考文档](http://docs.doctrine-project.org/projects/doctrine-orm/en/latest/)

[Twig参考文档](https://twig.sensiolabs.org/doc/2.x/)

[PoCMS项目](https://github.com/macrofengye/PoCMS)

## 安装

1、使用Composer进行安装,在项目路径下新建composer.json,内容如下:

    {
      "require": {
        "artful/polymer": "dev-master"
      },
      "autoload": {
        "psr-4": {
          "CMS\\": "app\\CMS\\"
        }
      },
      "repositories": {
        "packagist": {
          "type": "composer",
          "url": "https://packagist.phpcomposer.com"
        }
      }
    }

使用compsoer install 或者使用 php composer.phar install进行依赖包安装!

2、在项目路径下新建app、public_html文件夹,这里将用于项目功能的开发！

3、在app目录下新建CMS文件夹,在public_html下新建cms的文件夹!

4、新建index.php文件，内容如下:

    date_default_timezone_set('Asia/Shanghai');
    define('ROOT_PATH', dirname(dirname(__DIR__)));
    define('APP_NAME', 'CMS');
    define('TEMPLATE_PATH', ROOT_PATH . '/app/' . APP_NAME . '/Templates/');
    define('APP_PATH', ROOT_PATH . '/app/' . APP_NAME . '/');
    require ROOT_PATH . '/vendor/autoload.php';
    defined('APPLICATION_ENV') || define('APPLICATION_ENV', 'development');
    $app = new \Polymer\Boot\Application();
    $app->start();
将其保存在public_html\cms文件夹中!

5、服务器配置rewrite规则

5.1、Nginx的nginx.conf中新增:

    server {
        listen 80;
        root  项目路径\public_html\cms;
        index index.html index.php;
        server_name polymer.cms.com;
        if (-f $request_filename/index.php){
            rewrite (.*) $1/index.php;
        }
        if (!-f $request_filename){
            rewrite (.*) /index.php;
        }
        location / {
            try_files $uri $uri/ =404;
        }
        location ~ \.php$ {
        #   include snippets/fastcgi-php.conf;
        #
        #   # With php5-cgi alone:
            fastcgi_pass 127.0.0.1:9000;
        #   # With php5-fpm:
        #   fastcgi_pass unix:/var/run/php5-fpm.sock;
            include fastcgi_params;
            fastcgi_index index.php;
            fastcgi_param SCRIPT_FILENAME $document_root$fastcgi_script_name;
        }
    }
    
5.2、Apache的.htaccess内容如下:

    RewriteEngine On
    <IfModule mod_rewrite.c> 
    	RewriteEngine on
    	RewriteCond %{REQUEST_FILENAME} !-d 
    	RewriteCond %{REQUEST_FILENAME} !-f 
    	RewriteRule ^(.*)$ index.php [L,E=PATH_INFO:$1]
    </IfModule>
    
6、控制器:

新建Home.php,内容如下:
    
    <?php
    namespace CMS\controller;
    
    use Polymer\Controller\Controller;
    use Slim\Http\Request;
    use Slim\Http\Response;
    
    class Home extends Controller
    {
        public function index(Request $request, Response $response, $args)
        {
            return $this->withJson(['data' => 'data']);
        }
    }
将其保存在app\CMS]\Controller里面.

7、路由配置:

新建home_router.php文件，内容如下：

    <?php
    $app->map(['GET', 'POST'], '/', APP_NAME . '\\Controller\\Home:index')->setName(APP_NAME . '.home.index');
将其保存在app\CMS\Routers里面!

8、在浏览器里输入服务器配置的虚拟域名,例如:http://polymer.cms.com, 将显示如下内容:
    
    {'data':'data'}


9、[参考PoCMS项目](https://github.com/macrofengye/PoCMS),该项目是基于Polymer框架开发!






    