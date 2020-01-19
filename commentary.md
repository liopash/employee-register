# employee-register

### Spustenie
z konzoly (requirements: PHP7.4, composer, yarn/nodejs, symfony) príkazmi 
```
  git clone https://github.com/liopash/employee-register.git
  cd employee-register
  composer update
  yarn
  yarn dev
  symfony serve
  127.0.0.1:8000 <- URL
```

alebo cez docker (requirements: docker), build a warmup.sh script spustí composer aj yarn
```
  git clone https://github.com/liopash/employee-register.git
  cd employee-register
  docker-compose up
  172.17.0.2 <- URL (port 80, docker ip môže byť rôzna)
```

###
Použil som symfony 5 (flex), twig, yarn, encore webpack, bootstrap, chart.js a fontawsome.
*symfony flex* - ako microframework ponúka všetko čo som k vypracovaniu zadania potreboval,
správu formulárov, templating, routing (annotations), logging (použil som len pri debugovaní), 
maker bundle som pouzil pre generaciu niektorých tried. Symfony je silne prepojeny na 
doctrine orm/dbal použitím xml súboru ako storage niekoľko iných výhod stráca. 
Doctrine som teda ako storage/repository vrstvu nahradil triedami StorageXml a StorageAdapter.
Cieľom StorageXml/StorageAdapter je sprístupnit CRUD operácie nad xml súborom. 
Trieda SimpleXMLElement pokryla väčšinu potrieb ale kvôli formátovaniu som použil aj DOMDocument triedu.
Teoreticky by si StorageXml a StorageAdapter zaslúžili byť viac oddelený, aj od entity tried a controller-a, 
čo by umožnilo napríklad definovať StorageInterface a vytvoriť povedzme StorageCSV bez zásahu do ďalších častí kódu.
Samotna štruktúra xml súboru vyzerá nasledovne:
```
<?xml version="1.0" encoding="UTF-8"?>
<employees>
  <employee uuid="374e364f">
    <role>admin</role>
    <firstName>Emanuela</firstName>
    <lastName>Žákovičová</lastName>
    <gender>F</gender>
    <dob>1992-10-19</dob>
    <email>emazacka@gmail.com</email>
  </employee>
  <employee uuid="646c8a6e">
    <role>admin</role>
    <firstName>Viktor</firstName>
    <lastName>Čistý</lastName>
    <gender>M</gender>
    <dob>1984-03-04</dob>
    <email>vikto.cisty@gmail.com</email>
  </employee>
</employees>
```
Zadanie som rovno rozšíril o rolu/pozíciu, email a vek je v storage uložený ako dátum narodenia, kedže je to parameter 
viazaný na čas. Role sú v kóde definované triedami ako entity kde Employee je parent a samotné role su child triedy.
Konkrétne vo vypracovaní existuje Office staff, Developer staff, Support staff čo by malo modelovať povedzme 
malú IT firmu. Táketo modelovanie (entitami) som si vybral kvôli výberu frameworku aby som bol schopný použiť,
čo najviac z toho čo ponúka. Nakoniec je to tak trochu `overthinking` ale teoreticky by mohlo uľahčit tvorbu nových 
feature, priklad každá trieda ma iný výpočet na zobrazenie výšky prémie alebo iný do systému maju users rôzne práva
kde mať triedu pre každú rolu dáva zmysel.

Twig a encore webpack boli dostačujúce voľby pre dobre aj ked skromne vyzerajúce GUI, nepouzil som menu/navbar lebo mi to
prišlo pre zadanie zbytočné, graf s vekom riešený toggle ikonou ktorá zapína/vypína zobrazenie grafu v index view.
Snažil som sa použiť najnovšie technológie (php 7.4, webpack) ale sass/less som nepoužil lebo som nevidel na to potrebu.
Webpackom je samozrejme možné importovať aj sass (.enableSassLoader()).

Ako aplikáciu "servovať" nebolo definované v požiadavkach, počas práce som používal len symfony serve a yarn dev --watch.
Ale aby som pokryl aj túto oblasť tak som pripravil aj dokerizované riešenie aj keď nie moc sofistikované.

Pridanie nového attribútu napr. `shift preference` by vyžadovalo pridať property, getter a setter do entity/triedy, templatu a parametrov metód v iných triedach ktoré transformujú objekty xml <-> entity.
viď. pull request https://github.com/liopash/employee-register/compare/feature/shiftPreference?expand=1

