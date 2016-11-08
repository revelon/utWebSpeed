<?php

echo "\n\n\n";

$res   = [];
$recall = array('fails' => 0, 'nude' => [], 'notnude' => []);
$v2 = array('fails' => 0, 'nude' => [], 'notnude' => []);



$safes = 

['https://qzprod.files.wordpress.com/2015/08/8138372741_b34b8904bf_o.jpg?quality=80&strip=all&w=1600',
'https://encrypted-tbn3.gstatic.com/images?q=tbn:ANd9GcQoose8wb0ZJu53m74Fbhk8-GLH1JSTj6wOitCgeX-2t5yk18nFOw',
'https://encrypted-tbn0.gstatic.com/images?q=tbn:ANd9GcQ1A_r8uGK51mbPLZUq7XHzrWQvMw0pLmt3mkcwXXJfV7iniNLKhw',
'http://www.desktop-image.com/wp-content/uploads/2014/06/american-girl-backgrounds2.jpg',
'http://webneel.com/daily/sites/default/files/images/daily/05-2013/21-3d-girl-model-by-maskdemon.preview.jpg',
'https://s-media-cache-ak0.pinimg.com/736x/92/e8/64/92e86465da1f0c6b3e5673bf59c749af.jpg',
'https://encrypted-tbn3.gstatic.com/images?q=tbn:ANd9GcSHptn3j4hHp-rvNOROOqceQ5nXIrWqHpl9UVm_qG6d2hpfsCzY',
'https://encrypted-tbn3.gstatic.com/images?q=tbn:ANd9GcQh8KwboTRb9yk1QIcG6XEOmorhLsRfFSNmDc18QQZTczPkcMlfFQ',
'http://creativefan.com/important/cf/2012/03/hairstyles-for-little-girls/cute-little-girl.jpg',
'https://encrypted-tbn2.gstatic.com/images?q=tbn:ANd9GcTgGeFqL4XCQvf1Rk-AtEZcModaBOE1UNJeBzISl7jXi8DSFLAjHw',
'https://s3-eu-west-1.amazonaws.com/spiked-online.com/images/beach_body.jpg',
'https://thumbs.dreamstime.com/z/little-girl-playing-two-puppies-shocked-kid-pink-t-shirt-blue-shorts-bare-feet-white-green-meadow-40962637.jpg',
'http://main-designyoutrust.netdna-ssl.com/wp-content/uploads/2012/08/Cute-Little-Girls-111.jpg?iv=216',
'http://maramostafa.com/wp-content/uploads/2010/11/makeup.jpg',
'https://i.ytimg.com/vi/dcjxVVHTeU4/maxresdefault.jpg',
'http://www.somedayilllearn.com/wp-content/uploads/2015/01/Little-boys-hair-johnsonspartners-1.jpg',
'http://www.nzfilm.co.nz/sites/nzfc/files/styles/film-promotional-image/public/images/films/promotional-images/3_KEY%20STILL_mattgrace_9119.jpg?itok=Rw9fvZq6',
'https://s3.amazonaws.com/aphs.worldnomads.com/rosibud/9970/Mekong_2008_04_11_259x.jpg',
'https://c2.staticflickr.com/4/3432/3792206353_4855357015_b.jpg',
'http://i1.mirror.co.uk/incoming/article6766682.ece/ALTERNATES/s615b/baby.jpg',
'http://www.besthairstyles2013.com/wp-content/uploads/2013/09/kim-kardashian-blonde-1.jpg',
'http://weluvcelebs.com/wp-content/uploads/2014/01/emilia-clarke-blonde.jpg',
'http://www.alizeeart.com/wp-content/uploads/2014/05/blonde-album.jpg',
'http://www.hairworldmag.com/wp-content/uploads/2014/01/strawberry-blonde-hair-color.jpg',
'http://cdn1-www.beautyriot.com/assets/uploads/gallery/pamela-anderson/pamela-anderson-long-sexy-blonde.jpg',
'http://www.hairstylesupdate.com/wp-content/uploads/2015/06/Blonde-Hair-Color-Ideas_06.jpg',
'http://cdn.hotblondesnaked.com/2015-05-11/314149_01.jpg',
'http://wallpapersqq.net/wp-content/uploads/2016/02/blonde-Elsa-Hosk.jpg',
'http://wallpoper.com/images/00/39/79/45/women-redheads_00397945.jpg',
'http://1.bp.blogspot.com/-wxvs6w3igE8/TkWMr3wlCaI/AAAAAAAAAxM/KMhZzGsifaw/s1600/redheads-sexy-redhead-demotivational-poster-1282435291.jpg',
'http://i.imgur.com/zcwN7PG.jpg',
'http://2.bp.blogspot.com/_FqQEHPUq6Zc/S8m2QegZXHI/AAAAAAAAMZo/HpHo6-OdqzM/s1600/sexy_redhead_hot.jpg',
'http://www.bikeme.tv/wordpress/wp-content/uploads/2014/04/1497809_481446868640796_1958803657_n.jpg',
'http://1.bp.blogspot.com/_FqQEHPUq6Zc/S8m2Q66SfuI/AAAAAAAAMZ4/aZdVs7B-W7w/s1600/sexy-redhead-ariel-atelier-hot.jpg',
'http://3.bp.blogspot.com/-zMhcM2otAHw/UVnom4mNS2I/AAAAAAAASUQ/UZMJoFLpntI/s1600/gorgeous+redhead+cleavage+hot.jpg',
'http://i.huffpost.com/gen/1085932/thumbs/o-REDHEAD-BABY-NAMES-facebook.jpg',
'http://ihmc2.files.wordpress.com/2012/03/ariel-femjoy-redhead-green-eyes-nic.jpg',
'http://www.trainbodyandmind.com/wp-content/uploads/2010/12/Workout-in-the-park-hot-redhead-02.jpg',
'http://www.quick-break.net/c/2012/11/19/Sexy_redhead_Nemo_Valkyrja.jpeg',
'http://redheadnextdoor.com/wp-content/uploads/2013/12/selfie-redhead24.jpg',
'http://media-cache-ak0.pinimg.com/236x/17/98/ba/1798ba96ac02b31b5df1e1b805cf3e23.jpg',
'http://cdn.images.express.co.uk/img/dynamic/130/750x445/726972.jpg',
'http://s2.favim.com/orig/35/beautiful-beauty-blonde-body-brunette-Favim.com-281212.jpg',
'http://s5.favim.com/orig/74/body-brunette-car-cute-Favim.com-758343.jpg',
'http://s8.favim.com/orig/150329/bath-time-body-brunette-selfie-Favim.com-2604308.jpg',
'http://i.dailymail.co.uk/i/pix/2013/01/07/article-2258522-16C9DABC000005DC-498_306x550.jpg'

];

