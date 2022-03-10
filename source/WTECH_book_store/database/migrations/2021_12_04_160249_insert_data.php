<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class InsertData extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        DB::unprepared($this->query);
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        //
    }

    private $query = <<<EOT
    INSERT INTO categories (category_id, name, created_at, updated_at) VALUES
        (null, 'Knihy', now()::timestamp, now()::timestamp),
        (null, 'Elektronické knihy', now()::timestamp, now()::timestamp),
        (null, 'Audioknihy', now()::timestamp, now()::timestamp),
        (null, 'Darčeky', now()::timestamp, now()::timestamp),
        (1, 'beletria', now()::timestamp, now()::timestamp),
        (1, 'náučná literatúra', now()::timestamp, now()::timestamp),
        (1, 'pre deti', now()::timestamp, now()::timestamp),
        (5, 'detektívky', now()::timestamp, now()::timestamp),
        (5, 'sci-fi', now()::timestamp, now()::timestamp),
        (5, 'fantasy', now()::timestamp, now()::timestamp),
        (5, 'spoločenská beletria', now()::timestamp, now()::timestamp),
        (5, 'historická beletria', now()::timestamp, now()::timestamp),
        (5, 'horor', now()::timestamp, now()::timestamp),
        (6, 'história', now()::timestamp, now()::timestamp),
        (6, 'spoločenské vedy', now()::timestamp, now()::timestamp),
        (6, 'umenie a dizajn', now()::timestamp, now()::timestamp),
        (6, 'informatika', now()::timestamp, now()::timestamp),
        (6, 'prírodné vedy', now()::timestamp, now()::timestamp),
        (6, 'biznis a manažment', now()::timestamp, now()::timestamp),
        (2, 'beletria', now()::timestamp, now()::timestamp),
        (20, 'detektívky', now()::timestamp, now()::timestamp),
        (20, 'sci-fi', now()::timestamp, now()::timestamp),
        (20, 'fantasy', now()::timestamp, now()::timestamp)
    ;
    
    
    INSERT INTO authors (name, country, detail, created_at, updated_at) VALUES 
        ('undefined', 'undefined', null, now()::timestamp, now()::timestamp),
        ('Donald A. Norman', 'USA', null, now()::timestamp, now()::timestamp),
        ('Jan Řezáč', 'Česká Rebublika', null, now()::timestamp, now()::timestamp), 
        ('Jo Nesbo', 'Nórsko', 'Jo Nesbo je nórsky autor krimi románov. Iba v rodnom Nórsku sa predali vyše dva milióny jeho kníh, čo je pri počte 4,5 milióna obyvateľov neuveriteľné číslo. Nesbo je jeden z najpredávanejších a najoceňovanejších detektívnych autorov súčasnosti.', now()::timestamp, now()::timestamp),
        ('Dominik Dán', 'Slovenská Republika', 'Dominik Dán je pseudonymom slovenského spisovateľa úspešných detektívnych románov. Už viac ako dvadsaťpäť rokov sa zaoberá vyšetrovaním zločinov.', now()::timestamp, now()::timestamp),
        ('J. R. R. Tolkien', 'Anglicko', null, now()::timestamp, now()::timestamp)
    ;
    
    INSERT INTO products (category_id, author_id, product_type, name, price, main_photo, created_at, updated_at) VALUES 
        (16, 2, 'book', 'The Design of Everyday Things', 22, '1000000-main.jpg', now()::timestamp, now()::timestamp),
        (17, 3,'book', 'Web ostrý jako břitva', 30.99, '1000001-main.jpg', now()::timestamp, now()::timestamp),
        (8, 4,'book', 'Syn', 12.99, '1000002-main.jpg', now()::timestamp, now()::timestamp),
        (8, 4,'book', 'Ostrov Potkanov', 13.99, '1000003-main.jpg', now()::timestamp, now()::timestamp),
        (8, 4,'book', 'Šváby', 10.99, '1000004-main.jpg', now()::timestamp, now()::timestamp),
        (8, 5,'book', 'Bremeno Minulosti' , 12.99, '1000005-main.jpg', now()::timestamp, now()::timestamp),
        (8, 5,'book', 'Sára', 12.99, '1000006-main.jpg', now()::timestamp, now()::timestamp),
        (23, 6, 'e_book', 'Pán Prsteňov: Spoločenstvo prsteňa', 19.99, null, now()::timestamp, now()::timestamp),
        (23, 6, 'e_book', 'Pán prsteňov: Dve veže', 19.99, null, now()::timestamp, now()::timestamp),
        (23, 6, 'e_book', 'Pán prsteňov: Návrat kráľa', 18.99, null, now()::timestamp, now()::timestamp),
        (4, 1, 'merchandice', 'Čierna taška', 4.99, null, now()::timestamp, now()::timestamp)
    ;
    
    INSERT INTO languages (name, created_at, updated_at) VALUES
        ('Slovenský', now()::timestamp, now()::timestamp),
        ('Český', now()::timestamp, now()::timestamp),
        ('Anglický', now()::timestamp, now()::timestamp),
        ('Maďarský', now()::timestamp, now()::timestamp)
    ;
    
    INSERT INTO books (product_id, language_id, publisher, year, pages, country, isbn, stock, shipping_time, detail, long_detail, created_at, updated_at) VALUES
        (1000000, 3, 'The MIT Press', '2013', 368, 'Anglicko' ,'9780262525671', 0, 20, 'Even the smartest among us can feel inept as we try to figure out the shower controlin a hotel or attempt to navigate an unfamiliar television set or stove.', 'Even the smartest among us can feel inept as we try to figure out the shower controlin a hotel or attempt to navigate an unfamiliar television set or stove. When The Design ofEveryday Things was published in 1988, cognitive scientist Don Norman provocativelyproposed that the fault lies not in ourselves, but in design that ignores the needs and psychologyof people. Fully revised to keep the timeless principles of psychology up to date with ever-changingnew technologies, The Design of Everyday Things is a powerful appeal for gooddesign, and a reminder of how — and why — some products satisfy while others onlydisappoint.', now()::timestamp, now()::timestamp),
        (1000001, 2, 'Baroque Partners', '2016', 216, 'Česká republika', '9788027006441', 12, 14, '„Webdesign není jednoduchý, jen podceňovaný. A právě nadešel čas to změnit. Napsal jsem totiž první českou knihu o návrhu webu. Začínajícím webdesignerům ukáže cestu, zkušeným doplní mezery a zadavatelům ušetří statisíce za špatně navržený web nebo nesmyslný tendr.“', '„Webdesign není jednoduchý, jen podceňovaný. A právě nadešel čas to změnit. Napsal jsem totiž první českou knihu o návrhu webu. Začínajícím webdesignerům ukáže cestu, zkušeným doplní mezery a zadavatelům ušetří statisíce za špatně navržený web nebo nesmyslný tendr.“„Webdesign není jednoduchý, jen podceňovaný. A právě nadešel čas to změnit. Napsal jsem totiž první českou knihu o návrhu webu. Začínajícím webdesignerům ukáže cestu, zkušeným doplní mezery a zadavatelům ušetří statisíce za špatně navržený web nebo nesmyslný tendr.“„Webdesign není jednoduchý, jen podceňovaný. A právě nadešel čas to změnit. Napsal jsem totiž první českou knihu o návrhu webu. Začínajícím webdesignerům ukáže cestu, zkušeným doplní mezery a zadavatelům ušetří statisíce za špatně navržený web nebo nesmyslný tendr.“', now()::timestamp, now()::timestamp),
        (1000002, 1, 'Baroque Partners', '2016', 216, 'Slovenská republika', '9788027006441', 12, 14, '„Webdesign není jednoduchý, jen podceňovaný. A právě nadešel čas to změnit. Napsal jsem totiž první českou knihu o návrhu webu. Začínajícím webdesignerům ukáže cestu, zkušeným doplní mezery a zadavatelům ušetří statisíce za špatně navržený web nebo nesmyslný tendr.“', '„Webdesign není jednoduchý, jen podceňovaný. A právě nadešel čas to změnit. Napsal jsem totiž první českou knihu o návrhu webu. Začínajícím webdesignerům ukáže cestu, zkušeným doplní mezery a zadavatelům ušetří statisíce za špatně navržený web nebo nesmyslný tendr.“„Webdesign není jednoduchý, jen podceňovaný. A právě nadešel čas to změnit. Napsal jsem totiž první českou knihu o návrhu webu. Začínajícím webdesignerům ukáže cestu, zkušeným doplní mezery a zadavatelům ušetří statisíce za špatně navržený web nebo nesmyslný tendr.“„Webdesign není jednoduchý, jen podceňovaný. A právě nadešel čas to změnit. Napsal jsem totiž první českou knihu o návrhu webu. Začínajícím webdesignerům ukáže cestu, zkušeným doplní mezery a zadavatelům ušetří statisíce za špatně navržený web nebo nesmyslný tendr.“', now()::timestamp, now()::timestamp),
        (1000003, 1, 'Baroque Partners', '2016', 216, 'Slovenská republika', '9788027006441', 12, 14, '„Webdesign není jednoduchý, jen podceňovaný. A právě nadešel čas to změnit. Napsal jsem totiž první českou knihu o návrhu webu. Začínajícím webdesignerům ukáže cestu, zkušeným doplní mezery a zadavatelům ušetří statisíce za špatně navržený web nebo nesmyslný tendr.“', '„Webdesign není jednoduchý, jen podceňovaný. A právě nadešel čas to změnit. Napsal jsem totiž první českou knihu o návrhu webu. Začínajícím webdesignerům ukáže cestu, zkušeným doplní mezery a zadavatelům ušetří statisíce za špatně navržený web nebo nesmyslný tendr.“„Webdesign není jednoduchý, jen podceňovaný. A právě nadešel čas to změnit. Napsal jsem totiž první českou knihu o návrhu webu. Začínajícím webdesignerům ukáže cestu, zkušeným doplní mezery a zadavatelům ušetří statisíce za špatně navržený web nebo nesmyslný tendr.“„Webdesign není jednoduchý, jen podceňovaný. A právě nadešel čas to změnit. Napsal jsem totiž první českou knihu o návrhu webu. Začínajícím webdesignerům ukáže cestu, zkušeným doplní mezery a zadavatelům ušetří statisíce za špatně navržený web nebo nesmyslný tendr.“', now()::timestamp, now()::timestamp),
        (1000004, 1, 'Baroque Partners', '2016', 216, 'Slovenská republika', '9788027006441', 12, 14, '„Webdesign není jednoduchý, jen podceňovaný. A právě nadešel čas to změnit. Napsal jsem totiž první českou knihu o návrhu webu. Začínajícím webdesignerům ukáže cestu, zkušeným doplní mezery a zadavatelům ušetří statisíce za špatně navržený web nebo nesmyslný tendr.“', '„Webdesign není jednoduchý, jen podceňovaný. A právě nadešel čas to změnit. Napsal jsem totiž první českou knihu o návrhu webu. Začínajícím webdesignerům ukáže cestu, zkušeným doplní mezery a zadavatelům ušetří statisíce za špatně navržený web nebo nesmyslný tendr.“„Webdesign není jednoduchý, jen podceňovaný. A právě nadešel čas to změnit. Napsal jsem totiž první českou knihu o návrhu webu. Začínajícím webdesignerům ukáže cestu, zkušeným doplní mezery a zadavatelům ušetří statisíce za špatně navržený web nebo nesmyslný tendr.“„Webdesign není jednoduchý, jen podceňovaný. A právě nadešel čas to změnit. Napsal jsem totiž první českou knihu o návrhu webu. Začínajícím webdesignerům ukáže cestu, zkušeným doplní mezery a zadavatelům ušetří statisíce za špatně navržený web nebo nesmyslný tendr.“', now()::timestamp, now()::timestamp),
        (1000005, 1, 'Baroque Partners', '2016', 216, 'Slovenská republika', '9788027006441', 12, 14, '„Webdesign není jednoduchý, jen podceňovaný. A právě nadešel čas to změnit. Napsal jsem totiž první českou knihu o návrhu webu. Začínajícím webdesignerům ukáže cestu, zkušeným doplní mezery a zadavatelům ušetří statisíce za špatně navržený web nebo nesmyslný tendr.“', '„Webdesign není jednoduchý, jen podceňovaný. A právě nadešel čas to změnit. Napsal jsem totiž první českou knihu o návrhu webu. Začínajícím webdesignerům ukáže cestu, zkušeným doplní mezery a zadavatelům ušetří statisíce za špatně navržený web nebo nesmyslný tendr.“„Webdesign není jednoduchý, jen podceňovaný. A právě nadešel čas to změnit. Napsal jsem totiž první českou knihu o návrhu webu. Začínajícím webdesignerům ukáže cestu, zkušeným doplní mezery a zadavatelům ušetří statisíce za špatně navržený web nebo nesmyslný tendr.“„Webdesign není jednoduchý, jen podceňovaný. A právě nadešel čas to změnit. Napsal jsem totiž první českou knihu o návrhu webu. Začínajícím webdesignerům ukáže cestu, zkušeným doplní mezery a zadavatelům ušetří statisíce za špatně navržený web nebo nesmyslný tendr.“', now()::timestamp, now()::timestamp),
        (1000006, 1, 'Baroque Partners', '2016', 216, 'Slovenská republika', '9788027006441', 12, 14, '„Webdesign není jednoduchý, jen podceňovaný. A právě nadešel čas to změnit. Napsal jsem totiž první českou knihu o návrhu webu. Začínajícím webdesignerům ukáže cestu, zkušeným doplní mezery a zadavatelům ušetří statisíce za špatně navržený web nebo nesmyslný tendr.“', '„Webdesign není jednoduchý, jen podceňovaný. A právě nadešel čas to změnit. Napsal jsem totiž první českou knihu o návrhu webu. Začínajícím webdesignerům ukáže cestu, zkušeným doplní mezery a zadavatelům ušetří statisíce za špatně navržený web nebo nesmyslný tendr.“„Webdesign není jednoduchý, jen podceňovaný. A právě nadešel čas to změnit. Napsal jsem totiž první českou knihu o návrhu webu. Začínajícím webdesignerům ukáže cestu, zkušeným doplní mezery a zadavatelům ušetří statisíce za špatně navržený web nebo nesmyslný tendr.“„Webdesign není jednoduchý, jen podceňovaný. A právě nadešel čas to změnit. Napsal jsem totiž první českou knihu o návrhu webu. Začínajícím webdesignerům ukáže cestu, zkušeným doplní mezery a zadavatelům ušetří statisíce za špatně navržený web nebo nesmyslný tendr.“', now()::timestamp + (20 ||' minutes')::interval, now()::timestamp)
    ;
    
    INSERT INTO e_books (product_id, language_id, publisher, year, pages, format, detail, long_detail, created_at, updated_at) VALUES 
        (1000007, 1, 'vydavatelstvooo', '2012', 216, 'mp3', '„Webdesign není jednoduchý, jen podceňovaný. A právě nadešel čas to změnit. Napsal jsem totiž první českou knihu o návrhu webu. Začínajícím webdesignerům ukáže cestu, zkušeným doplní mezery a zadavatelům ušetří statisíce za špatně navržený web nebo nesmyslný tendr.“', '„Webdesign není jednoduchý, jen podceňovaný. A právě nadešel čas to změnit. Napsal jsem totiž první českou knihu o návrhu webu. Začínajícím webdesignerům ukáže cestu, zkušeným doplní mezery a zadavatelům ušetří statisíce za špatně navržený web nebo nesmyslný tendr.“„Webdesign není jednoduchý, jen podceňovaný. A právě nadešel čas to změnit. Napsal jsem totiž první českou knihu o návrhu webu. Začínajícím webdesignerům ukáže cestu, zkušeným doplní mezery a zadavatelům ušetří statisíce za špatně navržený web nebo nesmyslný tendr.“„Webdesign není jednoduchý, jen podceňovaný. A právě nadešel čas to změnit. Napsal jsem totiž první českou knihu o návrhu webu. Začínajícím webdesignerům ukáže cestu, zkušeným doplní mezery a zadavatelům ušetří statisíce za špatně navržený web nebo nesmyslný tendr.“', now()::timestamp, now()::timestamp),
        (1000008, 1, 'vydavatelstvooo', '2012', 216, 'mp3', '„Webdesign není jednoduchý, jen podceňovaný. A právě nadešel čas to změnit. Napsal jsem totiž první českou knihu o návrhu webu. Začínajícím webdesignerům ukáže cestu, zkušeným doplní mezery a zadavatelům ušetří statisíce za špatně navržený web nebo nesmyslný tendr.“', '„Webdesign není jednoduchý, jen podceňovaný. A právě nadešel čas to změnit. Napsal jsem totiž první českou knihu o návrhu webu. Začínajícím webdesignerům ukáže cestu, zkušeným doplní mezery a zadavatelům ušetří statisíce za špatně navržený web nebo nesmyslný tendr.“„Webdesign není jednoduchý, jen podceňovaný. A právě nadešel čas to změnit. Napsal jsem totiž první českou knihu o návrhu webu. Začínajícím webdesignerům ukáže cestu, zkušeným doplní mezery a zadavatelům ušetří statisíce za špatně navržený web nebo nesmyslný tendr.“„Webdesign není jednoduchý, jen podceňovaný. A právě nadešel čas to změnit. Napsal jsem totiž první českou knihu o návrhu webu. Začínajícím webdesignerům ukáže cestu, zkušeným doplní mezery a zadavatelům ušetří statisíce za špatně navržený web nebo nesmyslný tendr.“', now()::timestamp, now()::timestamp),
        (1000009, 1, 'vydavatelstvooo', '2012', 216, 'mp3', '„Webdesign není jednoduchý, jen podceňovaný. A právě nadešel čas to změnit. Napsal jsem totiž první českou knihu o návrhu webu. Začínajícím webdesignerům ukáže cestu, zkušeným doplní mezery a zadavatelům ušetří statisíce za špatně navržený web nebo nesmyslný tendr.“', '„Webdesign není jednoduchý, jen podceňovaný. A právě nadešel čas to změnit. Napsal jsem totiž první českou knihu o návrhu webu. Začínajícím webdesignerům ukáže cestu, zkušeným doplní mezery a zadavatelům ušetří statisíce za špatně navržený web nebo nesmyslný tendr.“„Webdesign není jednoduchý, jen podceňovaný. A právě nadešel čas to změnit. Napsal jsem totiž první českou knihu o návrhu webu. Začínajícím webdesignerům ukáže cestu, zkušeným doplní mezery a zadavatelům ušetří statisíce za špatně navržený web nebo nesmyslný tendr.“„Webdesign není jednoduchý, jen podceňovaný. A právě nadešel čas to změnit. Napsal jsem totiž první českou knihu o návrhu webu. Začínajícím webdesignerům ukáže cestu, zkušeným doplní mezery a zadavatelům ušetří statisíce za špatně navržený web nebo nesmyslný tendr.“', now()::timestamp, now()::timestamp)
    ;
    
    INSERT INTO merchandices (product_id, stock, shipping_time, detail, created_at, updated_at) VALUES
        (1000010, 12, 14, 'taska cierna', now()::timestamp, now()::timestamp)
    ;

    INSERT INTO photos (product_id, path, created_at, updated_at) VALUES
	(1000000, '1000000_2.jpg', now()::timestamp, now()::timestamp),
	(1000000, '1000000_3.jpg', now()::timestamp, now()::timestamp)
