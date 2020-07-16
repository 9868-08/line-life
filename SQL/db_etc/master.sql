CREATE USER repl@'%' IDENTIFIED WITH mysql_native_password BY 'slavepassss';
GRANT REPLICATION SLAVE ON *.* TO repl@'%';

CREATE USER 'zbx_monitor'@'%' IDENTIFIED BY 'ZBX_monitor';
GRANT USAGE,REPLICATION CLIENT,PROCESS,SHOW DATABASES,SHOW VIEW ON *.* TO 'zbx_monitor'@'%';

