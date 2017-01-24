Takze, ako funguju hentie optimalizacie?

* vyrazy v Dictionary su najprv rozdelenie do niekolkych kategorii:
** singleWords - asociativne pole ktore priraduje k jednotlivym slovam vsetky vyrazy, v ktorych sa vyskytuju
** wordBoundaries - asociativne pole ktore pre viacslovne vyrazy zoberie posledne pismeno slova + prve pismeno dalsieho slova a priradi ich podobnym sposobm ako singleWords
** substringWords - zoznam slov ktore sa musia vyhladavat cez substring. Ide o slova ktore su PARTIAL_MATCH, a maju len jedno slovo (takze sa nedaju hladat cez singleWords ani wordBoundaries)
* toto cele je cache-ovane

* pre kazde hladanie sa vytvori novy Optimizer, ktory dokaze z vyrazov v Dictionary vyfiltrovat len tie, ktore sa tykaju priamo posudzovanej sentence.
* Optimizer vyfiltruje pre dalsie pouzitie tieto vyrazy:
** pre kazde slovo sentence zoberie zo singleWords vyrazy pre dane slovo
** pre kazde hranice slova (posledne + prve pismeno za sebou iducich slov) zoberie z wordBoundaries vyrazy ktorych sa to tyka
** cele to doplni substringWords ktore su v danej sentence (len tu naozaj musi pouzit substring)
* tym sa z 2000+ potencialnych vyrazov stane 5-30 potencialnych vyrazov
* dalsie hladanie a pocitanie je ovela rychlejsie

