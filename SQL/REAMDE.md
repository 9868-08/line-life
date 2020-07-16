how-to
https://blog.anthonyaxenov.ru/2018/10/15/%D1%88%D0%BF%D0%B0%D1%80%D0%B3%D0%B0%D0%BB%D0%BA%D0%B0-master-slave-%D1%80%D0%B5%D0%BF%D0%BB%D0%B8%D0%BA%D0%B0%D1%86%D0%B8%D1%8F-mysql-5-7/  

url from Isakanov Vadim  (github.com/vadimisakanov)
https://ruhighload.com/%D0%9A%D0%B0%D0%BA+%D0%BD%D0%B0%D1%81%D1%82%D1%80%D0%BE%D0%B8%D1%82%D1%8C+mysql+master-slave+%D1%80%D0%B5%D0%BF%D0%BB%D0%B8%D0%BA%D0%B0%D1%86%D0%B8%D1%8E%3F  

to resolve replica error:
STOP SLAVE; SET GLOBAL SQL_SLAVE_SKIP_COUNTER = 1; START SLAVE;  
from: https://ma.ttias.be/fixing-mysql-master-slave-replication-upon-query-error/
