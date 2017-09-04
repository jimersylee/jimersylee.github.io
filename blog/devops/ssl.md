server {
        listen       443;
        server_name  www.jimersylee.com;
        ssl on;
        ssl_certificate /data/ssl_cert/Nginx/1_jimersylee.com_bundle.crt;         
        ssl_certificate_key /data/ssl_cert/Nginx/2_jimersylee.com.key;         
        ssl_session_timeout 5m;         
          ssl_protocols TLSv1 TLSv1.1 TLSv1.2; #按照这个协议配置 
          ssl_ciphers ECDHE-RSA-AES128-GCM-SHA256:HIGH:!aNULL:!MD5:!RC4:!DHE;#按照这个套件配置 
        ssl_prefer_server_ciphers on;
        root /data/www/www;

        index index.html index.htm index.php;
        location /
        {

            index index.html index.htm index.php;
            if (!-e $request_filename) {
               rewrite  ^/(.*)$  /index.php/$1  last;
               break;
            }
        }

        location ~ \.php/?.*$ {
            fastcgi_pass   127.0.0.1:9000;
            fastcgi_index  index.php;
            include        fastcgi_params;
            include        fastcgi.conf;
            #设置PATH_INFO并改写SCRIPT_FILENAME,SCRIPT_NAME服务器环境变量
            set $fastcgi_script_name2 $fastcgi_script_name;
            if ($fastcgi_script_name ~ "^(.+\.php)(/.+)$") {
              set $fastcgi_script_name2 $1;
              set $path_info $2;
            }
            fastcgi_param   PATH_INFO $path_info;
            fastcgi_param   SCRIPT_FILENAME   $document_root$fastcgi_script_name2;
            fastcgi_param   SCRIPT_NAME   $fastcgi_script_name2;
        }


        #location ~ .*\.(gif|jpg|jpeg|png|bmp|swf)$
        #{
        #        expires 30d;
        #}
        location ~ .*\.(js|css)?$
        {
                expires 1h;
        }
        access_log  /data/logs/www/www.log main;
}
server {     
listen  80;
server_name www.jimersylee.com;
rewrite ^(.*)$  https://$host$1 permanent;
}
