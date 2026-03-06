<?php
/**
 * Viaggia Col Baffo — Destinations Data
 *
 * Associative array of all 6 continent/destination slugs with full editorial content.
 * Used by destinazione.php to render the destination template page.
 *
 * Each entry contains: name, hero_image, intro_paragraphs (3), practical_info (5),
 * see_also (4 sub-destinations), curiosita (3 facts).
 */

$destinations = [

    // ─────────────────────────────────────────────────────────────────────
    // AMERICA
    // ─────────────────────────────────────────────────────────────────────
    'america' => [
        'name'       => 'America',
        'hero_image' => 'https://images.unsplash.com/photo-1534430480872-3498386e7856?w=1600&q=80',

        'intro_paragraphs' => [
            'L\'America è una promessa che il mondo continua a mantenere. Dai grattacieli di Manhattan che sfiorano le nuvole alle infinite distese rosse del Grand Canyon, questo continente spazia tra scenari così diversi da sembrare appartenere a pianeti differenti. Con Lorenzo al tuo fianco, ogni angolo assume un significato più profondo: non solo un luogo da vedere, ma un\'esperienza da vivere pienamente.',
            'Viaggiare in piccolo gruppo cambia tutto. Niente code davanti ai monumenti affollati, niente guide con l\'ombrellino alzato. Solo tu, un gruppo di persone curiose come te, e Lorenzo che ti porta nei quartieri dove i locals vivono davvero — dal jazz di New Orleans ai mercati di Brooklyn, dai canyon silenziosi dell\'Utah alle foreste pluviali della Patagonia argentina.',
            'L\'America sa sorprendere anche chi pensa di conoscerla. La vastità del paesaggio, la generosità delle persone, la capacità di reinventarsi ad ogni angolo di strada: questo continente non smette mai di emozionare. Lasciati travolgere dalla grandezza di un\'esperienza pensata su misura, dove ogni dettaglio è curato perché il tuo viaggio sia davvero indimenticabile.',
        ],

        'practical_info' => [
            ['icon' => 'fa-solid fa-coins',    'label' => 'Valuta',             'value' => 'Dollaro USA (USD)'],
            ['icon' => 'fa-solid fa-language', 'label' => 'Lingua',             'value' => 'Inglese'],
            ['icon' => 'fa-solid fa-sun',      'label' => 'Stagione Migliore',  'value' => 'Aprile – Ottobre'],
            ['icon' => 'fa-solid fa-clock',    'label' => 'Fuso Orario',        'value' => 'UTC-5 a UTC-8'],
            ['icon' => 'fa-solid fa-passport', 'label' => 'Visto',              'value' => 'ESTA (online, ~€14)'],
        ],

        'see_also' => [
            [
                'name'        => 'New York',
                'image'       => 'https://images.unsplash.com/photo-1546436836-07a91091f160?w=800&q=80',
                'description' => "La città che non dorme mai, dove ogni quartiere racconta una storia diversa.\nDai pontili di Brooklyn a Central Park, Manhattan è un labirinto meraviglioso.",
            ],
            [
                'name'        => 'Grand Canyon',
                'image'       => 'https://images.unsplash.com/photo-1474044159687-1ee9f3a51722?w=800&q=80',
                'description' => "Uno squarcio nella crosta terrestre che toglie il respiro al primo sguardo.\nMiliardi di anni di storia geologica incisi nel rosso vivo della roccia.",
            ],
            [
                'name'        => 'California',
                'image'       => 'https://images.unsplash.com/photo-1506905925346-21bda4d32df4?w=800&q=80',
                'description' => "Surf, sequoie millenarie e vigneti dorati nella luce del tramonto.\nDa San Francisco alla Highway 1, la California è pura libertà.",
            ],
            [
                'name'        => 'Patagonia',
                'image'       => 'https://images.unsplash.com/photo-1531761535209-180857e963b9?w=800&q=80',
                'description' => "Alla fine del mondo, dove i ghiacciai si tuffano nel mare color turchese.\nTorri di granito, condor in volo e silenzi che riempiono l\'anima.",
            ],
        ],

        'curiosita' => [
            [
                'icon'  => 'fa-solid fa-mountain',
                'title' => 'Il canyon più profondo del mondo',
                'text'  => 'Il Grand Canyon raggiunge una profondità di 1.800 metri ed è lungo 446 chilometri. Ogni strato di roccia rappresenta un\'era geologica diversa, trasformando la discesa nel canyon in un viaggio indietro di due miliardi di anni.',
            ],
            [
                'icon'  => 'fa-solid fa-star',
                'title' => 'New York, 800 lingue parlate',
                'text'  => 'New York è la città più linguisticamente diversificata del pianeta: si stimano oltre 800 lingue parlate quotidianamente. Ogni quartiere conserva la memoria delle ondate migratorie che hanno costruito l\'America.',
            ],
            [
                'icon'  => 'fa-solid fa-leaf',
                'title' => 'La foresta di sequoie più antica',
                'text'  => 'In California vivono gli alberi più alti e tra i più antichi della Terra. Le sequoie costiere possono superare i 115 metri d\'altezza, mentre i giganteschi sequoiadendron della Sierra Nevada possono vivere oltre 3.000 anni.',
            ],
        ],
    ],

    // ─────────────────────────────────────────────────────────────────────
    // ASIA
    // ─────────────────────────────────────────────────────────────────────
    'asia' => [
        'name'       => 'Asia',
        'hero_image' => 'https://images.unsplash.com/photo-1528360983277-13d401cdc186?w=1600&q=80',

        'intro_paragraphs' => [
            'L\'Asia è il continente dei contrasti assoluti. Dove le metropoli futuriste sfiorano antichi templi dorati, dove il profumo dei mercati notturni si mescola all\'incenso dei monasteri buddhisti. Ogni paese è un universo a sé: il Giappone con la sua precisione poetica, Bali con la sua spiritualità lussureggiante, la Thailandia con la sua calorosa ospitalità, il Vietnam con le sue acque smeraldo.',
            'Viaggiare in Asia con Lorenzo significa andare oltre le attrazioni da cartolina. Significa sedersi a colazione con una famiglia giapponese a Kyoto, imparare a cucinare un curry autentico in un mercato di Chiang Mai, o navigare al tramonto nella baia di Halong su una barca di legno laccato. Momenti che non si dimenticano, possibili solo quando si viaggia con qualcuno che conosce davvero questi luoghi.',
            'Il continente più vasto della Terra non smette mai di stupire. Ogni viaggio in Asia è diverso dal precedente, ogni destinazione offre nuovi strati da scoprire. Con un gruppo piccolo e attento, ci si immerge in culture millenarie che ancora oggi pulsano di vita autentica, lontano dai circuiti di massa e vicini alle persone reali che abitano questi luoghi straordinari.',
        ],

        'practical_info' => [
            ['icon' => 'fa-solid fa-coins',    'label' => 'Valuta',             'value' => 'Varia per paese'],
            ['icon' => 'fa-solid fa-language', 'label' => 'Lingua',             'value' => 'Varia per paese'],
            ['icon' => 'fa-solid fa-sun',      'label' => 'Stagione Migliore',  'value' => 'Ottobre – Aprile'],
            ['icon' => 'fa-solid fa-clock',    'label' => 'Fuso Orario',        'value' => 'UTC+7 a UTC+9'],
            ['icon' => 'fa-solid fa-passport', 'label' => 'Visto',              'value' => 'Varia per paese'],
        ],

        'see_also' => [
            [
                'name'        => 'Giappone',
                'image'       => 'https://images.unsplash.com/photo-1493976040374-85c8e12f0c0e?w=800&q=80',
                'description' => "Tra torii rossi nelle foreste e grattacieli al neon, il Giappone è un racconto senza fine.\nIl paese del Sol Levante unisce antichità millenaria e modernità visionaria come nessun altro.",
            ],
            [
                'name'        => 'Bali',
                'image'       => 'https://images.unsplash.com/photo-1537996194471-e657df975ab4?w=800&q=80',
                'description' => "L\'isola degli Dei, dove le terrazze di riso scendono verso il mare in un abbraccio verde.\nTempli avvolti dal fumo dell\'incenso e tramonti che dipingono il cielo di arancio bruciato.",
            ],
            [
                'name'        => 'Thailandia',
                'image'       => 'https://images.unsplash.com/photo-1552465011-b4e21bf6e79a?w=800&q=80',
                'description' => "Il paese del sorriso, con i suoi templi dorati e le spiagge incastonate tra faraglioni calcarei.\nBangkok, Chiang Mai, Koh Lanta: ogni città è un capitolo di un\'epopea tropicale.",
            ],
            [
                'name'        => 'Vietnam',
                'image'       => 'https://images.unsplash.com/photo-1557750255-c76072a7aad1?w=800&q=80',
                'description' => "Un filo verde che scorre dall\'altopiano del nord alle isole cristalline del sud.\nHanoi, Hoi An, la baia di Halong: il Vietnam è poesia in movimento.",
            ],
        ],

        'curiosita' => [
            [
                'icon'  => 'fa-solid fa-torii-gate',
                'title' => 'Il Giappone ha più di 80.000 santuari',
                'text'  => 'In tutto il Giappone si contano oltre 80.000 santuari shintoisti e 77.000 templi buddhisti. Sono così diffusi che in molte città si trovano piccoli santuari nascosti tra i palazzi moderni, veri angoli di silenzio nel caos urbano.',
            ],
            [
                'icon'  => 'fa-solid fa-water',
                'title' => 'La baia di Halong, 1.969 isole',
                'text'  => 'La baia di Halong nel Vietnam settentrionale ospita quasi 2.000 isole e isolotti calcarei che emergono dal mare color smeraldo. La leggenda racconta che siano le squame di un drago che si tuffò nell\'oceano per proteggere il paese.',
            ],
            [
                'icon'  => 'fa-solid fa-globe',
                'title' => 'Bali, l\'isola dei festival',
                'text'  => 'A Bali si celebrano oltre 20.000 cerimonie religiose ogni anno. Gli abitanti sono induisti in un paese a maggioranza musulmana, e la loro spiritualità permea ogni aspetto della vita quotidiana, dai rituali mattutini alle offerte di fiori davanti alle porte di casa.',
            ],
        ],
    ],

    // ─────────────────────────────────────────────────────────────────────
    // EUROPA
    // ─────────────────────────────────────────────────────────────────────
    'europa' => [
        'name'       => 'Europa',
        'hero_image' => 'https://images.unsplash.com/photo-1467269204594-9661b134dd2b?w=1600&q=80',

        'intro_paragraphs' => [
            'L\'Europa è il continente che non finisce mai di sorprendere, anche chi pensava di conoscerla. Non i soliti tour di capitale in capitale, ma i luoghi dove l\'Europa è rimasta fedele a se stessa: le Highlands scozzesi avvolte dalla nebbia, le scogliere dell\'Islanda che si tuffano nell\'Atlantico, i villaggi bianchi del Portogallo affacciati sull\'oceano, le isole greche dove il tempo scorre più lento.',
            'Lorenzo ha attraversato l\'Europa in lungo e in largo, lontano dagli itinerari standardizzati. Sa dove trovare la trattoria perfetta nel cuore di Lisbona, il sentiero che porta alla cascata nascosta in Islanda, la caletta greca raggiungibile solo a piedi. In piccolo gruppo, l\'Europa si rivela nella sua dimensione più autentica: umana, sorprendente, mai ovvia.',
            'C\'è un\'Europa che non finisce sulle guide turistiche, fatta di mercati del mattino, caffè che profumano di storia, traghetti che attraversano fiordi nel silenzio dell\'alba. È l\'Europa di chi viaggia con curiosità e rispetto, lasciandosi guidare da qualcuno che ama davvero questi luoghi e sa trasmetterne l\'anima più profonda.',
        ],

        'practical_info' => [
            ['icon' => 'fa-solid fa-coins',    'label' => 'Valuta',             'value' => 'Euro / valuta locale'],
            ['icon' => 'fa-solid fa-language', 'label' => 'Lingua',             'value' => 'Varia per paese'],
            ['icon' => 'fa-solid fa-sun',      'label' => 'Stagione Migliore',  'value' => 'Maggio – Settembre'],
            ['icon' => 'fa-solid fa-clock',    'label' => 'Fuso Orario',        'value' => 'UTC+0 a UTC+3'],
            ['icon' => 'fa-solid fa-passport', 'label' => 'Visto',              'value' => 'Nessun visto (UE)'],
        ],

        'see_also' => [
            [
                'name'        => 'Islanda',
                'image'       => 'https://images.unsplash.com/photo-1531366936337-7c912a4589a7?w=800&q=80',
                'description' => "Cascate che precipitano da altopiani vulcanici e aurore boreali sul ghiaccio eterno.\nL\'Islanda è un pianeta a parte, dove la natura parla con voce potente e silenziosa.",
            ],
            [
                'name'        => 'Portogallo',
                'image'       => 'https://images.unsplash.com/photo-1555881400-74d7acaacd8b?w=800&q=80',
                'description' => "Azulejos che raccontano secoli di storia, fado che sale dai vicoli di Alfama al tramonto.\nIl Portogallo è calore, malinconia dolce e un\'ospitalità che sa di casa.",
            ],
            [
                'name'        => 'Grecia',
                'image'       => 'https://images.unsplash.com/photo-1533105079780-92b9be482077?w=800&q=80',
                'description' => "Culle bianche e cupole blu sull\'Egeo, taverne sul mare e colonne che sfiorano il cielo.\nLa Grecia è dove la civiltà occidentale ha preso forma, e dove il Mediterraneo è più bello.",
            ],
            [
                'name'        => 'Scozia',
                'image'       => 'https://images.unsplash.com/photo-1586348943529-beaae6c28db9?w=800&q=80',
                'description' => "Castelli avvolti dalla nebbia, lochs silenziosi e highlands che si perdono all\'orizzonte.\nLa Scozia è un romanzo gotico che prende vita sotto il cielo più drammatico d\'Europa.",
            ],
        ],

        'curiosita' => [
            [
                'icon'  => 'fa-solid fa-fire',
                'title' => 'Islanda, la terra del fuoco e del ghiaccio',
                'text'  => 'L\'Islanda ospita oltre 130 vulcani, dei quali circa 30 sono attivi. Il paese produce quasi il 100% della sua elettricità da fonti rinnovabili — geotermia e idroelettrico — grazie all\'enorme energia naturale del sottosuolo.',
            ],
            [
                'icon'  => 'fa-solid fa-anchor',
                'title' => 'Il Portogallo, la nazione marittima',
                'text'  => 'Il Portogallo fu la prima nazione europea a circum-navigare il continente africano e a stabilire rotte commerciali dirette con l\'Asia. Lisbona divenne nel XV secolo la capitale del commercio mondiale, un primato che ha lasciato un\'impronta profonda nell\'architettura e nella cultura.',
            ],
            [
                'icon'  => 'fa-solid fa-sun',
                'title' => 'La Grecia, 227 isole abitate',
                'text'  => 'La Grecia conta oltre 6.000 isole, di cui circa 227 sono abitate. Le Cicladi, con le loro case bianche e le cupole azzurre, sono tra le immagini più iconiche del Mediterraneo. Ogni isola ha un carattere distinto, dalla mondana Mykonos alla spirituale Patmos.',
            ],
        ],
    ],

    // ─────────────────────────────────────────────────────────────────────
    // AFRICA
    // ─────────────────────────────────────────────────────────────────────
    'africa' => [
        'name'       => 'Africa',
        'hero_image' => 'https://images.unsplash.com/photo-1516026672322-bc52d61a55d5?w=1600&q=80',

        'intro_paragraphs' => [
            'L\'Africa è il continente dell\'essenziale. Dove la vita si svolge nei suoi ritmi più antichi, dove il silenzio della savana al tramonto vale più di qualsiasi parola, dove un elefante che attraversa la strada ti ricorda che sei ospite in casa di qualcun altro. Viaggiare in Africa è un\'esperienza che riscrive le priorità, che amplia lo sguardo, che lascia un segno impossibile da cancellare.',
            'Con Lorenzo si entra nell\'Africa vera, quella lontana dai resort chiusi e dai safari da copertina patinata. Si dorme in tented camp immersi nella natura, si cena al lume delle stelle ascoltando il rumore della notte africana, si incontra i Masai nella loro dignità quotidiana. Ogni mattino si parte verso nuovi orizzonti, con la consapevolezza che quello che si vede nessun altro nella vita quotidiana potrà mai comprenderlo davvero.',
            'Dal Marocco arabo-berbero alle pianure infinite della Tanzania, dall\'arida magnificenza della Namibia alle foreste pluviali del Madagascar, l\'Africa è un continente plurale che non si lascia mai ridurre a un\'unica narrazione. È un viaggio dentro se stessi tanto quanto nel mondo esterno — la sfida più bella che un viaggiatore possa affrontare.',
        ],

        'practical_info' => [
            ['icon' => 'fa-solid fa-coins',    'label' => 'Valuta',             'value' => 'Varia per paese'],
            ['icon' => 'fa-solid fa-language', 'label' => 'Lingua',             'value' => 'Varia per paese'],
            ['icon' => 'fa-solid fa-sun',      'label' => 'Stagione Migliore',  'value' => 'Giugno – Ottobre (safari)'],
            ['icon' => 'fa-solid fa-clock',    'label' => 'Fuso Orario',        'value' => 'UTC+0 a UTC+3'],
            ['icon' => 'fa-solid fa-passport', 'label' => 'Visto',              'value' => 'Varia per paese'],
        ],

        'see_also' => [
            [
                'name'        => 'Marocco',
                'image'       => 'https://images.unsplash.com/photo-1539020140153-e479b8c22e70?w=800&q=80',
                'description' => "Medine labirintiche, riads nascosti e il deserto che incontra l\'oceano.\nIl Marocco è un\'esplosione di colori, profumi e suoni che non si dimentica.",
            ],
            [
                'name'        => 'Namibia',
                'image'       => 'https://images.unsplash.com/photo-1509316785289-025f5b846b35?w=800&q=80',
                'description' => "Le dune rosse del Sossusvlei contro un cielo infinito, il deserto più antico del mondo.\nLa Namibia è geometria perfetta, silenzio assoluto e bellezza senza filtri.",
            ],
            [
                'name'        => 'Tanzania',
                'image'       => 'https://images.unsplash.com/photo-1516426122078-c23e76319801?w=800&q=80',
                'description' => "Il Serengeti, il Kilimanjaro e Zanzibar: tre volti di un paese straordinario.\nLa Tanzania ospita la più grande migrazione animale del pianeta.",
            ],
            [
                'name'        => 'Madagascar',
                'image'       => 'https://images.unsplash.com/photo-1580181921200-42a9e0fd0e2d?w=800&q=80',
                'description' => "L\'ottavo continente, dove l\'80% delle specie animali non esiste altrove sulla Terra.\nForeste di baobab al tramonto e lemuri dagli occhi d\'oro: il Madagascar è un mondo a parte.",
            ],
        ],

        'curiosita' => [
            [
                'icon'  => 'fa-solid fa-horse',
                'title' => 'La grande migrazione del Serengeti',
                'text'  => 'Ogni anno circa 1,5 milioni di gnu, 200.000 zebre e 300.000 gazzelle migrano in circolo tra Tanzania e Kenya in cerca di pascoli freschi. È la più grande migrazione di animali terrestri del pianeta, uno spettacolo che non ha eguali nel mondo naturale.',
            ],
            [
                'icon'  => 'fa-solid fa-mountain',
                'title' => 'Il Kilimanjaro, tetto d\'Africa',
                'text'  => 'Il Kilimanjaro è il vulcano isolato più alto del mondo (5.895 m) e l\'unica montagna in Africa a ospitare ghiacciai permanenti, anche se questi si stanno rapidamente riducendo. La sua vetta calotta di neve è visibile da centinaia di chilometri di distanza nella pianura tanzaniana.',
            ],
            [
                'icon'  => 'fa-solid fa-tree',
                'title' => 'Il Madagascar, isola dei baobab',
                'text'  => 'Il Madagascar si è separato dall\'Africa circa 165 milioni di anni fa, sviluppando una fauna e una flora uniche al mondo. L\'isola ospita 6 delle 8 specie di baobab esistenti sul pianeta, incluso il famoso Viale dei Baobab, una delle immagini più iconiche dell\'Africa subsahariana.',
            ],
        ],
    ],

    // ─────────────────────────────────────────────────────────────────────
    // OCEANIA
    // ─────────────────────────────────────────────────────────────────────
    'oceania' => [
        'name'       => 'Oceania',
        'hero_image' => 'https://images.unsplash.com/photo-1501785888041-af3ef285b470?w=1600&q=80',

        'intro_paragraphs' => [
            'L\'Oceania è la parte del mondo dove il cielo sembra più grande. Dove la Grande Barriera Corallina brilla sotto una superficie color turchese, dove i fiordi della Nuova Zelanda sembrano disegnati da un dio con troppa fantasia, dove le spiagge di Fiji offrono un\'idea di paradiso che non è mai banale. Questo angolo remoto del pianeta premia chi fa la fatica di raggiungerlo con panorami e incontri irripetibili.',
            'Lorenzo ha esplorato l\'Oceania con la stessa curiosità di sempre, cercando quello che i tour operatori di massa ignorano: i whale watching di Tasmania, i giardini di corallo nascosti delle Fiji, le fattorie di pecore maori della Nuova Zelanda dove si dorme sotto cieli stellati di rara purezza. In piccolo gruppo si riesce ad andare dove le comitive numerose non arrivano mai.',
            'Viaggiare in Oceania è anche immergersi in culture antichissime e profondamente vive. Gli Aborigeni australiani conservano la più antica tradizione orale del mondo. I Maori neozelandesi hanno reinventato il modo di vivere la propria identità. I polinesiani di Fiji hanno un rapporto con l\'oceano che tocca qualcosa di ancestrale in ognuno di noi. L\'Oceania non è solo paesaggio: è incontro.',
        ],

        'practical_info' => [
            ['icon' => 'fa-solid fa-coins',    'label' => 'Valuta',             'value' => 'AUD / NZD'],
            ['icon' => 'fa-solid fa-language', 'label' => 'Lingua',             'value' => 'Inglese'],
            ['icon' => 'fa-solid fa-sun',      'label' => 'Stagione Migliore',  'value' => 'Dicembre – Marzo'],
            ['icon' => 'fa-solid fa-clock',    'label' => 'Fuso Orario',        'value' => 'UTC+8 a UTC+13'],
            ['icon' => 'fa-solid fa-passport', 'label' => 'Visto',              'value' => 'ETA Australia (online)'],
        ],

        'see_also' => [
            [
                'name'        => 'Australia',
                'image'       => 'https://images.unsplash.com/photo-1523482580672-f109ba8cb9be?w=800&q=80',
                'description' => "L\'Uluru al tramonto, la Grande Barriera Corallina e Sydney che abbraccia il porto più bello del mondo.\nL\'Australia è un continente intero in un unico Paese, impossibile da ridurre a una sola visita.",
            ],
            [
                'name'        => 'Nuova Zelanda',
                'image'       => 'https://images.unsplash.com/photo-1507699622108-4be3abd695ad?w=800&q=80',
                'description' => "Fiordi che si aprono su acque buie come inchiostro, vulcani attivi e ghiacciai che scendono al mare.\nLa Nuova Zelanda è la natura nella sua forma più spettacolare e intatta.",
            ],
            [
                'name'        => 'Fiji',
                'image'       => 'https://images.unsplash.com/photo-1596394723269-b2cbca4e6313?w=800&q=80',
                'description' => "333 isole di corallo sparse nell\'oceano, dove il sorriso è la prima lingua parlata.\nFiji è l\'arcipelago dei colori impossibili: turchese, bianco, verde smeraldo.",
            ],
            [
                'name'        => 'Tasmania',
                'image'       => 'https://images.unsplash.com/photo-1548268770-66184a21657e?w=800&q=80',
                'description' => "Un\'isola selvaggia dove il 40% del territorio è parco nazionale o riserva naturale.\nLa Tasmania è l\'Australia primitiva, con foreste antiche e coste che sfidano l\'Antartide.",
            ],
        ],

        'curiosita' => [
            [
                'icon'  => 'fa-solid fa-water',
                'title' => 'La Grande Barriera Corallina, visibile dallo spazio',
                'text'  => 'La Grande Barriera Corallina si estende per oltre 2.300 km al largo del Queensland ed è la più grande struttura vivente del pianeta. È l\'unico ecosistema biologico visibile dallo spazio, ma sta subendo un forte stress termico a causa del riscaldamento oceanico.',
            ],
            [
                'icon'  => 'fa-solid fa-kiwi-bird',
                'title' => 'Nuova Zelanda, il paese degli uccelli',
                'text'  => 'Prima dell\'arrivo degli esseri umani, la Nuova Zelanda era abitata quasi esclusivamente da uccelli, molti dei quali incapaci di volare perché non avevano predatori terrestri. Il kiwi, simbolo nazionale, è l\'unico uccello al mondo con le narici sulla punta del becco.',
            ],
            [
                'icon'  => 'fa-solid fa-star',
                'title' => 'Le Fiji e il meridiano di cambio data',
                'text'  => 'Le isole Fiji sono tra i primi posti al mondo a vedere sorgere il sole ogni mattina, a pochi gradi dal 180° meridiano. Gli arcipelaghi polinesiani hanno una delle culture dell\'ospitalità più genuine del Pacifico: "Bula!" è il saluto che si sente ad ogni angolo, e significa davvero "Vita!".',
            ],
        ],
    ],

    // ─────────────────────────────────────────────────────────────────────
    // MEDIO ORIENTE
    // ─────────────────────────────────────────────────────────────────────
    'medio-oriente' => [
        'name'       => 'Medio Oriente',
        'hero_image' => 'https://images.unsplash.com/photo-1548079397-1d9b72d29e46?w=1600&q=80',

        'intro_paragraphs' => [
            'Il Medio Oriente è il luogo dove la civiltà ha preso forma. Petra scolpita nella roccia rosa di Giordania, il deserto del Wadi Rum che sembra la superficie di Marte, i souq di Muscat dove le spezie profumano l\'aria, la skyline di Dubai che sfida la gravità: questa regione racchiude in pochi chilometri quadrati più storia, bellezza e contrasti di quanto molti continenti riescano a offrire.',
            'Viaggiare in Medio Oriente con Lorenzo significa abbattere i pregiudizi costruiti da decenni di narrazione distorta. La gente di questa regione è tra le più ospitali del mondo: un tè offerto da uno sconosciuto a Petra, una cena condivisa con una famiglia beduina nel deserto, la voce del muezzin all\'alba in una medina silenziosa. Sono i momenti che trasformano un viaggio in un\'esperienza che cambia il modo di guardare il mondo.',
            'Dall\'antichità di Gerusalemme alla modernità visionaria di Dubai, dalla natura selvaggia del Wadi Rum alle spiagge cristalline dell\'Oman, il Medio Oriente offre una varietà sorprendente di esperienze. Con un gruppo piccolo e rispettoso, è possibile accedere a una profondità di incontro con queste culture che i tour di massa non potranno mai raggiungere.',
        ],

        'practical_info' => [
            ['icon' => 'fa-solid fa-coins',    'label' => 'Valuta',             'value' => 'USD / valuta locale'],
            ['icon' => 'fa-solid fa-language', 'label' => 'Lingua',             'value' => 'Arabo'],
            ['icon' => 'fa-solid fa-sun',      'label' => 'Stagione Migliore',  'value' => 'Ottobre – Aprile'],
            ['icon' => 'fa-solid fa-clock',    'label' => 'Fuso Orario',        'value' => 'UTC+2 a UTC+4'],
            ['icon' => 'fa-solid fa-passport', 'label' => 'Visto',              'value' => 'Varia per paese'],
        ],

        'see_also' => [
            [
                'name'        => 'Giordania',
                'image'       => 'https://images.unsplash.com/photo-1563177978-4c5ebf35c1f8?w=800&q=80',
                'description' => "Petra, la città rosa scolpita nella roccia, e il Wadi Rum sotto un cielo di stelle infinite.\nLa Giordania è storia millenaria e ospitalità beduina, un incontro che resta nel cuore.",
            ],
            [
                'name'        => 'Oman',
                'image'       => 'https://images.unsplash.com/photo-1578894381163-e72c17f2d45f?w=800&q=80',
                'description' => "Deserto, montagne, fiordi e spiagge tropicali nello stesso Paese.\nL\'Oman è il Medio Oriente autentico, dove la tradizione convive con una modernità discreta.",
            ],
            [
                'name'        => 'Dubai',
                'image'       => 'https://images.unsplash.com/photo-1512453979798-5ea266f8880c?w=800&q=80',
                'description' => "Il futuro costruito nel deserto in meno di cinquant\'anni, tra grattacieli e souk antichi.\nDubai è la città che ha riscritto le regole dell\'architettura e dell\'ambizione umana.",
            ],
            [
                'name'        => 'Israele',
                'image'       => 'https://images.unsplash.com/photo-1547507896-0fa79db0e2ae?w=800&q=80',
                'description' => "Gerusalemme, Città Santa di tre religioni, e Tel Aviv, metropoli vibrante sul Mediterraneo.\nIsraele è il crocevia della storia umana, dove ogni pietra racconta tremila anni di memoria.",
            ],
        ],

        'curiosita' => [
            [
                'icon'  => 'fa-solid fa-monument',
                'title' => 'Petra, la città perduta dei Nabatei',
                'text'  => 'Petra fu costruita dai Nabatei tra il IV secolo a.C. e il II secolo d.C. e rimase "perduta" per il mondo occidentale fino al 1812. La città è scavata direttamente nella roccia arenaria rosata ed è così vasta che solo il 15% circa di essa è stato scavato dagli archeologi.',
            ],
            [
                'icon'  => 'fa-solid fa-building',
                'title' => 'Dubai, da villaggio di pescatori a megalopoli',
                'text'  => 'Nel 1960 Dubai era un villaggio di pescatori con circa 40.000 abitanti. Oggi è una metropoli di oltre 3 milioni di persone, con il grattacielo più alto del mondo (828 m) e isole artificiali visibili dallo spazio. Questa trasformazione in meno di 60 anni non ha precedenti nella storia.',
            ],
            [
                'icon'  => 'fa-solid fa-water',
                'title' => 'Il Mar Morto, il punto più basso della Terra',
                'text'  => 'Il Mar Morto, condiviso tra Giordania e Israele, si trova a 430 metri sotto il livello del mare — il punto più basso della superficie terrestre. La concentrazione di sale è quasi 10 volte quella degli oceani normali, rendendo impossibile affondarvi e conferendo all\'acqua proprietà terapeutiche conosciute dall\'antichità.',
            ],
        ],
    ],

];
