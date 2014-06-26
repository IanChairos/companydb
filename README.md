companydb
=========
Zpracování úkolu při přijímacím řízení do firmy. Úkolem bylo udělat jednoduchou databázi firem a jejich kontaktních osob.

Postup instalace projektu (Debian Linux,localhost):

1] Rozbalíme projekt do složky X
2] Nastavíme složky X/temp,X/log a X/app/model/entity/proxy pro zápis
3] V souboru X/app/config/config.neon nastavíme připojeni do databáze
4] Nastavíme si VirtualHost do složky X
5] Přidáme doménu z VirtualHost do /etc/hosts
6] Pokud není, zapneme v Apachi symlinkem mod_rewrite modul
7] V prohlížeči přejdeme na danou doménu
8] Objeví se chyba s SQL dotazem na vytvoření databáze - ten spustíme ručně na SQL serveru
9] Pak dáme [Retry] a zmáčknem tlačítko [Init database] - tím se vytvoří tabulky (failsafe: X/scripts/lib/InitDatabaseSchema.sql > SQL)
10] Pokud chceme, necháme naplnit databázi nahodnými hodnotami [Randomize database]
11] It's ready to fly! :)