$porns = 

['http://www.mrstiff.com/uploads/pornstar/anetta-smrhova/anetta-smrhova-107.jpg',
'http://boypost.com/wp-content/uploads/2014/07/new-helix-gay-boys-3.jpg',
'http://gayteenboys18.com/wp-content/uploads/2013/02/gay-teen-boys-sex-1_thumb.jpg',
'http://justimg.com/pics/101/nudist-boys-having-sex.jpg',
'http://nakedboys.biz/wp-content/uploads/naked-boys/young-naked-boy.jpg',
'http://mandyf.files.wordpress.com/2011/08/800px-lesbians_in_bed.jpg',
'http://sexyhd.net/wallpaper-original/wallpapers/lesbians-kissing-ass-899.jpg',
'http://tse3.mm.bing.net/th?id=OIP.M759edda09caaada2186b253f4325c099H0&pid=15.1',
'http://blog.mysticpornsites.com/wp-content/uploads/bwlesbians/black-and-white-pussies-get-dripping-wet/04.jpg',
'http://pingping.fantasti.cc/big/a/v/r/avrgjoe/avrgjoe_27c534.jpg',
'http://gallerycontent.bwlesbians.com/vids/black-girl-kissing-white-pussy/01.jpg',
'http://blackandwhitelesbians.org/blog/wp-content/uploads/2014/09/black-and-white-lesbians-fucking.jpg',
'http://www.lesbianpornvideos.com/images/galleries/0599/27298/a8b8499827ee56b398dd9cf02fe7de89.jpg',
'http://tse4.mm.bing.net/th?id=OIP.M2998fe49fb1dc32d75ec343f86bc43a6o0&pid=15.1',
'http://www.fets.com/index/wp-content/uploads/2010/04/LegTexture1020-Milena_Pantyhose_Upskirt_PotD.jpg',
'http://www.maturexxxpics.net/images/fuqhzz/big-tits-brunette-ass-stockings-pantyhose-high-heels-european-danica-collins-close-up-mature-milf-09.jpg',
'http://sexyteenstockings.com/wp-content/uploads/2013/01/teenkasia_pantyhose_slave_08.jpg',
'http://tgp.superglam.com/tgp3/346586/images/014.jpg',
'http://s1d5.turboimagehost.com/sp/2367efa5353224a41950d006af7d1b04/anetta_keys_red_and_white_033.jpg',
'http://www.poornstars.net/galleries_ns/anetta_keys_hc_painter/pics/08.jpg',
'http://blondethumb.com/wp-content/uploads/2012/10/photo-Blonde-Blowjob-Hardcore-MILF-Pornstar-62684519.jpg',
'http://thatishardcore.net/porn-sex-hardcore-pics/2014/01/blonde-taking-a-ride.jpg',
'http://zorglist.com/files/data_1/3740/Image/hkiY-amateur_great_blonde_wife_fuck_15.jpg',
'http://blondethumb.com/wp-content/uploads/2012/07/photo-Blonde-Blowjob-120924895.jpg',
'http://blondethumb.com/wp-content/uploads/2012/07/photo-Blonde-Hot-Teen-104130125.jpg',
'http://blondethumb.com/wp-content/uploads/2012/12/photo-Blonde-Cumshot-119097227.jpg',
'http://www.eroticdb.com/wp-content/uploads/2014/07/swedish_nude_blonde0.jpg',
'http://girlcontent.com/wp-content/uploads/2012/10/photo-Big-Tits-Blonde-Petite-Teen-93096190.jpg',
'http://russiasexygirls.com/wp-content/uploads/2014/01/Gorgeous-blonde-has-really-nice-boobs-and-big-pussy-1.jpg',
'http://i.imgur.com/2BJowym.jpg',
'http://blondethumb.com/wp-content/uploads/2012/08/photo-Blonde-Pussy-479699666.jpg',
'http://www.babemansion.com/wp-content/uploads/2014/01/platinum-blonde-milf-nude3.jpg',
'http://www.sashablonde.org/wp-content/uploads/2010/05/04_12.jpg',
'http://jizzman.com/wp-content/uploads/Blonde-Beauty-Goldie-Takes-Big-Black-Cock-Blacked.jpg',
'http://justimg.com/pics/2970/blonde-teen-girl-strip.jpg',
'http://www.damplips.com/wp-content/uploads/2013/06/nude-blonde-goddess-21.jpg',
'http://www.bustyteengallery.com/images/015j0/nude-blonde-teens-big-tits.jpg',
'http://jizzman.com/wp-content/uploads/Blonde-Trophy-Wife-Gigi-Allens-Enjoys-Big-Black-Cock-Blacked.jpg',
'http://girlcontent.com/wp-content/uploads/2012/04/photo-Blonde-Emo-Petite-Teen-340640259.jpg',
'http://2.bp.blogspot.com/-3aAx8CTx6HE/Tx_JEXgbw9I/AAAAAAAADsA/rA5hcpjHik8/s1600/1.jpg',
'http://www.hairycult.com/pictures/hardcore/hairy-fever/hairy-redhead-teen-rides-cock/big.jpg',
'http://www.girlswelustfor.com/wp/wp-content/uploads/2012/11/tumblr_m8jtbc9bwk1ql2w82o1_12801.jpg',
'http://www.pezporn.com/media/images/4/redhead-pussy-pics/redhead-pussy-pics-122664.jpg',
'http://www.randomsexiness.net/wp-content/uploads/2013/04/13016-Redhead-babe-flashing-her-shaved-pussy.jpg',
'http://www.blondesandrednecks.com/uploads/RedheadBlueEyesNiceTits516.jpg',
'http://www.xxxswim.com/blowjob/redhead-dick-sucker/redhead-dick-sucker-blowjob.jpg',
'http://cyberkatz.com/wp-content/uploads/2012/09/photo-Babe-Red-Head-867783154.jpg'

];



foreach ($porns as $f) {
	$addr = json_encode(['image' => $f]);
	$addr = '"' . $f . '"';

    $a = shell_exec("curl -X POST -d '{$addr}' -H 'Content-Type: application/json' https://api.recallmi.com/module/adult");
    echo "recall-api -> " . $a;
    $reply = json_decode($a);
    if (isset($reply->confidence)) {
        if ($reply->nude) $recall['nude'][] = $addr;
        else $recall['notnude'][] = $addr;
    } else {
        $recall['fails'] += 1;
    }

    $a = shell_exec("curl -X POST -d '{$addr}' -H 'Content-Type: application/json' -H 'Authorization: Simple simELXZ6Dab23/2W+KD+e3zA7cr1' https://api.algorithmia.com/v1/algo/sfw/NudityDetectioni2v/0.2.3");
    echo "algo-v2 -> " . $a;
    $reply = json_decode($a);
    if (isset($reply->result)) {
        if ($reply->result->nude) $v2['nude'][] = $addr;
        else $v2['notnude'][] = $addr;
    } else {
        $v2['fails'] += 1;
    }
}

echo "\nV2 :"; var_dump($v2);

echo "\nRecall :"; var_dump($recall);

echo "\n\nFinished\n";