;
    
    INSERT INTO addresses (address, city, postal_code, phone, created_at, updated_at) VALUES
        ('Adminova adresa 11', 'Skalité', '02314', '+421000000', now()::timestamp, now()::timestamp)
    ;
    
    INSERT INTO users (address_id, name, email, password, created_at, updated_at, admin) VALUES
        (1, 'Admin', 'admin@admin.sk', '$2y$10\$N1cefrhjfcssfh83xdK.ZuV3DsoicT6HZGPnAlcMo6SjoR9RYOodO', now()::timestamp, now()::timestamp, true)
    ;
    
    INSERT INTO reviews (user_id, product_id, stars, content, created_at, updated_at) VALUES
        (1, 1000001, 5, 'Webdesign v dnešných dňoch je už pomerne rozsiahly odbor. Formálne vzdelávanie stále prakticky neexistuje a tak bohu vďaka za knihy tohto typu. Je to taký rozhľad z vrchu kopca do krajiny webdesignu. Nie je to návod ako sa dostať na konkrétne miesto. Ukáže vám ale že toto miesto existuje, aký je jeho význam a odporučí vám inú knihu/zdroj ak chcete ísť do hĺbky. Presne to, čo som v tomto čase potreboval.', now()::timestamp, now()::timestamp)
    ;
EOT;
}