Navod na spustenie v priecinku s docker-compose suborom:

1.) docker-compose build
2.) docker-compose up
3.) http://localhost:8080/

POZNAMKY:
- iniciacia databazy prebieha cez docker file, SQL subor je ulozeny v priecinku initdb a spusta sa iba raz pri vytvarani kontajnera (pre znovu-spustenie SQL suboru treba dat napr. docker system prune -a)
- kosik je naspodku, pod produktami
- email v objednavke musi byt v tvare xxx@xx.xx, PSC a cislo domu musia byt iba cisla
- reklama nie je ziadna defaultna, treba ju pridat cez admin panel (link na stranku + link na obrazok)
- testy sa nespustaju automaticky

Zdrojove subory a priecinky:
- frontend = react-app/src
- server = backend/server.js
- test = backend/test/test.js
- DB seed = dbinit/db.sql